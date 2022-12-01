<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Department;
use App\Models\Task;
use App\Models\AssignTask;
use App\Models\ActivityAssigne;
use App\Models\DeviceLoginHistory;
use App\Models\Notification;
use App\Models\SmsLog;
use App\Models\Comment;
use App\Models\EmailTemplate;
use App\Models\CompanySetting;
use App\Models\AssigneModule;
use App\Models\Journal;
use App\Models\Deviation;
use App\Models\Activity;
use App\Models\MobileBankIdLoginLog;
use App\Models\PersonalInfoDuringIp;
use App\Models\CategoryMaster;
use App\Models\CompanyWorkShift;
use App\Models\OVHour;
use App\Models\OauthAccessTokens;
use App\Models\PatientEmployee;
use Edujugon\PushNotification\PushNotification;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Events\EventNotification;
use Str as Str;
use App\Models\LicenceKeyManagement;
use mervick\aesEverywhere\AES256;

function getUser() {
    return auth('api')->user();
}

function userInfo($user_id) {
    return User::withoutGlobalScope('top_most_parent_id')->find($user_id);
}

function returnBoolean($bool=null) {
    return ($bool==1 || $bool==true) ? 1 : 0;
}

function logException($exception)
{
    return \Log::error($exception);
}

function aceussEncrypt($value)
{
    if(env('ENC_DEC', false))
    {
        return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
    }
    return $value;
}

function aceussDecrypt($value)
{
    if(env('ENC_DEC', false))
    {
        return (!empty($value)) ? AES256::decrypt($value, env('ENCRYPTION_KEY')) : NULL;
    }
    return $value;
}

//==================== For Api ================//

    //---------Api Msg/status-----------//
function prepareResult($status, $message, $payload, $status_code)
{
  if(empty($payload)) {
    $payload = new stdClass();
  } else {
     $payload = $payload;
  }
  return response()->json(['success' => $status, 'message' => $message, 'payload' => $payload, 'code' => $status_code],$status_code);
    
}

function dateTimeFormat($value) 
{
    return !empty($value) ? date('Y-m-d H:i:s', strtotime($value)) : null;
}

function getTopParent($id) {
    if(Auth::check()){
        $adminAgg = [];
        $patents = User::select('id')->get()->toArray();
        //dd($patents);
        $tree = Array();
        $tree = User::where('id',Auth::user()->parent_id)->where('id','<', Auth::id())->orderBy('id','DESC')->pluck('parent_id')->toArray();
        //dd($tree);
        foreach ($tree as $key => $val) {
            $ids = getTopParent($id);
            if (!empty($ids)) {
                if (count($ids) > 0) $tree = array_merge($tree, $ids);
            }
        }
        //dd($tree);
        return $tree;
    }
    return null;
}

function findTopParentId($parent_id) {

    if(is_null($parent_id) == false ){
        $parent = Department::find($parent_id);
        return $parent ? findTopParentId($parent->parent_id) : $parent_id;
    }else {
        return $parent_id;
    }
    
}
function findBranchTopParentId($branch_id) {

    if(is_null($branch_id) == false ){
        $branch = User::find($branch_id)->branch_id;
        return $branch ? findTopParentId($branch) : $branch_id;
    }else {
        return $branch_id;
    }
    
}

function buildTree(array $elements, $parentId = null, $level =1) {
    $branch = [];
    foreach ($elements as $key => $element) {
        if ($element['parent_id'] == $parentId) {
            $element['level'] = $level;
            $children = buildTree($elements, $element['id'],$level+1);
            if ($children) {
                $element['children'] = $children;
            }
            //$element['level'] = $level;
            $branch[] = $element;
        }
    }
    return $branch;
}





function getLatestParent()
{
    if(Auth::check()){
        $user = User::find(Auth::id());
        if($user->parentUnit) {
            return $user->parentUnit->getLatestParent();
        }
        return $user;
    }
    return null;
}
function getChildren($parent_id)
{
    $count = 0;
    if (!empty($parent_id))
    {
        $tree = User::where('parent_id', $parent_id)->pluck('id')
            ->toArray();
        foreach ($tree as $key => $val)
        {
            $ids = getChildren($val);
            if (!empty($ids))
            {
               $count  =  count($ids);
            }
        }
    }
    return $count;
}

function getUserLanguage()
{
    $getLang = env('APP_DEFAULT_LANGUAGE', '1');
    if(Auth::check())
    {
        $getLang = Auth::user()->language_id;
        if(empty($getLang))
        {
            $getLang = env('APP_DEFAULT_LANGUAGE', '1');
        }
    }
    return $getLang;
}

function getLangByLabelGroups($groupName,$label_name)
{
    $lang = getUserLanguage();
    $getGroup = Group::select('id')
    ->with(['LabelGroups' => function($q) use ($lang, $label_name) {
        $q->select('id','group_id','label_value')
        ->where('language_id', $lang)
        ->where('label_name', $label_name);
    }])
    ->where('name', $groupName)
    ->first();
    if(!$getGroup)
    {
        $getGroup = Group::select('id')
        ->with(['LabelGroups' => function($q) use ($lang, $label_name) {
            $q->select('id','group_id','label_value')
            ->where('language_id', 1)
            ->where('label_name', $label_name);
        }])
        ->where('name', $groupName)
        ->first();
    }
    $data = @$getGroup->LabelGroups;
    return @$data['label_value'];
}


function getTemplate($mail_for, $obj, $user, $otp=null)
{
    $mail_subject = false;
    $mail_body = false;
    $getTemp = EmailTemplate::where('mail_sms_for', $mail_for)->first();
    if($getTemp)
    {
        $mail_body = $getTemp->mail_body;
        $arrayVal = [
            '{{token}}'   => $otp,
            '{{name}}'  => $user['name'],
            '{{company_name}}' => $obj['company_name'],
        ];
        $mail_body = strReplaceAssoc($arrayVal, $mail_body);
        if(!empty($user))
        {
            $arrayVal = [
                '{{name}}'  => $user['name'],
                '{{company_name}}' => $obj['company_name'],
                '{{company_email}}' => $obj['company_email'],
            ];
            $mail_body = strReplaceAssoc($arrayVal, $mail_body);
        }
        $mail_subject = $getTemp->mail_subject;
    }
    $returnObj = [
        'subject'   => $mail_subject,
        'body'      => $mail_body
    ];
    return $returnObj;      
}

function addTask($task,$resource_id){
    $user = getUser();
    $addTask = new Task;
    $addTask->type_id = $task['type_id']; 
    $addTask->resource_id = $resource_id; 
    $addTask->parent_id = $task['parent_id']; 
    $addTask->title = $task['title']; 
    $addTask->description = $task['description']; 
    $addTask->start_date = $task['start_date']; 
    $addTask->start_time = $task['start_time']; 
    $addTask->is_repeat = ($task['is_repeat']) ? 1:0; 
    $addTask->every = $task['every']; 
    $addTask->repetition_type = $task['repetition_type']; 
    $addTask->week_days = ($task['week_days']) ? json_encode($task['week_days']) :null;
    $addTask->month_day = $task['month_day'];
    $addTask->end_date = $task['end_date'];
    $addTask->end_time =$task['end_time'];
    $addTask->created_by =$user->id;
    $addTask->save();
    if(is_array($task['employees']) ){
        foreach ($task['employees'] as $key => $employee) {
            $taskAssign = new AssignTask;
            $taskAssign->task_id = $addTask->id;
            $taskAssign->user_id = $employee;
            $taskAssign->assignment_date = date('Y-m-d');
            $taskAssign->assigned_by = $user->id;
            $taskAssign->save();
        }
    }
    if($task){
        return $task;
    } else {
        return null;
    }
    

}



