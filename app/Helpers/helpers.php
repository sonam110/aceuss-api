<?php
use App\Models\User;
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

function getLatestParent()
{
    $user = User::find(Auth::id());
    if($user->parentUnit) {
        return $user->parentUnit->getLatestParent();
    }
    return $user;
}

?>