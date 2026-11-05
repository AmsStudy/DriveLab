<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuizImageService
{
    /**
     * Format ekstensi gambar yang diizinkan.
     */
    protected static array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Kompresi dan simpan gambar dengan mempertahankan ketajaman (anti-blur).
     *
     * @param UploadedFile $file
     * @param string $subfolder 'questions' atau 'options'
     * @param int $maxWidth Maksimal lebar gambar (1200px untuk soal, 650px untuk opsi)
     * @return string|null Path relatif di disk public
     */
    public static function optimizeAndStore(UploadedFile $file, string $subfolder = 'questions', int $maxWidth = 1200): ?string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::$allowedExtensions, true)) {
            return null;
        }

        // Pastikan direktori tujuan ada di storage/app/public/quiz_images/{subfolder}
        $targetDirectory = 'quiz_images/' . trim($subfolder, '/');
        Storage::disk('public')->makeDirectory($targetDirectory);

        $filename = Str::random(32) . '.webp';
        $relativeStoragePath = $targetDirectory . '/' . $filename;
        $absoluteDestination = Storage::disk('public')->path($relativeStoragePath);

        // Jika GD tidak tersedia, simpan langsung
        if (!function_exists('imagecreatefromstring')) {
            $path = $file->storeAs($targetDirectory, Str::random(32) . '.' . $ext, 'public');
            return $path;
        }

        $imageContent = file_get_contents($file->getRealPath());
        $sourceImage = @imagecreatefromstring($imageContent);

        if (!$sourceImage) {
            // Fallback jika decode gagal
            return $file->storeAs($targetDirectory, Str::random(32) . '.' . $ext, 'public');
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Resize proporsional jika melebihi maxWidth
        $targetImage = $sourceImage;
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);

            // Gunakan bicubic interpolation untuk hasil gambar yang sangat tajam dan tidak blur
            $scaled = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($scaled, false);
            imagesavealpha($scaled, true);
            imagecopyresampled($scaled, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            $targetImage = $scaled;
        }

        // Simpan sebagai WebP jika didukung (kualitas 85: tajam, tidak blur, ukuran sangat efisien)
        if (function_exists('imagewebp')) {
            \imagewebp($targetImage, $absoluteDestination, 85);
        } elseif (function_exists('imagejpeg')) {
            // Fallback JPEG kualitas 85 jika WebP tidak didukung
            $filename = Str::random(32) . '.jpg';
            $relativeStoragePath = $targetDirectory . '/' . $filename;
            $absoluteDestination = Storage::disk('public')->path($relativeStoragePath);
            \imagejpeg($targetImage, $absoluteDestination, 85);
        } else {
            // Fallback PNG jika WebP & JPEG tidak tersedia
            $filename = Str::random(32) . '.png';
            $relativeStoragePath = $targetDirectory . '/' . $filename;
            $absoluteDestination = Storage::disk('public')->path($relativeStoragePath);
            \imagepng($targetImage, $absoluteDestination, 6);
        }

        if ($targetImage !== $sourceImage) {
            imagedestroy($targetImage);
        }
        imagedestroy($sourceImage);

        return $relativeStoragePath;
    }

    /**
     * Hapus gambar dari disk public jika ada.
     */
    public static function deleteImage(?string $path): void
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
