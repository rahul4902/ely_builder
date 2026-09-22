<?php

namespace App\Http\Controllers;

use App\BudgetMasterTableModel;
use App\ProjectMasterTableModel;
use App\RequirementMasterTableModel;
use App\SourceMasterTableModel;
use Illuminate\Http\Request;
use DB;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.is.admin');
    }

    public function projectList(Request $request){

        $project_list = ProjectMasterTableModel::where('status', '=', '1')->where('project_name','!=','other')->get();
        return view('master.project_list_page',['project_lists'=>$project_list,'flag'=>1]);
    }
    public function deleteProject(Request $request){
        DB::table('project_master')
            ->where('id', $request->input('id'))
            ->update(['status' => 0]);
        $project_list = ProjectMasterTableModel::where('status', '=', '1')->where('project_name','!=','other')->get();

        return view('master.project_list_page',['project_lists'=>$project_list,'flag'=>1]);


    }

    public function editProject(Request $request){
        $project_list = ProjectMasterTableModel::where('id', '=', $request->input('id'))->first();
        return view('master.project_edit_page',['project_list'=>$project_list,'flag'=>1])->render();


    }
    public function saveEditProject(Request $request){
        DB::table('project_master')
            ->where('id', $request->input('project_edit_id'))
            ->update(['project_name' => $request->input('project_name'),
                      'project_location'=> $request->input('project_location')
                     ]);
        $project_list = ProjectMasterTableModel::where('status', '=', '1')->where('project_name','!=','other')->get();

        return view('master.project_list_page',['project_lists'=>$project_list]);

    }
    public function saveProjectName(Request $request){

        $data= ProjectMasterTableModel::where('project_name', '=', $request->input('project_name'))->get();
        $count = count($data);
        if($count>0){
            $project_list = ProjectMasterTableModel::where('status', '=', '1')->where('project_name','!=','other')->get();
            $msg = "project name is already exist";
            return view('master.project_list_page',['msg' => $msg,'project_lists'=>$project_list,'flag'=>1]);
        }
        $project_data = new ProjectMasterTableModel();
        $project_data->project_name = $request->input('project_name');
        $project_data->project_location = $request->input('project_location');
        $project_data->save();
        $project_list = ProjectMasterTableModel::where('status', '=', '1')->where('project_name','!=','other')->get();

        return view('master.project_list_page',['project_lists'=>$project_list,'flag'=>1]);
    }

    public function requirementList(Request $request){
        $requirement_list = RequirementMasterTableModel::where('status', '=', '1')->where('requirement_name','!=','other')->get();
        return view('master.requitement_list_page',['requirement_lists'=>$requirement_list]);
    }

    public function deleteRequirement(Request $request)
    {
        DB::table('requirement_master')
            ->where('id', $request->input('id'))
            ->update(['status' => 0]);
        $requirement_list = RequirementMasterTableModel::where('status', '=', '1')->where('requirement_name','!=','other')->get();
        return view('master.requitement_list_page',['requirement_lists'=>$requirement_list]);

    }
    public function editRequirement(Request $request)
    {
        $requirement_list = RequirementMasterTableModel::where('id', '=', $request->input('id'))->first();
        return view('master.requirement_edit_page',['requirement_lists'=>$requirement_list])->render();

    }
public  function saveEditRequirement(Request $request)
{
    DB::table('requirement_master')
        ->where('id', $request->input('requirement_edit_id'))
        ->update(['requirement_name' => $request->input('requirement_name')
        ]);
    $requirement_list = RequirementMasterTableModel::where('status', '=', '1')->where('requirement_name','!=','other')->get();
    return view('master.requitement_list_page',['requirement_lists'=>$requirement_list]);
}

