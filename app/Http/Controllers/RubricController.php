<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Artifact;
use App\Cohort;
use App\Criteria;
use App\Form_Template;
use App\Programme;
use App\Project;
use App\Rubric;
use App\Rubric_Assessment;
use App\Rubric_Artifact;
use App\Rubric_Criteria;
use App\Staff;
use App\Supervisor_Cohort;
use App\Student;
use App\Team;
use Carbon\Carbon;
use Log;
use Excel;
use PDF;
use Session;
use Storage;
use View;
use App\Imports\UsersImport;

class RubricController extends Controller {

    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function rubricIndex(Request $request) {
		if (Session::has('cohortId')) {
			$cohortDetail = Cohort::where('cohortId', Session::get('cohortId'))->first();
		}
		
        if ($request->get('rubricAssessment')) {
            return self::getRubricTemplate();
        } elseif ($request->get('markSummaryProject1') && Session::has('cohortId')) {
            $type = "Project 1";
            Session::put('type', $type);
            return self::getMarkSummaryProject($request);
        } elseif ($request->get('markSummaryProject2') && Session::has('cohortId')) {
            $type = "Project 2";
            Session::put('type', $type);
            return self::getMarkSummaryProject($request);
        } elseif ($request->get('templateVersion') && Session::has('cohortId')) {
            return self::getTemplateVersion();
        } elseif ($request->get('downloadAssessmentRubrics') && Session::has('cohortId')) {
            return view('rubric.downloadRubric');
        } elseif ($request->get('studentAssessment') && Session::has('cohortId')) {
			$staffId = Session::get('staffId');
			$cohortId = Session::get('cohortId');
			
			$supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();
		
			if(empty($supervisorCohort)) {
				Session::flash('fail', 'Assessment form is not ready (Staff Pairing is not done) Please refer to Faculty Admin for further action.');
				return view('rubric.rubricMain');
			}
			
			if(empty($cohortDetail['project1Rubric']) || empty($cohortDetail['project2Rubric'])) {
				Session::flash('fail', 'Assessment rubric is not ready (Assessment template has not been chosen) Please refer to Faculty Admin for further action.');
				return view('rubric.rubricMain');
			}
			
            $assessmentArray = self::getStudentAssessment();
            return view('rubric.studentAssessment', compact('assessmentArray'));
        } else {
			return redirect ('showallcohort');
		}
    }

    public function uploadRubric(Request $request) {
        $staffId = Session::get('staffId');

        $validator = Validator::make($request->all(), [
                    'rubric' => 'mimes:xls,xlsx'
        ]);

        if ($validator->fails()) {
            return redirect('invalidRubricUpload')
                            ->withErrors($validator)
                            ->withInput();
        }

        $rubricType = $request->get('rubricType');

        if ($rubricType == "projectI") {
            $type = "Project I";
        } else {
            $type = "Project II";
        }

        $latestRubric = Rubric::where('type', $type)->orderBy('created_at', 'desc')->first();

        if ($latestRubric == "") {
            $version = 1;
        } else {
            $version = $latestRubric['version'] + 1;
        }

        $folderName = "Rubric Template/".$type;
        $path = $request->file('rubric')->storeAs($folderName, $version . "_" . $request->rubric->getClientOriginalName());

        $rubric = new Rubric();
        $rubric->fileName = $path;
        $rubric->version = $version;
        $rubric->type = $type;
        $rubric->staffId = $staffId;
        $rubric->save();

        $rubricDetails = Rubric::where('fileName', $rubric->fileName)->orderBy('created_at', 'desc')->first();

        $rurbicId = $rubricDetails['rubricId'];

        self::storeRubric($rubric->fileName, $rurbicId);
        self::getRubric($rurbicId);

        return view('rubric.retrieveRubric', compact('type'));
    }

    public function getRubricTemplate() {
        $rubricTemplate = Rubric::orderBy('created_at', 'desc')->first();

        // $sysDate = new Carbon(date("Y-m-d H:i:s", strtotime('+8 hours')));
        // $rubricTemplateDate = new Carbon($rubricTemplate['created_at']);
        // $diff = $sysDate->diffInHours($rubricTemplateDate) . ':' . $sysDate->diff($rubricTemplateDate)->format('%I:%S');
        // $diffHours = explode(":", $diff);
        // $HoursValue = $diffHours[0];

        // if ($HoursValue <= 24) {
        //     Session::put('rubricTemplate', $rubricTemplate);
        // }
        Session::put('rubricTemplate', $rubricTemplate);

        return view('rubric.uploadRubric');
    }