function pushNotification($sms_for,$companyObj,$obj,$save_to_database,$module,$id,$screen, $status_code,$actionNoti=null)
{
    if(!empty($obj['user_id']))
    {
        $userDeviceInfo = DeviceLoginHistory::where('user_id',$obj['user_id'])
            ->whereNotNull('device_token')
            ->whereIn('login_via',['1','2'])
            ->orderBy('created_at', 'DESC')
            ->first();

        $title  = false;
        $body   = false;
        $getMsg = EmailTemplate::where('mail_sms_for', $sms_for)->first();
       
        if($getMsg)
        {
            $body = $getMsg->notify_body;
            $title = $getMsg->mail_subject;
            $arrayVal = [
                '{{name}}'              => $obj['name'],
                '{{email}}'             => $obj['email'],
                '{{title}}'             => $obj['title'],
                '{{patient_id}}'        => $obj['patient_id'],
                '{{start_date}}'        => $obj['start_date'],
                '{{start_time}}'        => $obj['start_time'],
                '{{company_name}}'      => $companyObj['company_name'],
                '{{company_address}}'   => $companyObj['company_address'],
            ];
            $body = strReplaceAssoc($arrayVal, $body);
            $title = strReplaceAssoc($arrayVal, $title);
        }


        if($userDeviceInfo && $getMsg)
        { 
            $push = new PushNotification('fcm');
            $push->setMessage([
                "notification"=>[
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                    'android_channel_id' => '1',
                    //'timestamp' => date('Y-m-d G:i:s')
                ],
                'data'=>[
                    'id'  => $id,
                    'user_type'  => $obj['user_type'],
                    'module'  => $module,
                    'screen'  => $screen
                ]                        
            ])
            ->setApiKey(env('FIREBASE_KEY'))
            ->setDevicesToken($userDeviceInfo->device_token)
            ->send();
        }

        if($save_to_database == true)
        {
            $notification = new Notification;
            $notification->user_id          = $obj['user_id'];
            $notification->sender_id        = Auth::id();
            $notification->device_id        = $userDeviceInfo ? $userDeviceInfo->id : null;
            $notification->device_platform  = $userDeviceInfo ? $userDeviceInfo->login_via : null;
            $notification->type             = $obj['type'];
            $notification->status_code      = $status_code;
            $notification->user_type        = $obj['user_type'];
            $notification->module           = $module;
            $notification->title            = $title;
            $notification->sub_title        = null;
            $notification->message          = $body;
            $notification->image_url        = '';
            $notification->screen           = $screen;
            $notification->data_id          = $id;
            $notification->extra_param      = json_encode($extra_param);
            $notification->read_status      = false;
            $notification->save();

            $userUniqueId = User::select('unique_id')->find($obj['user_id']);
            if($userUniqueId)
            {
                \broadcast(new EventNotification($notification, $obj['user_id'], $userUniqueId->unique_id, $actionNoti));
            }
        }    
    }
    return true;
}


function actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param = null,$actionNoti=null)
{
    if(env('IS_NOTIFICATION_ENABLE')== true)
    {
        if(!empty($user))
        {
            if(!empty($notification_template))
            {
                $title = $notification_template->mail_subject;
                $body = strReplaceAssoc($variable_data,$notification_template->notify_body);
                $module = $notification_template->module;
                $type = $notification_template->type;
                $event = $notification_template->event;
                $screen = $notification_template->screen;
                $status_code = $notification_template->status_code;
                $save_to_database = $notification_template->save_to_database;
            }
            else
            {
                $title = '';
                $body = '';
                $module = '';
                $type = '';
                $event = '';
                $screen = '';
                $status_code = '';
                $save_to_database = '';
            }

            $userDeviceInfo = DeviceLoginHistory::where('user_id',$user->id)
                ->whereNotNull('device_token')
                ->whereIn('login_via',['1','2'])
                ->orderBy('created_at', 'DESC')
                ->first();

            if($userDeviceInfo)
            { 
                $push = new PushNotification('fcm');
                $push->setMessage([
                    "notification"=>[
                        'title' => $title,
                        'body'  => $body,
                        'sound' => 'default',
                        'android_channel_id' => '1',
                        //'timestamp' => date('Y-m-d G:i:s')
                    ],
                    'data'=>[
                        'id'        => $data_id,
                        'user_type' => $user->user_type_id,
                        'module'    => $module,
                        'event'     => $event,
                        'screen'    => $screen,
                        'extra_param'=>$extra_param,
                    ]                        
                ])
                ->setApiKey(env('FIREBASE_KEY'))
                ->setDevicesToken($userDeviceInfo->device_token)
                ->send();
            }

            if($save_to_database == true)
            {
                $notification = new Notification;
                $notification->user_id          = $user->id;
                $notification->sender_id        = Auth::id();
                $notification->device_id        = $userDeviceInfo ? $userDeviceInfo->id : null;
                $notification->device_platform  = $userDeviceInfo ? $userDeviceInfo->login_via : null;
                $notification->type             = $type;
                $notification->status_code      = $status_code;
                $notification->user_type        = $user->user_type_id;
                $notification->module           = $module;
                $notification->event           = $event;
                $notification->title            = $title;
                $notification->sub_title        = null;
                $notification->message          = $body;
                $notification->image_url        = '';
                $notification->screen           = $screen;
                $notification->data_id          = $data_id;
                $notification->extra_param          = json_encode($extra_param);
                $notification->read_status      = false;
                $notification->save();

                \broadcast(new EventNotification($notification, $user->id, $user->unique_id, $actionNoti));
            }    
        }
    }
    return true;
}

function strReplaceAssoc(array $replace, $subject) 
{ 
    return str_replace(array_keys($replace), array_values($replace), $subject);
}

function sendMessage($sms_for,$obj, $companyObj)
{
    $isSent = false;
    $message = false;
    $getMsg = EmailTemplate::where('mail_sms_for', $sms_for)->first();
    if($getMsg && env('IS_ENABLED_SEND_SMS', false)==true)
    {
        $message = $getMsg->sms_body;
         $arrayVal = [
            '{{name}}'  => $obj['name'],
            '{{email}}' => $obj['email'],
            '{{title}}' => $obj['title'],
            '{{patient_id}}' => $obj['patient_id'],
            '{{start_date}}' => $obj['start_date'],
            '{{start_time}}' => $obj['start_time'],
            '{{company_name}}' => $companyObj['company_name'],
            '{{company_address}}' =>  $companyObj['company_address'],
        ];
        $message = strReplaceAssoc($arrayVal, $message);

        $ch = curl_init();
        $phone_number   = ltrim($obj['contact_number'], '0');
        $receivers      = ((strlen($phone_number))==9) ? env('COUNTRY_CODE').$phone_number : $phone_number; 
        $sender         =  env('SENDERID', 'Aceuss');
        $account        = env('PIXIE_ACCOUNT', '12106497');
        $password       = env('PIXIE_PASS', 'iTGYqEBR');
         
        curl_setopt($ch, CURLOPT_URL,  "http://smsserver.pixie.se/sendsms?");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "account=$account&pwd=$password&sender=$sender&receivers=$receivers&message=$message&quality=2");
        $buffer = curl_exec($ch);

        // create SMS log
        smslog($obj['company_id'],'1', $obj, $receivers, $message);
    }
    
    return $isSent;     
}

function smslog($top_most_parent_id,$type_id ,$resource_id, $phone_number, $message)
{
    //Create SMS log
    $smsLog = new SmsLog;
    $smsLog->top_most_parent_id = $top_most_parent_id;
    $smsLog->type_id = $type_id;
    $smsLog->resource_id = $resource_id;
    $smsLog->mobile = $phone_number;
    $smsLog->message = $message;
    $smsLog->save();
    return true;
}

function comment($source_id, $source_namen,$comment) 
{ 
    if(Auth::check()){
        $addComment = new Comment;
        $addComment->source_id = $source_id ;
        $addComment->source_name = $source_namen;
        $addComment->comment = $comment;
        $addComment->created_by = Auth::id();
        $addComment->save();
        return $addComment;
    }
    return null;
}

function generateRandomNumber($len = 12) {
    return Str::random($len);
}


function companySetting($company_id){
    $user = CompanySetting::select(array('company_settings.*', DB::raw("(SELECT organization_number from users WHERE users.id = ".$company_id.") organization_number")))->where('user_id',$company_id)->first();
    $settingDetail  = [];
    if($user){
        $settingDetail =[
            "company_name" => $user->company_name,
            "company_logo" => $user->company_logo,
            "company_email" => $user->company_email,
            "company_contact" => $user->company_contact,
            "company_address" => $user->company_address,
            "organization_number" => $user->organization_number,
            "contact_person_name" => $user->contact_person_name,
            "contact_person_email" => $user->contact_person_email,
            "contact_person_phone" => $user->contact_person_phone,
            "follow_up_reminder" => $user->follow_up_reminder,
            "before_minute" => $user->before_minute,
            "extra_hour_rate" => $user->extra_hour_rate,
            "ob_hour_rate" => $user->ob_hour_rate,
            "relaxation_time" => $user->relaxation_time,
        ];
        return  $settingDetail;

    }
}

