<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\team;
use App\project;
use App\student;
use App\project_supervisor;
use DB;
use Session;

class teamsController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function LinkHome()
    {
        return view('HomeMian');
    }
    public static function store($teamId)
    {
        $team = new Team();
        $team->teamId = $teamId;
        $team->status = 'unassigned';
        $team->save();
    }

    public static function getLatestTeamId()
    {
//get cohort from student table while log in
        $student = student::find(Session::get('studentId'));
        $cohort = $student->cohortId."_";
//get team ID from teamId
        $team1 = DB::select(DB::raw("select teamId
                from team 
                where teamId like :cohortId
                ORDER BY teamId DESC LIMIT 1;"),array('cohortId'=>$cohort."%"));
				if($team1)
				{
					$team = explode("_",$team1[0]->teamId);
					$comteam = $team[0].'_'.++$team[1];
				}
				else
				{
					$team1=$cohort."T1000";
					$team = explode("_",$team1);
					$comteam = $team;
				}
        $team[3] = $student->studentId;
        $team[4] = $student->studentName;
        return $comteam;
        
    }
    
    public static function storeProjectDetail($id, $supervisor, $moderator, $code, $status)
    {
        $team = team::find($id);
        $team->supervisor = $supervisor;
		$team->moderator = $moderator;
        $team->status = $status;
        $team->projectCode = $code;
        $team->save();
        
    }
    
    public function ApproveProject()
    {
        return view('ApproveProjectRegistration');
    }
    
    public function showAllProject($supervisor)
    {
        $team = DB::select(DB::raw("select teamId, status, projectCode, supervisor
                from team 
                where supervisor = :supp"),array('supp'=>$supervisor));
        return response()->json($team);
    }
    
    public function respondProject(Request $request)
    {
        $team = team::find($request->get('teamId'));
        $team->supervisor = $request->get('supervisor');
        $team->status = $request->get('status');
        $team->projectCode = $request->get('projectCode');
        $team->save();
        $project_supervisor = DB::select(DB::raw("UPDATE project_supervisor set status = :status
                where supervisorId = :idsupervisor
                and projectCode = :code"),
        array('status'=> $request->get('status'),'idsupervisor' => $request->get('supervisor'), 'code' => $request->get('projectCode')));
        return view('ApproveProjectRegistration');
    }
    
    public function showAllTeam()
    {
        return view("ShowAllTeam");
    }
    
    public function autoAssign()
    {
        $project = project::where('status', '=', 'unassigned')->get();
        $student = student::where('teamId', '=', null)->get();
        $number = DB::select(DB::raw("SELECT * FROM student WHERE teamId IS null"));
//create new team
        for($i=0;$i<sizeof($number);$i++)
        {
        $cohort = $student[$i]->cohortId."_";
        $team1 = DB::select(DB::raw("select teamId
                from team 
                where teamId like :cohortId
                ORDER BY teamId DESC LIMIT 1;"),array('cohortId'=>$cohort."%"));
        $team = explode("_",$team1[0]->teamId);
        $teamId2 = $team[0]."_".++$team[1];
        $newTeam = new team();
        $newTeam->teamId = $teamId2;
        $newTeam->save();
//assign student to team
        $teamId3 = $team[0]."_".$team[1];
        $student[$i]->teamId = $teamId3;
        $student[$i]->save();
//assign supervisor to team
        $supervisor = project_supervisor::where("status","=", "unassigned")->get();
        $storeSupervisorTeam = DB::select(DB::raw("UPDATE team set supervisor = :idSupervisor, status = 'pending' WHERE teamId = :idTeam;"),
        array('idSupervisor' => $supervisor[$i]->supervisorId, ':idTeam' => $teamId3));
        }
    }
        
//        $project = project::where('status', '=', 'unassigned')->get();
//        $student = student::where('teamId', '=', null)->get();
////create new team
//        $teamId = team::latest('teamId')->first();
//            $teamId2 = $team[0]."_".++$team[1];
//            $newTeam = new team();
//            $newTeam->teamId = $teamId2;
//            $newTeam->save();
//            $teamId3 = $teamId->teamId;
//            
//        for($i=0;$i<sizeof($project);$i++)
//        {   
////store student into team
//            for($x=0;$x<$project[$i]->teamSize;$x++)
//                {
//                        $student[$x]->teamId = $teamId3;
//                        $student[$x]->save();
//                }
////assign project to team
//            $storeTeam = team::find($teamId3);
//            $storeTeam->supervisor = $project[$i]->supervisor;
//            $storeTeam->status = "pending";
//            $storeTeam->projectCode = $project[$i]->projectCode;
//            $storeTeam->save();
//            $storeProjectSupervisor = DB::select(DB::raw("UPDATE project_supervisor set status = 'pending'
//                where supervisorId = :idsupervisor
//                and projectCode = :code"),
//        array('idsupervisor' => $project[$i]->supervisor, 'code' => $project[$i]->projectCode));
//            $storeProject = project::find($project[$i]->projectCode);
//            $storeProject->status = "pending";
//            $storeProject->save();
//        }
//        
////wait supervisor confirm project
//        return response()->json($storeTeam);
//        return response()->json($storeProject);
    
    public function allTeam()
    {
        $showTeam = DB::select(DB::raw("select teamId, status, projectCode, supervisor
                from team"));
        return response()->json($showTeam);
    }
}
