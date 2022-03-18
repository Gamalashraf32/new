<?php

namespace App\Traits;


use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ImageUpload
{
    function uploadImage($image, $directory, $quality, $width = false, $height = false): string
    {
        // making a name to the image
        $file_extension = $image->getClientOriginalExtension();
        $file_name = Str::random(20) . '.' . $file_extension;
        $path = 'gmS1gBS6N1plepfpcPCi/uploaded/' . $directory;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $image_resize = Image::make($image->getRealPath());
        if ($image->getSize() > 5120) {
            $image_resize->resize(1000, 700);
        }
        if ($width == true or $height == true) {
            $image_resize->resize($width, $height);
        }

        $image_resize->save($path . '/' . $file_name, $quality);
        return $path . '/' . $file_name;
    }

}
