#!/usr/bin/env python3
"""
ffmpeg_validator.py
===================
Validates and optionally normalises a video file to meet Shopee's upload
requirements using ffprobe (inspection) and ffmpeg (re-encoding).

Shopee requirements enforced
-----------------------------
  - Resolution  : 1280 × 720 minimum (720p)
  - Video codec : H.264 (libx264)
  - Audio codec : AAC
  - Frame rate  : 30 fps
  - Max duration: 60 seconds
  - Max size    : 50 MB

Usage
-----
    # Validate only (no re-encoding):
    python3 ffmpeg_validator.py --action validate --input /path/to/video.mp4

    # Validate + normalize if needed:
    python3 ffmpeg_validator.py --action normalize --input /path/to/video.mp4 --output /path/to/output.mp4

Output (stdout — always JSON)
------------------------------
    {
        "success": true|false,
        "output_path": "/abs/path/to/output.mp4",   # same as input for validate-only
        "metadata": {
            "duration":    int,      # seconds
            "width":       int,
            "height":      int,
            "video_codec": str,      # e.g. "h264"
            "audio_codec": str,      # e.g. "aac"
            "fps":         float,
            "file_size_mb": float
        },
        "rejection_reason": ""       # non-empty if success=false
    }
"""

import argparse
import json
import os
import subprocess
import sys

# ---------------------------------------------------------------------------
# Shopee requirements
# ---------------------------------------------------------------------------

SHOPEE_MIN_WIDTH      = 1280
SHOPEE_MIN_HEIGHT     = 720
SHOPEE_TARGET_FPS     = 30
SHOPEE_MAX_DURATION   = 60      # seconds
SHOPEE_MAX_SIZE_MB    = 50.0
SHOPEE_VIDEO_CODEC    = "h264"
SHOPEE_AUDIO_CODEC    = "aac"

FFMPEG_BIN  = os.environ.get("FFMPEG_BINARY",  "ffmpeg")
FFPROBE_BIN = os.environ.get("FFPROBE_BINARY", "ffprobe")


# ---------------------------------------------------------------------------
# Metadata extraction
# ---------------------------------------------------------------------------

def get_metadata(input_path: str) -> dict | None:
    """
    Use ffprobe to extract stream metadata from a video file.

    Returns a metadata dict or None on error.
    """
    cmd = [
        FFPROBE_BIN,
        "-v", "quiet",
        "-print_format", "json",
        "-show_streams",
        "-show_format",
        input_path,
    ]

    try:
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=30,
        )
        probe = json.loads(result.stdout)
    except (subprocess.TimeoutExpired, json.JSONDecodeError, FileNotFoundError) as exc:
        return None

    video_stream = next(
        (s for s in probe.get("streams", []) if s.get("codec_type") == "video"),
        None,
    )
    audio_stream = next(
        (s for s in probe.get("streams", []) if s.get("codec_type") == "audio"),
        None,
    )
    fmt = probe.get("format", {})

    if not video_stream:
        return None

    # Parse frame rate (can be "30/1" or "30000/1001")
    fps = 0.0
    r_frame_rate = video_stream.get("r_frame_rate", "0/1")
    try:
        num, den = r_frame_rate.split("/")
        fps = round(int(num) / int(den), 2) if int(den) > 0 else 0.0
    except (ValueError, ZeroDivisionError):
        pass

    duration = int(float(fmt.get("duration", 0)))
    size_bytes = int(fmt.get("size", 0))

    return {
        "duration":     duration,
        "width":        int(video_stream.get("width",  0)),
        "height":       int(video_stream.get("height", 0)),
        "video_codec":  video_stream.get("codec_name", "").lower(),
        "audio_codec":  (audio_stream.get("codec_name", "").lower() if audio_stream else ""),
        "fps":          fps,
        "file_size_mb": round(size_bytes / (1024 * 1024), 2),
    }


# ---------------------------------------------------------------------------
# Validation
# ---------------------------------------------------------------------------

def check_requirements(meta: dict) -> list[str]:
    """
    Return a list of unmet requirements.
    Empty list means the video is valid for Shopee upload.
    """
    issues = []

    if meta["width"] < SHOPEE_MIN_WIDTH or meta["height"] < SHOPEE_MIN_HEIGHT:
        issues.append(
            f"Resolution {meta['width']}x{meta['height']} is below "
            f"minimum {SHOPEE_MIN_WIDTH}x{SHOPEE_MIN_HEIGHT}"
        )

    if meta["video_codec"] != SHOPEE_VIDEO_CODEC:
        issues.append(
            f"Video codec '{meta['video_codec']}' is not H.264 (h264)"
        )

    if meta["audio_codec"] and meta["audio_codec"] != SHOPEE_AUDIO_CODEC:
        issues.append(
            f"Audio codec '{meta['audio_codec']}' is not AAC"
        )

    if meta["duration"] > SHOPEE_MAX_DURATION:
        issues.append(
            f"Duration {meta['duration']}s exceeds maximum {SHOPEE_MAX_DURATION}s"
        )

    if meta["file_size_mb"] > SHOPEE_MAX_SIZE_MB:
        issues.append(
            f"File size {meta['file_size_mb']}MB exceeds maximum {SHOPEE_MAX_SIZE_MB}MB"
        )

    # FPS: accept 29.97 (≈30) and 30
    if meta["fps"] > 0 and not (28.0 <= meta["fps"] <= 31.0):
        issues.append(
            f"Frame rate {meta['fps']} fps is not 30 fps"
        )

    return issues


# ---------------------------------------------------------------------------
# Normalisation
# ---------------------------------------------------------------------------

