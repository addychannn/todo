<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\FilesTrait;

use App\Models\Files;

class SampleController extends Controller
{
    use FilesTrait;
    
    public function UploadFile(Request $request){
        $is_last = $request->input('is_last');

        /**
         * FileUpload function can be found in FilesTrait.php
         * @params Request $request, $location = ""
         * @returns array
         *  if($is_last == 1)
         *      'name', // filename used in storage
         *      'file_name', // Original file name
         *      'path',
         *      'ext',
         *      'mime',
         *      'size',
         *      'file_hash_md5',
         *      'file_hash_sha1',
         *      'file_hash_sha256',
         *  else
         *      'uid',
         *      'timestamp'
         */
        
        $request->validate([
            'uid' => 'nullable|required_if:is_last,false',
            'timestamp' => 'nullable|required_if:is_last,false',
            'file' => 'required|file|max:2048',
            'name' => 'required',
            'chunk' => 'required|integer',
            'ext' => 'required',
            'is_last' => 'required|in:1,0',
        ]);

        $chunk = $request->input('chunk');

        /**
         * Note! You can only validate the mime type on the first chunk.
         */
        // if($chunk <= 0){
        //     $validatedData = $request->validate([
        //         'file' => 'required|file|max:2048|mimes:pdf',
        //     ]);
        // }
        $result = $this->FileUpload($request, 'Sample');
        if($is_last == 1){
            $file = Files::create([
                'name' => $result['name'],
                'file_name' => $result['file_name'],
                'path' => $result['path'],
                'ext' => $result['ext'],
                'mime' => $result['mime'],
                'size' => $result['size'],
                'hash_md5' => $result['file_hash_md5'],
                'hash_sha1' => $result['file_hash_sha1'],
                'hash_sha256' => $result['file_hash_sha256'],
            ]);
            return response([
                "message" => "File Uploaded Successfully",
                "data" => $file,
            ]);
        }else{
            return response($result);
        }
    }
}
