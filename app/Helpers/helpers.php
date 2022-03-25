<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Department;
use App\Models\Task;
use App\Models\AssignTask;
use App\Models\DeviceLoginHistory;
use App\Models\Notification;
use Edujugon\PushNotification\PushNotification;
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

function mailTemplateContent($content,$variables){
    //$variables = array("first_name"=>"John","last_name"=>"Smith","status"=>"won");
    $string = $content;
    foreach($variables as $key => $value){
        $string = str_replace('{{'.($key).'}}', $value, $string);
    }
    return $string;

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



function pushNotification($title,$body,$user,$type,$save_to_database,$user_type,$module,$id,$screen)
{
    if(!empty($user))
    {
        $userDeviceInfo = DeviceLoginHistory::where('user_id',$user->id)->whereIn('login_via',['1','2'])->orderBy('created_at', 'DESC')->first();
        if(!empty($userDeviceInfo))
        { 
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
                        'user_type'  => $user_type,
                        'module'  => $module,
                        'screen'  => $screen
                    ]                        
                ])
                ->setApiKey(env('FIREBASE_KEY'))
                ->setDevicesToken($userDeviceInfo->device_token)
                ->send();
                /*if($userDeviceInfo->platform=='Android')
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
                            'user_type'  => $user_type,
                            'module'  => $module,
                            'screen'  => $screen
                        ]                        
                    ])
                    ->setApiKey(env('FIREBASE_KEY'))
                    ->setDevicesToken($userDeviceInfo->device_token)
                    ->send();
                }
                elseif($userDeviceInfo->platform=='iOS')
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
            $notification->user_id          = $user->id;
            $notification->sender_id        = Auth::id();
            $notification->device_uuid      = $userDeviceInfo ? $userDeviceInfo->id : null;
            $notification->device_platform  = $userDeviceInfo ? $userDeviceInfo->login_via : null;
            $notification->type             = $type;
            $notification->user_type        = $user_type;
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

?>