def normalize(input_path: str, output_path: str, meta: dict) -> dict:
    """
    Re-encode the video to meet all Shopee requirements using ffmpeg.

    Returns the standard result dict.
    """
    os.makedirs(os.path.dirname(os.path.abspath(output_path)), exist_ok=True)

    # Scale: upscale to 1280x720 if too small, keep aspect ratio otherwise.
    # vf scale: -2 means "choose value divisible by 2 to maintain aspect ratio"
    scale_filter = (
        f"scale='max({SHOPEE_MIN_WIDTH},iw)':'max({SHOPEE_MIN_HEIGHT},ih)':"
        f"force_original_aspect_ratio=increase,"
        f"crop={SHOPEE_MIN_WIDTH}:{SHOPEE_MIN_HEIGHT}"
    )

    cmd = [
        FFMPEG_BIN,
        "-y",                         # overwrite output
        "-i", input_path,
        "-t", str(SHOPEE_MAX_DURATION),  # trim to 60s
        # Video
        "-vf", scale_filter,
        "-c:v", "libx264",
        "-preset", "fast",
        "-crf", "23",                 # quality (18=high, 28=low)
        "-r", str(SHOPEE_TARGET_FPS),
        "-profile:v", "high",
        "-level", "4.0",
        # Audio
        "-c:a", "aac",
        "-b:a", "128k",
        "-ar", "44100",
        # Container
        "-movflags", "+faststart",    # optimize for streaming
        "-f", "mp4",
        output_path,
    ]

    try:
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=300,
        )

        if result.returncode != 0:
            return _error_result(
                meta,
                f"ffmpeg normalization failed: {result.stderr[-500:]}",
            )

        # Verify the output file
        if not os.path.isfile(output_path):
            return _error_result(meta, "Normalized file was not created.")

        new_meta = get_metadata(output_path)
        if new_meta is None:
            return _error_result(meta, "Could not read normalized file metadata.")

        size_mb = round(os.path.getsize(output_path) / (1024 * 1024), 2)
        if size_mb > SHOPEE_MAX_SIZE_MB:
            return _error_result(
                new_meta,
                f"Normalized file ({size_mb}MB) still exceeds {SHOPEE_MAX_SIZE_MB}MB limit.",
            )

        return {
            "success":          True,
            "output_path":      os.path.abspath(output_path),
            "metadata":         new_meta,
            "rejection_reason": "",
        }

    except subprocess.TimeoutExpired:
        return _error_result(meta, "ffmpeg normalization timed out after 300s.")
    except FileNotFoundError:
        return _error_result(meta, f"ffmpeg binary not found at '{FFMPEG_BIN}'.")
    except Exception as exc:  # noqa: BLE001
        return _error_result(meta, f"Unexpected error: {exc}")


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def _error_result(meta: dict, reason: str) -> dict:
    return {
        "success":          False,
        "output_path":      "",
        "metadata":         meta or {},
        "rejection_reason": reason,
    }


# ---------------------------------------------------------------------------
# Main actions
# ---------------------------------------------------------------------------

def action_validate(input_path: str) -> dict:
    """
    Validate a video file and return a result dict without re-encoding.
    """
    if not os.path.isfile(input_path):
        return _error_result({}, f"File not found: {input_path}")

    meta = get_metadata(input_path)
    if meta is None:
        return _error_result({}, "Failed to read video metadata with ffprobe.")

    issues = check_requirements(meta)

    if issues:
        return {
            "success":          False,
            "output_path":      "",
            "metadata":         meta,
            "rejection_reason": "; ".join(issues),
        }

    return {
        "success":          True,
        "output_path":      os.path.abspath(input_path),
        "metadata":         meta,
        "rejection_reason": "",
    }


def action_normalize(input_path: str, output_path: str) -> dict:
    """
    Validate first; if valid, return as-is. Otherwise re-encode.
    """
    if not os.path.isfile(input_path):
        return _error_result({}, f"File not found: {input_path}")

    meta = get_metadata(input_path)
    if meta is None:
        return _error_result({}, "Failed to read video metadata with ffprobe.")

    # If duration is way too long (>2× limit), reject outright — normalizing
    # would produce a very different video than intended.
    if meta["duration"] > SHOPEE_MAX_DURATION * 2:
        return _error_result(
            meta,
            f"Duration {meta['duration']}s is too long to normalize (max acceptable: {SHOPEE_MAX_DURATION * 2}s).",
        )

    issues = check_requirements(meta)

    if not issues:
        # Already valid — no re-encoding needed
        return {
            "success":          True,
            "output_path":      os.path.abspath(input_path),
            "metadata":         meta,
            "rejection_reason": "",
        }

    # Needs normalization
    return normalize(input_path, output_path, meta)


# ---------------------------------------------------------------------------
# CLI entry point
# ---------------------------------------------------------------------------

def main() -> None:
    parser = argparse.ArgumentParser(
        description="Validate/normalize a video for Shopee upload requirements.",
    )
    parser.add_argument(
        "--action",
        required=True,
        choices=["validate", "normalize"],
        help="validate: inspect only | normalize: re-encode if needed",
    )
    parser.add_argument("--input",  required=True, help="Absolute path to the input video file")
    parser.add_argument("--output", default="",    help="Absolute path for the normalized output (normalize only)")
    args = parser.parse_args()

    if args.action == "validate":
        result = action_validate(args.input)
    else:
        if not args.output:
            # Auto-generate output path next to input
            base, _ = os.path.splitext(args.input)
            args.output = base + "_normalized.mp4"
        result = action_normalize(args.input, args.output)

    print(json.dumps(result, ensure_ascii=False))
    sys.exit(0 if result["success"] else 1)


if __name__ == "__main__":
    main()
