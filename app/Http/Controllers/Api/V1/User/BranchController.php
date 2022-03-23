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
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[ 
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|same:confirm-password|min:8|max:30',
                'contact_number' => 'required', 

            ],
            [
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            'password.required' =>  getLangByLabelGroups('UserValidation','password'),
            'password.min' =>  getLangByLabelGroups('UserValidation','password_min'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            if(auth()->user()->user_type_id=='1'){
                $top_most_parent_id = auth()->user()->id;
            }
            elseif(auth()->user()->user_type_id=='2')
            {
                $top_most_parent_id = auth()->user()->id;
            } else {
                $top_most_parent_id = auth()->user()->top_most_parent_id;
            }

            $topParent = findBranchTopParentId($request->branch_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->branch_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create branch more than five level ',[], $this->unprocessableEntity);

            }
           
            $user = new User;
            $user->user_type_id = '11';
            $user->role_id = '11';
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->top_most_parent_id = $top_most_parent_id;
            $user->parent_id = $userInfo->id;
            $user->branch_id = $request->branch_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->contact_person_name = $request->contact_person_name;
            $user->contact_person_email = $request->contact_person_email;
            $user->contact_person_phone = $request->contact_person_phone;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->created_by = $userInfo->id;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();

            $role = Role::where('id','11')->first();
            $user->assignRole($role->name);

            if(env('IS_MAIL_ENABLE',false) == true){ 
                    $variables = ([
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'city' => $user->city,
                    'zipcode' => $user->zipcode,
                    ]);   
                $emailTem = EmailTemplate::where('id','2')->first();           
                $content = mailTemplateContent($emailTem->content,$variables);
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
            $userdetail = User::with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','create') ,$userdetail, $this->success);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[  
                'name' => 'required', 
                'contact_number' => 'required', 

            ],
            [
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }

            if(auth()->user()->user_type_id=='1'){
                $top_most_parent_id = auth()->user()->id;
            }
            elseif(auth()->user()->user_type_id=='2')
            {
                $top_most_parent_id = auth()->user()->id;
            } else {
                $top_most_parent_id = auth()->user()->top_most_parent_id;
            }

            $checkId = User::where('id',$id)->where('top_most_parent_id',$top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','id_not_found'), [],$this->not_found);
            }
            $topParent = findBranchTopParentId($request->branch_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->branch_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create branch more than five level ',[], $this->unprocessableEntity);

            }
            
            $user = User::find($id);
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->branch_id = $request->branch_id;
            $user->name = $request->name;
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
           // $user->organization_number = $request->organization_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->status = ($request->status) ? $request->status: 1 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            $userdetail = User::with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','update'),$userdetail, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function checkLevel($branch_id){
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
