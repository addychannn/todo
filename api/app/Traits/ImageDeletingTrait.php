<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait ImageDeletingTrait
{
    use LoggerTrait;

    protected static function deleteImage($pathToFile){
        if(file_exists($pathToFile)){
            File::delete($pathToFile);
        }
    }

    protected static function boot(){
        parent::boot();
        self::deleting(function (\App\Models\Images $image) {
            $thumbsizes = ['sm', 'md', 'lg', 'xl'];
            $DS = DIRECTORY_SEPARATOR;
            $ext = pathinfo(storage_path($image->path.$image->name), PATHINFO_EXTENSION);
            $file_name = pathinfo(storage_path($image->path.$image->name), PATHINFO_FILENAME);
            $pathToFile = storage_path($image->path.$file_name.'.'.$ext);
            if(file_exists($pathToFile)){
                $toCopySmall = storage_path($image->path.'thumbs'.$DS.$file_name.'_sm.'.$ext);
                $folder_name = "images".$DS."__DEL__".$DS.$image->hash."_".$image->id;
                File::ensureDirectoryExists(Storage::disk('local')->path($folder_name));

                $dest = Storage::disk('local')->path($folder_name.$DS.$image->name);
                $toCopy = file_exists($toCopySmall) ? $toCopySmall : $pathToFile;
                File::copy($toCopy, $dest);
            }
            self::deleteImage($pathToFile);

            foreach ($thumbsizes as $size){
                $toDelete = storage_path($image->path.'thumbs'.$DS.$file_name.'_'.$size.'.'.$ext);
                self::deleteImage($toDelete);
            }
        });
    } 
}