<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Session;
class DisplaySubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }

    public function index(){

        if($this->validate_supervisor()) {
            $supervisorID = Auth::user()->staffId;
			$cohortId = Session::get('cohortId');
			
            $projects = DB::table('Team')->select('Project.projectCode','Project.title', 'Team.isCompetition', 'Team.teamID')->
            join('Project', 'Team.projectCode', '=', 'Project.projectCode')->
			join('Student', 'Team.teamId', '=', 'Student.teamId')->
			where('Student.cohortId', '=', $cohortId)->
            where('Team.supervisor', '=', $supervisorID)->
            //orderBy('teams.isCompetition', 'desc')->
            orderBy('Project.title', 'asc')->
			distinct()->
            get();

            $submission_status = [];
            foreach ($projects as $project){
                $student_count_normal = 0;
                $submission_count_normal = 0;
                $student_count_competition = 0;
                $submission_count_competition = 0;

                //get number of students under the projects
                $students = DB::table('Student')->select('studentId', 'studentName')->where('teamId', $project->teamID)->get();

                //get the submission status for both competition and normal submission
                if($project->isCompetition == '1'){
                    if(sizeof($students)>0){
                        //get number student who submitted for competition submission
                        foreach ($students as $student){
                            $student_count_competition++;
                            $submission = DB::table('Submission')->where('studentID', $student->studentId)->where('submission_type', 'competition')->count();
                            if($submission>0){
                                $submission_count_competition++;
                            }
                        }
                        $fraction = $submission_count_competition."/".$student_count_competition;
                        array_push($submission_status, $fraction);

                        //get number student who submitted for normal submission
                        foreach ($students as $student){
                            $student_count_normal++;
                            $submission = DB::table('Submission')->where('studentId', $student->studentId)->where('submission_type', 'normal')->count();
                            if($submission>0){
                                $submission_count_normal++;
                            }
                        }
                        $fraction = $submission_count_normal."/".$student_count_normal;
                        array_push($submission_status, $fraction);
                    }
                    else{
                        $fraction = "No student";
                        array_push($submission_status, $fraction);
                        array_push($submission_status, $fraction);
                    }
                }
                //get the submission status for normal submission
                else{
                    if(sizeof($students)>0){
                        //get number student who submitted for competition
                        foreach ($students as $student){
                            $student_count_normal++;
                            $submission = DB::table('Submission')->where('studentId', $student->studentId)->where('submission_type', 'normal')->count();
                            if($submission>0){
                                $submission_count_normal++;
                            }
                        }
                        $fraction = $submission_count_normal."/".$student_count_normal;
                        array_push($submission_status, $fraction);
                    }
                    else{
                        $fraction = "No student";
                        array_push($submission_status, $fraction);
                    }
                }
            }
            return view('pages.manage_project.display_submission_projects', compact('projects','submission_status', 'cohortId'));
        }
        else {
            return view("index");
        }
    }

    public function show($projectCode, $teamID, $isCompetition){
		$cohortId = Session::get('cohortId');
        if($isCompetition == 0){
            $submission_type = 'normal';
        }
        else{
            $submission_type = 'competition';
        }

        $projects = DB::table('Project')->select('project.title')->
        where('projectCode', $projectCode)->get();
        $title = $projects[0]->title;
        if($isCompetition == '1'){
            $title .= " (Competition)";
        }
        if($this->validate_supervisor()) {
            //retrieve every students in the team
            $all_students = DB::table('Student')->select('studentId', 'studentName')->
            where('teamId', $teamID)->orderBy('studentName')->get();

            //retrieve only students in the team who submitted
            $students = DB::table('Student')->select('Student.studentId', 'Student.studentName', 'Submission.submission_id', 'Submission.submission_date')->
            join('Submission', 'Submission.studentID','=','Student.studentId')->
            where('Student.teamID', $teamID)->
            where('Submission.submission_type', $submission_type)->get();


            if(sizeof($students)>0){
                foreach ($students as $student){
                    $submissions = DB::table('Submission_Item')->select('Submission_Item.submission_id','Submission_Item.item_id','Submission_Item.file_name', 'Submission_Item.file_extension', 'Submission_Item.file_size', 'Submission_Item.content', 'Deliverable.deliverable_name')->
                    join('Deliverable','Deliverable.deliverable_id', '=', 'Submission_Item.deliverable_id')->
                    where('Submission_Item.submission_id', $student->submission_id)->get();
                }
            }
            //return $students;
            return view('pages.manage_project.display_submission_students', compact('title', 'all_students','students','submissions', 'cohortId'));
        }
        else {
            return view("index");
        }
    }

    public function download($item_id){
        if($this->validate_supervisor()) {
            $file_paths = DB::table('Submission_Item')->select('file_path')->where('item_id', $item_id)->get();
            $file_path = $file_paths[0]->file_path;
            return response()->download(storage_path("app/" . $file_path));
        }
        else{
            return view("index");
        }
    }

	public function remove($submission_id){
		if($this->validate_supervisor()){
			DB::table('submission_item')->where('submission_id', $submission_id)->delete();
			DB::table('submission')->where('submission_id', $submission_id)->delete();
			return redirect()->back();
		}
	}
	
    public function validate_supervisor(){
		/*
        $staff = DB::table('Role')->select('admin','FYPcommittee','supervisor')->where('staffID', Auth::user()->id)->get();
        //check if page is being access by supervisor
        if($staff[0]->supervisor != "1")
            //unauthorized access
            return false;
        else {
            return true;
        }
		*/
		return true;
    }
}