function activityDateFrame($start_date,$end_date,$is_repeat,$every,$repetition_type,$repeat_dates)
{
    $from = Carbon::parse($start_date);
    $to =   (!empty($end_date)) ? Carbon::parse($end_date) : Carbon::parse($start_date);
    $start_from = $from->format('Y-m-d');
    $end_to = $to->format('Y-m-d');
    $dateTimeFrame = []; 
    if($is_repeat == true){
        if($repetition_type == '1'){
            for($d = $from; $d->lte($to); $d->addDay($every)) {
                $dateTimeFrame[] = $d->format('Y-m-d');
            }
        }
        elseif($repetition_type == '2'){
           $dateTimeFrame  = $repeat_dates;
          
        }
        elseif($repetition_type == '3'){
             $dateTimeFrame  = $repeat_dates;
           
        }   
        elseif($repetition_type == '4'){
            $dateTimeFrame  = $repeat_dates;
        }else{
            $dateTimeFrame[] = $from->format('Y-m-d');
        }
        
    } else {
        for($d = $from; $d->lte($to); $d->addDay()) {
            $dateTimeFrame[] = $d->format('Y-m-d');
        }
    }
    
    return $dateTimeFrame;
}

function taskDateFrame($start_date,$end_date,$is_repeat,$every,$repetition_type,$repeat_dates)
{
    $from = Carbon::parse($start_date);
    $to =   (!empty($end_date)) ? Carbon::parse($end_date) : Carbon::parse($start_date);
    $start_from = $from->format('Y-m-d');
    $end_to = $to->format('Y-m-d');
    $dateTimeFrame = []; 
    
    if($is_repeat == true)
    {
        if($repetition_type == '1'){
            for($d = $from; $d->lte($to); $d->addDay($every)) {
                $dateTimeFrame[] = $d->format('Y-m-d');
            }
        }
        elseif($repetition_type == '2') {
           $dateTimeFrame  = $repeat_dates;
        }
        elseif($repetition_type == '3') {
             $dateTimeFrame  = $repeat_dates;
           
        }   
        elseif($repetition_type == '4') {
            $dateTimeFrame  = $repeat_dates;
        } 
        else {
            $dateTimeFrame[] = $from->format('Y-m-d');
        }
        
    } else {
        $dateTimeFrame[] = $from->format('Y-m-d');
        /*for($d = $from; $d->lte($to); $d->addDay()) {
            $dateTimeFrame[] = $d->format('Y-m-d');
        }*/
    }
    return $dateTimeFrame;
}

/*function activityTimeFrame($start_date,$is_repeat,$every,$repetition_type,$week_days,$month_day,$end_date)
{
   
    $from = Carbon::parse($start_date);
    $to =   (!empty($end_date)) ? Carbon::parse($end_date) : Carbon::parse($start_date);
    $start_from = $from->format('Y-m-d');
    $end_to = $to->format('Y-m-d');

    $dateTimeFrame = []; 
    if($is_repeat == true){
        if($repetition_type == '1'){
            for($d = $from; $d->lte($to); $d->addDay($every)) {
                $dateTimeFrame[] = $d->format('Y-m-d');
            }
        }
        elseif($repetition_type == '2'){
           for($w = $from; $w->lte($to); $w->addWeeks($every)) {
                $date = Carbon::parse($w);
                $startWeek = $w->startOfWeek()->format('Y-m-d');
                $weekNumber = $date->weekNumberInMonth;
                $start = Carbon::createFromFormat("Y-m-d", $startWeek);
                $end = $start->copy()->endOfWeek()->format('Y-m-d');
                for($p = $start; $p->lte($end); $p->addDays()) {
                    if(strtotime($start_from) <= strtotime($p) && strtotime($end_to) >= strtotime($p) ) {
                        if(in_array($p->dayOfWeek, $week_days)){
                            array_push($dateTimeFrame, $p->copy()->format('Y-m-d'));
                        }
                    }
                }
               
            }
          
        }
        elseif($repetition_type == '3'){
            for($m = $from; $m->lte($to); $m->addMonths($every)) {
                $start = Carbon::parse($m)->startOfMonth();
                $end = Carbon::parse($m)->endOfMonth();
                for($q = $start; $q->lte($end); $q->addDays()) {
                    if(strtotime($start_from) <= strtotime($q) && strtotime($end_to) >= strtotime($q) ) {
                        if(in_array($q->day, [$month_day])){
                            array_push($dateTimeFrame, $q->copy()->format('Y-m-d'));
                        }
                    }
                }
            }
           
        }   
        elseif($repetition_type == '4'){
            for($y = $from; $y->lte($to); $y->addYears($every)) {
                $dateTimeFrame[] = $y->format('Y-m-d');
            }
        }else{
            $dateTimeFrame[] = $from->format('Y-m-d');
        }
        
    } else {
        $dateTimeFrame[] = $from->format('Y-m-d');
    }
    return $dateTimeFrame;
}*/
 
function monthDaysBetween($month_day, $start, $end,$every){
    $result = [];
    while ($start->lt($end)) {
        if(in_array($start->day, [$month_day])){
            array_push($result, $start->copy()->format('Y-m-d'));
        }
        $start->addMonths($every);
    }

    return $result;
}


function weekDaysBetween($requiredDays, $start, $end,$every)
{
    $result = [];
    while ($start->lt($end)) {
        if(in_array($start->dayOfWeek, $requiredDays)){
            array_push($result, $start->copy()->format('Y-m-d'));
        }
        $start->addDays();
    }
    return $result;
}

function getBranchId()
{
    if(Auth::check())
    {
        if(auth()->user()->user_type_id=='11') {
            $branch_id = auth()->user()->id;
        }
        else {
            $branch_id = auth()->user()->branch_id;
        } 
        if(empty($branch_id))
        {
            $branch_id = auth()->user()->top_most_parent_id;
        }
        return $branch_id;
    }
    return null;
}


function checkAssignModule($module_id)
{
    if(Auth::check()){
        $checkModule = AssigneModule::where('user_id',auth()->user()->top_most_parent_id)->where('module_id',$module_id)->first();
        $is_assign = ($checkModule) ? true :false;
        return $is_assign;
    }
    return null;
}

function journal($activity_id)
{
    $activity = Activity::find($activity_id);
    $journal = new Journal;
    $journal->activity_id = $activity_id;
    $journal->branch_id = getBranchId();
    $journal->patient_id = $activity->patient_id;
    $journal->emp_id = auth()->id();
    $journal->category_id = $activity->category_id;
    $journal->subcategory_id = $activity->subcategory_id;
    $journal->date = !empty($activity->end_date) ? $activity->end_date : date('Y-m-d');
    $journal->time = !empty($activity->end_time) ? $activity->end_time :date('h:i');
    $journal->description = $activity->description;
    $journal->activity_note = $activity->comment;
    $journal->entry_mode =  (!empty($activity->entry_mode)) ? $activity->entry_mode :'Web';
    $journal->is_signed = 0;
    $journal->is_secret = 0;
    $journal->save();

    if($journal) {
        return $journal->id;
    } else {
        return null;
    }
}

function deviation($activity_id)
{
    $activity = Activity::find($activity_id);
    $date = !empty($activity->end_date) ? $activity->end_date : date('Y-m-d');
    $time = !empty($activity->end_time) ? $activity->end_time :date('h:i');

    $category_id = $activity->category_id;
    $subcategory_id = $activity->subcategory_id;

    $getFirstDeviationCatId = CategoryMaster::where('name', 'Ej utförd insatser')->withoutGlobalScope('top_most_parent_id')->first();
    if($getFirstDeviationCatId)
    {
        $getFirstDeviationSubCatId = CategoryMaster::where('parent_id', $getFirstDeviationCatId->id)->withoutGlobalScope('top_most_parent_id')->first();
        $category_id = $getFirstDeviationCatId->id;
        if($getFirstDeviationSubCatId)
        {
            $subcategory_id = $getFirstDeviationSubCatId->id;
        }
    }
    
    $deviation = new Deviation;
    $deviation->activity_id = $activity->id;
    $deviation->branch_id = $activity->branch_id;
    $deviation->patient_id = $activity->patient_id;
    $deviation->emp_id = auth()->id();
    $deviation->category_id = $category_id;
    $deviation->subcategory_id = $subcategory_id;
    $deviation->date_time = $date.' '.$time;
    $deviation->description = $activity->description;
    $deviation->activity_note = $activity->comment;
    $deviation->immediate_action = null;
    $deviation->critical_range = 1;
    $deviation->is_secret = 0;
    $deviation->is_signed = 0;
    $deviation->is_completed = 0;          
    $deviation->entry_mode = (!empty($activity->entry_mode)) ? $activity->entry_mode :'Web';
    $deviation->save();
    if($deviation){
        return $deviation->id;
    }else {
        return null;
    }
}

