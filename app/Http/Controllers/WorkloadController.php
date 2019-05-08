<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;
use App\Cohort;
use App\Student;
use App\WL_Formula;

class WorkloadController extends Controller {
    
    public function __construct() 
    {
        $this->middleware('auth:staff');
    }
    
    public function viewworkload() {
        $workloadformula = WL_Formula::all();
        $fypstaff = Staff::all();
        $cohort = Cohort::all();
        return view('workload.viewworkload', compact('fypstaff', 'cohort', 'workloadformula'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate() {
        $workloadformula = WL_Formula::all();
        $fypstaff = Staff::all();
        $cohort = Cohort::all();
        return view('workload.generateworkloadreport', compact('fypstaff', 'cohort', 'workloadformula'));
    }
    
    
    //get staff name in Team(supervisor), get teamID and then search in Student(teamID), display
    public function view($staffName, $cohortID, $selectedSection) {
        $fypstaff = Staff::where('staffId', '=', $staffName)->first();
        $student = Student::all();
        return view('workload.viewworkloaddetails', compact('fypstaff', 'student', 'staffName', 'cohortID', 'selectedSection'));
    }
    
    public function edit(){
        return view('workload.updateformula');
    }
    
    public function update(Request $request, $id) {
        $formula = WL_Formula::where('formulaId', '=', $id)->first();
        $formula->totalMinutes = $request->get('totalMinutes');
        $formula->totalWeeks = $request->get('totalWeeks');
        $formula->PTClaims = $request->get('ptclaims');
        $formula->save();
        return redirect()->route('workload.viewworkload')->with('success', 'Formula has been updated');
    }


}