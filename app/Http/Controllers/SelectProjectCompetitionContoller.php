<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Session;
use App\ProjectOld;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Expr\Array_;


class SelectProjectCompetitionContoller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }

    public function index()
    {
		$cohortId = Session::get('cohortId');
        //if index() is called by redirect, set active tab to the tab before redirect
        if(session('is_redirect') == 'true'){
            session()->flash('active_tab', session('active_tab'));
        }
        //else set active tab to first tab
        else{
            session()->flash('active_tab', '0');
        }
        session()->flash('is_redirect', 'false');
        //$staff = DB::table('Role')->select('admin','FYPcommittee','supervisor')->where('staffID', Auth::user()->id)->get();
        //check if page is being access by supervisor
        //if($staff[0]->supervisor != "1")
            //unauthorized access
            //return view("admin-dashboard");
        //else
            return view('pages.manage_project.select_project_competition', compact('cohortId'));
    }

    public function ajax_load_unselected(){
		$cohortID = Session::get('cohortId');
        $supervisorID = Auth::user()->staffId;
		
        $projects = DB::table('Team')->select('Project.projectCode','Project.title', 'Team.teamID')->
        join('Project', 'Team.projectCode', '=', 'Project.projectCode')->
		join('Student', 'Team.teamId', '=', 'Student.teamId')->
		where('Student.cohortId', '=', $cohortID)->
        where('Team.supervisor', '=', $supervisorID)->where('Team.isCompetition', '=', '0')->
        orderBy('Project.title')->distinct()->get();
        return json_encode($projects);
    }

    public function ajax_load_previously_selected(){
		$cohortID = Session::get('cohortId');
		$supervisorID = Auth::user()->staffId;
		
        $projects = DB::table('Team')->select('Project.projectCode','Project.title', 'Team.teamID')->
        join('Project', 'Team.projectCode', '=', 'Project.projectCode')->
        join('Student', 'Team.teamId', '=', 'Student.teamId')->
		where('Student.cohortId', '=', $cohortID)->
		where('Team.supervisor', '=', $supervisorID)->where('Team.isCompetition', '=', '1')->
        orderBy('Project.title')->distinct()->get();

	
        $submission_status = [];
        foreach ($projects as $project){
            $student_count = 0;
            $submission_count = 0;
            //get number of students under the projects
            $students = DB::table('Student')->select('studentId', 'studentName')->where('teamID', $project->teamID)->get();
            if(sizeof($students)>0){
                //get number student who submitted for competition
                foreach ($students as $student){
                    $student_count++;
                    $submission = DB::table('Submission')->where('studentID', $student->studentId)->where('submission_type', 'competition')->count();
                    if($submission>0){
                        $submission_count++;
                    }
                }
                $fraction = $submission_count."/".$student_count;
            }
            else{
                $fraction = "No student";
            }
            array_push($submission_status, $fraction);
        }

        return Response::json(['projects' => $projects, 'submission_status' => $submission_status]);
        //return json_encode($array);
    }

    public function select(){
        $teamIDs = INPUT::get('unselected_student');
        foreach($teamIDs as $teamID){
            $team = Team::find($teamID);
            if ($team) {
                $team->isCompetition = 1;
                $team->save();
            }
        }
        session()->flash('is_redirect', 'true');
        session()->flash('active_tab', '0');
        return redirect("/selectprojectcompetition");
    }

    public function unselect(){
        $teamIDs = INPUT::get('previous_selected_student');
        foreach($teamIDs as $teamID){
            $team = Team::find($teamID);
            if ($team) {
                $team->isCompetition = 0;
                $team->save();
            }
        }
        session()->flash('is_redirect', 'true');
        session()->flash('active_tab', '1');
        return redirect("/selectprojectcompetition");
    }
}
