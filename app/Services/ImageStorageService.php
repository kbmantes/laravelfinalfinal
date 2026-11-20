<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class ImageStorageService
{
    public function save(?UploadedFile $file, bool $avatar = false): ?string
    {
        if (!$file) return null;
        if (!$file->isValid() || $file->getSize() > 2 * 1024 * 1024) throw new InvalidArgumentException('Choose a valid image no larger than 2 MB.');
        $path = $file->getRealPath();
        $info = @getimagesize($path);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) throw new InvalidArgumentException('Only genuine JPEG, PNG, or WebP images are allowed.');
        if ($info[0] > 4000 || $info[1] > 4000 || $info[0] * $info[1] > 12000000) throw new InvalidArgumentException('Use an image up to 4000 pixels per side and 12 megapixels.');
        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path), IMAGETYPE_PNG => @imagecreatefrompng($path), IMAGETYPE_WEBP => @imagecreatefromwebp($path), default => false,
        };
        if (!$source) throw new InvalidArgumentException('This image could not be decoded.');
        if ($avatar) {
            $side = min($info[0], $info[1]); $width = $height = 256;
            $sx = (int)(($info[0] - $side) / 2); $sy = (int)(($info[1] - $side) / 2); $sw = $sh = $side;
        } else {
            $ratio = min(1, 800 / max($info[0], $info[1])); $width = max(1, (int)round($info[0] * $ratio)); $height = max(1, (int)round($info[1] * $ratio));
            $sx = $sy = 0; $sw = $info[0]; $sh = $info[1];
        }
        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, $sx, $sy, $width, $height, $sw, $sh);
        ob_start(); $ok = imagejpeg($canvas, null, 85); $bytes = ob_get_clean();
        imagedestroy($source); imagedestroy($canvas);
        if (!$ok || !$bytes) throw new RuntimeException('Unable to prepare the image. Check PHP GD is enabled.');
        $name = bin2hex(random_bytes(16)).'.jpg';
        if (!Storage::disk('local')->put('uploads/'.$name, $bytes)) throw new RuntimeException('Unable to save the image.');
        return $name;
    }

    public function delete(?string $name): void
    {
        if ($name && preg_match('/^[A-Za-z0-9-]+\.jpg$/D', $name)) Storage::disk('local')->delete('uploads/'.$name);
    }
}
