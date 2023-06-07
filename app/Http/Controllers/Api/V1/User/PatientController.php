<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\IpAssigneToEmployee;
use App\Models\IpTemplate;
use App\Models\IpFollowUpCreation;
use App\Models\IpFollowUp;
use App\Models\PersonalInfoDuringIp;
use App\Models\Activity;
use App\Models\UserType;
use App\Models\Task;
use Validator;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EmailTemplate;
use PDF;
use Carbon\Carbon;

class PatientController extends Controller
{

    public function __construct()
    {

        $this->middleware('permission:ip-browse',['except' => ['show']]);
        $this->middleware('permission:ip-add', ['only' => ['store']]);
        $this->middleware('permission:ip-edit', ['only' => ['update']]);
        $this->middleware('permission:ip-read', ['only' => ['show']]);
        $this->middleware('permission:ip-delete', ['only' => ['destroy']]);
        
    }

    public function ipsList(Request $request)
    {
        $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
        try {
            $user = getUser();
            if(!empty($user->branch_id)) {
                if($user->user_type_id==11)
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                    $allChilds[] = $user->id;
                }
                else
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                }
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }

            $whereRaw = $this->getWhereRawFromRequest($request);

            $query = PatientImplementationPlan::select('patient_implementation_plans.*')
            ->where('patient_implementation_plans.is_latest_entry',1)
            ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','ipFollowUps','branch:id,name,branch_name')
            ->with(
                ['patient' => function ($query) {
                    $query->withCount(['patientPlan','patientActivity']);
                }]
            )
            ->withCount(
                ['ipFollowUps' => function ($query) use ($date) {
                    $query->whereDate('start_date','>=',$date)
                    ->where('is_latest_entry', 1);
                },'activities' => function ($query) use ($date) {
                    $query->whereDate('start_date','>=',$date)
                    ->where('is_latest_entry', 1);
                }, 'persons']
            );

