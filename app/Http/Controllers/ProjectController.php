<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Project;
use App\Team;
use App\Staff;
use DB;
use Session;
use Excel;
use File;
use Log;
use App\Student;
use App\project_supervisor;

class ProjectController extends Controller {

    private $msgheader="There have the error(s) when importing the file.<br> "
            . "The error might be:<br> "
            . "Incorrect table column name in excel file.<br>"
            . "dulpicate from database or missing the project Code.<br>"
            . "The error(s) detail display as below:<br>";
    
    private $errmsg;
    
    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }
    
    public function viewproject() {
        $team = Team::all();
        $fypstaff = Staff::all();
        $projectsupervisor = project_supervisor::all();
        return view('project.viewproject', compact('fypstaff', 'projectsupervisor', 'team'));
    }

    public function destroy($projectCode) {
        $team = Team::where('projectCode', '=', $projectCode)->first();
        if ($team === null) {
			DB::delete('delete from project_supervisor where projectCode = ?', [$projectCode]);
            DB::delete('delete from project where projectCode = ?', [$projectCode]);
            return redirect()->route('project.viewproject')->with('success', 'Project has been deleted');
        } else {
            return redirect()->route('project.viewproject')->with('fail', 'Project cannot be deleted');
        }
    }

    public function create() {
        return view('project.createproject');
    }

    public function store(Request $request) {
        $projectAdvisor = "";
        $project = Project::where('projectCode', '=', $request->get('code'))->first();
        if ($project === null) {
            $project = new Project();
            $project->projectCode = $request->get('code');
            $project->title = $request->get('title');
            $project->description = $request->get('desc');
            if ($request->get('newgroup') == "") {
                $project->projectGroup = $request->get('group');
            } else {
                $project->projectGroup = $request->get('newgroup');
            }
            if ($request->get('newcluster') == "") {
                $project->cluster = $request->get('cluster');
            } else {
                $project->cluster = $request->get('newcluster');
            }
            $project->level = $request->get('level');
            $project->clientName = $request->get('clientName');
			$project->enhancement = $request->get('enhancement');
			$project->generation = 1;
			$project->scope = $request->get('scope');
			$project->status = "New";
            if (isset($_POST['ingredients'])) {
                $projectAdvisor = $_POST['ingredients'];
                $projectAdvisor=implode(', ',$projectAdvisor);
                $project->advisor = $projectAdvisor;
            }
            $project->teamSize = $request->get('teamsize');
            $project->save();
			if (isset($_POST['supervisors'])) {
                foreach($_POST['supervisors'] as $supervisor){
					$projectSupervisor = new project_supervisor();
					$projectSupervisor->projectCode = $request->get('code');
					$projectSupervisor->supervisorId = $supervisor;
					$projectSupervisor->status = "unassigned";
					$projectSupervisor->save();
				}
            }
            return redirect()->route('project.viewproject')->with('success', 'New project has been added');
        } else {
            return redirect()->route('project.createproject')->with('fail', 'Project code already exist');
        }
    }

    public function updateproject($projectCode) {
		$project_supervisor = project_supervisor::all();
        $project = Project::where('projectCode', '=', $projectCode)->get();
        return view('project.updateproject', compact('project', 'projectCode', 'project_supervisor'));
    }

    public function removeproject($projectCode) {
        $project = Project::where('projectCode', '=', $projectCode)->get();
        return view('project.removeproject', compact('project', 'projectCode'));
    }

    public function update(Request $request, $projectCode) {
        DB::delete('delete from project_supervisor where projectCode = ?', [$projectCode]);
		$projectAdvisor = "";

        if ($projectCode == $request->get('code')) {
            $project = Project::where('projectCode', '=', $projectCode)->first();
            $project->projectCode = $request->get('code');
            $project->title = $request->get('title');
            $project->description = $request->get('desc');
            if ($request->get('newgroup') == "") {
                $project->projectGroup = $request->get('group');
            } else {
                $project->projectGroup = $request->get('newgroup');
            }
            if ($request->get('newcluster') == "") {
                $project->cluster = $request->get('cluster');
            } else {
                $project->cluster = $request->get('newcluster');
            }
            $project->clientName = $request->get('clientName');
			$project->scope = $request->get('scope');
			$project->enhancement = $request->get('enhancement');
            $project->level = $request->get('level');
            if (isset($_POST['ingredients'])) {
                $projectAdvisor = $_POST['ingredients'];
                $projectAdvisor=implode(', ',$projectAdvisor);
                $project->advisor = $projectAdvisor;
            }
            $project->status = $request->get('status');
			if($request->get('status') == "Continued"){
				$project->generation = $project->generation + 1;
			}
            $project->teamSize = $request->get('teamsize');
            $project->save();
			if (isset($_POST['supervisors'])) {
                foreach($_POST['supervisors'] as $supervisor){
					$projectSupervisor = new project_supervisor();
					$projectSupervisor->projectCode = $request->get('code');
					$projectSupervisor->supervisorId = $supervisor;
					$projectSupervisor->status = "unassigned";
					$projectSupervisor->save();
				}
            }
            return redirect()->route('project.viewproject')->with('success', 'Project has been updated');
        } else {
            $project = Project::where('projectCode', '=', $request->get('code'))->first();
            if ($project === null) {
                $project = Project::where('projectCode', '=', $projectCode)->first();
                $project->projectCode = $request->get('code');
                $project->title = $request->get('title');
                $project->description = $request->get('desc');
                if ($request->get('newgroup') == "") {
                    $project->projectGroup = $request->get('group');
                } else {
                    $project->projectGroup = $request->get('newgroup');
                }
                if ($request->get('newcluster') == "") {
                    $project->cluster = $request->get('cluster');
                } else {
                    $project->cluster = $request->get('newcluster');
                }
                $project->clientName = $request->get('clientName');
                $project->level = $request->get('level');
				$project->scope = $request->get('scope');
				$project->enhancement = $request->get('enhancement');
                if (isset($_POST['ingredients'])) {
                $projectAdvisor = $_POST['ingredients'];
                $projectAdvisor=implode(', ',$projectAdvisor);
                $project->advisor = $projectAdvisor;
				}
                $project->status = $request->get('status');
				if($request->get('status') == "Continued"){
				$project->generation = $project->generation + 1;
				}
                $project->teamSize = $request->get('teamsize');
                $project->save();
				if (isset($_POST['supervisors'])) {
                foreach($_POST['supervisors'] as $supervisor){
					$projectSupervisor = new project_supervisor();
					$projectSupervisor->projectCode = $request->get('code');
					$projectSupervisor->supervisorId = $supervisor;
					$projectSupervisor->status = "unassigned";
					$projectSupervisor->save();
				}
            }
                return redirect()->route('project.viewproject')->with('success', 'Project has been updated');
            } else {
                return redirect()->action('ProjectController@updateproject', $projectCode)->with('fail', 'Project code already exist');
            }
        }
    }

    public function import(Request $request)
    {
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));

        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {

                $collection = (new FastExcel)->import($request->file,function($line)
                {
                    if(!empty($line['projectcode']))
                    {
                        try
                        {
                        return Project::create([
                            'projectCode'=>$line['projectcode'],
                            'title'=>$line['title'],
                            'description'=>$line['description'],
                            'projectGroup'=>$line['projectgroup'],
                            'cluster'=>$line['cluster'],
                            'status'=>$line['status'],
                            'clientName'=>$line['clientname'],
                            'advisor'=>$line['advisor']
                        ]);
                        }
                        catch(\Illuminate\Database\QueryException $ex)
                        {
                            $this->errmsg .= $ex->getMessage()."<br>";
                        }
                        catch(\ErrorException $e)
                        {
                            if(empty($this->errmsg))
                            {
                                $this->errmsg .= $e->getMessage()."<br>";
                            }
                        }
                    }
                });

                if (empty($this->errmsg)) {
                    Session::flash('success', 'Your Data has successfully imported');
                }else {
                    Session::flash('error', $this->msgheader.$this->errmsg);
                    return back();
                }
                return back();

            }else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }

    //Tee Ren MIan
    public function showAllTeam()
    {
        $studentId = Session::get('studentId');
        $studentDetail = Student::where('studentId','=', $studentId)->first();

        $studentTeam = DB::select(DB::raw("select teamId
                from student
                where studentId = :idStudent"),array('idStudent'=>Session::get('studentId')));
        $supervisors = DB::select(DB::raw("select s.staffName from team t, staff s
			where s.staffId = t.supervisor
			and teamId = :teamId;"),array('teamId'=>$studentTeam[0]->teamId));
			if($supervisors)
			{
				$supervisor = $supervisors[0]->staffName;
			}
			else{
				$supervisor = null;
			}
        
        $student = DB::select(DB::raw("select s.studentName from student s, team t
        where s.teamId = t.teamId
        and t.teamId = :teamId;"),array('teamId'=>$studentDetail['teamId']));
		
		$haveproject = Team::where('teamId','=',$studentTeam[0]->teamId)->first();
		if($haveproject)
		{
			$title = Project::find($haveproject->projectCode);
		}
		else
		{
			$title=null;
		}
        \error_log($student[0]->studentName);
        return view('RegisterProject',compact('student','supervisor','title'));
    }

}
