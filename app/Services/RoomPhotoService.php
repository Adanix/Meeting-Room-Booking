<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoomPhotoService
{
    public function uploadPhoto(UploadedFile $file): string
    {
        // 1. Double check MIME
        $mime = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (! in_array($mime, $allowedMimes, true)) {
            throw ValidationException::withMessages([
                'photo' => __('The uploaded file must be a valid JPEG, PNG, or WebP image.'),
            ]);
        }

        // 2. Validate dimensions
        $imageSize = @getimagesize($file->getRealPath());
        if (! $imageSize || $imageSize[0] < 200 || $imageSize[1] < 150) {
            throw ValidationException::withMessages([
                'photo' => __('Image dimensions must be at least 200x150 pixels.'),
            ]);
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'rooms/' . Str::uuid() . '.' . strtolower($extension);

        Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));

        return $filename;
    }

    public function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
