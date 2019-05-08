<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Team;
use App\Staff;
use App\Student;
use App\project_supervisor;
use DB;

class ProjectListController extends Controller {

    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function viewprojectlist() {
        $project = Project::all();
        $fypstaff = Staff::all();
        $student = Student::all();
        return view('projectlist.viewprojectlists', compact('team', 'project', 'fypstaff', 'student'));
    }

    public function view($projectCode) {
        $team = Team::where('projectCode', '=', $projectCode)->get();
        $project = Project::all();
        $fypstaff = Staff::all();
        $student = Student::all();
        return view('projectlist.compareprojectlist', compact('team', 'project', 'fypstaff', 'student', 'projectCode'));
    }

    public function edit($teamID) {
        $fypstaff = Staff::all();
        $team = Team::where('teamId', '=', $teamID)->get();
        return view('projectlist.updateprojectlist', compact('team', 'fypstaff'));
    }

    public function update(Request $request, $teamID) {
        $team = Team::where('teamId', '=', $teamID)->first();
        $team->teamScope = $request->get('teamScope');
		$team->supervisor = $request->get('supervisor');
        $team->moderator = $request->get('moderator');
        $team->competitionName = $request->get('competitionName');
        $team->status = $request->get('status');
        $team->save();
        return redirect()->route('projectlist.viewprojectlists')->with('success', 'Project list has been updated');
    }

}
