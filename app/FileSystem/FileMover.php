<?php

declare(strict_types= 1);

namespace App\FileSystem;

interface FileMover
{
    public function move(string $sourcePath, string $targetPath): bool;
}