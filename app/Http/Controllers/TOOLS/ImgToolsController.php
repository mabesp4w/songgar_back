<?php

namespace App\Http\Controllers\TOOLS;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImgToolsController extends Controller
{
    public function addImage($folder, $file)
    {
        // set name image and get extension
        $name = time() . '.' . $file->getClientOriginalExtension();
        // destination path
        return Storage::putFileAs($folder, $file, $name);
    }

    public function addBase64Image($folder, $base64Image)
    {
        // Decode base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $type = strtolower($type[1]); // jpg, png, gif, etc.
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $image = base64_decode($base64Image);
            $imageName = $folder . '/' . Str::random(10) . '.' . $type;

            // Save the image to storage
            Storage::disk('public')->put($imageName, $image);

            return $imageName;
        }

        return null;
    }
}
