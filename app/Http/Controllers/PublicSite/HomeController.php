<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $disk = Storage::disk('public');

        $localDisk = Storage::disk('local');

        $laboratories = Laboratory::query()
            ->orderBy('name')
            ->get()
            ->map(function (Laboratory $laboratory) use ($disk, $localDisk) {
                $photoPaths = collect($laboratory->photos ?? [])
                    ->filter()
                    ->map(function (string $path) {
                        $path = ltrim($path, '/');

                        if (str_starts_with($path, 'public/')) {
                            $path = substr($path, strlen('public/'));
                        }

                        return $path;
                    })
                    ->values();

                $coverPath = $photoPaths->first();

                if ($coverPath && ! $disk->exists($coverPath) && $localDisk->exists($coverPath)) {
                    $disk->put($coverPath, $localDisk->get($coverPath));
                }

                $laboratory->cover_photo_url = $coverPath && $disk->exists($coverPath)
                    ? $disk->url($coverPath)
                    : null;

                $laboratory->gallery_photo_urls = $photoPaths
                    ->filter(function (string $path) use ($disk, $localDisk) {
                        if ($disk->exists($path)) {
                            return true;
                        }

                        if ($localDisk->exists($path)) {
                            $disk->put($path, $localDisk->get($path));

                            return true;
                        }

                        return false;
                    })
                    ->map(fn (string $path) => $disk->url($path))
                    ->all();

                if (! $laboratory->cover_photo_url && $laboratory->gallery_photo_urls) {
                    $laboratory->cover_photo_url = $laboratory->gallery_photo_urls[0];
                }

                return $laboratory;
            });

        return view('public.home', [
            'laboratories' => $laboratories,
        ]);
    }
}
