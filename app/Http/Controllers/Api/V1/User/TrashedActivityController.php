<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityAssigne;
use Illuminate\Http\Request;
use DB;

class TrashedActivityController extends Controller
{
    public function trashedActivites(Request $request)
    {
        try {
            $user = getUser();
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            
            $whereRaw = $this->getWhereRawFromRequest($request);
            $query = Activity::with('Category:id,name','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email','Patient:id,name')->onlyTrashed();
            if($user->user_type_id =='2'){
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }

            if($user->user_type_id =='3'){
                $agnActivity  = ActivityAssigne::where('user_id',$user->id)->pluck('activity_id');
                $query = $query->whereIn('id', $agnActivity);

            }
            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $query->where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                });
            }
            
            if($whereRaw != '') { 
                $query = $query->whereRaw($whereRaw)
                
                ->orderBy('id', 'DESC');
            } else {
                $query = $query->orderBy('id', 'DESC')->with('Category:id,name','ImplementationPlan.ipFollowUps:id,ip_id,title');
            }
           
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $query = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $query = $query->makeHidden('internal_comment');

                } 
                $pagination =  [
                    'data' => $query,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $query = $query->makeHidden('internal_comment');

                } 
            }
            return prepareResult(true,getLangByLabelGroups('Activity','message_list'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));  
        }
    }

    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $checkId= Activity::where('id', $id)->onlyTrashed()->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->onlyTrashed()->forceDelete();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','message_delete'),[],config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $checkId= Activity::where('id', $id)->onlyTrashed()->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->onlyTrashed()->restore();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','message_restore'),[],config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('patient_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_id = "."'" .$request->input('patient_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "category_id = "."'" .$request->input('category_id')."'".")";
        }

        if (is_null($request->start_date) == false || is_null($request->end_date) == false) {
           
            if ($w != '') {$w = $w . " AND ";}

            if ($request->start_date != '')
            {
              $w = $w . "("."start_date >= '".date('y-m-d',strtotime($request->start_date))."')";
            }
            if (is_null($request->start_date) == false && is_null($request->end_date) == false) 
                {

              $w = $w . " AND ";
            }
            if ($request->end_date != '')
            {
                $w = $w . "("."start_date <= '".date('y-m-d',strtotime($request->end_date))."')";
            }
            
          
           
        }
        if (is_null($request->input('title')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "title like '%" .trim(strtolower($request->input('title'))) . "%')";

             
        }
        if (is_null($request->input('title')) == false) {
            if ($w != '') {$w = $w . " OR ";}
             $w = $w . "(" . "description like '%" .trim(strtolower($request->input('title'))) . "%')";
             
        }
        return($w);

    }
}
