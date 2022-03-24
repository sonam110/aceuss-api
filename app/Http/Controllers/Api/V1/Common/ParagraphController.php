<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paragraph;
use Validator;
use Auth;
use Exception;
use DB;
class ParagraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function paragraphs(Request $request)
    {
        try {
            $query = Paragraph::orderBy('id', 'DESC');
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Paragraph list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Paragraph list",$Paragraph,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
        
    }

    

   public function store(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'paragraph' => 'required',   
            ],
            [
            'paragraph.required' => 'Paragraph Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $Paragraph = new Paragraph;
            $Paragraph->paragraph = $request->paragraph;
            $Paragraph->save();
             return prepareResult(true,getLangByLabelGroups('CompanyType','create') ,$Paragraph, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
            $validator = Validator::make($request->all(),[
                'paragraph' => 'required',   
            ],
            [
            'paragraph.required' => 'Paragraph Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkId = Paragraph::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            
            $Paragraph = Paragraph::find($id);
            $Paragraph->paragraph = $request->paragraph;
            $Paragraph->save();
            return prepareResult(true,getLangByLabelGroups('CompanyType','update'),$Paragraph, $this->success);
                
               
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
        
        try {
            $checkId= Paragraph::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            $Paragraph = Paragraph::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('CompanyType','delete') ,[], $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