    public function storeRubric($rubricFileName, $rubricId) {
        $rows = Excel::toArray(new UsersImport, $rubricFileName);

        foreach ($rows as $row) {
            foreach ($row as $ro) {
                if ($ro[0] != 'CLO' && isset($ro[0])) {
                    $artifact = new Artifact();
                    $artifact->CLO = $ro[0];
                    $artifact->description = $ro[1];
                    $artifact->totalMarks = $ro[2];
                    $artifact->rubricId = $rubricId;
                    $artifact->save();
                }

                $artifactDetail = Artifact::orderBy('artifactId', 'desc')->first();

                if ($ro[0] != 'CLO' && $ro[3] != 'Criteria' && isset($ro[3])) {
                    $criteria = new Criteria();
                    $criteria->criteriaName = $ro[3];
                    $criteria->description = $ro[4];
                    $criteria->poor = $ro[5];
                    $criteria->accomplished = $ro[6];
                    $criteria->good = $ro[7];
                    $criteria->artifactId = $artifactDetail['artifactId'];
                    $criteria->save();
                }
            }
        }
    }

    public function getRubric($rubricId) {
        $rubricLists = Rubric::all();
        $artifactLists = Artifact::all();
        $criteriaLists = Criteria::all();

        foreach ($rubricLists as $rubricList) {
            if ($rubricId == $rubricList['rubricId']) {
                foreach ($artifactLists as $artifactList) {
                    if ($rubricList['rubricId'] == $artifactList['rubricId']) {
                        $rubricId = $artifactList['rubricId'];
                    }
                }
            }
        }

        $artifactDetails = Artifact::where('rubricId', $rubricId)->get();

        Session::put('artifactDetails', $artifactDetails);
        Session::put('criteriaLists', $criteriaLists);
    }

    public function deleteRubricTemplate(Request $request) {
        $rubricTemplate = Session::get('rubricTemplate');
        $rubricTemplateDetail = Rubric::find($rubricTemplate['rubricId']);
        $artifactDetail = Artifact::where('rubricId', $rubricTemplate['rubricId'])->get();
		
		$rubricAssessmentDetail = Rubric_Assessment::where('rubricId', $rubricTemplate['rubricId'])->first();
		
		if (!empty($rubricAssessmentDetail)) {
			return redirect('removeRubricTemplate')->with('removeRubricTemplateFail', 'Rubric Template cannot be removed.');
		}
		
			foreach ($artifactDetail as $artifact) {
				$criteriaDetail = Criteria::where('artifactId', $artifact['artifactId'])->delete();
			}

			$artifactDetail = Artifact::where('rubricId', $rubricTemplate['rubricId'])->delete();
			$rubricTemplateDetail->delete();

			Session::forget('rubricTemplate');
			return redirect('removeRubricTemplate')->with('removeRubricTemplateSuccess', 'Rubric Template has been removed.');
    }

