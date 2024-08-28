<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\ImageTrait;

class ImagesController extends Controller
{
    use ImageTrait;
    
    public function display($hash)
    {
        return $this->displayImage($hash, null);
    }

    public function display_thumb($thumbsize, $hash){
        return $this->displayImage($hash, $thumbsize);
    }

    public function logo($size = null){
        $file = 'baguioseal';
        $file_ext = ".png";
        $location = 'app\\public\\';
        $sizes = array('xs', 'sm', 'md', 'lg');
        if(in_array($size, $sizes)){
            $file = $file."-".$size;
        }

        $ext = pathinfo(storage_path($location.$file.$file_ext), PATHINFO_EXTENSION);
        $file_name = pathinfo(storage_path($location.$file.$file_ext), PATHINFO_FILENAME);
        
        $pathToFile = storage_path($location.$file.$file_ext);
        if(!file_exists($pathToFile)){
            return response(['message' => 'Image Not Found!'], 404);
        }
        
        if($ext == 'svg'){
            $ext = 'svg+xml';
        }

        $headers = ['Content-Type' => 'image/'.$ext];
        return response()->file($pathToFile, $headers);
    }
}
