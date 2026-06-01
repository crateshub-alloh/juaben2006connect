<?php
/**
 * Handles all file uploads securely.
 */
class UploadController
{
    private string $uploadDir;

    public function __construct(string $subDir = 'general')
    {
        $this->uploadDir = UPLOADS_PATH . '/' . $subDir . '/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload an image and return the stored filename on success.
     */
    public function uploadImage(array $file, int $maxMb = MAX_UPLOAD_MB): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error code: ' . $file['error']);
        }

        $maxBytes = $maxMb * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new RuntimeException("File too large. Max {$maxMb}MB.");
        }

        // Validate MIME via content sniffing (not just extension)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
            throw new RuntimeException('Invalid file type. Only JPEG, PNG, WebP, GIF allowed.');
        }

        $ext      = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => throw new RuntimeException('Unsupported image type.'),
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Failed to save uploaded file.');
        }

        // Compress/resize if GD available
        if (extension_loaded('gd') && in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            $this->resize($dest, $mime, 1200, 900);
        }

        return $filename;
    }

    public function deleteFile(string $filename): void
    {
        $path = $this->uploadDir . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function resize(string $path, string $mime, int $maxW, int $maxH): void
    {
        [$origW, $origH] = getimagesize($path);
        if ($origW <= $maxW && $origH <= $maxH) return;

        $ratio  = min($maxW / $origW, $maxH / $origH);
        $newW   = (int)round($origW * $ratio);
        $newH   = (int)round($origH * $ratio);

        $src = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default      => null,
        };

        if (!$src) return;

        $dst = imagecreatetruecolor($newW, $newH);
        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        match ($mime) {
            'image/jpeg' => imagejpeg($dst, $path, 85),
            'image/png'  => imagepng($dst, $path, 7),
            'image/webp' => imagewebp($dst, $path, 85),
            default      => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}