    public function getStudentAssessment() {
        $staffId = Session::get('staffId');
        $cohortId = Session::get('cohortId');

        $superviseTeamLists = array();
        $moderateTeamLists = array();
        $c = 0;
        $d = 0;

        $supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();

        $staffDetail = Staff::where('staffId', $supervisorCohort['staffId'])->get();
        $studentLists = Student::where('cohortId', $cohortId)->get();
        $projectLists = Project::all();

        $superviseTeams = Team::where('supervisor', $supervisorCohort['staffId'])->get();
        $moderateTeams = Team::where('moderator', $supervisorCohort['staffId'])->get();

        foreach ($superviseTeams as $superviseTeam) {
            foreach ($studentLists as $studentList) {
                if ($superviseTeam['teamId'] == $studentList['teamId']) {
                    $superviseTeamLists[$c] = $superviseTeam;
                    $c++;
                    break;
                }
            }
        }

        foreach ($moderateTeams as $moderateTeam) {
            foreach ($studentLists as $studentList) {
                if ($moderateTeam['teamId'] == $studentList['teamId']) {
                    $moderateTeamLists[$d] = $moderateTeam;
                    $d++;
                    break;
                }
            }
        }

        $superviseProjectLists = array();
        $moderateProjectLists = array();
        $a = 0;
        $b = 0;

        foreach ($superviseTeamLists as $superviseTeamList) {
            foreach ($projectLists as $projectList) {
                if ($superviseTeamList['projectCode'] == $projectList['projectCode']) {
                    $superviseProjectLists[$a] = $projectList['projectCode'] . " ~ " . $projectList['title'] . " *" . $superviseTeamList['teamId'] . "*";
                    $a++;
                }
            }
        }

        foreach ($moderateTeamLists as $moderateTeamList) {
            foreach ($projectLists as $projectList) {
                if ($moderateTeamList['projectCode'] == $projectList['projectCode']) {
                    $moderateProjectLists[$b] = $projectList['projectCode'] . " ~ " . $projectList['title'] . " *" . $moderateTeamList['teamId'] . "*";
                    $b++;
                }
            }
        }

        return array($staffDetail, $superviseProjectLists, $moderateProjectLists);
    }

    public function getStringBetween($str, $from, $to) {
        $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
        return substr($sub, 0, strpos($sub, $to));
    }

    //For student drop down list
    public function getStudents(Request $request) {
        $teamId = $request->get('teamId');
        $teamIdValue = self::getStringBetween($teamId, "*", "*");

        $studentNames = Student::where('teamId', $teamIdValue)->orderBy('studentName', 'asc')->get();
        return response()->json($studentNames);
    }