            if($user->user_type_id =='2'){
                $query = $query->orderBy('patient_implementation_plans.id','DESC');
            } elseif($user->user_type_id =='3') {
                $user_records = getAllowUserList('visible-all-patients-ip');
                $query->whereIn('patient_implementation_plans.user_id', $user_records);
            } else{
                $query =  $query->whereIn('patient_implementation_plans.branch_id',$allChilds);
            }

            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $query->where(function ($q) use ($user) {
                    $q->where('patient_implementation_plans.user_id', $user->id)
                        ->orWhere('patient_implementation_plans.user_id', $user->parent_id);
                });
            }

            if(!empty($request->with_activity) && $request->with_activity==1)
            {
                $query->join('activities', function ($join) {
                    $join->on('patient_implementation_plans.id', '=', 'activities.ip_id');
                })
                ->withoutGlobalScope('top_most_parent_id')
                ->where('activities.top_most_parent_id', $user->top_most_parent_id);

                $query->where('patient_implementation_plans.top_most_parent_id', $user->top_most_parent_id);
            }

            if(!empty($request->with_followup) && $request->with_followup==1)
            {
                $query->join('ip_follow_ups', function ($join) {
                    $join->on('patient_implementation_plans.id', '=', 'ip_follow_ups.ip_id');
                })
                ->withoutGlobalScope('top_most_parent_id')
                ->where('ip_follow_ups.top_most_parent_id', $user->top_most_parent_id);

                $query->where('patient_implementation_plans.top_most_parent_id', $user->top_most_parent_id);
            }

            if(!empty($request->branch_id))
            {
                $query->where('patient_implementation_plans.branch_id', $request->branch_id);
            }

            if(!empty($request->status) && $request->status!=0)
            {
                $query->where('patient_implementation_plans.status', $request->status);
            }
            elseif(!empty($request->status) && $request->status!='no')
            {
                $query->where('patient_implementation_plans.status', 0);
            }

            if(!empty($request->category_id))
            {
                $query->where('patient_implementation_plans.category_id', $request->category_id);
            }

            if(!empty($request->subcategory_id))
            {
                $query->where('patient_implementation_plans.subcategory_id', $request->subcategory_id);
            }

            if(!empty($request->patient_id))
            {
                $query->where('patient_implementation_plans.user_id', $request->patient_id);
            }

            if(!empty($request->title))
            {
                $query->where('patient_implementation_plans.title', 'LIKE', '%'.$request->title.'%');
            }

            if(!empty($request->start_date) && !empty($request->end_date))
            {
                $query->whereDate('patient_implementation_plans.start_date', '>=', $request->start_date)->whereDate('patient_implementation_plans.end_date', '<=', $request->end_date);
            }
            elseif(!empty($request->start_date) && empty($request->end_date))
            {
                $query->whereDate('patient_implementation_plans.start_date', ">=" ,$request->start_date);
            }
            elseif(empty($request->start_date) && !empty($request->end_date))
            {
                $query->whereDate('patient_implementation_plans.end_date', '<=', $request->end_date);
            }

            if($whereRaw != '') { 
                $query = $query->whereRaw($whereRaw)
                
                ->orderBy('patient_implementation_plans.id', 'DESC');
            } else {
                $query = $query->orderBy('patient_implementation_plans.id', 'DESC');
            }

            ////////Counts
            $ipCounts = PatientImplementationPlan::select([
                \DB::raw('COUNT(IF(status = 0, 0, NULL)) as total_not_approved'),
                \DB::raw('COUNT(IF(status = 1, 0, NULL)) as total_incompleted'),
                \DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_completed'),
            ])
            ->where('is_latest_entry', 1);
            if(!empty($request->user_id))
            {
                $ipCounts->where('patient_implementation_plans.user_id',$request->user_id);
            }
            if($user->user_type_id =='2'){

            } elseif($user->user_type_id =='3') {
                $user_records = getAllowUserList('visible-all-patients-ip');
                $ipCounts->whereIn('patient_implementation_plans.user_id', $user_records);
            } else{
                $ipCounts =  $ipCounts->whereIn('patient_implementation_plans.branch_id',$allChilds);
            }

            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $ipCounts->where(function ($q) use ($user) {
                    $q->where('patient_implementation_plans.user_id', $user->id)
                        ->orWhere('patient_implementation_plans.user_id', $user->parent_id);
                });
            }

            if(!empty($request->start_date) && !empty($request->end_date))
            {
                $ipCounts->whereDate('patient_implementation_plans.start_date', '>=', $request->start_date)->whereDate('end_date', '<=', $request->end_date);
            }
            elseif(!empty($request->start_date) && empty($request->end_date))
            {
                $ipCounts->whereDate('patient_implementation_plans.start_date', ">=" ,$request->start_date);
            }
            elseif(empty($request->start_date) && !empty($request->end_date))
            {
                $ipCounts->whereDate('patient_implementation_plans.end_date', '<=', $request->end_date);
            }
            $ipCounts = $ipCounts->first();

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
                    'last_page' => ceil($total / $perPage),
                    'total_not_approved' => $ipCounts->total_not_approved,
                    'total_incompleted' => $ipCounts->total_incompleted,
                    'total_completed' => $ipCounts->total_completed,
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('IP','message_list'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
           
            $user = getUser();
            
            $data = [ 'data' => $request->all() ];
            $validator = Validator::make($data,[ 
                'data.*.category_id' => 'required|exists:category_masters,id',   
                'data.*.subcategory_id' => 'required|exists:category_masters,id',   
                'data.*.title' => 'required',       
 
            ],
            [   
            '*.category_id' =>  getLangByLabelGroups('IP','message_category_id'),   
            '*.subcategory_id' =>  getLangByLabelGroups('IP','message_subcategory_id'),             
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $ids = null;
            $impPlan_ids = [];
            
            if(is_array($data['data']) ){
                foreach ($data['data'] as $key => $patient) {
                    if(!empty(@$patient['category_id']))
                    {
                        $patientPlan = new PatientImplementationPlan;
                        $patientPlan->user_id = @$patient['user_id'];
                        $patientPlan->branch_id = getBranchId();
                        $patientPlan->category_id = @$patient['category_id'];
                        $patientPlan->subcategory_id = @$patient['subcategory_id'];
                        $patientPlan->title = @$patient['title'];
                        $patientPlan->goal = @$patient['goal'];
                        $patientPlan->limitations = @$patient['limitations'];
                        $patientPlan->limitation_details = @$patient['limitation_details'];
                        $patientPlan->how_support_should_be_given = @$patient['how_support_should_be_given'];
                        $patientPlan->who_give_support = (!empty(@$patient['who_give_support'])) ? json_encode(@$patient['who_give_support']) : null;
                        $patientPlan->sub_goal = @$patient['sub_goal'];
                        $patientPlan->sub_goal_details = @$patient['sub_goal_details'];
                        $patientPlan->sub_goal_selected = @$patient['sub_goal_selected'];
                        $patientPlan->overall_goal = @$patient['overall_goal'];
                        $patientPlan->overall_goal_details = @$patient['overall_goal_details'];
                        $patientPlan->body_functions = @$patient['body_functions'];
                        $patientPlan->personal_factors = @$patient['personal_factors'];
                        $patientPlan->health_conditions = @$patient['health_conditions'];
                        $patientPlan->other_factors = @$patient['other_factors'];
                        $patientPlan->treatment = @$patient['treatment'];
                        $patientPlan->working_method = @$patient['working_method'];
                        $patientPlan->start_date = @$patient['start_date'];
                        $patientPlan->end_date = @$patient['end_date'];
                        $patientPlan->save_as_template = (@$patient['save_as_template']) ? 1:0;
                        $patientPlan->documents = !empty(@$patient['documents']) ? json_encode(@$patient['documents']) : null;
                        $patientPlan->step_one = (!empty(@$patient['step_one'])) ? @$patient['step_one']:0;
                        $patientPlan->step_two = (!empty(@$patient['step_two'])) ? @$patient['step_two']:0;
                        $patientPlan->step_three = (!empty(@$patient['step_three'])) ? @$patient['step_three']:0;
                        $patientPlan->step_four = (!empty(@$patient['step_four'])) ? @$patient['step_four']:0;
                        $patientPlan->step_five = (!empty(@$patient['step_five'])) ? @$patient['step_five']:0;
                        $patientPlan->step_six = (!empty(@$patient['step_six'])) ? @$patient['step_six']:0;
                        $patientPlan->step_seven = (!empty(@$patient['step_seven'])) ? @$patient['step_seven']:0;
                        $patientPlan->created_by = $user->id;
                        $patientPlan->is_latest_entry = 1;
                        $patientPlan->approval_comment = $request->approval_comment;
                        $patientPlan->entry_mode = $request->entry_mode;
                        $patientPlan->save();

                        /*--notify-user-ip-created--*/
                        $data_id =  $patientPlan->id;
                        $notifyUser = User::find($patientPlan->user_id);
                        if($notifyUser)
                        {
                            $notification_template = EmailTemplate::where('mail_sms_for', 'ip-created')->first();
                            $variable_data = [
                                '{{name}}' => aceussDecrypt($notifyUser->name),
                                '{{created_by}}' => aceussDecrypt(Auth::User()->name),
                                '{{title}}' => $patientPlan->title
                            ];
                            $socket = ($notifyUser->id==auth()->id()) ? false : true;
                            actionNotification($notifyUser,$data_id,$notification_template,$variable_data, null, null, $socket);
                        }
                        //------------------------------//

                        $impPlan_ids[] = $patientPlan->id;
                        $ids = implode(', ',$impPlan_ids);
                        if(!empty(@$patient['save_as_template']) && @$patient['save_as_template'] == true){
                            
                            if (empty(@$patient['title'])) {
                                return prepareResult(false,'Title field is required',[], config('httpcodes.bad_request')); 
                            }
                            $ipTemplate = new IpTemplate;
                            $ipTemplate->ip_id = $patientPlan->id;
                            $ipTemplate->template_title = @$patient['title'];
                            $ipTemplate->created_by = $user->id;
                            $ipTemplate->save();
                        }
                        /*-----------IP assigne to employee*/
                        /*if(!empty(@$patient['emp_id']) ){
                            $ipAssigne = new IpAssigneToEmployee;
                            $ipAssigne->user_id = @$patient['emp_id'];
                            $ipAssigne->ip_id = $patientPlan->id;
                            $ipAssigne->status = '1';
                            $ipAssigne->save();
                        }
                        if(auth()->user()->user_type_id==3)
                        {
                            $ipAssigne = new IpAssigneToEmployee;
                            $ipAssigne->user_id = auth()->id();
                            $ipAssigne->ip_id = $patientPlan->id;
                            $ipAssigne->status = '1';
                            $ipAssigne->save();
                        }*/
                        /*-----------------Persons Informationn ----------------*/
                        if(is_array(@$patient['persons']) && sizeof(@$patient['persons']) >0 ){
                            foreach (@$patient['persons'] as $key => $value) {
                                $userInfo = userInfo(@$value['id']);
                                if($userInfo)
                                {
                                    $personalInfo = new PersonalInfoDuringIp;
                                    $personalInfo->patient_id = $userInfo->parent_id;
                                    $personalInfo->user_id = $userInfo->id;
                                    $personalInfo->ip_id = $patientPlan->id;
                                    $personalInfo->is_presented = returnBoolean(@$value['is_presented']);
                                    $personalInfo->is_participated = returnBoolean(@$value['is_participated']);
                                    $personalInfo->how_helped = @$value['how_helped'];
                                    $patientPlan->entry_mode = $request->entry_mode;
                                    $personalInfo->save();
                                }
                            }
                        }
                    }
                }
                
                DB::commit();
                $patientImpPlan = PatientImplementationPlan::select('patient_implementation_plans.*')
                ->where('patient_implementation_plans.is_latest_entry',1)
                ->whereIn('id',$impPlan_ids)
                ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','activities','ipFollowUps','patient', 'persons.user:id,name,gender,email,branch_id,contact_number,personal_number,country_id,full_address,avatar','persons.user','children','assignEmployee:id,ip_id,user_id','branch:id,name,branch_name')
                ->withCount('ipFollowUps','activities', 'persons')
                ->with(
                    ['patient' => function ($query) {
                        $query->withCount(['patientPlan','patientActivity']);
                    }]
                )
                ->get();
                return prepareResult(true,getLangByLabelGroups('IP','message_create') ,$patientImpPlan, config('httpcodes.success'));
            } else {
                return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            }
            
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $data = [ 'data' => $request->all() ];
            $validator = Validator::make($data,[  
                'data.*.category_id' => 'required|exists:category_masters,id',   
                'data.*.subcategory_id' => 'required|exists:category_masters,id',   
                'data.*.title' => 'required',      
                'data.*.reason_for_editing' => 'required',      
            ],
            [    
            '*.category_id' =>  getLangByLabelGroups('IP','message_category_id'),   
            '*.subcategory_id' =>  getLangByLabelGroups('IP','message_subcategory_id'),        
            ]);
           
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $ids = null;
            $impPlan_ids = [];
            $parent_id  = $id;

            if(!$user->hasPermissionTo('isCategoryEditPermission-edit')){
               return prepareResult(false,getLangByLabelGroups('BcCommon','message_unauthorized'), [],config('httpcodes.not_found'));     
            } 
            if(is_array($data['data']) ){
                foreach ($data['data'] as $key => $patient) {
                    if(!empty(@$patient['category_id']))
                    {
                        $getIpInfo = PatientImplementationPlan::find($parent_id);
                        if($getIpInfo)
                        {
                            //new entry create for log
                            $patientPlan = $getIpInfo->replicate();
                            $patientPlan->parent_id = $getIpInfo->id;
                            $patientPlan->is_latest_entry = 0;
                            $patientPlan->entry_mode = $request->entry_mode;
                            $patientPlan->created_at = $getIpInfo->created_at;
                            $patientPlan->save();

                            //person assigned
                            $connectedPersons = PersonalInfoDuringIp::where('ip_id', $parent_id)
                            ->get();
                            foreach ($connectedPersons as $key => $person) {
                                $personalInfoDuringIp = $person->replicate();
                                $personalInfoDuringIp->ip_id = $patientPlan->id;
                                $patientPlan->entry_mode = $request->entry_mode;
                                $personalInfoDuringIp->save();
                            }

                            //update Existing or current record
                            $getIpInfo->user_id = @$patient['user_id'];
                            $getIpInfo->parent_id = null;
                            $getIpInfo->branch_id = getBranchId();
                            $getIpInfo->category_id = @$patient['category_id'];
                            $getIpInfo->subcategory_id = @$patient['subcategory_id'];
                            $getIpInfo->title = @$patient['title'];
                            $getIpInfo->goal = @$patient['goal'];
                            $getIpInfo->limitations = @$patient['limitations'];
                            $getIpInfo->limitation_details = @$patient['limitation_details'];
                            $getIpInfo->how_support_should_be_given = @$patient['how_support_should_be_given'];
                            $getIpInfo->who_give_support =  json_encode(@$patient['who_give_support']);
                            $getIpInfo->sub_goal = @$patient['sub_goal'];
                            $getIpInfo->sub_goal_details = @$patient['sub_goal_details'];
                            $getIpInfo->sub_goal_selected = @$patient['sub_goal_selected'];
                            $getIpInfo->overall_goal = @$patient['overall_goal'];
                            $getIpInfo->overall_goal_details = @$patient['overall_goal_details'];
                            $getIpInfo->body_functions = @$patient['body_functions'];
                            $getIpInfo->personal_factors = @$patient['personal_factors'];
                            $getIpInfo->health_conditions = @$patient['health_conditions'];
                            $getIpInfo->other_factors = @$patient['other_factors'];
                            $getIpInfo->treatment = @$patient['treatment'];
                            $getIpInfo->working_method = @$patient['working_method'];
                            $getIpInfo->reason_for_editing = @$patient['reason_for_editing'];
                            $getIpInfo->start_date = @$patient['start_date'];
                            $getIpInfo->end_date = @$patient['end_date'];
                            $getIpInfo->save_as_template = (@$patient['save_as_template'] == true) ? 1:0;
                            $getIpInfo->documents = json_encode(@$patient['documents']);
                            $getIpInfo->step_one = (!empty(@$patient['step_one'])) ? @$patient['step_one']:0;
                            $getIpInfo->step_two = (!empty(@$patient['step_two'])) ? @$patient['step_two']:0;
                            $getIpInfo->step_three = (!empty(@$patient['step_three'])) ? @$patient['step_three']:0;
                            $getIpInfo->step_four = (!empty(@$patient['step_four'])) ? @$patient['step_four']:0;
                            $getIpInfo->step_five = (!empty(@$patient['step_five'])) ? @$patient['step_five']:0;
                            $getIpInfo->step_six = (!empty(@$patient['step_six'])) ? @$patient['step_six']:0;
                            $getIpInfo->step_seven = (!empty(@$patient['step_seven'])) ? @$patient['step_seven']:0;
                            $getIpInfo->edited_by = $user->id;
                            $getIpInfo->is_latest_entry = 1;
                            $patientPlan->entry_mode = $request->entry_mode;
                            $patientPlan->created_at = Carbon::now();
                            $patientPlan->approval_comment = $request->approval_comment;
                            $getIpInfo->save();

                        }
                        else
                        {
                            $patientPlan = new PatientImplementationPlan;
                            $patientPlan->user_id = @$patient['user_id'];
                            $patientPlan->parent_id = $parent_id;
                            $patientPlan->branch_id = getBranchId();
                            $patientPlan->category_id = @$patient['category_id'];
                            $patientPlan->subcategory_id = @$patient['subcategory_id'];
                            $patientPlan->title = @$patient['title'];
                            $patientPlan->goal = @$patient['goal'];
                            $patientPlan->limitations = @$patient['limitations'];
                            $patientPlan->limitation_details = @$patient['limitation_details'];
                            $patientPlan->how_support_should_be_given = @$patient['how_support_should_be_given'];
                            $patientPlan->who_give_support =  json_encode(@$patient['who_give_support']);
                            $patientPlan->sub_goal = @$patient['sub_goal'];
                            $patientPlan->sub_goal_details = @$patient['sub_goal_details'];
                            $patientPlan->sub_goal_selected = @$patient['sub_goal_selected'];
                            $patientPlan->overall_goal = @$patient['overall_goal'];
                            $patientPlan->overall_goal_details = @$patient['overall_goal_details'];
                            $patientPlan->body_functions = @$patient['body_functions'];
                            $patientPlan->personal_factors = @$patient['personal_factors'];
                            $patientPlan->health_conditions = @$patient['health_conditions'];
                            $patientPlan->other_factors = @$patient['other_factors'];
                            $patientPlan->treatment = @$patient['treatment'];
                            $patientPlan->working_method = @$patient['working_method'];
                            $patientPlan->reason_for_editing = @$patient['reason_for_editing'];
                            $patientPlan->start_date = @$patient['start_date'];
                            $patientPlan->end_date = @$patient['end_date'];
                            $patientPlan->save_as_template = (@$patient['save_as_template'] == true) ? 1:0;
                            $patientPlan->documents = !empty(@$patient['documents']) ? json_encode(@$patient['documents']) : null;
                            $patientPlan->step_one = (!empty(@$patient['step_one'])) ? @$patient['step_one']:0;
                            $patientPlan->step_two = (!empty(@$patient['step_two'])) ? @$patient['step_two']:0;
                            $patientPlan->step_three = (!empty(@$patient['step_three'])) ? @$patient['step_three']:0;
                            $patientPlan->step_four = (!empty(@$patient['step_four'])) ? @$patient['step_four']:0;
                            $patientPlan->step_five = (!empty(@$patient['step_five'])) ? @$patient['step_five']:0;
                            $patientPlan->step_six = (!empty(@$patient['step_six'])) ? @$patient['step_six']:0;
                            $patientPlan->step_seven = (!empty(@$patient['step_seven'])) ? @$patient['step_seven']:0;
                            $patientPlan->edited_by = $user->id;
                            $patientPlan->is_latest_entry = 1;
                            $patientPlan->entry_mode = $request->entry_mode;
                            $patientPlan->approval_comment = $request->approval_comment;
                            $patientPlan->save();
                        }

                        $impPlan_ids[] = $patientPlan->id;
                        $ids = implode(', ',$impPlan_ids);
                        if(!empty(@$patient['save_as_template']) && @$patient['save_as_template'] == true){
                            if (empty(@$patient['title'])) {
                                return prepareResult(false,'Title field is required',[], config('httpcodes.bad_request')); 
                            }
                            $ipTemplate = new IpTemplate;
                            $ipTemplate->ip_id = $patientPlan->id;
                            $ipTemplate->template_title = @$patient['title'];
                            $ipTemplate->created_by = $user->id;
                            $patientPlan->entry_mode = $request->entry_mode;
                            $ipTemplate->save();
                        }
                        /*-----------IP assigne to employee*/
                        /*if(!empty(@$patient['emp_id']) ){
                            $ipAssigne = new IpAssigneToEmployee;
                            $ipAssigne->user_id = @$patient['emp_id'];
                            $ipAssigne->ip_id = $patientPlan->id;
                            $ipAssigne->status = '1';
                            $ipAssigne->save();
                        }*/

                        /*------Check ip behalf actvity-----*/

                        $checkActivity = Activity::where('ip_id',$id)->get();
                        if(!empty($checkActivity)){
                            foreach ($checkActivity as $key => $activity) {
                                $updateCat = Activity::find($activity->id);
                                $updateCat->category_id = @$patient['category_id'];
                                $updateCat->subcategory_id = @$patient['subcategory_id'];
                                $updateCat->save();
                            }
                        }
                        /*-----------------Persons Informationn ----------------*/
                        if(is_array(@$patient['persons']) && sizeof(@$patient['persons']) >0 ){
                            foreach (@$patient['persons'] as $key => $value) {
                                $userInfo = userInfo(@$value['id']);
                                if($userInfo)
                                {
                                    $checkExistPerson = PersonalInfoDuringIp::where('ip_id', $parent_id)
                                        ->where('user_id', $userInfo->id)
                                        ->count();
                                    if($checkExistPerson>0)
                                    {
                                        $personalInfo = PersonalInfoDuringIp::where('ip_id', $parent_id)
                                        ->where('user_id', $userInfo->id)
                                        ->first();
                                    }
                                    else
                                    {
                                        $personalInfo = new PersonalInfoDuringIp;
                                    }
                                    $personalInfo->patient_id = $userInfo->parent_id;
                                    $personalInfo->user_id = $userInfo->id;
                                    $personalInfo->ip_id = $parent_id;
                                    $personalInfo->is_presented = returnBoolean(@$value['is_presented']);
                                    $personalInfo->is_participated = returnBoolean(@$value['is_participated']);
                                    $personalInfo->how_helped = @$value['how_helped'];
                                    $patientPlan->entry_mode = $request->entry_mode;
                                    $personalInfo->save();
                                }
                            }
                        }
                    }
                }
                DB::commit();
                $patientImpPlan = PatientImplementationPlan::select('patient_implementation_plans.*')
                    ->where('id', $parent_id)
                    ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','activities','ipFollowUps','patient', 'persons.user:id,name,gender,email,branch_id,contact_number,personal_number,country_id,full_address,avatar','persons.user','children','assignEmployee:id,ip_id,user_id','branch:id,name,branch_name')
                    ->withCount('ipFollowUps','activities', 'persons')
                    ->with(
                        ['patient' => function ($query) {
                            $query->withCount(['patientPlan','patientActivity']);
                        }]
                    )
                    ->get();
                return prepareResult(true,getLangByLabelGroups('IP','message_create') ,$patientImpPlan, config('httpcodes.success'));
            } else {
                return prepareResult(false, 'something went wrong.',[], config('httpcodes.internal_server_error'));
            }
              
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            Task::where('resource_id',$id)->where('type_id','2')->delete();
            $patientPlan = PatientImplementationPlan::where('id',$id)->delete();
            PersonalInfoDuringIp::where('ip_id', $id)->delete();
            Activity::where('ip_id', $id)->delete();
            IpTemplate::where('ip_id', $id)->delete();
            IpFollowUpCreation::where('ip_id', $id)->delete();
            IpFollowUp::where('ip_id', $id)->delete();
            return prepareResult(true,getLangByLabelGroups('IP','message_delete') ,[], config('httpcodes.success'));
                
                
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    public function show($id, Request $request)
    {
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            if($request->log=='yes')
            {
                $patientPlan = PatientImplementationPlan::select('patient_implementation_plans.*')
                ->where('id',$id)
                ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','activities','ipFollowUps','patient','persons','children','assignEmployee:id,ip_id,user_id','branch:id,name,branch_name', 'persons.user.Country')
                ->withCount(
                    ['ipFollowUps','activities', 'persons']
                )
                ->with(
                    ['patient' => function ($query) {
                        $query->withCount(['patientPlan','patientActivity']);
                    }, 'persons']
                )
                ->first();
            }
            else
            {
                $patientPlan = PatientImplementationPlan::select('patient_implementation_plans.*')
                ->where('patient_implementation_plans.is_latest_entry',1)
                ->where('id',$id)
                ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','activities','ipFollowUps','patient','persons','children','assignEmployee:id,ip_id,user_id','branch:id,name,branch_name', 'persons.user.Country')
                ->withCount(
                    ['ipFollowUps' => function ($query) {
                        $query->where('is_latest_entry', 1);
                    },'activities' => function ($query) {
                        $query->where('is_latest_entry', 1);
                    }, 'persons']
                )
                ->with(
                    ['patient' => function ($query) {
                        $query->withCount(['patientPlan','patientActivity']);
                    }, 'persons']
                )
                ->first();
            }
            
            return prepareResult(true,getLangByLabelGroups('IP','message_show') ,$patientPlan, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function approvedPatientPlan(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' => 'required',   
            ],
            [
            'id' =>  getLangByLabelGroups('IP','message_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->id;
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $patientPlan = PatientImplementationPlan::find($id);
            $patientPlan->approved_by = $user->id;
            $patientPlan->approved_date = date('Y-m-d');
            $patientPlan->status = '1';
            $patientPlan->save();
            return prepareResult(true,getLangByLabelGroups('IP','message_approve') ,$patientPlan, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function ipAssigneToEmployee(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' => 'required',   
                'ip_id' => 'required',     
            ],
            [
            'user_id.required' => getLangByLabelGroups('IP','message_user_id'),
            'ip_id.required' => getLangByLabelGroups('IP','message_ip_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkAlready = IpAssigneToEmployee::where('user_id',$request->user_id)->where('ip_id',$request->ip_id)->first(); 
            if($checkAlready) {
                return prepareResult(false,getLangByLabelGroups('IP','message_already_assigne'),[], config('httpcodes.bad_request')); 
            }
            
            $ipAssigne = new IpAssigneToEmployee;
            $ipAssigne->user_id = $request->user_id;
            $ipAssigne->ip_id = $request->ip_id;
            $ipAssigne->status = '1';
            $ipAssigne->save();

            /*--notify-user-ip-assigned--*/
            $notifyUser = User::find($request->user_id);
            $data_id =  $ipAssigne->ip_id;
            $notification_template = EmailTemplate::where('mail_sms_for', 'ip-assigned')->first();
            if($notifyUser)
            {
                $variable_data = [
                    '{{name}}' => aceussDecrypt($notifyUser->name),
                    '{{assigned_by}}' => aceussDecrypt(Auth::User()->name)
                ];
                $socket = ($notifyUser->id==auth()->id()) ? false : true;
                actionNotification($notifyUser,$data_id,$notification_template,$variable_data, null, null, $socket);
            }
            //------------------------------//
            $ipAssigneEmp = IpAssigneToEmployee::where('id',$ipAssigne->id)->with('User:id,name','PatientImplementationPlan')->first();
            return prepareResult(true,getLangByLabelGroups('IP','message_assigne') ,$ipAssigneEmp, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function viewIpAssigne(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' => 'required',   
            ],
            [
            'id' =>  getLangByLabelGroups('IP','message_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->id;
            $checkId= IpAssigneToEmployee::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $ipAssigne = IpAssigneToEmployee::where('id',$id)->with('User:id,name','PatientImplementationPlan')->first();
            return prepareResult(true,getLangByLabelGroups('IP','message_assigne_show') ,$ipAssigne, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function ipEditHistory(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'parent_id' => 'required|exists:patient_implementation_plans,id',   
            ],
            [
            'parent_id' =>  'Parent id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->parent_id;
            $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
            $parent_id = 
            $query= PatientImplementationPlan::with('patient:id,name')
                ->withCount(
                    ['ipFollowUps' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date)
                        ->where('is_latest_entry', 1);
                    },'activities' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date)
                        ->where('is_latest_entry', 1);
                    }, 'persons']
                )
                ->where('parent_id',$id);
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
                return prepareResult(true,"Edited Ip list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('IP','message_log') ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function ipTemplateList(Request $request)
    {
        try {

            $whereRaw = $this->getWhereRawFromRequest1($request);
            if($whereRaw != '') { 
                $query= IpTemplate::select('id','ip_id','template_title')->whereRaw($whereRaw)->orderBy('id','DESC');
            } else {
                $query= IpTemplate::select('id','ip_id','template_title')->orderBy('id','DESC');
            }
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
                return prepareResult(true,"Edited Ip list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('IP','message_template_list') ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function ipAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'ip_id' => 'required|exists:patient_implementation_plans,id',   
                'status'     => 'required|in:1,2,3',  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $is_action_perform = true;
           
            /*$isAssignEmp = IpAssigneToEmployee::where('user_id',$user->id)->where('ip_id', $request->ip_id)->first();
            if(is_object($isAssignEmp)){
                $is_action_perform = true; 
            }*/
            
            $isBranch = PatientImplementationPlan::where('id', $request->ip_id)
                ->where(function ($q) use ($user) {
                    $q->where('branch_id', $user->id)
                        ->orWhere('top_most_parent_id', auth()->user()->id);
                })
                ->first();
            if(is_object($isBranch)){
                $is_action_perform = true; 
            }
            if($is_action_perform == false){
                return prepareResult(false,getLangByLabelGroups('IP','message_unauthorized'),[], config('httpcodes.bad_request')); 
            }

            $id = $request->ip_id;
            $ipAction = PatientImplementationPlan::find($id);
            if($ipAction->status==0 && $request->status==2)
            {
                return prepareResult(false,getLangByLabelGroups('IP','message_not_approved'),[], config('httpcodes.bad_request')); 
            }

            $ipAction->status = $request->status;
            $ipAction->action_by = $user->id;
            $ipAction->action_date = date('Y-m-d');
            $ipAction->comment = $request->comment;
            $ipAction->save();

            /*$updateStatus = IpAssigneToEmployee::where('ip_id',$request->ip_id)->update(['status'=> $request->status]);*/
            
            DB::commit();
               
            return prepareResult(true,'Action Done successfully' ,$ipAction, config('httpcodes.success'));
           
        
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('user_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.user_id = "."'" .$request->input('user_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.category_id = "."'" .$request->input('category_id')."'".")";
        }
         if (is_null($request->input('subcategory_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_implementation_plans.subcategory_id = "."'" .$request->input('subcategory_id')."'".")";
        }
        if (is_null($request->input('goal')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "patient_implementation_plans.goal_id like '%" .trim(strtolower($request->input('goal_id'))) . "%')";

             
        }
        return($w);

    }
    
    private function getWhereRawFromRequest1(Request $request) 
    {
        $w = '';
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
         if (is_null($request->input('created_by')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "created_by = "."'" .$request->input('created_by')."'".")";
        }
        
        return($w);

    }

    public function ipFollowupsPrint(Request $request, $ip_id)
    {
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$ip_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }

            $patientPlan = PatientImplementationPlan::where('id',$ip_id)->with('Parent','Category','Subcategory','CreatedBy','patient','persons','children')->first();
            $filename = $patientPlan->id."-".time().".pdf";
            $data['ipfollowupInfo'] = $patientPlan; 
            $data['bankid_verified'] = $request->bankid_verified;
            $pdf = PDF::loadView('print-followups', $data);
            $pdf->save('reports/followups/'.$filename);

            $returnData = [
                'url' => env('CDN_DOC_URL').'reports/followups/'.$filename
            ];

            return prepareResult(true,'Print FollowUp',$returnData, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            \Log::info($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    /*public function deletePerson(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $person= PersonalInfoDuringIp::where('id',$id)->first();
            if (!is_object($person)) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $user = User::where('email',$person->email)->first();
            if($user){

                $user->delete();
            }
            $person->delete();

            return prepareResult(true,'Person Delete Successfully',[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            \Log::info($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }*/

    public function ipPrint(Request $request, $ip_id)
    {
        try {
            $user = getUser();
            $patientImpPlan= PatientImplementationPlan::where('id', $ip_id)->get();
            if ($patientImpPlan->count()<1) {
                return prepareResult(false,getLangByLabelGroups('IP','message_record_not_found'), [],config('httpcodes.not_found'));
            }

            $filename = $ip_id."-".time().".pdf";
            $data['ips'] = $patientImpPlan;
            $pdf = PDF::loadView('print-ip', $data);
            $pdf->save('reports/ip/'.$filename);

            $returnData = [
                'url' => env('CDN_DOC_URL').'reports/ip/'.$filename
            ];

            return prepareResult(true,'Print IP',$returnData, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            \Log::info($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
}