function dates($value) 
{
    $s = $value%60;
    $m = floor(($value %3600)/60);
    $h = floor(($value %86400)/3600);
    $d = floor(($value %2592000)/86400);
    $M = floor($value /2592000);
    $data = "$h:$m:$s";
    return $data;
}

function getDuration($totalDuration,$type="0")
{
    $duration = "";
    $hours = floor($totalDuration / 3600);
    $min = floor($totalDuration / 60) % 60;
    $minuts = $min;//($min > 60) ? gmdate("I", $totalDuration % 3600): $min ;
    $hours = ($hours>9?$hours:'0'.$hours);
    $minuts = ($minuts>9?$minuts:'0'.$minuts);
    $seconds = gmdate("s", $totalDuration % 60);
    $diff = round($totalDuration / 3600).'h'.' '.$minuts.'m';
    // dd($totalDuration);
    if($hours > 0){
        $duration .= $hours.'h ';
    }else{
        $duration .= '00h ';
    }
    if($minuts > 0 || $hours > 0){

        $duration .=  $minuts.'m ';
    }else{
        $duration .= '00m ';
    }
    $duration .= $seconds.'s';
    if($type=="0"){
        return $diff;
    }else{
        return $duration;
    }    
}


function branchChilds($branch_id)
{

    $tree = Array();
    if (!empty($branch_id))
    {
        $tree = User::where('branch_id',$branch_id)->pluck('id')
            ->toArray();
        foreach ($tree as $key => $val)
        {
            $ids = branchChilds($val);
            if (!empty($ids))
            {
                if (count($ids) > 0) $tree = array_merge($tree, $ids);
            }
        }
    }
     
    return $tree;
}

function userChildBranches(User $user)
{
    $allBranches = [];
    if ($user->allChildBranches->count() > 0) {
        foreach ($user->allChildBranches as $child) {
            $allBranches[] = $child->id;
            $allBranches = array_merge($allBranches,is_array(userChildBranches($child))?userChildBranches($child):[] );
        }
    }
    if(in_array(@auth()->user()->user_type_id, [1,3,4,5,6,7,8,9,10,12,13,14,15]))
    {
        $allBranches = array_merge($allBranches, [$user->id]);
    }
    //$allBranches = array_merge($allBranches, [$user->id]);
    return array_keys(array_flip($allBranches));
}

function userTrashedChildBranches(User $user)
{
    $allBranches = [];
    if ($user->allTrashedChildBranches->count() > 0) {
        foreach ($user->allTrashedChildBranches as $child) {
            $allBranches[] = $child->id;
            $allBranches = array_merge($allBranches,is_array(userTrashedChildBranches($child))?userTrashedChildBranches($child):[] );
        }
    }
    if(in_array(@auth()->user()->user_type_id, [1,3,4,5,6,7,8,9,10,12,13,14,15]))
    {
        $allBranches = array_merge($allBranches, [$user->id]);
    }
    //$allBranches = array_merge($allBranches, [$user->id]);
    return array_keys(array_flip($allBranches));
}

function bankIdVerification($personalNumber, $person_id, $group_token_or_id, $loggedInUserId, $request_from, $top_most_parent_id, $method, $display_message=null)
{
    if(env('IS_MOBILE_BANK_ON', false))
    {
        $ch = curl_init();
        //$method = 1 (Auth) else 2 (Sign)
        if($method==1)
        {
            curl_setopt($ch, CURLOPT_URL, env('BANKIDAPIURL', 'https://client.grandid.com').'/json1.1/FederatedLogin?apiKey='.env('BANKIDAPIKEY', '479fedcee8e6647423d3b4614c25f50b').'&authenticateServiceKey='.env('BANKIDAPISECRET', '18c7f582c64cdf0ae758e2b1e80ae396'));

            $userVisibleData = null;
            $userNonVisibleData = null;
        }
        else
        {
            curl_setopt($ch, CURLOPT_URL, env('BANKIDSIGNAPIURL', 'https://client.grandid.com').'/json1.1/FederatedLogin?apiKey='.env('BANKIDAPIKEY', '479fedcee8e6647423d3b4614c25f50b').'&authenticateServiceKey='.env('BANKIDSIGNAPISECRET', 'ad462cb0fe1aa1b0adabca6ffffe1d59'));

            $userVisibleData = base64_encode('hello');
            $userNonVisibleData = base64_encode('hello');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "personalNumber=".$personalNumber."&thisDevice=false&askForSSN=false&mobileBankId=true&deviceChoice=false&userVisibleData=".$userVisibleData."&userNonVisibleData=".$userNonVisibleData."&callbackUrl=".env('BANKCALLBACKURL')."/".base64_encode($person_id)."/".base64_encode($loggedInUserId)."/".$request_from."/".$method);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $message = curl_error($ch);
            curl_close($ch);
            \Log::error('something_went_wrong:curl error:');
            \Log::error($message);
            $error = 1;
        } elseif(!empty($resDecode['errorObject'])) {
            $message = $resDecode['errorObject']['message'];
            \Log::error('something_went_wrong:curl error:');
            \Log::error($message);
            $error = 1;
        } else {
            $resDecode = json_decode($result, true);
            if(!empty(@$resDecode['errorObject']))
            {
                $message = $resDecode['errorObject']['message'];
                \Log::error('something_went_wrong:curl error:');
                \Log::error($message);
                $error = 1;
            }
            else
            {
                //Generate Log
                mobileBankIdLoginLog($top_most_parent_id, $resDecode['sessionId'], substr($personalNumber,0,8), null, null, $request_from, $group_token_or_id);
                $error = 0;
            }
        }
    }
    else
    {
        $error = 0;
        $sessionId = generateRandomNumber(32);

        //Generate Log
        mobileBankIdLoginLog($top_most_parent_id, $sessionId, substr($personalNumber,0,8), null, null, $request_from, $group_token_or_id);
        
        $data = [
            'sessionId' => $sessionId,
            'redirectUrl' => env('BANKCALLBACKURL')."/".base64_encode($person_id)."/".base64_encode($loggedInUserId)."/".$request_from."/".$method.'?grandidsession='.$sessionId
        ];
        $resDecode = $data;
        $personalNumber = $personalNumber;
    }
    $return = [
        'error' => $error,
        'response' => $resDecode,
        'personnel_number' => $personalNumber
    ];
    return $return;
}

function mobileBankIdLoginLog($top_most_parent_id, $sessionId, $personnel_number, $name, $ip, $request_from, $extra_info=null)
{
    $checkSession = MobileBankIdLoginLog::where('sessionId', $sessionId)->count();
    if($checkSession<1)
    {
        //Create Mobile BankID log
        $mobileBankIdLoginLog = new MobileBankIdLoginLog;
        $mobileBankIdLoginLog->top_most_parent_id = $top_most_parent_id;
        $mobileBankIdLoginLog->sessionId = $sessionId;
        $mobileBankIdLoginLog->personnel_number = $personnel_number;
        $mobileBankIdLoginLog->name = $name;
        $mobileBankIdLoginLog->ip = $ip;
        $mobileBankIdLoginLog->request_from = $request_from;
        $mobileBankIdLoginLog->extra_info = $extra_info;
        $mobileBankIdLoginLog->save();
    }
    return true;
}

function getRoleInfo($top_most_parent_id, $role_name)
{
    $role = Role::where('name', $top_most_parent_id.'-'.$role_name)->first();
    return $role;
}

function getJournal($id)
{
    $journal = Journal::where('id',$id)
        ->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','JournalLogs','journalActions.journalActionLogs.editedBy', 'branch:id,name,branch_name','signedByUser:id,name')
        ->withCount('journalActions')
        ->first();
    return $journal;
}