    public function studentRubric(Request $request) {
        $staffId = Session::get('staffId');
        $cohortId = Session::get('cohortId');

        $supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();

        $staffDetails = Staff::where('staffId', $supervisorCohort['staffId'])->get();

        if (Session::has('identifier1')) {
            $role = Session::get('role');
        } else {
            $role = $request->get('role');
            Session::put('role', $role);
        }

        if (Session::has('identifier1')) {
            $studentId = Session::get('studentName');
            $projectCode = Session::get('projectCode');
        } else {
            if ($role == "supervisor") {
                $studentId = $request->get('superviseStudent');
                $pTitle = $request->get('superviseProject');
            } else {
                $studentId = $request->get('moderateStudent');
                $pTitle = $request->get('moderateProject');
            }

            $pCode = explode("~", $pTitle);
            $projectCode = $pCode[0];
            Session::put('studentName', $studentId);
            Session::put('projectCode', $projectCode);
        }

        $studentDetail = Student::where('studentId', $studentId)->first();
        $programmeId = $studentDetail['programmeId'];

        $programmeDetail = Programme::where('programmeId', $programmeId)->first();
        $programmeName = $programmeDetail['programmeName'];

        $projectDetail = Project::where('projectCode', $projectCode)->first();
        $projectTitle = $projectDetail['title'];

        $teamDetails = Team::where('projectCode', $projectCode)->get();

        foreach ($teamDetails as $team) {
            $supervisorId = $team['supervisor'];
            $moderatorId = $team['moderator'];
        }
		
        $supervisorDetail = Staff::where('staffId', $supervisorCohort['staffId'])->first();
        $moderatorDetail = Staff::where('staffId', $supervisorCohort['moderatorId'])->first();

        $supervisorName = $supervisorDetail['title'] . " " . $supervisorDetail['staffName'];
        $moderatorName = $moderatorDetail['title'] . " " . $moderatorDetail['staffName'];

        if (Session::has('identifier1')) {
            $rubricType = Session::get('rubricType');
        } else {
            $rubricType = $request->get('project');
            Session::put('rubricType', $rubricType);
        }

        Session::forget('identifier1');

        $cohortId = Session::get('cohortId');
        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();

        $project1RubricId = $cohortDetail['project1Rubric'];
        $project2RubricId = $cohortDetail['project2Rubric'];

        if ($rubricType == "projectI") {
            $rubricDetail = Rubric::where('rubricId', $project1RubricId)->first();
        } else {
            $rubricDetail = Rubric::where('rubricId', $project2RubricId)->first();
        }

        $rubricId = $rubricDetail['rubricId'];
        $rubricType = $rubricDetail['type'];

        // Compare if the rubricAssesment for student exists
        $studentAssessmentList = Rubric_Assessment::where('studentId', $studentId)->where('rubricId', $rubricId)->get();
        $artifactLists = Artifact::where('rubricId', $rubricId)->get();
        $criteriaLists = Criteria::all();

        // Create new rubricAssessment if no record found
        if ($studentAssessmentList->isEmpty()) {
            $rubricAssessment = new Rubric_Assessment();
            $rubricAssessment->rubricId = $rubricId;
            $rubricAssessment->studentId = $studentId;
            $rubricAssessment->save();

            $rubricAssessDetail1 = Rubric_Assessment::where('studentId', $studentId)->where('rubricId', $rubricId)->get();

            // Create new Rubric Artifact for a student
            foreach ($rubricAssessDetail1 as $rubricAssessDetail) {
                $rubricAssessId1 = $rubricAssessDetail['rubricAssessId'];
            }

            foreach ($artifactLists as $artifactList) {
                $rubricArtifact = new Rubric_Artifact();
                $rubricArtifact->artifactId = $artifactList['artifactId'];
                $rubricArtifact->rubricAssessId = $rubricAssessId1;
                $rubricArtifact->save();
            }

            // Create new Rubric Criteria for a student
            $rubricArtifactLists = Rubric_Artifact::where('rubricAssessId', $rubricAssessId1)->get();

            foreach ($artifactLists as $artifactList) {
                foreach ($rubricArtifactLists as $rubricArtifactList) {
                    if ($artifactList['artifactId'] == $rubricArtifactList['artifactId']) {
                        foreach ($criteriaLists as $criteriaList) {
                            if ($criteriaList['artifactId'] == $artifactList['artifactId']) {
                                $rubricCriteria = new Rubric_Criteria();
                                $rubricCriteria->criteriaId = $criteriaList['criteriaId'];
                                $rubricCriteria->rubricArtifactId = $rubricArtifactList['rubricArtifactId'];
                                $rubricCriteria->save();
                            }
                        }
                    }
                }
            }
        }

        // Get rubric contents to be displayed
        $rubricAssessDetails = Rubric_Assessment::where('studentId', $studentId)->where('rubricId', $rubricId)->get();
        // Retrieve rubric_Assessment again with existed record for storing "comment" in studentMark() function
        Session::put('rubricAssessDetails', $rubricAssessDetails);
        foreach ($rubricAssessDetails as $rubricAssessDetail) {
            $rubricArtifactDetails = Rubric_Artifact::where('rubricAssessId', $rubricAssessDetail['rubricAssessId'])->get();
            // $rubricAssessDetail->save();
        }

        $rubricCriteriaDetails = Rubric_Criteria::all();

        // Get final marks to be displayed
        $idArray = array();
        $finalMarkArray = array();
        $subtotalCLOArray = array();
        $grandTotal = 0;
        $a = 0;
        $t = 0;
        $k = 0;

        foreach ($rubricArtifactDetails as $rubricArtifactDetail) {
            foreach ($rubricCriteriaDetails as $rubricCriteriaDetail) {
                if ($rubricArtifactDetail['rubricArtifactId'] == $rubricCriteriaDetail['rubricArtifactId']) {
                    $finalMark = 0;
                    if ($rubricCriteriaDetail['markSupervisor'] !== null && $rubricCriteriaDetail['markModerator'] !== null) {
                        $finalMark = ($rubricCriteriaDetail['markSupervisor'] + $rubricCriteriaDetail['markModerator']) / 2;
                    } elseif ($rubricCriteriaDetail['markSupervisor'] === 0 || $rubricCriteriaDetail['markModerator'] === 0) {
                        $finalMark = ($rubricCriteriaDetail['markSupervisor'] + $rubricCriteriaDetail['markModerator']) / 2;
                    } elseif ($rubricCriteriaDetail['markSupervisor'] === null && $rubricCriteriaDetail['markModerator'] === null) {
                        $finalMark = 0;
                    } elseif ($rubricCriteriaDetail['markSupervisor'] === null) {
                        $finalMark = $rubricCriteriaDetail['markModerator'];
                    } elseif ($rubricCriteriaDetail['markModerator'] === null) {
                        $finalMark = $rubricCriteriaDetail['markSupervisor'];
                    }
                    $finalMarkArray[$t] = $finalMark;
                    $t++;
                }
            }
        }

        foreach ($rubricArtifactDetails as $rubricArtifactDetail) {
            $count = 0;
            foreach ($rubricCriteriaDetails as $rubricCriteriaDetail) {
                if ($rubricArtifactDetail['rubricArtifactId'] == $rubricCriteriaDetail['rubricArtifactId']) {
                    $count++;
                }
            }
            $idArray[$a] = $count;
            $a++;
        }

        for ($i = 0; $i < count($idArray); $i++) {
            $subtotalCLO = 0;
            for ($j = 1; $j <= $idArray[$i]; $j++) {
                $subtotalCLO += $finalMarkArray[$k];
                $k++;
            }
            $subtotalCLOArray[$i] = $subtotalCLO;
        }

        foreach ($subtotalCLOArray as $subtotal) {
            $grandTotal += $subtotal;
        }

        switch (true) {
            case $grandTotal >= 79.5:
                $grade = "A";
                break;
            case $grandTotal >= 74.5:
                $grade = "A-";
                break;
            case $grandTotal >= 69.5:
                $grade = "B+";
                break;
            case $grandTotal >= 64.5:
                $grade = "B";
                break;
            case $grandTotal >= 59.5:
                $grade = "B-";
                break;
            case $grandTotal >= 54.5:
                $grade = "C+";
                break;
            case $grandTotal >= 49.5:
                $grade = "C";
                break;
            default:
                $grade = "F";
                break;
        }

        $studentRubricArray = array();
        $studentRubricArray[0] = $idArray;
        $studentRubricArray[1] = $staffDetails;
        $studentRubricArray[2] = $role;
        $studentRubricArray[3] = $rubricType;
        $studentRubricArray[4] = $supervisorName;
        $studentRubricArray[5] = $moderatorName;
        $studentRubricArray[6] = $studentDetail;
        $studentRubricArray[7] = $programmeName;
        $studentRubricArray[8] = $projectTitle;
        $studentRubricArray[9] = $rubricAssessDetails;
        $studentRubricArray[10] = $rubricArtifactDetails;
        $studentRubricArray[11] = $rubricCriteriaDetails;
        $studentRubricArray[12] = $artifactLists;
        $studentRubricArray[13] = $criteriaLists;
        $studentRubricArray[14] = $finalMarkArray;
        $studentRubricArray[15] = $subtotalCLOArray;
        $studentRubricArray[16] = $grandTotal;
        $studentRubricArray[17] = $grade;
        Session::put('studentRubricArray', $studentRubricArray);

        return view('rubric.studentRubric', compact('idArray', 'staffDetails', 'role', 'rubricType', 'supervisorName', 'moderatorName', 'studentDetail', 'programmeName', 'projectTitle', 'rubricAssessDetails', 'rubricArtifactDetails', 'rubricCriteriaDetails', 'artifactLists', 'criteriaLists', 'finalMarkArray', 'subtotalCLOArray', 'grandTotal', 'grade'));
    }

