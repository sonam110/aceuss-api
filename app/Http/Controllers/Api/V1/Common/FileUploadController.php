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
                        return prepareResult(false,getLangByLabelGroups('fileUploadValidation','message_file_not_allowed'),[], config('httpcodes.bad_request'));
                    }
                    $fileName   = time().'-'.rand(0,99999).'.' . $value->getClientOriginalExtension();
                    $extension = $value->getClientOriginalExtension();
                    $fileSize = $value->getSize();
                    $value->move($destinationPath, $fileName);

                    if($request->store_in_db==1)
                    {
                        $this->storeFileInDB($request->title, env('CDN_DOC_URL').$destinationPath.$fileName, 1, $request->user_type_id,$request->company_ids, $request->all_company);
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
                    return prepareResult(false,getLangByLabelGroups('fileUploadValidation','message_file_not_allowed'),[], config('httpcodes.bad_request'));
                }
                
                $file->move($destinationPath, $fileName);
                if($request->store_in_db==1)
                {
                    $this->storeFileInDB($request->title, env('CDN_DOC_URL').$destinationPath.$fileName, 1, $request->user_type_id, $request->company_ids, $request->all_company);
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

    private function storeFileInDB($title, $file_path, $is_public, $user_type_id=null,$company_ids=null, $all_company)
    {
        if(!empty($user_type_id))
        {
            foreach (explode(',', $user_type_id) as $key => $usertype) {
                $filesave = new AdminFile;
                $filesave->title = !empty($title) ? $title : 'File uploaded';
                $filesave->file_path = $file_path;
                $filesave->is_public = $is_public;
                $filesave->created_by = auth()->id();
                $filesave->user_type_id = $usertype;
                $filesave->save();
            }
        }
        elseif($all_company=='yes')
        {
            $comIds = ['all'];
            $filesave = new AdminFile;
            $filesave->title = !empty($title) ? $title : 'File uploaded';
            $filesave->file_path = $file_path;
            $filesave->is_public = $is_public;
            $filesave->created_by = auth()->id();
            $filesave->company_ids = json_encode($comIds);
            $filesave->top_most_parent_id = auth()->user()->top_most_parent_id;
            $filesave->save();
        }
        elseif(!empty($company_ids))
        {
            $comIds = [];
            foreach(explode(',', $company_ids) as $companyId)
            {
                $comIds[] = $companyId;
            }
            $filesave = new AdminFile;
            $filesave->title = !empty($title) ? $title : 'File uploaded';
            $filesave->file_path = $file_path;
            $filesave->is_public = $is_public;
            $filesave->created_by = auth()->id();
            $filesave->company_ids = json_encode($comIds);
            $filesave->top_most_parent_id = auth()->user()->top_most_parent_id;
            $filesave->save();
        }
        else
        {
            $filesave = new AdminFile;
            $filesave->top_most_parent_id = auth()->user()->top_most_parent_id;
            $filesave->title = !empty($title) ? $title : 'File uploaded';
            $filesave->file_path = $file_path;
            $filesave->is_public = $is_public;
            $filesave->created_by = auth()->id();
            $filesave->save();
        }
        
        return true;
    }
}