public function saveRequirementName(Request $request){
      $data= RequirementMasterTableModel::where('requirement_name','=',$request->input('requirement_name'))->get();
      $count = count($data);
      if($count>0){
          $requirement_list = RequirementMasterTableModel::where('status', '=', '1')->where('requirement_name','!=','other')->get();
          $msg = "Requirement Name already Exist";
          return view('master.requitement_list_page',['requirement_lists'=>$requirement_list,'msg'=>$msg])->render();
      }
    $reuirement_data = new RequirementMasterTableModel();
        $reuirement_data->requirement_name = $request->input('requirement_name');
        $reuirement_data->save();
    $requirement_list = RequirementMasterTableModel::where('status', '=', '1')->where('requirement_name','!=','other')->get();
    return view('master.requitement_list_page',['requirement_lists'=>$requirement_list]);
}
public function budgetList(Request $request){
    $budget_list = BudgetMasterTableModel::where('status', '=', '1')->where('budget_range','!=','other')->get();
    return view('master.budget_list_page',['budget_lists'=>$budget_list]);
}

public function deleteBudgetRange(Request $request)
{
    DB::table('budget_master')
        ->where('Id', $request->input('id'))
        ->update(['status' => 0]);
    $budget_list = BudgetMasterTableModel::where('status', '=', '1')->where('budget_range','!=','other')->get();
    return view('master.budget_list_page',['budget_lists'=>$budget_list]);
}

public function editBudgetRange(Request $request)
{
    $budget_list = BudgetMasterTableModel::where('id', '=', $request->input('id'))->first();
    return view('master.budget_edit_page',['budget_lists'=>$budget_list])->render();
}

public function saveEditBudgetRange(Request $request){

    DB::table('budget_master')
        ->where('id', $request->input('budget_edit_id'))
        ->update(['budget_range' => $request->input('budget_range')
        ]);
    $budget_list = BudgetMasterTableModel::where('status', '=', '1')->where('budget_range','!=','other')->get();
    return view('master.budget_list_page',['budget_lists'=>$budget_list]);
}

public function saveBudgetRange(Request $request){
     $data = BudgetMasterTableModel::where('budget_range','=',$request->input('budget_range'))->get();
     $count= count($data);
     if($count>0) {
         $budget_list = BudgetMasterTableModel::where('status', '=', '1')->where('budget_range','!=','other')->get();
         $msg = "Budge Range Already Exist";
         return view('master.budget_list_page', ['budget_lists' => $budget_list, 'msg' => $msg])->render();
     }
    $budget_range = new BudgetMasterTableModel();
    $budget_range->budget_range = $request->input('budget_range');
    $budget_range->save();

    $budget_list = BudgetMasterTableModel::where('status', '=', '1')->where('budget_range','!=','other')->get();
    return view('master.budget_list_page',['budget_lists'=>$budget_list]);
}

public function sourceList(Request $request){
    $source_list = SourceMasterTableModel::where('status', '=', '1')->where('source_name','!=','other')->get();
    return view('master.source_list_page',['source_lists'=>$source_list]);
}

public function deleteSource(Request $request)
{
    DB::table('source_master')
        ->where('id', $request->input('id'))
        ->update(['status' => 0]);
    $source_list = SourceMasterTableModel::where('status', '=', '1')->where('source_name','!=','other')->get();
    return view('master.source_list_page',['source_lists'=>$source_list]);
}

public function editSource(Request $request){
    $source_list = SourceMasterTableModel::where('id', '=', $request->input('id'))->first();
    return view('master.source_edit_page',['source_lists'=>$source_list])->render();
}
public function saveEditSource(Request $request)
{
    DB::table('source_master')
        ->where('id', $request->input('source_edit_id'))
        ->update(['source_name' => $request->input('source_name')
        ]);
    $source_list = SourceMasterTableModel::where('status', '=', '1')->where('source_name','!=','other')->get();
    return view('master.source_list_page',['source_lists'=>$source_list]);
}

public function saveSourceName(Request $request)
{
$data = SourceMasterTableModel::where('source_name','=',$request->input('source_name'))->get();
$count = count($data);
if($count>0){
    $source_list = SourceMasterTableModel::where('status', '=', '1')->where('source_name','!=','other')->get();
    $msg = "Source Name Already Exist";
    return view('master.source_list_page',['source_lists'=>$source_list,'msg'=>$msg])->render();

}
    $source_data = new SourceMasterTableModel();
    $source_data->source_name = $request->input('source_name');
    $source_data->save();
    $source_list = SourceMasterTableModel::where('status', '=', '1')->where('source_name','!=','other')->get();
    return view('master.source_list_page',['source_lists'=>$source_list]);
}

}