    public function studentMark(Request $request, $role) {
        $rubricCriteriaArray = Session::get('rubricCriteriaArray');

        $supervisorMarks = array();
        $moderatorMarks = array();
        $a = 0;
        $b = 0;
        $rubricCriteriaLists = Rubric_Criteria::all();


        for ($i = 0; $i < count($rubricCriteriaArray); $i++) {
            $id = "s_" . $rubricCriteriaArray[$i];
            $supervisorMarks[$i] = $request->get($id);
        }

        for ($i = 0; $i < count($rubricCriteriaArray); $i++) {
            $id = "m_" . $rubricCriteriaArray[$i];
            $moderatorMarks[$i] = $request->get($id);
        }

        if ($role == "supervisor") {
            foreach ($rubricCriteriaArray as $rubricCriteriaId) {
                foreach ($rubricCriteriaLists as $rubricCriteriaList) {
                    if ($rubricCriteriaId == $rubricCriteriaList['rubricCriteriaId']) {
                        $rubricCriteriaList['markSupervisor'] = $supervisorMarks[$a];
                        $rubricCriteriaList->save();
                        $a++;
                    }
                }
            }
        }

        foreach ($rubricCriteriaArray as $rubricCriteriaId) {
            foreach ($rubricCriteriaLists as $rubricCriteriaList) {
                if ($rubricCriteriaId == $rubricCriteriaList['rubricCriteriaId']) {
                    $rubricCriteriaList['markModerator'] = $moderatorMarks[$b];
                    $rubricCriteriaList->save();
                    $b++;
                }
            }
        }

        $rubricAssessDetails = Session::get('rubricAssessDetails');

        foreach ($rubricAssessDetails as $rubricAssessDetail) {
            $rubricAssessDetail->comment = $request->get('comment');
            if ($role == "supervisor") {
                $rubricAssessDetail->dateBySupervisor = date("Y-m-d H:i:s", strtotime('+8 hours'));
            } else {
                $rubricAssessDetail->dateByModerator = date("Y-m-d H:i:s", strtotime('+8 hours'));
            }
            $rubricAssessDetail->save();
        }

        // To differentiate if the student is in the Session
        Session::put('identifier1', 'identifier1');

		Session::flash('success', 'Assessment details are saved.');
        return self::studentRubric($request);
    }