function getJournals($ids)
{
    $journals = Journal::whereIn('id',$ids)
        ->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','JournalLogs','journalActions.journalActionLogs.editedBy')->withCount('journalActions')
        ->get();
    return $journals;
}

function dateDifference($start_date, $end_date, $differenceFormat = '%d' )
{
    $start = strtotime($start_date);
    $end = strtotime($end_date);

    $days_between = ceil(abs($end - $start) / 86400);
    return $days_between;
}

function getTopMostParent()
{
    $top_most_parent = User::find(auth()->user()->top_most_parent_id);
    return $top_most_parent;
}

function getPersonUserId($personal_info_during_ips_id)
{
    $user_id = null;
    $person = PersonalInfoDuringIp::select('id', 'user_id', 'email')->find($personal_info_during_ips_id);
    if($person)
    {
        $user = User::select('id', 'email')
            ->where('email', $person->email)
            ->withoutGlobalScope('top_most_parent_id')
            ->first();
        if($user)
        {
            //Update person user id
            $person->user_id = $user->id;
            $person->save();
            
            $user_id = $user->id;
        }
    }
    return $user_id;
}

function checkUserToken($token) 
{
    // break up the token_name(token)en into its three parts
    $token_parts = explode('.', $token);
    if (is_array($token_parts) && array_key_exists('1', $token_parts)) {
       $token_header =  $token_parts[1];
    } else {
        $token_header = null;
    }

    // base64 decode to get a json string
    $token_header_json = base64_decode($token_header);

    // then convert the json to an array
    $token_header_array = json_decode($token_header_json, true);

    $user_token = (is_array($token_header_array) && array_key_exists('jti', $token_header_array)) ? $token_header_array['jti'] : null;

    // find the user ID from the oauth access token table
    // based on the token we just got
    if($user_token) {
        $userAccessToken = OauthAccessTokens::find($user_token);
        $result  = [
            "user_token"=> $user_token,
            "user_id"   => $userAccessToken->user_id,
        ];
        return $result;
    } 
    return false;
}

function calculateDates($start_date,$end_date,$every_week,$week_days)
{  
    if(empty($week_days))
    {
        $week_days = [0,1,2,3,4,5,6];
    }                  
    $from = \Carbon\Carbon::parse($start_date);
    $to =   (!empty($end_date)) ? \Carbon\Carbon::parse($end_date) : \Carbon\Carbon::parse($start_date);
    $start_from = $from->format('Y-m-d');
    $end_to = $to->format('Y-m-d');

    $dates = [];
    

    for($w = $from; $w->lte($to); $w->addWeeks($every_week)) {
        $date = \Carbon\Carbon::parse($w);
        $startWeek = $w->startOfWeek()->format('Y-m-d');
        $weekNumber = $date->weekNumberInMonth;
        $start = \Carbon\Carbon::createFromFormat("Y-m-d", $startWeek);
        $end = $start->copy()->endOfWeek()->format('Y-m-d');
        for($p = $start; $p->lte($end); $p->addDays()) {
            if(strtotime($start_from) <= strtotime($p) && strtotime($end_to) >= strtotime($p) ) {
                if(in_array($p->dayOfWeek, $week_days)){
                    array_push($dates, $p->copy()->format('Y-m-d'));
                }
            }
        }
    }

    return $dates;
}

function getStartEndTime($from, $end, $date=null)
{
    if(empty($date))
    {
        $date = date('Y-m-d');
    }
    $startTime = $date.' '.$from;
    $endTime = $date.' '.$end;
    if(strtotime($startTime)>strtotime($endTime))
    {
        $endTime = date('Y-m-d H:i:s', strtotime('1 days', strtotime($endTime)));
    }
    $return = [
        'start_time'    => $startTime,
        'end_time'      => $endTime,
    ];
    return $return;
}



function getLeaveDatesByGroupId($group_id)
{
    $leave = Leave::where('group_id',$group_id)->get();
    $dates = [];
    foreach ($leave as $key => $value) {
        $dates[] = $value->date;
    }
    return $dates;
}

function getLeaveDatesByUserId($user_id)
{
    $leave = Leave::where('user_id',$user_id)->get();
    $dates = [];
    foreach ($leave as $key => $value) {
        $dates[] = $value->date;
    }
    return $dates;
}

function getDays($date1,$date2)
{
    $date1 = new DateTime($date1);
    $date2 = new DateTime($date2);
    $days  = $date2->diff($date1)->format('%a');

    return $days + 1;
}



function basicScheduleTimeCalculation($scheduleStartTime, $scheduleEndTime, $relaxationTime, $punchIn, $punchOut, $obHoursFrom, $obHoursTo, $isStamplingOn)
{
    //Define IN/OUT relaxation time
    $scheduleStartTimeWithRelaxationBefore = date('Y-m-d H:i', strtotime('-'.$relaxationTime. ' minutes', strtotime($scheduleStartTime)));
    $scheduleStartTimeWithRelaxationAfter = date('Y-m-d H:i', strtotime($relaxationTime. ' minutes', strtotime($scheduleStartTime)));

    $scheduleEndTimeWithRelaxationBefore = date('Y-m-d H:i', strtotime('-'.$relaxationTime. ' minutes', strtotime($scheduleEndTime)));
    $scheduleEndTimeWithRelaxationAfter = date('Y-m-d H:i', strtotime($relaxationTime. ' minutes', strtotime($scheduleEndTime)));

    $isInExtraHours = false;
    $isOutExtraHours = false;
    $punchInTimeLock = 0;
    $punchOutTimeLock = 0;
    $stamplingTimeDifference = 0;
    $inLateOrExtraHours = 0;
    $outBeforeOrExtraHours = 0;
    $startOBHoursTime = "00:00";
    $endOBHoursTime = "00:00";
    $scheduleTimeDifferece = timeDifference($scheduleStartTime, $scheduleEndTime);
    $obHoursTimeDifference = timeDifference($obHoursFrom, $obHoursTo);
    if($isStamplingOn)
    {
        // check punch in time for lock
        if(strtotime($punchIn) >= strtotime($scheduleStartTimeWithRelaxationBefore) && strtotime($punchIn) <= strtotime($scheduleStartTimeWithRelaxationAfter))
        {
            $punchInTimeLock = $scheduleStartTime;
        }
        else
        {
            $punchInTimeLock = $punchIn;
            $isInExtraHours = true;
        }

        // check punch out time for lock
        if(strtotime($punchOut) >= strtotime($scheduleEndTimeWithRelaxationBefore) && strtotime($punchOut) <= strtotime($scheduleEndTimeWithRelaxationAfter))
        {
            $punchOutTimeLock = $scheduleEndTime;
        }
        else
        {
            $punchOutTimeLock = $punchOut;
            $isOutExtraHours = true;
        }

        // time cal if extra hours IN true
        if($isInExtraHours)
        {
            $inLateOrExtraHours = strtotime($punchInTimeLock) - strtotime($scheduleStartTime);
        }

        // time cal if extra hours OUT true
        if($isOutExtraHours)
        {
            $outBeforeOrExtraHours = strtotime($punchOutTimeLock) - strtotime($scheduleEndTime);
        }

        ////////////////////OB hours with stampling
        if(strtotime($obHoursFrom) > strtotime($obHoursTo))
        {
            $obHoursTo = date('Y-m-d H:i',strtotime('+1 day',strtotime($obHoursTo)));
        }
        if(strtotime($punchIn) <= strtotime($obHoursFrom) && strtotime($punchOut) >= strtotime($obHoursFrom))
        {
            $startOBHoursTime = $obHoursFrom;
        }
        elseif(strtotime($punchIn) >= strtotime($obHoursFrom))
        {
            $startOBHoursTime = $punchIn;
            
        }

        if($startOBHoursTime!='00:00')
        {
            if(strtotime($punchOut) <= strtotime($obHoursTo))
            {
                $endOBHoursTime = $punchOut;
            }
            elseif(strtotime($punchOut) >= strtotime($obHoursTo))
            {
                $endOBHoursTime = $obHoursTo;
                
            }
        }

        if(($startOBHoursTime=='00:00' || $endOBHoursTime=='00:00') || strtotime($startOBHoursTime) >= strtotime($endOBHoursTime))
        {
            $startOBHoursTime = "00:00";
            $endOBHoursTime = "00:00";
        }

        $stamplingTimeDifference = timeDifference($punchInTimeLock, $punchOutTimeLock);
        $totalExtraTime = ($stamplingTimeDifference - $scheduleTimeDifferece);
        $totalScheduleTime = ($stamplingTimeDifference - $totalExtraTime);
        $workingPercentage = calculatePercentage($stamplingTimeDifference, $scheduleTimeDifferece);
    }
    else
    {
        //punch IN/OUT set null if module not active
        $punchIn = null;
        $punchOut = null;

        ////////////////////OB hours with schedule
        if(strtotime($scheduleStartTime) <= strtotime($obHoursFrom) && strtotime($scheduleEndTime) >= strtotime($obHoursFrom))
        {
            $startOBHoursTime = $obHoursFrom;
        }
        elseif(strtotime($scheduleStartTime) >= strtotime($obHoursFrom))
        {
            $startOBHoursTime = $scheduleStartTime;
            
        }

        if($startOBHoursTime!='00:00')
        {
            if(strtotime($scheduleEndTime) <= strtotime($obHoursTo))
            {
                $endOBHoursTime = $scheduleEndTime;
            }
            elseif(strtotime($scheduleEndTime) >= strtotime($obHoursTo))
            {
                $endOBHoursTime = $obHoursTo;
                
            }
        }

        if(($startOBHoursTime=='00:00' || $endOBHoursTime=='00:00') || strtotime($startOBHoursTime) >= strtotime($endOBHoursTime))
        {
            $startOBHoursTime = "00:00";
            $endOBHoursTime = "00:00";
        }

        $totalExtraTime = 0;
        $totalScheduleTime =  ($scheduleTimeDifferece - $totalExtraTime);
        $workingPercentage = calculatePercentage($totalScheduleTime, $totalScheduleTime);
    }
    
    $totalOBHoursTime = ((strtotime($endOBHoursTime) - strtotime($startOBHoursTime))/60);
    $totalScheduleTime = $totalScheduleTime - $totalOBHoursTime;
    if($totalExtraTime < 0)
    {
        $totalScheduleTime = $stamplingTimeDifference - $totalOBHoursTime;
        $totalExtraTime = 0;
    }


    return [
        'scheduleStartTime' => $scheduleStartTime,
        'scheduleEndTime' => $scheduleEndTime,
        'relaxationTime' => $relaxationTime,
        'punchInTime' => $punchIn,
        'punchOutTime' => $punchOut,
        'obHoursFrom' => $obHoursFrom,
        'obHoursTo' => $obHoursTo,
        'isStamplingOn' => $isStamplingOn,
        'punchInTimeLock' => $punchInTimeLock,
        'punchOutTimeLock' => $punchOutTimeLock,
        'inLateOrExtraHours' => ($inLateOrExtraHours/60),
        'outBeforeOrExtraHours' => ($outBeforeOrExtraHours/60),
        'scheduleTimeDifferece' => $scheduleTimeDifferece,
        'stamplingTimeDifference' => $stamplingTimeDifference,
        'obHoursTimeDifference' => $obHoursTimeDifference,
        'totalScheduleTime' => $totalScheduleTime,
        'totalExtraTime' => $totalExtraTime,
        'totalOBHoursTime' => $totalOBHoursTime,
        'workingPercentage' => $workingPercentage,
    ];
}

