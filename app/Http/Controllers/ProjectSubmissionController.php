<?php

namespace App\Http\Controllers;


use App\Student;
use App\Submission_item;
use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectSubmissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:student');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($isCompetition=null)
    {		
        $cohort = auth()->user()->cohortId;
        $programmeID = auth()->user()->programmeId;
		$departmentid = DB::table('programme')->select('departmentId')->where('programmeId',$programmeID)->get();
        $faculty = DB::table('Department')->select('facultyId')->where('departmentId',$departmentid[0]->departmentId)->get();
        $facultyID = $faculty[0]->facultyId;
        //$submission_status = auth()->user()->submissionStatus;

		$project = DB::table('Student')->select('Project.title')->join('Team', 'Student.teamId', '=', 'Team.teamId')->join('Project', 'Team.projectCode', '=', 'Project.projectCode')
		->where('Student.studentId', '=', auth()->user()->studentId)->first();
		//check whether student has a team/project first 
		if($project){
		
			//Get number of submission if isCompetition is not null
			if($isCompetition !=null){
				if($isCompetition == "1")
					$submission_type = "competition";
				else if($isCompetition == "0")
					$submission_type = "normal";
				$submission = DB::table('Submission')->where('studentID', auth()->user()->studentId)->where('submission_type', $submission_type)->count();
			}

			if($isCompetition !=null) {
				//If number of submission is less than 1, means the student has not submit
				if ($submission < 1) {
					$cohort_deliverable_id = DB::table('Cohort_Deliverable')->select('cohort_deliverable_id')->where([
						['cohortID', '=', $cohort],
						//['programmeID', '=', $programmeID],
						['facultyID', '=', $facultyID],
						['isCompetition', '=', $isCompetition]
					])->get();

					if (count($cohort_deliverable_id) > 0) {
						$deliverables = DB::table('Deliverable')->
						select('Deliverable.deliverable_id', 'Deliverable.deliverable_name', 'Deliverable_type.deliverable_type', 'Deliverable_Type.deliverable_extension', 'Deliverable_Type.field_type')->
						join('Deliverable_Type', 'Deliverable.deliverable_type_id', '=', 'Deliverable_Type.deliverable_type_id')->where('deliverable.cohort_deliverable_id', $cohort_deliverable_id[0]->cohort_deliverable_id)->
						orderBy('Deliverable_Type.field_type', 'desc')->get();
						return view('pages.project_submission')->with('deliverables', $deliverables)->with('isCompetition', $isCompetition);
					}
					//This means the students do not have a FYP submission to submit.
					else {
						$deliverables = [];
						return view('pages.project_submission')->with('deliverables', $deliverables)->with('isCompetition', $isCompetition);
					}
				}
				//Yay! Student have previously submitted project.
				else {
					$deliverables = [];
					return view('pages.project_submission')->with("deliverables", $deliverables)->with("submission_message", "Woohoo! You have already submitted your project.")->with('isCompetition', $isCompetition);
				}
			}
			//Since $isCompetiton is null, this request is assumed to be a redirection right after project submitted.
			else{
				$deliverables = [];
				return view('pages.project_submission')->with("deliverables", $deliverables)->with("submission_message", "Woohoo! You have already submitted your project.")->with('isCompetition', $isCompetition);
			}
			
		//Student who has no team/project attempted to visit this page
		}else{
				return "No project";
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cohort = auth()->user()->cohortId;
		$isCompetition = $request->input('isCompetition');
		
        $programmeID = auth()->user()->programmeId;
		$departmentid = DB::table('programme')->select('departmentId')->where('programmeId',$programmeID)->get();
        $faculty = DB::table('Department')->select('facultyId')->where('departmentId',$departmentid[0]->departmentId)->get();
        $facultyID = $faculty[0]->facultyId;

        $cohort_deliverable_id = DB::table('Cohort_Deliverable')->select('cohort_deliverable_id')->where([
            ['cohortID', '=', $cohort],
            ['facultyID', '=', $facultyID],
            ['isCompetition', '=', $isCompetition]
        ])->get();

        if (count($cohort_deliverable_id) > 0) {
            $deliverables = DB::table('Deliverable')->
            select('deliverable.deliverable_id', 'deliverable_type.deliverable_extension', 'deliverable_type.field_type')->
            join('deliverable_type', 'deliverable.deliverable_type_id', '=', 'deliverable_type.deliverable_type_id')->
            where('deliverable.cohort_deliverable_id', $cohort_deliverable_id[0]->cohort_deliverable_id)->
            orderBy('deliverable_type.field_type', 'desc')->get();
        } else {
            return redirect("/projectsubmission")->with("error", "Oops, server has ran into problem. Please try submitting again.");
        }

       //Input Validation
        $required_flag = 0;
        $extension_flag = 0;
        foreach ($deliverables as $deliverable) {
            if ($deliverable->field_type === "Text" || $deliverable->field_type === "Textarea") {
                //required input validation for Text/Textarea field
                if ($request->input($deliverable->deliverable_id) == "") {
                    $required_flag = 1;
                }
            } else if ($deliverable->field_type === "File") {
                //required input validation for File field
                if ($request->hasFile($deliverable->deliverable_id) == false) {
                    $required_flag = 1;
                } //file extension validation
                else {
                    //compare them with uploaded file extension
                    $uploaded_file_extension = $request->file($deliverable->deliverable_id)->getClientOriginalExtension();
                    $deliverable_extension = explode(",", $deliverable->deliverable_extension);
                    if (!in_array($uploaded_file_extension, $deliverable_extension)) {
                        $extension_flag = 1;
                    }
                }
            }
        }

        //return error message if found invalid input
        if ($required_flag == 1 && $extension_flag == 1) {
            return redirect("/projectsubmission")->with("error", "Please ensure that all fields have been filled.<br>Please ensure that all files uploaded are of valid format.");
        } else if ($required_flag == 1 && $extension_flag == 0) {
            return redirect("/projectsubmission")->with("error", "Please ensure that all fields have been filled.");
        } else if ($required_flag == 0 && $extension_flag == 1) {
            return redirect("/projectsubmission")->with("error", "Please ensure that files uploaded are of valid format.");
        }

        //update student submissionstatus
        //$id = auth()->user()->id;
        //$student = User::find($id);
        //$student->submissionStatus = 1;
        //$student->updated_at =\Carbon\Carbon::now();
        //$student->save();

        //insert data into submission
        $studentID = auth()->user()->studentId;
        $date = date("Y-m-d");
        if($isCompetition == "1"){
            $submission_type = "competition";
        }
        else if($isCompetition == "0"){
            $submission_type = "normal";
        }

        $submission = new Submission;
        $submission->studentID = $studentID;
        $submission->submission_date = $date;
        $submission->submission_type = $submission_type;
        $submission->save();
		//using submission->id instead of submission->submission_id because in submission.php primary key is set to id
        $submission_id = $submission->id;

        //insert data into submission_item and upload files to file system
        $i=0;
        foreach($deliverables as $deliverable) {
            $submission_item = new Submission_item;
            $projectCode = "1";
            $cohortID = "0518";
            $teamListID = "1";
            $folderpath = '/uploads/' . $projectCode . '/' . $cohortID . '/' . $teamListID . '/' . $studentID;

            $submission_item->submission_id = $submission_id;
            if ($deliverable->field_type === "Text" || $deliverable->field_type === "Textarea") {
                $submission_item->content = $request->input($deliverable->deliverable_id);
                $submission_item->file_name = null;
                $submission_item->file_extension = null;
                $submission_item->file_size = null;
                $submission_item->file_path = null;
            } else if ($deliverable->field_type === "File") {
                $fileSize = $request->file($deliverable->deliverable_id)->getClientSize();
                $fileNameWithExt = $request->file($deliverable->deliverable_id)->getClientOriginalName();
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME) . '_' . time();
                $extension = $request->file($deliverable->deliverable_id)->getClientOriginalExtension();
                $fileNameToStore = $fileName . '.' . $extension;
                $filepath = $request->file($deliverable->deliverable_id)->storeAs($folderpath, $fileNameToStore);

                $submission_item->content = null;
                $submission_item->file_name = $fileName;
                $submission_item->file_extension = $extension;
                $submission_item->file_size = $fileSize;
                $submission_item->file_path = $filepath;
            }
            $submission_item->deliverable_id = $deliverable->deliverable_id;
            $submission_item->save();
        }
        return redirect("/projectsubmission");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
