<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingScreenshot;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ScreenshotController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'nullable|string|max:200',
            'order'     => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image'     => 'required|image|max:8192',
        ]);

        $manager  = new ImageManager(new Driver());
        $img      = $manager->read($request->file('image')->getRealPath());
        $img->scaleDown(1600, 1000);

        $filename = 'landing/' . Str::uuid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        @mkdir(dirname($fullPath), 0755, true);
        $img->toJpeg(90)->save($fullPath);

        LandingScreenshot::create([
            'title'      => $request->title ?: null,
            'image_path' => $filename,
            'order'      => $request->order,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.screenshots.index')
            ->with('success', 'Screenshot adicionado com sucesso.');
    }

    public function update(Request $request, LandingScreenshot $screenshot)
    {
        $request->validate([
            'title'     => 'nullable|string|max:200',
            'order'     => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image'     => 'nullable|image|max:8192',
        ]);

        $data = [
            'title'     => $request->title ?: null,
            'order'     => $request->order,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $manager  = new ImageManager(new Driver());
            $img      = $manager->read($request->file('image')->getRealPath());
            $img->scaleDown(1600, 1000);

            $filename = 'landing/' . Str::uuid() . '.jpg';
            $fullPath = storage_path('app/public/' . $filename);
            @mkdir(dirname($fullPath), 0755, true);
            $img->toJpeg(90)->save($fullPath);

            $data['image_path'] = $filename;
        }

        $screenshot->update($data);

        return redirect()->route('admin.screenshots.index')
            ->with('success', 'Screenshot atualizado com sucesso.');
    }
}