function hoursAndMins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function calculatePercentage($v1, $v2)
{
    return number_format((($v1 * 100) / $v2),2) ;
}

function timeDifference($time1, $time2)
{
    $time2 = strtotime($time2);
    $time1 = strtotime($time1);
    return ($time2 - $time1)/60;
}


function timeWithRelaxation($scheduled_time,$relaxationTime)
{
    $time['before'] = strtotime(date('Y-m-d H:i', strtotime('-'.$relaxationTime. ' minutes', strtotime($scheduled_time))));
    $time['after'] = strtotime(date('Y-m-d H:i', strtotime($relaxationTime. ' minutes', strtotime($scheduled_time))));
    return $time;
}

function getObDuration($date,$time1, $time2,$rest_start_time=null,$rest_end_time=null)
{
    //-------------------------red day---------------------------------//
    $red_ob = [];
    $red_data = OVHour::where(function ($q) use($date){
                $q->where('date',$date)
                    ->orWhereNull('date');
            })->where('ob_type','red')->orderBy('id','desc')->first();
    if(!empty($red_data))
    {
        $red_ob['type'] = $red_data->ob_type;
        $red_ob['start_time'] = $red_data->start_time;
        $red_ob['end_time'] = $red_data->end_time;
        $time1 = strtotime($time1);
        $time2 = strtotime($time2);
        $rest_ob_duration = 0;

        $red_obtime1 = strtotime($date.' '.$red_data->start_time);
        $red_obtime2 = strtotime($date.' '.$red_data->end_time);

        if($red_obtime1 > $red_obtime2)
        {
            $red_obtime2 = strtotime(date('Y-m-d H:i',strtotime('+1 day',strtotime($red_obtime2))));
        }

        if(($red_obtime1 <= $time1) && ($red_obtime2 <= $time1))
        {
            $red_ob['duration'] = 0;
        }
        elseif(($red_obtime1 >= $time2) && ($red_obtime2 >= $time2))
        {
            $red_ob['duration'] = 0;
        }
        elseif(($red_obtime1 >= $time2) && ($red_obtime2 <= $time2))
        {
            $red_ob['duration'] = 0;
        }
        elseif(($red_obtime1 >= $time1) && ($red_obtime2 <= $time2))
        {
            $red_ob['duration'] = ($red_obtime2 - $red_obtime1)/60;
        }
        elseif (($red_obtime1 <= $time1) && ($red_obtime2 >= $time2)) 
        {
            $red_ob['duration'] = ($time2 - $time1)/60;
        }
        elseif (($red_obtime1 <= $time1) && ($red_obtime2 <= $time2)) 
        {
            $red_ob['duration'] = ($red_obtime2 - $time1)/60;
        }
        elseif (($red_obtime1 >= $time1) && ($red_obtime2 >= $time2)) 
        {
            $red_ob['duration'] = ($time2 - $red_obtime1)/60;
        }

        if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $rest_start_time))
        {
            $resttime1 = strtotime($rest_start_time);
            $resttime2 = strtotime($rest_end_time);

            if(($red_obtime1 <= $resttime1) && ($red_obtime2 <= $resttime1))
            {
                $rest_red_ob_duration = 0;
            }
            elseif(($red_obtime1 >= $resttime2) && ($red_obtime2 >= $resttime2))
            {
                $rest_red_ob_duration = 0;
            }
            elseif(($red_obtime1 >= $resttime2) && ($red_obtime2 <= $resttime2))
            {
                $rest_red_ob_duration = 0;
            }
            elseif(($red_obtime1 >= $resttime1) && ($red_obtime2 <= $resttime2))
            {
                $rest_red_ob_duration = ($red_obtime2 - $red_obtime1)/60;
            }
            elseif (($red_obtime1 <= $resttime1) && ($red_obtime2 >= $resttime2)) 
            {
                $rest_red_ob_duration = ($resttime2 - $resttime1)/60;
            }
            elseif (($red_obtime1 <= $resttime1) && ($red_obtime2 <= $resttime2)) 
            {
                $rest_red_ob_duration = ($red_obtime2 - $resttime1)/60;
            }
            elseif (($red_obtime1 >= $resttime1) && ($red_obtime2 >= $resttime2)) 
            {
                $rest_red_ob_duration = ($resttime2 - $red_obtime1)/60;
            }

            $red_ob['duration'] = $red_ob['duration'] - $rest_red_ob_duration;
        }
    }
    else
    {
        $red_ob['type']= null;
        $red_ob['duration'] = 0;
        $red_ob['start_time'] = null;
        $red_ob['end_time'] = null;
    }

    //----------------------------weekend-------------------------//

    $weekend_ob = [];
    $weekend_data = OVHour::where(function ($q) use($date){
                $q->where('date',$date)
                    ->orWhereNull('date');
            })->where('ob_type','weekend')->orderBy('id','desc')->first();
    if(!empty($weekend_data))
    {
        $weekend_ob['type'] = $weekend_data->ob_type;
        $weekend_ob['start_time'] = $weekend_data->start_time;
        $weekend_ob['end_time'] = $weekend_data->end_time;
        $rest_ob_duration = 0;

        $weekend_obtime1 = strtotime($date.' '.$weekend_data->start_time);
        $weekend_obtime2 = strtotime($date.' '.$weekend_data->end_time);

        if($weekend_obtime1 > $weekend_obtime2)
        {
            $weekend_obtime2 = strtotime(date('Y-m-d H:i',strtotime('+1 day',strtotime($weekend_obtime2))));
        }

        if(($weekend_obtime1 <= $time1) && ($weekend_obtime2 <= $time1))
        {
            $weekend_ob['duration'] = 0;
        }
        elseif(($weekend_obtime1 >= $time2) && ($weekend_obtime2 >= $time2))
        {
            $weekend_ob['duration'] = 0;
        }
        elseif(($weekend_obtime1 >= $time2) && ($weekend_obtime2 <= $time2))
        {
            $weekend_ob['duration'] = 0;
        }
        elseif(($weekend_obtime1 >= $time1) && ($weekend_obtime2 <= $time2))
        {
            $weekend_ob['duration'] = ($weekend_obtime2 - $weekend_obtime1)/60;
        }
        elseif (($weekend_obtime1 <= $time1) && ($weekend_obtime2 >= $time2)) 
        {
            $weekend_ob['duration'] = ($time2 - $time1)/60;
        }
        elseif (($weekend_obtime1 <= $time1) && ($weekend_obtime2 <= $time2)) 
        {
            $weekend_ob['duration'] = ($weekend_obtime2 - $time1)/60;
        }
        elseif (($weekend_obtime1 >= $time1) && ($weekend_obtime2 >= $time2)) 
        {
            $weekend_ob['duration'] = ($time2 - $weekend_obtime1)/60;
        }

        if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $rest_start_time))
        {
            $resttime1 = strtotime($rest_start_time);
            $resttime2 = strtotime($rest_end_time);

            if(($weekend_obtime1 <= $resttime1) && ($weekend_obtime2 <= $resttime1))
            {
                $rest_weekend_ob_duration = 0;
            }
            elseif(($weekend_obtime1 >= $resttime2) && ($weekend_obtime2 >= $resttime2))
            {
                $rest_weekend_ob_duration = 0;
            }
            elseif(($weekend_obtime1 >= $resttime2) && ($weekend_obtime2 <= $resttime2))
            {
                $rest_weekend_ob_duration = 0;
            }
            elseif(($weekend_obtime1 >= $resttime1) && ($weekend_obtime2 <= $resttime2))
            {
                $rest_weekend_ob_duration = ($weekend_obtime2 - $weekend_obtime1)/60;
            }
            elseif (($weekend_obtime1 <= $resttime1) && ($weekend_obtime2 >= $resttime2)) 
            {
                $rest_weekend_ob_duration = ($resttime2 - $resttime1)/60;
            }
            elseif (($weekend_obtime1 <= $resttime1) && ($weekend_obtime2 <= $resttime2)) 
            {
                $rest_weekend_ob_duration = ($weekend_obtime2 - $resttime1)/60;
            }
            elseif (($weekend_obtime1 >= $resttime1) && ($weekend_obtime2 >= $resttime2)) 
            {
                $rest_weekend_ob_duration = ($resttime2 - $weekend_obtime1)/60;
            }

            $weekend_ob['duration'] = $weekend_ob['duration'] - $rest_weekend_ob_duration;
        }
    }
    else
    {
        $weekend_ob['type']= null;
        $weekend_ob['duration'] = 0;
        $weekend_ob['start_time'] = null;
        $weekend_ob['end_time'] = null;
    }


    //-------------------------------------week-day--------------------//
    $weekday_ob = [];
    $weekday_data = OVHour::where(function ($q) use($date){
                $q->where('date',$date)
                    ->orWhereNull('date');
            })->where('ob_type','weekday')->orderBy('id','desc')->first();
    if(!empty($weekday_data))
    {
        $weekday_ob['type'] = $weekday_data->ob_type;
        $weekday_ob['start_time'] = $weekday_data->start_time;
        $weekday_ob['end_time'] = $weekday_data->end_time;
        $rest_ob_duration = 0;

        $weekday_obtime1 = strtotime($date.' '.$weekday_data->start_time);
        $weekday_obtime2 = strtotime($date.' '.$weekday_data->end_time);

        if($weekday_obtime1 > $weekday_obtime2)
        {
            $weekday_obtime2 = strtotime(date('Y-m-d H:i',strtotime('+1 day',strtotime($weekday_obtime2))));
        }

        if(($weekday_obtime1 <= $time1) && ($weekday_obtime2 <= $time1))
        {
            $weekday_ob['duration'] = 0;
        }
        elseif(($weekday_obtime1 >= $time2) && ($weekday_obtime2 >= $time2))
        {
            $weekday_ob['duration'] = 0;
        }
        elseif(($weekday_obtime1 >= $time2) && ($weekday_obtime2 <= $time2))
        {
            $weekday_ob['duration'] = 0;
        }
        elseif(($weekday_obtime1 >= $time1) && ($weekday_obtime2 <= $time2))
        {
            $weekday_ob['duration'] = ($weekday_obtime2 - $weekday_obtime1)/60;
        }
        elseif (($weekday_obtime1 <= $time1) && ($weekday_obtime2 >= $time2)) 
        {
            $weekday_ob['duration'] = ($time2 - $time1)/60;
        }
        elseif (($weekday_obtime1 <= $time1) && ($weekday_obtime2 <= $time2)) 
        {
            $weekday_ob['duration'] = ($weekday_obtime2 - $time1)/60;
        }
        elseif (($weekday_obtime1 >= $time1) && ($weekday_obtime2 >= $time2)) 
        {
            $weekday_ob['duration'] = ($time2 - $weekday_obtime1)/60;
        }

        if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $rest_start_time))
        {
            $resttime1 = strtotime($rest_start_time);
            $resttime2 = strtotime($rest_end_time);

            if(($weekday_obtime1 <= $resttime1) && ($weekday_obtime2 <= $resttime1))
            {
                $rest_weekday_ob_duration = 0;
            }
            elseif(($weekday_obtime1 >= $resttime2) && ($weekday_obtime2 >= $resttime2))
            {
                $rest_weekday_ob_duration = 0;
            }
            elseif(($weekday_obtime1 >= $resttime2) && ($weekday_obtime2 <= $resttime2))
            {
                $rest_weekday_ob_duration = 0;
            }
            elseif(($weekday_obtime1 >= $resttime1) && ($weekday_obtime2 <= $resttime2))
            {
                $rest_weekday_ob_duration = ($weekday_obtime2 - $weekday_obtime1)/60;
            }
            elseif (($weekday_obtime1 <= $resttime1) && ($weekday_obtime2 >= $resttime2)) 
            {
                $rest_weekday_ob_duration = ($resttime2 - $resttime1)/60;
            }
            elseif (($weekday_obtime1 <= $resttime1) && ($weekday_obtime2 <= $resttime2)) 
            {
                $rest_weekday_ob_duration = ($weekday_obtime2 - $resttime1)/60;
            }
            elseif (($weekday_obtime1 >= $resttime1) && ($weekday_obtime2 >= $resttime2)) 
            {
                $rest_weekday_ob_duration = ($resttime2 - $weekday_obtime1)/60;
            }

            $weekday_ob['duration'] = $weekday_ob['duration'] - $rest_weekday_ob_duration;
        }
    }
    else
    {
        $weekday_ob['type']= null;
        $weekday_ob['duration'] = 0;
        $weekday_ob['start_time'] = null;
        $weekday_ob['end_time'] = null;
    }
    return $ob = ['red_ob'=>$red_ob,'weekend_ob'=>$weekend_ob,'weekday_ob'=>$weekday_ob];
}