    public function printStudentRubric() {
        require_once('tcpdf/tcpdf.php');

        $view = View::make('rubric.studentRubricPDF');
        $html = $view->render();

        $title = "Student Rubric";
        $pdfName = "Student Rubric";

        PDF::SetCreator(PDF_CREATOR);
        PDF::SetTitle($title);
        PDF::SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        PDF::SetDefaultMonospacedFont('helvetica');
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        PDF::SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
        PDF::setPrintHeader(false);
        PDF::setPrintFooter(false);
        PDF::SetAutoPageBreak(TRUE, 10);
        PDF::SetFont('helvetica', '', 11);
        PDF::AddPage('L', 'A4');

        PDF::writeHTML($html, true, false, false, false, '');
        PDF::Output($pdfName . '.pdf', 'I');
    }

    public function getMarkSummaryProject(Request $request) {
        $staffId = Session::get('staffId');
        $cohortId = Session::get('cohortId');
        $type = Session::get('type');

        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();

        if ($type == "Project 1") {
            $rubricDetail = Rubric::where('rubricId', $cohortDetail['project1Rubric'])->first();
            $type = "Project I";
        } else {
            $rubricDetail = Rubric::where('rubricId', $cohortDetail['project2Rubric'])->first();
            $type = "Project II";
        }

        $rubricId = $rubricDetail['rubricId'];
        $artifactCLO = Artifact::where('rubricId', $rubricId)->get();

        $staffPairing = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();
        $supervisor = Staff::where('staffId', $staffPairing['staffId'])->first();
        $moderator = Staff::where('staffId', $staffPairing['moderatorId'])->first();

        $teamLists = Team::where('supervisor', $staffId)->get();
        $studentLists = Student::where('cohortId', $cohortId)->get();
        $projectLists = Project::all();
        $rubricAssessLists = Rubric_Assessment::where('rubricId', $rubricId)->get();
        $rubricArtifactLists = Rubric_Artifact::all();
        $rubricCriteriaLists = Rubric_Criteria::all();

        $teamStudent = new Student();
        $teamStudentLists = array();
        $teamProjectArray = array();
        $studentMarkArray = array();
        $studentTotalMarkArray = array();
        $count = array();
        $b = 0;
        $c = 0;
        $d = 0;
        $e = 0;

        foreach ($teamLists as $teamList) {
            foreach ($studentLists as $studentList) {
                foreach ($rubricAssessLists as $rubricAssessList) {
                    if ($teamList['teamId'] == $studentList['teamId'] && $studentList['studentId'] == $rubricAssessList['studentId']) {
                        $teamStudent = $studentList;
                        $teamStudentLists[$c] = $teamStudent;
                        $c++;
                    }
                }
            }
        }

        $count1 = array();
        $count2 = array();

        // Sort studentName in ascending order
        foreach ($teamStudentLists as $key => $row) {
            $count1[$key] = $row['programmeId'];
            $count2[$key] = $row['studentName'];
        }

        if ($count1 != "" && $count2 != "") {
            array_multisort($count1, SORT_ASC, $count2, SORT_ASC, $teamStudentLists);
        }

        foreach ($teamStudentLists as $teamStudentList) {
            foreach ($teamLists as $teamList) {
                if ($teamStudentList['teamId'] == $teamList['teamId']) {
                    foreach ($projectLists as $projectList) {
                        if ($projectList['projectCode'] == $teamList['projectCode']) {
                            $teamProjectArray[$e] = $projectList['title'];
                            $e++;
                        }
                    }
                }
            }
        }

        foreach ($teamStudentLists as $teamStudentList) {
            foreach ($rubricAssessLists as $rubricAssessList) {
                if ($rubricAssessList['studentId'] == $teamStudentList['studentId']) {
                    $a = 0;
                    foreach ($rubricArtifactLists as $rubricArtifactList) {
                        if ($rubricArtifactList['rubricAssessId'] == $rubricAssessList['rubricAssessId']) {
                            $CLOmarks = 0;
                            foreach ($rubricCriteriaLists as $rubricCriteriaList) {
                                if ($rubricCriteriaList['rubricArtifactId'] == $rubricArtifactList['rubricArtifactId']) {
                                    if ($rubricCriteriaList['markSupervisor'] !== null && $rubricCriteriaList['markModerator'] !== null) {
                                        $marks = ($rubricCriteriaList['markSupervisor'] + $rubricCriteriaList['markModerator']) / 2;
                                    } elseif ($rubricCriteriaList['markSupervisor'] === 0 || $rubricCriteriaList['markModerator'] === 0) {
                                        $marks = ($rubricCriteriaList['markSupervisor'] + $rubricCriteriaList['markModerator']) / 2;
                                    } elseif ($rubricCriteriaList['markSupervisor'] === null && $rubricCriteriaList['markModerator'] === null) {
                                        $marks = 0;
                                    } elseif ($rubricCriteriaList['markSupervisor'] === null) {
                                        $marks = $rubricCriteriaList['markModerator'];
                                    } elseif ($rubricCriteriaList['markModerator'] === null) {
                                        $marks = $rubricCriteriaList['markSupervisor'];
                                    }
                                    $CLOmarks += $marks;
                                }
                            }
                            $CLOarray[$a] = $CLOmarks;
                            $a++;
                        }
                    }
                    $studentMarkArray[$b] = $CLOarray;
                    $b++;

                    $totalMark = 0;
                    foreach ($CLOarray as $CLO) {
                        $totalMark += $CLO;
                    }
                    $studentTotalMarkArray[$d] = $totalMark;
                    $d++;
                }
            }
        }

        $markSummaryArray = array();
        $markSummaryArray[0] = $type;
        $markSummaryArray[1] = $cohortId;
        $markSummaryArray[2] = $supervisor;
        $markSummaryArray[3] = $moderator;
        $markSummaryArray[4] = $artifactCLO;
        $markSummaryArray[5] = $teamStudentLists;
        $markSummaryArray[6] = $teamProjectArray;
        $markSummaryArray[7] = $studentMarkArray;
        $markSummaryArray[8] = $studentTotalMarkArray;
        Session::put('markSummaryArray', $markSummaryArray);

        Session::forget($type);

        if ($request->get('generatePDF')) {
            return self::printMarkSummary($type);
        }

        return view('rubric.markSummaryProject', compact('type', 'cohortId', 'supervisor', 'moderator', 'artifactCLO', 'teamStudentLists', 'teamProjectArray', 'studentMarkArray', 'studentTotalMarkArray'));
    }

