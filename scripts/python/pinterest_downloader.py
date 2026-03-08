#!/usr/bin/env python3
"""
pinterest_downloader.py
=======================
Downloads a Pinterest video (or any yt-dlp-supported URL) to the specified
output path in the best available MP4 format.

Usage
-----
    python3 pinterest_downloader.py --url <URL> --output <OUTPUT_PATH>

Output (stdout — always JSON)
------------------------------
    Success: {"success": true,  "filepath": "/abs/path/to/file.mp4", "error": ""}
    Failure: {"success": false, "filepath": "",                       "error": "<reason>"}

Exit codes
----------
    0  — success
    1  — download or validation error
"""

import argparse
import json
import os
import sys

try:
    import yt_dlp
except ImportError:
    print(json.dumps({
        "success": False,
        "filepath": "",
        "error": "yt-dlp is not installed. Run: pip install yt-dlp",
    }))
    sys.exit(1)


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def _build_ydl_opts(output_path: str) -> dict:
    """
    Build yt-dlp options to download the best MP4 (video + audio) up to 1080p.

    Format preference:
      1. Best combined MP4 up to 1080p.
      2. Best video-only + best audio → merged into MP4 via ffmpeg.
      3. Any best format as fallback.
    """
    output_dir = os.path.dirname(output_path)
    if output_dir:
        os.makedirs(output_dir, exist_ok=True)

    return {
        # Best MP4 up to 1080p; fall back to any best format
        "format": (
            "bestvideo[ext=mp4][height<=1080]+bestaudio[ext=m4a]"
            "/bestvideo[ext=mp4][height<=1080]+bestaudio"
            "/bestvideo[height<=1080]+bestaudio"
            "/best[ext=mp4]"
            "/best"
        ),
        "outtmpl": output_path,
        # Merge into MP4 container (requires ffmpeg)
        "merge_output_format": "mp4",
        # Do not write extra metadata files
        "writeinfojson":   False,
        "writedescription": False,
        "writethumbnail":  False,
        # Suppress progress output so only JSON goes to stdout
        "quiet":           True,
        "no_warnings":     True,
        # Retry settings
        "retries":         3,
        "fragment_retries": 3,
        # User-agent rotation to avoid blocks
        "http_headers": {
            "User-Agent": (
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
                "AppleWebKit/537.36 (KHTML, like Gecko) "
                "Chrome/124.0.0.0 Safari/537.36"
            ),
        },
    }


def _find_actual_output(requested_path: str) -> str:
    """
    yt-dlp may alter the extension (e.g. .webm → .mp4 after merge).
    Look for the file on disk with the expected base name.
    """
    if os.path.isfile(requested_path):
        return requested_path

    base, _ = os.path.splitext(requested_path)
    for ext in (".mp4", ".mkv", ".webm", ".mov"):
        candidate = base + ext
        if os.path.isfile(candidate):
            return candidate

    return requested_path  # Return as-is; caller will detect failure


def _file_size_mb(path: str) -> float:
    try:
        return round(os.path.getsize(path) / (1024 * 1024), 2)
    except OSError:
        return 0.0


# ---------------------------------------------------------------------------
# Core download function
# ---------------------------------------------------------------------------

def download(url: str, output_path: str) -> dict:
    """
    Download the video at *url* and save it to *output_path*.

    Returns a dict compatible with the PHP service contract:
        {
            "success":  bool,
            "filepath": str,   # absolute path if success, else ""
            "error":    str,   # empty string if success
            "size_mb":  float, # file size in MB (0 on failure)
        }
    """
    # TODO: Pinterest-specific cookie / session injection if needed.
    # Pinterest may require authentication for some pins:
    #   opts["cookiefile"] = "/path/to/pinterest_cookies.txt"

    ydl_opts = _build_ydl_opts(output_path)

    try:
        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            info = ydl.extract_info(url, download=True)

        actual_path = _find_actual_output(output_path)

        if not os.path.isfile(actual_path):
            return {
                "success":  False,
                "filepath": "",
                "error":    "Download completed but output file was not found.",
                "size_mb":  0.0,
            }

        return {
            "success":  True,
            "filepath": os.path.abspath(actual_path),
            "error":    "",
            "size_mb":  _file_size_mb(actual_path),
        }

    except yt_dlp.utils.DownloadError as exc:
        return {
            "success":  False,
            "filepath": "",
            "error":    f"DownloadError: {exc}",
            "size_mb":  0.0,
        }
    except yt_dlp.utils.ExtractorError as exc:
        return {
            "success":  False,
            "filepath": "",
            "error":    f"ExtractorError: {exc}",
            "size_mb":  0.0,
        }
    except Exception as exc:  # noqa: BLE001
        return {
            "success":  False,
            "filepath": "",
            "error":    f"Unexpected error: {exc}",
            "size_mb":  0.0,
        }


# ---------------------------------------------------------------------------
# CLI entry point
# ---------------------------------------------------------------------------

def main() -> None:
    parser = argparse.ArgumentParser(
        description="Download a Pinterest/social video via yt-dlp.",
    )
    parser.add_argument("--url",    required=True, help="Video URL to download")
    parser.add_argument("--output", required=True, help="Absolute output file path (.mp4)")
    args = parser.parse_args()

    result = download(url=args.url, output_path=args.output)

    # Always print clean JSON to stdout (PHP will parse this)
    print(json.dumps(result, ensure_ascii=False))
    sys.exit(0 if result["success"] else 1)


if __name__ == "__main__":
    main()
