<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait ImageTrait
{
    use UploadFileTrait;

    protected function uploadImg(UploadedFile $file, string $filename, string $folder): string
    {
        // storeFile accepts (folder, file, filename)
        return $this->storeFile($folder, $file, pathinfo($filename, PATHINFO_FILENAME));
    }
}
