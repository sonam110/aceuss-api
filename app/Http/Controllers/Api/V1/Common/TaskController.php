<?php

namespace App\Http\Controllers\Api\v1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\AssignTask;
use Validator;
use Auth;
use Exception;
use DB;
class TaskController extends Controller
{
    public function tasks(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Task::select('id','type_id','parent_id','title','status','branch_id','created_by','start_date','end_date')->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Task::select('id','type_id','parent_id','title','status','branch_id','created_by','start_date','end_date')->orderBy('id', 'DESC');
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
                return prepareResult(true,"Task list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Task list",$query,$this->success);
	       
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
		    'title' => 'required',   
		    'description' => 'required',   
		    'start_time' => 'required',    
		    "employees"    => "required|array",
		    "employees.*"  => "required|string|distinct", 
		    ],
		    [
		    'title.required' =>  getLangByLabelGroups('Task','title'),
		    'description.required' =>  getLangByLabelGroups('Task','description'),
		    'start_time.required' =>  getLangByLabelGroups('Task','start_time'),
		    ]);
		    if ($validator->fails()) {
		        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		    }
		    if($request->is_repeat){
		        $validator = Validator::make($request->all(),[     
		            'every' => 'required|numeric',          
		        ]);
		        if ($validator->fails()) {
		            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		        }

		        if($request->repetition_type=='2'){
		            $validator = Validator::make($request->all(),[     
		                "week_days"    => "required|array",
		                "week_days.*"  => "required|string|distinct",        
		            ]);
		            if ($validator->fails()) {
		                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		            }

		        }
		        if($request->repetition_type=='3'){
		            $validator = Validator::make($request->all(),[     
		                "month_day"    => "required",      
		            ]);
		            if ($validator->fails()) {
		                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		            }

		        }

		    } else{
		        $validator = Validator::make($request->all(),[   
		            'start_date' => 'required|date',      
		        ],
		        [ 
		            'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),      
		        ]);
		        if ($validator->fails()) {
		            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		        }

		    }
		    $task = new Task;
		    $task->type_id = $request->type_id; 
		    $task->resource_id = $request->resource_id; 
		    $task->parent_id = $request->parent_id; 
		    $task->title = $request->title; 
		    $task->description = $request->description; 
		    $task->start_date = $request->start_date; 
		    $task->start_time = $request->start_time; 
		    $task->is_repeat = ($request->is_repeat) ? 1:0; 
		    $task->every = $request->every; 
		    $task->repetition_type = $request->repetition_type; 
		    $task->week_days = ($request->week_days) ? json_encode($request->week_days) :null;
		    $task->month_day = $request->month_day;
		    $task->end_date = $request->end_date;
		    $task->end_time =$request->end_time;
		    $task->created_by =$user->id;
		    $task->save();
		    if(is_array($request->employees) ){
		        foreach ($request->employees as $key => $employee) {
		            $taskAssign = new AssignTask;
		            $taskAssign->task_id = $task->id;
		            $taskAssign->user_id = $employee;
		            $taskAssign->assignment_date = date('Y-m-d');
		            $taskAssign->assigned_by = $user->id;
		            $taskAssign->save();
		        }
		    }
	        return prepareResult(true,'Task Added successfully' ,$task, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
		    'title' => 'required',   
		    'description' => 'required',   
		    'start_time' => 'required',    
		    "employees"    => "required|array",
		    "employees.*"  => "required|string|distinct", 
		    ],
		    [
		    'title.required' =>  getLangByLabelGroups('Task','title'),
		    'description.required' =>  getLangByLabelGroups('Task','description'),
		    'start_time.required' =>  getLangByLabelGroups('Task','start_time'),
		    ]);
		    if ($validator->fails()) {
		        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		    }
		    if($request->is_repeat){
		        $validator = Validator::make($request->all(),[     
		            'every' => 'required|numeric',          
		        ]);
		        if ($validator->fails()) {
		            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		        }

		        if($request->repetition_type=='2'){
		            $validator = Validator::make($request->all(),[     
		                "week_days"    => "required|array",
		                "week_days.*"  => "required|string|distinct",        
		            ]);
		            if ($validator->fails()) {
		                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		            }

		        }
		        if($request->repetition_type=='3'){
		            $validator = Validator::make($request->all(),[     
		                "month_day"    => "required",      
		            ]);
		            if ($validator->fails()) {
		                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		            }

		        }

		    } else{
		        $validator = Validator::make($request->all(),[   
		            'start_date' => 'required|date',      
		        ],
		        [ 
		            'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),      
		        ]);
		        if ($validator->fails()) {
		            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
		        }

		    }
		    $task = Task::find($id);
		    $task->type_id = $request->type_id; 
		    $task->resource_id = $request->resource_id; 
		    $task->parent_id = $request->parent_id; 
		    $task->title = $request->title; 
		    $task->description = $request->description; 
		    $task->start_date = $request->start_date; 
		    $task->start_time = $request->start_time; 
		    $task->is_repeat = ($request->is_repeat) ? 1:0; 
		    $task->every = $request->every; 
		    $task->repetition_type = $request->repetition_type; 
		    $task->week_days = ($request->week_days) ? json_encode($request->week_days) :null;
		    $task->month_day = $request->month_day;
		    $task->end_date = $request->end_date;
		    $task->end_time =$request->end_time;
		    $task->edited_by =$user->id;
		    $task->save();
		    if(is_array($request->employees) ){
		        foreach ($request->employees as $key => $employee) {
		            $taskAssign = new AssignTask;
		            $taskAssign->task_id = $task->id;
		            $taskAssign->user_id = $employee;
		            $taskAssign->assignment_date = date('Y-m-d');
		            $taskAssign->assigned_by = $user->id;
		            $taskAssign->save();
		        }
		    }
	    	
	        return prepareResult(true,getLangByLabelGroups('FollowUp','update') ,$task, $this->success);
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
        	$Task = Task::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('FollowUp','delete') ,[], $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
        	$Task = Task::where('id',$id)->with('assignEmployee.employee:id,name,email,contact_number','CategoryType:id,name')->first();
	        return prepareResult(true,'View Task' ,$Task, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function calanderTask(Request $request){
        try {
	    	$user = getUser();
	    	$whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Task::select('id','type_id','parent_id','title','status','branch_id','created_by', DB::raw('DATE(start_date) as date'))->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Task::select('id','type_id','parent_id','title','status','branch_id','created_by', DB::raw('DATE(start_date) as date'))->orderBy('id', 'DESC');
            }
             $data = [];
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
                foreach ($result as $key => $value) {
		    		$taskList =  Task::where('start_date',$value->date)->get();
		    		$history =[];
		    		foreach ($taskList as $key => $task) {
		    			$history[] = [
		                    'name' => $task->title,
		                    'status' => $task->status,
	                    ];
		    		}

		    		$data[$value->date]= $history;
		    		
		    	}
                $pagination =  [
                    'data' => $data,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Calander Task",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
	            foreach ($query as $key => $value) {
		    		$taskList =  Task::where('start_date',$value->date)->get();
		    		$history =[];
		    		foreach ($taskList as $key => $task) {
		    			$history[] = [
		                    'name' => $task->title,
		                    'status' => $task->status,
	                    ];
		    		}
		    		$data[$value->date]= $history;
		    		
		    	}
		    	
		    	return prepareResult(true,'Calander Task' ,$data, $this->success);
          	}
        	
	        
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "type_id = "."'" .$request->input('type_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('created_by')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "created_by = "."'" .$request->input('created_by')."'".")";
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
        return($w);

    }
}
