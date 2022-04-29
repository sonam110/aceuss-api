<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use DB;

class TrashedActivityController extends Controller
{
    public function trashedActivites(Request $request)
    {
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ? $user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $whereRaw = $this->getWhereRawFromRequest($request);
            $query = Activity::with('Category:id,name','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email')->onlyTrashed();
            if($user->user_type_id =='2'){
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('id',$allChilds);
            }

            if($user->user_type_id =='3'){
                $agnActivity  = ActivityAssigne::where('user_id',$user->id)->pluck('activity_id')->implode(',');
                $query = $query->whereIn('id',explode(',',$agnActivity));

            }
            if($user->user_type_id =='6'){
                $query = $query->where('patient_id',$user->id);

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
            return prepareResult(true,"Activity list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
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
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->onlyTrashed()->forceDelete();
            DB::commit();
            return prepareResult(true,"Activity deleted",[],config('httpcodes.success'));
        }
        catch(Exception $exception) {
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
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->onlyTrashed()->restore();
            DB::commit();
            return prepareResult(true,"Activity restored",[],config('httpcodes.success'));
        }
        catch(Exception $exception) {
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
