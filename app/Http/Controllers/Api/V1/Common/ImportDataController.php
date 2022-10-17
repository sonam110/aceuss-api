<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Imports\PatientsImport;
use Excel;
use Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ImportDataController extends Controller
{
    protected $top_most_parent_id;
    public function __construct()
    {
        //$this->middleware('permission:patient-import',['only' => ['patientImport', 'downloadPatientImportSampleFile']]);
        
        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
    }

    public function patientImport(Request $request)
    {
        $userInfo = getUser();
        $validator = Validator::make($request->all(),[
            'file' => 'required',   
        ],
        [
            'file' =>  'File is required.',
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        $file = $request->file;
        $extension = $file->getClientOriginalExtension();
        $allowedExt = ['xlsx'];
        if (!in_array($extension, $allowedExt)) {
            return prepareResult(false, 'Only XLSX file extension allowed.',[], config('httpcodes.bad_request'));
        }

        $patients = Excel::toArray(new PatientsImport(), $file);
        $excalRow   = $patients[0];
        $errorShow = false;
        $error = null;
        foreach($excalRow as $key => $patient)
        {
            $checkEmail = User::where('email', $patient['email'])->withoutGlobalScope('top_most_parent_id')->first();
            $checkUnique = User::where('custom_unique_id', $patient['patient_unique_id'])->withoutGlobalScope('top_most_parent_id')->first();
            if($checkEmail)
            {
                $errorShow = true;
                $error .= 'ROW no. '.($key + 1). ' is invalid because '. $patient['email'].' email is already exist.<br>'; 
            }
            elseif($checkUnique)
            {
                $errorShow = true;
                $error .= 'ROW no. '.($key + 1). ' is invalid because '. $patient['patient_unique_id'].' patient unique ID is already exist.<br>'; 
            }
            else
            {
                $company_type_id = null;
                if($patient['company_type_id']=='group living')
                {
                    $company_type_id[] = "1";
                }
                elseif($patient['company_type_id']=='home living')
                {
                    $company_type_id[] = "2";
                }
                elseif($patient['company_type_id']=='single living')
                {
                    $company_type_id[] = "3";
                }

                $roleInfo = getRoleInfo($this->top_most_parent_id, 'Patient');

                $user = new User;
                $user->unique_id = generateRandomNumber();
                $user->branch_id = getBranchId();
                $user->custom_unique_id = $patient['patient_unique_id'];
                $user->user_type_id = 6;
                $user->role_id = $roleInfo->id;
                $user->company_type_id = !empty($company_type_id) ? json_encode($company_type_id) : null;
                $user->category_id = $userInfo->category_id;
                $user->top_most_parent_id = $this->top_most_parent_id;
                $user->parent_id = $userInfo->id;
                $user->name = $patient['patient_name'];
                $user->email = $patient['email'];
                $user->password = !empty($patient['password']) ? Hash::make($patient['password']) : Hash::make($patient['personal_number']);
                $user->contact_number = $patient['contact_number'];
                $user->gender = $patient['gender'];
                $user->personal_number = $patient['personal_number'];
                $user->full_address = $patient['full_address'];
                $user->user_color = '#ff0000';
                $user->disease_description = $patient['disease_description'];
                $user->created_by = $userInfo->id;
                
                $user->is_secret = ($patient['is_secret'] == 'yes') ? 1 : 0;
                $user->step_one = 1;
                $user->step_two = 0 ;
                $user->step_three = 0 ;
                $user->step_four = 0 ;
                $user->step_five = 0 ;
                $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $user->save();
                if(!empty($roleInfo))
                {
                    $role = $roleInfo;
                    $user->assignRole($role->name);
                }
            }
        }

        if($errorShow)
        {
            return prepareResult(true, getLangByLabelGroups('BcCommon','message_some_data_not_imported'). $error, [], config('httpcodes.success'));
        }
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_import') , [], config('httpcodes.success'));
    }

    public function downloadPatientImportSampleFile(Request $request)
    {
        $filePath = asset('sample/patient_import.xlsx');
        return prepareResult(true,'Sample file' , ['filepath' => $filePath], config('httpcodes.success'));
    }
}
