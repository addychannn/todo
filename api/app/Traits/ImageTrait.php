<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Images;
use App\Models\Gallery;

use Image;

trait ImageTrait
{
    use FilesTrait;

    protected $thumbsizes = [
        'sm' => [128, 128],
        'md' => [256, 256],
        'lg' => [512, 512],
        'xl' => [1024, 1024]
    ];

    
    public function displayImage($hash, $thumbsize = null){

        $image = Images::byHashOrFail($hash);
        $pathToFile = '';
        $ext = 'png';

        $tsize = '';
        $size = '';

        if($thumbsize != null && $this->thumbsizes[$thumbsize] != null && !in_array($image->extension, ["gif", "svg"])){
            $tsize = 'thumbs'.$this->DS;
            $size = '_'.$thumbsize;
        }

        if($image && file_exists(storage_path($image->path.$image->name))){
            $ext = pathinfo(storage_path($image->path.$image->name), PATHINFO_EXTENSION);
            $file_name = pathinfo(storage_path($image->path.$image->name), PATHINFO_FILENAME);
            
            $pathToFile = storage_path($image->path.$tsize.$file_name.$size.'.'.$ext);
            if(!file_exists($pathToFile)){
                return response(['message' => 'Image Not Found!'], 404);
            }
        }else{
            return response(['message' => 'Image Not Found!'], 404);
        }
        if($ext == 'svg'){
            $ext = 'svg+xml';
        }

        $headers = ['Content-Type' => 'image/'.$ext];
        return response()->file($pathToFile, $headers);
    }

    public function uploadImage(Request $request, $location = "")
    {
        $request->validate([
            'uid' => 'nullable|required_if:is_last,false',
            'timestamp' => 'nullable|required_if:is_last,false',
            'file' => 'required|file|max:2048',
            'name' => 'required',
            'chunk' => 'required|integer',
            'ext' => 'required',
            'is_last' => 'required|in:1,0',
            'gallery_id' => 'nullable|exists:galleries,id',
        ]);

        $chunk = $request->input('chunk');
        $isMain = $request->input('is_main', false);

        if($chunk <= 0){
            $validatedData = $request->validate([
                'file' => 'required|file|max:2048|mimes:jpg,png,jpeg,gif,svg',
            ]);
        }
        $uid_timestamp = $this->upload($request);
        if($request->input('is_last') == 1){
            $uid = $uid_timestamp['uid'];
            $timestamp = $uid_timestamp['timestamp'];
            $name = $request->input('name');
            $gallery_id = $request->input('gallery_id', null);
            return $this->merge_image_chunks($uid, $timestamp, $name, $gallery_id, $location, $isMain);
        }else{
            return response($uid_timestamp);
        }
    }

    private function merge_image_chunks($uid, $timestamp, $alt, $gallery_id, $location, $isMain = false){
        $path = Storage::disk('local')->path('chunks'.$this->DS.$uid.'_'.$timestamp);

        $files = File::allFiles($path);

        $ext = pathinfo(pathinfo($files[0], PATHINFO_FILENAME), PATHINFO_EXTENSION);
        $file_name = pathinfo(pathinfo($files[0], PATHINFO_FILENAME), PATHINFO_FILENAME);
        $path = Storage::disk('local')->path('chunks'.$this->DS.$uid.'_'.$timestamp);
        $name = $uid.'_'.$timestamp.'.'.$ext;
        $folder_name = trim($location) == '' ? 'images' : 'images'.$this->DS.$location;
        $file_location = Storage::disk('local')->path($folder_name.$this->DS.$name);
        File::ensureDirectoryExists(Storage::disk('local')->path($folder_name));

        for($i = 0; $i < count($files); $i++){
            File::append($file_location, file_get_contents($path.$this->DS.$file_name.'.'.$ext.'.part_'.$i));
        }
        
        File::deleteDirectory($path);

        if($ext != 'gif' && $ext != 'svg'){
            foreach($this->thumbsizes as $key => $value){
                $thumb_location = Storage::disk('local')->path($folder_name.$this->DS.'thumbs');
                $file_path = Storage::disk('local')->path($folder_name.$this->DS.'thumbs'.$this->DS.$uid.'_'.$timestamp.'_'.$key.'.'.$ext);
                File::ensureDirectoryExists($thumb_location);
                File::copy($file_location, $file_path);
                $this->createThumbnail($file_path, $value[0], $value[1]);
            }
        }

        if($isMain && !!$gallery_id){
            Images::where('gallery_id', $gallery_id)->update(['main' => false]);
        }

        $image = Images::create([
            'name' => $name,
            'path' => 'app'.$this->DS.$folder_name.$this->DS,
            'alt' => $alt == null ? $file_name : $alt,
            'mime' => File::mimeType($file_location),
            'extension' => $ext,
            'main' => $isMain,
            'gallery_id' => $gallery_id,
        ]);



        return $image;
    }

    private function createThumbnail($path, $width, $height){
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}