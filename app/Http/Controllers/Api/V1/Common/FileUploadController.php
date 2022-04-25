<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
