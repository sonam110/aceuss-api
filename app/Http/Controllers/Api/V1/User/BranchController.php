<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CategoryType;
use App\Models\EmailTemplate;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class BranchController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:branch-add', ['only' => ['store']]);
        $this->middleware('permission:branch-edit', ['only' => ['update']]);
        
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[ 
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|same:confirm-password|min:8|max:30', 
                'contact_number' => 'required', 
                'company_type_id' => 'required', 

            ],
            [
            'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','message_email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','message_email_invalid'),
            'password.required' =>  getLangByLabelGroups('UserValidation','message_password'),
            'password.min' =>  getLangByLabelGroups('UserValidation','message_password_min'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','message_contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            
            $top_most_parent_id = auth()->user()->top_most_parent_id;
            $roleInfo = getRoleInfo($top_most_parent_id, 'Branch');

            $topParent = findBranchTopParentId($request->branch_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->branch_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create branch more than five level ',[], config('httpcodes.bad_request'));
            }
           
            $user = new User;
            $user->user_type_id = '11';
            $user->unique_id = generateRandomNumber();
            $user->role_id = $roleInfo->id;
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->top_most_parent_id = $top_most_parent_id;
            $user->parent_id = $userInfo->id;
            $user->branch_id = !empty($request->branch_id) ? $request->branch_id : getBranchId();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->establishment_year = User::find($top_most_parent_id)->establishment_year;
            $user->user_color = $request->user_color;
            $user->created_by = $userInfo->id;
            $user->is_file_required = ($request->is_file_required) ? 1 : 0;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode : 'Web';
            $user->contact_person_name = $request->contact_person_name;
            $user->contact_person_number = $request->contact_person_number;
            $user->documents = is_array($request->documents) ? json_encode($request->documents) : null;
            $user->save();

            if($roleInfo)
            {
                $role = $roleInfo;
                $user->assignRole($role->name);
            }

            if(env('IS_MAIL_ENABLE',false) == true){ 
                $content = ([
                    'company_id' => $user->top_most_parent_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->id,
                ]);   
                Mail::to($user->email)->send(new WelcomeMail($content));
            }
            
            /*$categoryTypes = CategoryType::where('created_by','1')->get();
            if(!empty($categoryTypes)) {
                foreach ($categoryTypes as $key => $type) {
                    $addType = new CategoryType;
                    $addType->top_most_parent_id = $user->id;
                    $addType->created_by = $user->id;
                    $addType->name = $type->name;
                    $addType->status = '1';
                    $addType->save();
                }
            }*/

             DB::commit();
            $userdetail = User::with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_create') ,$userdetail, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[  
                'name' => 'required',  
                'contact_number' => 'required', 

            ],
            [
            'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','message_contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $top_most_parent_id = auth()->user()->top_most_parent_id;

            $checkId = User::where('id',$id)->where('top_most_parent_id',$top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $topParent = findBranchTopParentId($request->branch_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->branch_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create branch more than five level ',[], config('httpcodes.bad_request'));

            }
            
            $user = User::find($id);
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->branch_id = !empty($request->branch_id) ? $request->branch_id : getBranchId();
            $user->name = $request->name;
            $user->contact_number = $request->contact_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->establishment_year = User::find($top_most_parent_id)->establishment_year;
            $user->user_color = $request->user_color;
            $user->is_file_required = ($request->is_file_required) ? 1 : 0;
            $user->status = ($request->status) ? $request->status : 1;
            $user->contact_person_number = $request->contact_person_number;
            $user->contact_person_name = $request->contact_person_name;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode : 'Web';
            $user->documents = is_array($request->documents) ? json_encode($request->documents) : null;
            $user->save();

            DB::commit();
            $userdetail = User::with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_update'),$userdetail, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function checkLevel($branch_id)
    {
        $firstLevel =  User::where('branch_id',$branch_id)->get();
        $level = 1;
        foreach ($firstLevel as $key => $level1) {
            if (count($level1->branchChildren)>0) {
                $level = $level+1 ;
                $secondLevel =  User::where('branch_id',$level1->id)->get();
                foreach ($secondLevel as $key => $level2) {
                   if (count($level2->branchChildren)>0) {
                    $level = $level+1 ;
                    $thirdLevel =  User::where('branch_id',$level2->id)->get();
                    foreach ($thirdLevel as $key => $level3) {
                       if (count($level3->branchChildren)>0) {
                        $level = $level+1 ;
                        $fourthLevel =  User::where('branch_id',$level3->id)->get();
                        foreach ($fourthLevel as $key => $level4) {
                           if (count($level4->branchChildren)>0) {
                            $level = $level+1 ;
                            $fiveLevel =  User::where('branch_id',$level4->id)->get();
                            foreach ($fiveLevel as $key => $level5) {
                               if (count($level5->branchChildren)>0) {
                                //$level = $level+1 ;
                                
                               }
                            }

                           }
                        }

                       }
                    }

                   }
                }

            }
        }
        return $level;
    }

}