function scheduleWorkCalculation($date,$start_time,$end_time,$schedule_type,$shift_type = null, $rest_start_time = null, $rest_end_time = null,$user_id = null,$assignedWork_id = null)
{
    $result = [];
    $ob = getObDuration($date,$start_time,$end_time,$rest_start_time,$rest_end_time);
    $ob_duration = $ob['red_ob']['duration'] + $ob['weekend_ob']['duration'] + $ob['weekday_ob']['duration'];
    $rest_duration = 0;

    if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $end_time))
    {
        $rest_duration = timeDifference($rest_start_time,$rest_end_time);
    }
    $monday = date("Y-m-d", strtotime('monday this week', strtotime($date)));
    $sunday = date("Y-m-d", strtotime('sunday this week', strtotime($date)));


    $assigned_minutes = null;
    $worked_minutes = null;
    if(!empty($user_id))
    {
        $user = User::find($user_id);
        if(!empty($assignedWork_id))
        {
            $assignedWork = EmployeeAssignedWorkingHour::find($assignedWork_id);
        }
        else
        {
            $assignedWork = $user->assignedWork;
        }
        
        if(!empty($assignedWork))
        {
            $assigned_minutes = ($assignedWork->assigned_working_hour_per_week)*60;
            $assignedWork_id = $assignedWork->id;
        }
        // $worked_minutes = Schedule::where('user_id',$shift['user_id'])->where('shift_date', '>=',$monday)->where('shift_date','<=',$sunday)->sum(\DB::raw('scheduled_work_duration + extra_work_duration'));
        $worked_minutes = App\Models\Schedule::where('user_id',$user_id)->where('shift_date', '>=',$monday)->where('shift_date','<=',$sunday)->sum('scheduled_work_duration');
    } 

    // if($schedule_type == 'basic')
    // {
        $scheduled_duration = timeDifference($start_time,$end_time) - $rest_duration;
        $scheduled_duration = $scheduled_duration - $ob_duration;
        $extra_duration =  0;
        if(($worked_minutes + $scheduled_duration) > $assigned_minutes)
        {
            $extra_duration = ($worked_minutes + $scheduled_duration) - $assigned_minutes;
        }

        if($extra_duration > 0)
        {
            $scheduled_duration = $scheduled_duration - $extra_duration;
        }
        
        $emergency_duration = 0;
        if(($shift_type == 'emergency') || ($shift_type == 'emergency') || ($shift_type == 'sleeping_emergency_red') || ($shift_type == 'sleeping_emergency_weekday') || ($shift_type == 'sleeping_emergency_weekend'))
        {
            $emergency_duration = $scheduled_duration + $extra_duration;
            $scheduled_duration = 0;
            $extra_duration = 0;
        }
    // }
    // else
    // {
    //     $scheduled_duration = 0;
    //     $emergency_duration = 0;
    //     $extra_duration =  timeDifference($start_time,$end_time) - $rest_duration;
    //     $scheduled_duration = 0;
    //     $extra_duration = $extra_duration - $ob_duration;
    //     if($shift_type == 'emergency')
    //     {
    //         $emergency_duration = $extra_duration;
    //         $extra_duration = 0;
    //     }
    // }
    $result['ob_type'] = NULL;
    $result['ob_start_time'] = NULL;
    $result['ob_end_time'] =NULL;
    $result['ob_work_duration'] = NULL;
    $result['ob_red_start_time'] = $ob['red_ob']['start_time'];
    $result['ob_red_end_time'] = $ob['red_ob']['end_time'];
    $result['ob_red_work_duration'] = $ob['red_ob']['duration'];
    $result['ob_weekend_start_time'] = $ob['weekend_ob']['start_time'];
    $result['ob_weekend_end_time'] = $ob['weekend_ob']['end_time'];
    $result['ob_weekend_work_duration'] = $ob['weekend_ob']['duration'];
    $result['ob_weekday_start_time'] = $ob['weekday_ob']['start_time'];
    $result['ob_weekday_end_time'] = $ob['weekday_ob']['end_time'];
    $result['ob_weekday_work_duration'] = $ob['weekday_ob']['duration'];
    $result['scheduled_work_duration'] = $scheduled_duration;
    $result['emergency_work_duration'] = $emergency_duration;
    $result['ob_work_duration'] = $ob_duration;
    $result['extra_work_duration'] = $extra_duration;
    $result['assignedWork_id'] = $assignedWork_id;
    return $result;
}

