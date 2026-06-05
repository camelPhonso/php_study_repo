<?php

declare(strict_types= 1);

namespace App\FileSystem;

class NativeFileMover implements FileMover
{
    public function move(string $sourcePath, string $targetPath): bool
    {
        return move_uploaded_file($sourcePath, $targetPath);
    }
}