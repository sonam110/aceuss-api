<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFile;

class FileUploadController extends Controller
{
    public function uploadFiles(Request $request)
    {
        if($request->is_multiple==1)
        {
            $validation = \Validator::make($request->all(),[ 
                'file'     => 'required|array|max:20000|min:1'
            ]);
        }
        else
        {
            $validation = \Validator::make($request->all(),[ 
                'file'     => 'required|max:10000',
            ]);
        }
        if ($validation->fails()) {
            return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request'));
        }
        try
        {
            $file = $request->file;
            $destinationPath = 'uploads/';
            $fileArray = array();
            $formatCheck = ['doc','docx','png','jpeg','jpg','pdf','svg','mp4','tif','tiff','bmp','gif','eps','raw','jfif','webp','pem','csv'];

            if($request->is_multiple==1)
            {
                foreach ($file as $key => $value) 
                {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if(!in_array($extension, $formatCheck))
                    {
                        return prepareResult(false,getLangByLabelGroups('fileUploadValidation','file_not_allowed'),[], config('httpcodes.bad_request'));
                    }
                    $fileName   = time().'-'.rand(0,99999).'.' . $value->getClientOriginalExtension();
                    $extension = $value->getClientOriginalExtension();
                    $fileSize = $value->getSize();
                    $value->move($destinationPath, $fileName);

                    if($request->store_in_db==1)
                    {
                        $this->storeFileInDB($request->title, env('CDN_DOC_URL').$destinationPath.$fileName, 1, $request->user_type_id);
                    }
                    
                    
                    $fileArray[] = [
                        'title'         => $request->title,
                        'file_name'         => env('CDN_DOC_URL').$destinationPath.$fileName,
                        'file_extension'    => $value->getClientOriginalExtension(),
                        'uploading_file_name' => $value->getClientOriginalName(),
                    ];
                }

                return prepareResult(true,"File upload",$fileArray,config('httpcodes.success'));
            }
            else
            {
                $fileName   = time().'-'.rand(0,99999).'.' . $file->getClientOriginalExtension();
                $extension = strtolower($file->getClientOriginalExtension());
                $fileSize = $file->getSize();
                if(!in_array($extension, $formatCheck))
                {
                    return prepareResult(false,getLangByLabelGroups('fileUploadValidation','file_not_allowed'),[], config('httpcodes.bad_request'));
                }
                
                $file->move($destinationPath, $fileName);
                if($request->store_in_db==1)
                {
                    $this->storeFileInDB($request->title, env('CDN_DOC_URL').$destinationPath.$fileName, 1, $request->user_type_id);
                }
                
                $fileInfo = [
                    'title'         => $request->title,
                    'file_name'         => env('CDN_DOC_URL').$destinationPath.$fileName,
                    'file_extension'    => $file->getClientOriginalExtension(),
                    'uploading_file_name' => $file->getClientOriginalName(),
                ];
                return prepareResult(true,"File upload",$fileInfo,config('httpcodes.success'));
            }   
        }
        catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    private function storeFileInDB($title, $file_path, $is_public, $user_type_id=null)
    {
        if(!empty($user_type_id))
        {
            foreach ($user_type_id as $key => $usertype) {
                $filesave = new AdminFile;
                $filesave->title = $title;
                $filesave->file_path = $file_path;
                $filesave->is_public = $is_public;
                $filesave->created_by = auth()->id();
                $filesave->user_type_id = $usertype);
                $filesave->save();
            }
        }
        else
        {
            $filesave = new AdminFile;
            $filesave->top_most_parent_id = auth()->user()->top_most_parent_id;
            $filesave->title = $title;
            $filesave->file_path = $file_path;
            $filesave->is_public = $is_public;
            $filesave->created_by = auth()->id();
            $filesave->save();
        }
        
        return true;
    }
}
