<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;

trait FilesTrait
{
    use Helpers;
    
    protected $DS = DIRECTORY_SEPARATOR;

    public function FileUpload(Request $request, $location = ""){
        $is_last = $request->input('is_last');

        // Upload and store chunk in temporary storage
        $uid_timestamp = $this->upload($request);

        // If last chunk, then merge all chunks and store in private storage
        if($is_last == 1){
            return $this->merge_chunks($uid_timestamp['uid'], $uid_timestamp['timestamp'], $this->filenameValidator($request->name), $location);
        }else{
            return $uid_timestamp;
        }
    }

    public function upload(Request $request){
        $validatedData = $request->validate([
            // 'uid' => 'required',
            // 'timestamp' => 'required',
            'file' => 'required|file|max:2048',
            'name' => 'required',
            'chunk' => 'required|integer',
            'ext' => 'required',
        ]);
        $uid = $request->input('uid') ?? null;
        $timestamp = $request->input('timestamp') ?? null;
        
        // Create uid and timestamp if not exists. This is created when the first chunk is uploaded.
        if($uid == null || $uid == 'null') {
            $loc = $this->prepare_file_storage();
            $uid = $loc['uid'];
            $timestamp = $loc['timestamp'];
        }

        $file = $request->file('file');
        $chunk = $request->input('chunk');
        $ext = $request->input('ext');
        $is_last = $request->input('is_last');

        $name = $uid.'_'.$timestamp.'.'.$ext.'.part_'.$chunk;

        $path = Storage::disk('local')->path(
            'chunks'.$this->DS.$uid.'_'.$timestamp
        );

        $file->move($path, $name);
        
        return [
            'uid' => $uid,
            'timestamp' => $timestamp,
        ];
    }

    private function prepare_file_storage(){
        $uid = uniqid();
        $timestamp = time();
        $path = Storage::disk('local')->path("chunks".$this->DS.$uid."_".$timestamp);
        if(!File::exists($path)){
            File::makeDirectory($path, 0777, true, true);
        }
        return ['uid' => $uid, 'timestamp' => $timestamp];
    }

    public function merge_chunks($uid, $timestamp, $name, $folderName = ""){
        $path = Storage::disk('local')->path('chunks'.$this->DS.$uid.'_'.$timestamp);

        $files = File::allFiles($path);

        $ext = pathinfo(pathinfo($files[0], PATHINFO_FILENAME), PATHINFO_EXTENSION);
        $file_name = pathinfo(pathinfo($files[0], PATHINFO_FILENAME), PATHINFO_FILENAME);
        $folder = $folderName == "" ? "" : $folderName.$this->DS;
        
        $file_location = Storage::disk('local')->path('files'.$this->DS.$folder.$uid.'_'.$timestamp.'.'.$ext);
        File::ensureDirectoryExists(Storage::disk('local')->path('files'.$this->DS.$folder));

        for($i = 0; $i < count($files); $i++){
            File::append($file_location, file_get_contents($path.$this->DS.$uid.'_'.$timestamp.'.'.$ext.'.part_'.$i));
        }

        File::deleteDirectory($path);

        $md5 = md5_file($file_location);
        $sha1 = sha1_file($file_location);
        $sha256 = hash_file('sha256', $file_location);
        $size = filesize($file_location);
        $mime = mime_content_type($file_location);

        $rename = basename($name, '.'.$ext) .'_'.uniqid().'.'.$ext;
        return [
            'name' => $rename, // filename used in storage
            'file_name' => $file_name, // Original file name
            'path' => 'files'.$this->DS.$folder,
            'ext' => strtolower($ext),
            'mime' => $mime,
            'size' => $size,
            'file_hash_md5' => $md5,
            'file_hash_sha1' => $sha1,
            'file_hash_sha256' => $sha256,
        ];
    }

    /**
     * Download file from storage
     * @param App\Models\Files Object
     * @return Stream response
     */
    public function download($file){

        $fileName = $file->path.$this->DS.$file->file_name.'.'.$file->ext;

        if(!Storage::disk('local')->exists($fileName)){
            return response([
                'message' => 'The file you are trying to download cannot be found.',
                'path' => $fileName,
            ], 404);
        }


        //disable execution time limit when downloading a big file.
        set_time_limit(0);

        $fs = Storage::disk('local')->getDriver();
        $size = Storage::disk('local')->size($fileName);
        $type = Storage::disk('local')->mimeType($fileName);

        // $metaData = $fs->getMetadata($fileName);
        $stream = $fs->readStream($fileName);

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
            }, 200, [
            'Content-Type' => $type,
            'Content-Length: '. $size,
            'Content-disposition' => 'attachment; filename="' . $file->name,
        ]);
    }

    public function preview($file){
        $fileName = $file->path.$file->file_name.'.'.$file->ext;

        if(!Storage::disk('local')->exists($fileName)){
            return response([
                'message' => 'The file you are trying to download cannot be found.',
                'path' => $fileName,
            ], 404);
        }

        $pdfFile = Storage::disk('local')->path($fileName);
        $headers = array('Content-Type' => $file->mime);

        // return response(['path' => $pdfFile]);
        return response()->file($pdfFile, $headers);
    }
}