<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\student;
use App\team;
use App\Http\controllers\teamsController;
use DB;
use Session;
use Auth;

class studentsController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function showRegisterTeam()
    {
		$teamid = Auth::user()->teamId;
		if($teamid == null)
		{
			$team = teamsController::getLatestTeamId();
			$student = null;
		}
		else
		{
			$team = $teamid;
			$student = student::where('teamId','=',$teamid)->get();
		}
        
        /*
        $member = DB::select(DB::raw("Select * from student where teamId = :idTeam and studentId != :idStudent"),
        array('idTeam' => $exist->teamId,'idstudent' => Session::get('studentId')));
         */
        return view('RegisterTeam',compact('student','team'));
    }

    public function addTeam(Request $request, $teamId)
    {
        teamsController::store($teamId);
        $array = $request->arr;
        \error_log($array[0]);
        for($i=0;$i<sizeof($array);$i++)
        {
            $updateTeamId = student::where('studentId', '=', $array[$i])->first();
            $updateTeamId->teamId=$teamId;
            $updateTeamId->save();
        }
        echo "Add in team successful";
        
    }

    public function cancelTeam($studentId)
    {
        $updateTeamId = DB::select(DB::raw("UPDATE students set teamId = 'null' where studentId = :idstudent"),
        array('idstudent' => $studentId));
        return redirect()->route("RegisterTeam");
    }

    public function viewStudent($id)
    {
        $student = Student::find($id);
        return response()->json($student);
    }
    
    public function viewAllStudent($cohortId)
    {
        $student = DB::select(DB::raw("SELECT * FROM student ORDER BY programmeId, studentName"));
        return response()->json($student);
    }
    
    public static function viewStudentTeam($id)
    {
        $student = Student::where('teamId',$id)->get();
        return $student;
    }
    
}
