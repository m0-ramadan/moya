<?php

namespace App\Http\Traits;

use App\Traits\UploadFileTrait;
use Illuminate\Http\UploadedFile;

trait ImageTrait
{
    use UploadFileTrait;

    protected function uploadImg(UploadedFile $file, string $filename, string $folder): string
    {
        return $this->storeFile($folder, $file, pathinfo($filename, PATHINFO_FILENAME));
    }
}