    public function printMarkSummary($type) {
        require_once('tcpdf/tcpdf.php');

        $view = View::make('rubric.markSummaryPDF');
        $html = $view->render();

        if ($type == "Project I") {
            $title = "Mark Summary Report - BACS3403 Project I";
            $pdfName = "MarkSummaryReport_ProjectI";
        } else {
            $title = "Mark Summary Report - BACS3413 Project II";
            $pdfName = "MarkSummaryReport_ProjectII";
        }

        PDF::SetCreator(PDF_CREATOR);
        PDF::SetTitle($title);
        PDF::SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        PDF::SetDefaultMonospacedFont('helvetica');
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        PDF::SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
        PDF::setPrintHeader(false);
        PDF::setPrintFooter(false);
        PDF::SetAutoPageBreak(TRUE, 10);
        PDF::SetFont('helvetica', '', 11);
        PDF::AddPage('L', 'A4');

        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output($pdfName . '.pdf', 'I');
    }

    public function getTemplateVersion() {
        $project1RubricDetails = Rubric::where('type', 'Project I')->orderBy('created_at', 'desc')->get();
        $project2RubricDetails = Rubric::where('type', 'Project II')->orderBy('created_at', 'desc')->get();
        $form2TemplateDetails = Form_Template::where('type', 'Form 2')->orderBy('created_at', 'desc')->get();
        $form3TemplateDetails = Form_Template::where('type', 'Form 3')->orderBy('created_at', 'desc')->get();
        $form4iTemplateDetails = Form_Template::where('type', 'Form 4 (i)')->orderBy('created_at', 'desc')->get();
        $form4iiTemplateDetails = Form_Template::where('type', 'Form 4 (ii)')->orderBy('created_at', 'desc')->get();

        return view('rubric.templateVersion', compact('project1RubricDetails', 'project2RubricDetails', 'form2TemplateDetails', 'form3TemplateDetails', 'form4iTemplateDetails', 'form4iiTemplateDetails'));
    }

