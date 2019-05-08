<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\supervisor_cohort;
use App\staff;
use Illuminate\Support\Facades\DB;

class supervisor_cohortController extends Controller
{
    
    public function __construct() 
    {
        $this->middleware('auth:staff');
    }
/*
    public function viewAllSupervisor($cohortId)
    {
        $supervisor_cohort = DB::select(DB::raw("select s.staffId, s.title, s.staffName, s.phoneNo, s.email, s.status, s.role, s.specialization, s.departmentId
                from staff s, supervisor_cohort c 
                where s.staffId = c.staffId
                and c.cohortId = :id"),array('id'=>$cohortId));
        return response()->json($supervisor_cohort);
		<thead><tr><td>Title</td><td>Satff Name</td><td>ProjectCode</td><td>Student Name</td></tr></thead><tbody>"
    }
	*/
	public function viewAllSupervisor($cohortId)
    {
        $supervisor_cohort = DB::select(DB::raw("select s.title, s.staffName, t.projectCode, st.studentName
                from staff s, team t,  student st
                where s.staffId = t.supervisor
				and st.teamId = t.teamId
                and st.cohortId = :id
				order by s.staffName"),array('id'=>$cohortId));
        return response()->json($supervisor_cohort);
    }
}
