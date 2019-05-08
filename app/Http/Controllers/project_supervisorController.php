<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\project_supervisor;
use App\Http\controllers\teamsController;
use DB;
use Session;

class project_supervisorController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function showProject()
    {
        $project_supervisor =  DB::select(DB::raw('select p.projectCode, ps.supervisorId, s.staffId, s.staffName, ps.status from project p, project_supervisor ps, staff s
        where p.projectCode = ps.projectCode
        and ps.supervisorId = s.staffId
		and ps.status = "unassigned";'));
        return response()->json($project_supervisor);
    }
    
    public function RegNewProj(Request $request)
    {
        $studentTeam = DB::select(DB::raw("select teamId
                from student
                where studentId = :idStudent;"),array('idStudent'=>Session::get('studentId')));
        $teamId = DB::select(DB::raw("select teamId
                from student
                where teamId = :teamId;"),array('teamId'=>$studentTeam[0]->teamId));
        $supervisor = $request->get('supervisorId');
		$moderator = DB::select(DB::raw("select moderatorId
                from supervisor_cohort
                where staffId = :staffId
				and cohortId = :cohortId;"),array('staffId'=>$supervisor,'cohortId'=>Session('cohortId')));
        $code = $request->get('projectCode');
        $storeTeam = teamsController::storeProjectDetail($teamId[0]->teamId,$supervisor,$moderator[0]->moderatorId, $code, 'assigned');
        $project_supervisor = DB::select(DB::raw("UPDATE project_supervisor set status = 'assigned'
                where supervisorId = :idsupervisor
                and projectCode = :code;"),
        array('idsupervisor' => $supervisor, 'code' => $code));
		$project = DB::select(DB::raw("UPDATE project set status = 'Ongoing';"));
        return response()->json($project_supervisor);
    }
}
