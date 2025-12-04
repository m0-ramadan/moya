<?php

namespace App\Traits;

trait UploadFileTrait
{
    private function uploadFile($path, $file)
    {
        $image_name = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path("images/" . $path), $image_name);
        return "images/" . $path . "/" . $image_name;
    }

    private function generalUploadFile($path, $file)
    {
        $file_name = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path($path), $file_name);
        return $file_name;
    }
    
    
    function uploadImage($folder, $image)
{
    //$image->store( $folder);
    $filename = $image->hashName();
    $path2 = public_path("images/".$folder);
    $image->move($path2,$filename);
    $path = 'images/' . $folder . '/' . $filename;
    return $path;
}

function  uploadImages($image,$folder,$imagename){
    $image->move(public_path('images/'.$folder), $imagename);
      $path = 'images/' . $folder . '/' . $imagename;
    return $path;

}

}