    public function storeTemplateVersion(Request $request) {
        $project1Id = $request->get('project1');
        $project2Id = $request->get('project2');
        $form2Id = $request->get('form2');
        $form3Id = $request->get('form3');
        $form4iId = $request->get('form4i');
        $form4iiId = $request->get('form4ii');

        $cohortId = Session::get('cohortId');
        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();
        $cohortDetail->project1Rubric = $project1Id;
        $cohortDetail->project2Rubric = $project2Id;
        $cohortDetail->form2TemplateId = $form2Id;
        $cohortDetail->form3TemplateId = $form3Id;
        $cohortDetail->form4iTemplateId = $form4iId;
        $cohortDetail->form4iiTemplateId = $form4iiId;
        $cohortDetail->save();

        return redirect('templateVersion')->with('templateVersionSuccess', 'Template Version is selected successfully');
    }

    public function downloadProject1Rubric(Request $request) {
		if (Session::has('cohortId')) {
			$cohortId = Session::get('cohortId');
		} else {
			$studentId = Session::get('studentId');
			$studentDetail = Student::where('studentId', $studentId)->first();
			$cohortId = $studentDetail['cohortId'];
		}
		
        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();

        $project1Rubric = Rubric::where('rubricId', $cohortDetail['project1Rubric'])->first();

        if (!empty($project1Rubric)) {
            $isRubricTemplateExist = Storage::disk('local')->exists($project1Rubric->fileName);

            if ($isRubricTemplateExist) {
                return Storage::download($project1Rubric->fileName);
            }
        } else {
            return redirect('errorProject1Rubric')->with('errorDlProject1Rubric', 'Project I Assessment Rubric is not ready to be downloaded.');
        }
    }

    public function downloadProject2Rubric(Request $request) {
        if (Session::has('cohortId')) {
			$cohortId = Session::get('cohortId');
		} else {
			$studentId = Session::get('studentId');
			$studentDetail = Student::where('studentId', $studentId)->first();
			$cohortId = $studentDetail['cohortId'];
		}
		
        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();

        $project2Rubric = Rubric::where('rubricId', $cohortDetail['project2Rubric'])->first();

        if (!empty($project2Rubric)) {
            $isRubricTemplateExist = Storage::disk('local')->exists($project2Rubric->fileName);

            if ($isRubricTemplateExist) {
                return Storage::download($project2Rubric->fileName);
            }
        } else {
            return redirect('errorProject2Rubric')->with('errorDlProject2Rubric', 'Project II Assessment Rubric is not ready to be downloaded.');
        }
    }

}
