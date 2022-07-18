<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use Validator;
use Auth;
use Exception;
use DB;
class QuestionController extends Controller
{
    protected $top_most_parent_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function questions(Request $request)
    {
        try {
            $user = getUser();
            $group_questions  =[];
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                // $query = Question::where('top_most_parent_id',$this->top_most_parent_id)->whereRaw($whereRaw)->orWhereNull('top_most_parent_id')->groupBy('group_name')->orderBy('id', 'DESC');

                $query = Question::where('top_most_parent_id',$this->top_most_parent_id)->orWhereNull('top_most_parent_id')->groupBy('group_name')->orderBy('id', 'DESC');
               
            } else {
                $query = Question::where('top_most_parent_id',$this->top_most_parent_id)->orWhereNull('top_most_parent_id')->groupBy('group_name')->orderBy('id', 'DESC');
                 
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
                return prepareResult(true,"Question list",$pagination,'200');
            }
            else
            {
                $query = $query->get();
            }

            foreach ($query as $key => $group) {
               $questionList = Question::where('group_name',$group->group_name)->where('is_visible', $request->is_visible)->get();
               if($questionList->count()>0)
               {
                   $group_questions[]=[
                    "group_name" => $group->group_name,
                    "questions" => $questionList,
                   ];
               }
            }


            return prepareResult(true,"Question list",$group_questions,'200');
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
        
    }

     public function store(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'group_name' => 'required',   
                'question' => 'required',   
            ],
            [
            'group_name.required' => 'Group name  field is required',
            'question.required' => 'question field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
           
            $question = new Question;
            $question->top_most_parent_id = $this->top_most_parent_id;
            $question->created_by = $user->id;
            $question->group_name = $request->group_name;
            $question->question = $request->question;
            $question->is_visible = ($request->is_visible)? $request->is_visible:0;
            $question->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $question->save();
             return prepareResult(true,getLangByLabelGroups('CompanyType','message_create') ,$question, '200');
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }
    public function show($id){
        
        try {
            $user = getUser();
            $checkId= Question::where('top_most_parent_id',$this->top_most_parent_id)->where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_id_not_found'), [],'404');
            }
            
            $question = Question::where('id',$id)->first();
            return prepareResult(true,'View Quesion' ,$question, '200');
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), '500');
            
        }
    }

    public function update(Request $request,$id){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'group_name' => 'required',   
                'question' => 'required',   
            ],
            [
            'group_name.required' => 'Group name  field is required',
            'question.required' => 'question field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[],'422'); 
            }
            $checkId = Question::where('top_most_parent_id',$this->top_most_parent_id)->where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CompanyType','message_id_not_found'), [],'404');
            }
            
            $question = Question::find($id);
            $question->group_name = $request->group_name;
            $question->question = $request->question;
            $question->is_visible = ($request->is_visible)? $request->is_visible:0;
            $question->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $question->save();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_update'),$question, '200');
                
               
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }
    public function destroy($id){
        
        try {
            $user = getUser();
            $checkId= Question::where('top_most_parent_id',$this->top_most_parent_id)->where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_id_not_found'), [],'404');
            }
            
            $question = Question::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('CompanyType','message_delete') ,[], '200');
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('group_name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "group_name = "."'" .$request->input('group_name')."'".")";
        }
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('is_visible')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "is_visible = "."'" .$request->input('is_visible')."'".")";
        }
        return($w);

    }

    
}
