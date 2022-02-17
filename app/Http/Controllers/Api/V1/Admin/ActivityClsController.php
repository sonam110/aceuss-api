<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityClassification;
use Validator;
use Auth;
use Exception;
use DB;
class ActivityClsController extends Controller
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
    public function activitycls(Request $request)
    {
        try {
            
            $query = ActivityClassification::orderBy('id', 'DESC');
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
                return prepareResult(true,"Activity Classification list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Activity Classification list",$query,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('Activity','name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkAlready = ActivityClassification::where('name',$request->name)->first(); 
            if($checkAlready) {
                return prepareResult(false,getLangByLabelGroups('Activity','name_already_exists'),[], $this->unprocessableEntity); 
            }
            $activityClassification = new ActivityClassification;
            $activityClassification->name = $request->name;
            $activityClassification->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $activityClassification->save();
            return prepareResult(true,getLangByLabelGroups('Activity','create') ,$activityClassification, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $checkId= ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $activityClassification = ActivityClassification::where('id',$id)->first();
            return prepareResult(true,'View Activity Classification',$activityClassification, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'name' => 'required',   
            
            ],
            [
            'name.required' => getLangByLabelGroups('Activity','name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkId = ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $checkAlready = ActivityClassification::where('id','!=',$id)->where('name',$request->name)->first(); 
            if($checkAlready) {

                return prepareResult(false,getLangByLabelGroups('Activity','name_already_exists'),[], $this->unprocessableEntity); 

            }
            $activityClassification = ActivityClassification::find($id);
            $activityClassification->name = $request->name;
            $activityClassification->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $activityClassification->save();
            return prepareResult(true,getLangByLabelGroups('Activity','update'),$activityClassification, $this->success);
                
               
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkId= ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $activityClassification = ActivityClassification::findOrFail($id);
            $activityClassification->delete();
            return prepareResult(true,getLangByLabelGroups('Activity','delete') ,[], $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
