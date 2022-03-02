<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Department;
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

?>