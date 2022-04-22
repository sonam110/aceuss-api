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
use Edujugon\PushNotification\PushNotification;
use Carbon\Carbon;
function getUser() {
    return auth('api')->user();
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
function getTopParent($id) {
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

function findTopParentId($parent_id) {

    if(is_null($parent_id) == false ){
        $parent = Department::find($parent_id)->parent_id;
        return $parent ? findTopParentId($parent) : $parent_id;
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
    $user = User::find(Auth::id());
    if($user->parentUnit) {
        return $user->parentUnit->getLatestParent();
    }
    return $user;
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



function getLangByLabelGroups($groupName,$label_name)
{
    $lang = env('APP_DEFAULT_LANGUAGE',1);
    $getGroup = Group::select('id')
    ->with(['LabelGroups' => function($q) use ($lang, $label_name) {
        $q->select('id','group_id','label_value')
        ->where('language_id', $lang)
        ->where('label_name', $label_name);
    }])
    ->where('name', $groupName)
    ->first();
    $data = @$getGroup->LabelGroups;
    return @$data['label_value'];
}


function getTemplate($mail_for, $obj, $otp=null,$user)
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



function pushNotification($sms_for,$companyObj,$obj,$save_to_database,$module,$id,$screen)
{
    if(!empty($obj['user_id']))
    {
        $userDeviceInfo = DeviceLoginHistory::where('user_id',$obj['user_id'])->whereIn('login_via',['1','2'])->orderBy('created_at', 'DESC')->first();
        if(!empty($userDeviceInfo))
        { 
            $title = false;
            $body = false;
            $getMsg = EmailTemplate::where('mail_sms_for', $sms_for)->first();
           
            if($getMsg)
            {
                $body = $getMsg->notify_body;
                $title = $getMsg->mail_subject;
                $arrayVal = [
                    '{{name}}'  => $obj['name'],
                    '{{email}}' => $obj['email'],
                    '{{title}}' => $obj['title'],
                    '{{patient_id}}' => $obj['patient_id'],
                    '{{start_date}}' => $obj['start_date '],
                    '{{start_time}}' => $obj['start_time'],
                    '{{company_name}}' => $companyObj['company_name'],
                    '{{company_address}}' =>  $companyObj['company_address'],
                ];
                $body = strReplaceAssoc($arrayVal, $message);
                $title = strReplaceAssoc($arrayVal, $title);
                if(!empty($userDeviceInfo->device_token))
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
                    /*if($userDeviceInfo->login_via=='1')
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
                    elseif($userDeviceInfo->login_via=='2')
                    {
                        $push = new PushNotification('apn');

                        $push->setMessage([
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body
                                ],
                                'sound' => 'default',
                                'badge' => 1

                            ],
                            'extraPayLoad' => [
                                'custom' => 'My custom data',
                            ]                       
                        ])
                        ->setDevicesToken($userDeviceInfo->device_token);
                        $push = $push->send();
                        //return $push->getFeedback();
                    }*/
                }
            }

            if($save_to_database == true)
            {
                $notification = new Notification;
                $notification->user_id          = $obj['user_id'];
                $notification->sender_id        = Auth::id();
                $notification->device_id        = $userDeviceInfo ? $userDeviceInfo->id : null;
                $notification->device_platform  = $userDeviceInfo ? $userDeviceInfo->login_via : null;
                $notification->type             = $obj['type'];;
                $notification->user_type        = $obj['user_type'];
                $notification->module           = $module;
                $notification->title            = $title;
                $notification->sub_title        = $title;
                $notification->message          = $body;
                $notification->image_url        = '';
                $notification->screen           = $screen;
                $notification->data_id          = $id;
                $notification->read_status      = false;
                $notification->save();
            }
        }
        
    }
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
    if($getMsg)
    {
        $message = $getMsg->sms_body;
         $arrayVal = [
            '{{name}}'  => $obj['name'],
            '{{email}}' => $obj['email'],
            '{{title}}' => $obj['title'],
            '{{patient_id}}' => $obj['patient_id'],
            '{{start_date}}' => $obj['start_date '],
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
    $addComment = new Comment;
    $addComment->source_id = $source_id ;
    $addComment->source_name = $source_namen;
    $addComment->comment = $comment;
    $addComment->created_by = Auth::id();
    $addComment->save();
    return $addComment;
}

function generateRandomNumber($len = 12) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),1,$len);
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


function weekDaysBetween($requiredDays, $start, $end,$every){
    $result = [];
    while ($start->lt($end)) {
        if(in_array($start->dayOfWeek, $requiredDays)){
            array_push($result, $start->copy()->format('Y-m-d'));
        }

        $start->addDays();
        

    }
    
    return $result;
}

function getBranchId(){
    if(auth()->user()->user_type_id=='11') {
        $branch_id = auth()->user()->id;
    }
    else {
        $branch_id = auth()->user()->branch_id;
    } 
    return $branch_id;
}


function checkAssignModule($module_id){

    $checkModule = AssigneModule::where('user_id',auth()->user()->top_most_parent_id)->where('module_id',$module_id)->first();
    $is_assign = ($checkModule) ? true :false;
    return $is_assign;


}

function journal($parent_id,$deviation_id,$activity_id,$patient_id,$category_id,$subcategory_id,$title,$description,$is_deviation,$is_social){
    $Journal = new Journal;
    $Journal->parent_id = $parent_id;
    $Journal->deviation_id = $deviation_id;
    $Journal->activity_id = $activity_id;
    $Journal->branch_id = getBranchId();
    $Journal->patient_id = $patient_id;
    $Journal->category_id = $category_id;
    $Journal->subcategory_id = $subcategory_id;
    $Journal->title = $title;
    $Journal->description = $description;
    $Journal->is_deviation = $is_deviation;
    $Journal->is_social = $is_social;
    $Journal->save();
    if($Journal){
        return $Journal->id;
    }else {
        return null;
    }


}
function deviation($parent_id,$journal_id,$activity_id,$patient_id,$category_id,$subcategory_id,$title,$description){
    $Deviation = new Deviation;
    $Deviation->parent_id = $parent_id;
    $Deviation->journal_id = $journal_id;
    $Deviation->activity_id = $activity_id;
    $Deviation->patient_id = $patient_id;
    $Deviation->branch_id = $patient_id;
    $Deviation->category_id = $category_id;
    $Deviation->subcategory_id = $subcategory_id;
    $Deviation->title = $title;
    $Deviation->description = $description;
    $Deviation->save();
    if($Deviation){
        return $Deviation->id;
    }else {
        return null;
    }

}

function dates($value) {
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

?>