function schedule($user_id,$top_most_parent_id,$date,$shift_start_time,$shift_end_time,$schedule_type,$patient_id,$assignedWork_id,$schedule_template_id,$status,$entry_mode,$group_id,$schedule_id,$shift_id,$shift_name,$shift_color)
{
    $date = date('Y-m-d',strtotime($date));
    $startEndTime = getStartEndTime($shift_start_time, $shift_end_time, $date);
    $shift_start_time = $startEndTime['start_time'];
    $shift_end_time = $startEndTime['end_time'];

    $result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,$request->schedule_type);

    $schedule = new Schedule;
    $schedule->top_most_parent_id = $top_most_parent_id;
    $schedule->user_id = $user_id;
    $schedule->patient_id = $patient_id;
    $schedule->shift_id = $shift_id;
    $schedule->parent_id = $parent_id;
    $schedule->created_by = Auth::id();
    $schedule->slot_assigned_to = null;
    $schedule->employee_assigned_working_hour_id = $assignedWork_id;
    $schedule->schedule_template_id = $schedule_template_id;
    $schedule->schedule_type = $schedule_type;
    $schedule->shift_date = $date;
    $schedule->group_id = $group_id;
    $schedule->shift_name = $shift_name;
    $schedule->shift_color = $shift_color;
    $schedule->shift_start_time = $shift_start_time;
    $schedule->shift_end_time = $shift_end_time;
    $schedule->leave_applied = 0;
    $schedule->leave_group_id = null;
    $schedule->leave_type = null;
    $schedule->leave_reason = null;
    $schedule->leave_approved = 0;
    $schedule->leave_approved_by = null;
    $schedule->leave_approved_date_time = null;
    $schedule->leave_notified_to = null;
    $schedule->notified_group = null;
    $schedule->is_active = 1;
    $schedule->scheduled_work_duration = $result['scheduled_work_duration'];
    $schedule->extra_work_duration = $result['extra_work_duration'];
    $schedule->ob_work_duration = $result['ob_work_duration'];
    $schedule->ob_type = $result['ob_type'];
    $schedule->status = $status ? $status :0;
    $schedule->entry_mode = $entry_mode?$entry_mode:'Web';
    $schedule->save();

    return $schedule;
}

function getAllowUserList($permission)
{
    $user = auth()->user();
    $getList = false;
    if(!$user->hasPermissionTo($permission) && $user->user_type_id==3)
    {
        $getList = PatientEmployee::where('employee_id', $user->id)
            ->pluck('patient_id')->toArray();

        $gl_patients = User::where('company_type_id', 'LIKE','%1%')
            ->pluck('id')->toArray();

        $branchs = User::where('branch_id', $user->branch_id)
            ->pluck('id')->toArray();

        $getList = array_merge($getList, $gl_patients, $branchs);
    }
    else
    {
        $getList = User::where('branch_id', $user->branch_id)
            ->pluck('id')->toArray();
    }
    $getList[] = $user->id;
    return array_unique($getList);
}


function checkEmpPartientCount($top_most_parent_id, $user_type_id)
{
    //3 for employees, 6 for patients

    $allowed = false;
    $top_most_parent_lic_key = User::select('licence_key')
    ->withoutGlobalScope('top_most_parent_id')
    ->find($top_most_parent_id);
    if($top_most_parent_lic_key)
    {
        $getLicences = LicenceKeyManagement::where('licence_key', $top_most_parent_lic_key->licence_key)
            ->whereDate('expire_at', '>=', date('Y-m-d'))
            ->first();
        if($getLicences)
        {
            $packageInfo = json_decode($getLicences->package_details, true);
            $allowedCounts = ($user_type_id==3) ? $packageInfo['number_of_employees'] : $packageInfo['number_of_patients'];
            
            $userCounts = User::where('user_type_id', $user_type_id)
            ->where('top_most_parent_id', $top_most_parent_id)
            ->withoutGlobalScope('top_most_parent_id')
            ->count();

            if($userCounts<$allowedCounts)
            {
                $allowed = true;
            }
        }
    }
    return $allowed;
}

function getLicInfo($top_most_parent_id)
{
    $bankId = false;
    $textMsg = false;

    $top_most_parent_lic_key = User::select('licence_key')
    ->withoutGlobalScope('top_most_parent_id')
    ->find($top_most_parent_id);

    $getLicences = LicenceKeyManagement::where('licence_key', $top_most_parent_lic_key->licence_key)
        ->whereDate('expire_at', '>=', date('Y-m-d'))
        ->first();
        if($getLicences)
        {
            $packageInfo = json_decode($getLicences->package_details, true);
            $bankId = $packageInfo['is_sms_enable'];
            $textMsg = !empty($packageInfo['sms_charges']) ? 1 : 0;
        }
    return [
        'bankId' => $bankId,
        'textMsg' => $textMsg
    ];
}