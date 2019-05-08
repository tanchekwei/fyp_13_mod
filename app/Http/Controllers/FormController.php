<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Artifact;
use App\Cohort;
use App\Criteria;
use App\Form1;
use App\Form2;
use App\Form3;
use App\Form3_Requirement;
use App\Form_Template;
use App\Programme;
use App\Project;
use App\Project_Requirement;
use App\Rubric_Assessment;
use App\Rubric_Artifact;
use App\Rubric_Criteria;
use App\Staff;
use App\Student;
use App\Supervisor_Cohort;
use App\Team;
use Carbon\Carbon;
use Excel;
use Log;
use PDF;
use Session;
use Storage;
use View;
use App\Imports\UsersImport;

class FormController extends Controller {

    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function formIndex(Request $request) {
		if (Session::has('cohortId')) {
			$cohortDetail = Cohort::where('cohortId', Session::get('cohortId'))->first();
		}
		
        if ($request->get('form1')) {
            return self::getForm1();
        } elseif ($request->get('form2')) {
            return self::getForm2();
        } elseif ($request->get('form3Assessment') && Session::has('cohortId')) {
			$staffId = Session::get('staffId');
			$cohortId = Session::get('cohortId');
			
			$supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();
		
			if(empty($supervisorCohort)) {
				Session::flash('fail', 'Form 3 is not ready (Staff Pairing is not done) Please refer to Faculty Admin for further action.');
				return view('form.formMain');
			}
			
			if(empty($cohortDetail['form3TemplateId'])) {
				Session::flash('fail', 'Form 3 is not ready (Form 3 Template has not been chosen) Please refer to Faculty Admin for further action.');
				return view('form.formMain');
			}
			
            $form3Array = self::getStudentAssessment();
            return view('form.form3Assessment', compact('form3Array'));
        } elseif ($request->get('form4')) {
            return view('form.form4');
        } elseif ($request->get('formTemplate')) {
            return self::getFormTemplate();
        } elseif ($request->get('form2StudentProposal') && Session::has('cohortId')){
            return self::getStudentProposal();
        } else {
			return redirect ('showallcohort');
		}
    }

    public function getForm1() {
        $studentId = Session::get('studentId');

        $studentDetail = Student::where('studentId', $studentId)->first();

        $cohortDetail = Cohort::where('cohortId', $studentDetail['cohortId'])->first();
        $project1Year = substr($cohortDetail['project1startingDate'], 0, 4);
        $cohortSession = $project1Year + 1;
        $academicSession = $project1Year . "/" . $cohortSession;

        $programmeId = $studentDetail['programmeId'];
        $programmeDetail = Programme::where('programmeId', $programmeId)->first();

        $form1Detail = Form1::where('studentId', $studentId)->first();

        return view('form.form1', compact('studentDetail', 'programmeDetail', 'form1Detail', 'academicSession'));
    }

    public function storeForm1(Request $request) {
        $studentId = Session::get('studentId');

        $form1Detail = Form1::where('studentId', $studentId)->get();

        if (!$form1Detail->isEmpty()) {
            foreach ($form1Detail as $form1) {
                if ($studentId == $form1['studentId']) {
                    $form1->termContact = $request->get('hpTerm');
                    $form1->permanentContact = $request->get('hpPermanent');
                    $form1->emailPersonal = $request->get('emailPersonal');
                    $form1->save();
                }
            }
        } else {
            $form1 = new Form1();
            $form1->studentId = $studentId;
            $form1->termContact = $request->get('hpTerm');
            $form1->permanentContact = $request->get('hpPermanent');
            $form1->emailPersonal = $request->get('emailPersonal');
            $form1->save();
        }

        $studentDetail = Student::where('studentId', $studentId)->first();

        if ($studentDetail['phoneNo'] != $request->get('hpNo')) {
            $studentDetail['phoneNo'] = $request->get('hpNo');
            $studentDetail->save();
        }

		Session::flash('success', 'Form 1 details are saved.');
        return self::getForm1();
    }

    public function getForm2Array() {
        $studentId = Session::get('studentId');

        if (Session::has('form2Array')) {
            Session::forget('form2Array');
        }

        $studentDetail = Student::where('studentId', $studentId)->first();
        $teamDetail = Team::where('teamId', $studentDetail['teamId'])->first();
        $projectDetail = Project::where('projectCode', $teamDetail['projectCode'])->first();
        $form2Detail = Form2::where('studentId', $studentId)->get();

        $form2Array = array();
        $form2Array[0] = $studentDetail;
        $form2Array[1] = $teamDetail;
        $form2Array[2] = $projectDetail;
        $form2Array[3] = $form2Detail;

        Session::put('form2Array', $form2Array);
    }

    public function getForm2() {
        self::getForm2Array();
        return view('form.form2');
    }

    public function downloadProposalTemplate(Request $request) {
        $studentId = Session::get('studentId');
        $studentDetail = Student::where('studentId', $studentId)->first();

        $cohortDetail = Cohort::where('cohortId', $studentDetail['cohortId'])->first();
        $form2Template = Form_Template::where('formTemplateId', $cohortDetail['form2TemplateId'])->first();

        if (!empty($form2Template)) {
            $isFormTemplateExist = Storage::disk('local')->exists($form2Template->fileName);

            if ($isFormTemplateExist) {
                return Storage::download($form2Template->fileName);
            }
        } else {
            return redirect('errorForm2')->with('errorDlForm2', 'Form 2 is not ready to be downloaded.');
        }
    }

    public function storeProposal(Request $request) {
        $studentId = Session::get('studentId');

        $studentDetail = Student::where('studentId', $studentId)->first();
        $studentDetail->individualTitle = $request->get('individualTitle');
        $studentDetail->save();

		if ($studentDetail['teamId'] !== NULL) {
			$teamDetail = Team::where('teamId', $studentDetail['teamId'])->first();
			$teamDetail->competitionName = $request->get('competitionName');
			$teamDetail->save();
			
			if ($teamDetail['projectCode'] !== NULL) {
				$projectDetail = Project::where('projectCode', $teamDetail['projectCode'])->first();
				$projectDetail->clientName = $request->get('clientName');
				$projectDetail->save();
			}
		}

        if (count($request->all()) == 5) {
            $validator = Validator::make($request->all(), [
                        'proposal' => 'mimes:doc,docx,pdf'
            ]);

            if ($validator->fails()) {
                return redirect('invalidProposalUpload')
                                ->withErrors($validator)
                                ->withInput();
            }

            $folderName = "Project Proposals/" . $studentId;
            $path = $request->file('proposal')->storeAs($folderName, $studentDetail['studentName'] . "_" . $request->proposal->getClientOriginalName());

            $form2 = new Form2();
            $form2->studentId = $studentId;
            $form2->fileName = $path;
            $form2->save();
        }

        self::getForm2Array();

        return redirect('uploadForm2')->with('uploadProposalSuccess', 'Proposal details are saved.');
    }

    public function deleteProposal(Request $request) {
        $studentId = Session::get('studentId');
        $form2Detail = Form2::find($studentId);
        $form2Detail->delete();

        self::getForm2Array();

        return redirect('unsubmitForm2')->with('unsubmitProposalSuccess', 'Proposal had been unsubmitted');
    }

    public function downloadForm4iTemplate(Request $request) {
        $studentId = Session::get('studentId');

        $studentDetail = Student::where('studentId', $studentId)->first();

        $cohortDetail = Cohort::where('cohortId', $studentDetail['cohortId'])->first();

        $form4Template = Form_Template::where('formTemplateId', $cohortDetail['form4iTemplateId'])->first();

        if (!empty($form4Template)) {
            $isFormTemplateExist = Storage::disk('local')->exists($form4Template->fileName);

            if ($isFormTemplateExist) {
                return Storage::download($form4Template->fileName);
            }
        } else {
            return redirect('errorForm4i')->with('errorDlForm4i', 'Form 4 (i) is not ready to be downloaded.');
        }
    }

    public function downloadForm4iiTemplate(Request $request) {
        $studentId = Session::get('studentId');

        $studentDetail = Student::where('studentId', $studentId)->first();

        $cohortDetail = Cohort::where('cohortId', $studentDetail['cohortId'])->first();

        $form4Template = Form_Template::where('formTemplateId', $cohortDetail['form4iiTemplateId'])->first();

        if (!empty($form4Template)) {
            $isFormTemplateExist = Storage::disk('local')->exists($form4Template->fileName);

            if ($isFormTemplateExist) {
                return Storage::download($form4Template->fileName);
            }
        } else {
            return redirect('errorForm4ii')->with('errorDlForm4ii', 'Form 4 (ii) is not ready to be downloaded.');
        }
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
        $moderateTeams= Team::where('moderator', $supervisorCohort['staffId'])->get();

        foreach($superviseTeams as $superviseTeam) {
            foreach($studentLists as $studentList) {
                if ($superviseTeam['teamId'] == $studentList['teamId']) {
                    $superviseTeamLists[$c] = $superviseTeam;
                    $c++;
                    break;
                }
            }
        }

        foreach($moderateTeams as $moderateTeam) {
            foreach($studentLists as $studentList) {
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

    public function createForm3(Request $request) {
        $staffId = Session::get('staffId');
        $cohortId = Session::get('cohortId');

        $supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();

        $staffDetails = Staff::where('staffId', $supervisorCohort['staffId'])->get();

        if (Session::has('identifier2')) {
            $role = Session::get('role2');
        } else {
            $role = $request->get('role');
            Session::put('role2', $role);
        }

        if (Session::has('identifier2')) {
            $studentId = Session::get('studentName2');
            $projectCode = Session::get('projectCode2');
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
            Session::put('studentName2', $studentId);
            Session::put('projectCode2', $projectCode);
        }

        Session::forget('identifier2');

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

        $cohortDetail = Cohort::where('cohortId', $cohortId)->first();
        $rubricId = $cohortDetail['project1Rubric'];
        $form3TemplateId = $cohortDetail['form3TemplateId'];
        $form3Template = Form_Template::where('formTemplateId', $form3TemplateId)->get();

        $projectRequirementLists = Project_Requirement::where('formTemplateId', $form3TemplateId)->get();

        // Compare if the form3 for student exists
        $form3AssessmentList = Form3::where('studentId', $studentId)->get();

        // Create new Form3 if no record found
        if ($form3AssessmentList->isEmpty()) {
            $form3 = new Form3();
            $form3->studentId = $studentId;
            $form3->formTemplateId = $form3TemplateId;
            $form3->save();

            // Create new Form3_Requirement for a student
            foreach ($projectRequirementLists as $projectRequirementList) {
                $form3Requirement = new Form3_Requirement();
                $form3Requirement->requirementId = $projectRequirementList['requirementId'];
                $form3Requirement->studentId = $studentId;
                $form3Requirement->save();
            }
        }

        // Get form3_requirements contents to be displayed
        $form3AssessmentDetail = Form3::where('studentId', $studentId)->first();
        // Retrieve Form3 again with existed record for storing "feedback"
        Session::put('form3AssessmentDetail', $form3AssessmentDetail);

        $form3RequirementDetails = Form3_Requirement::where('studentId', $studentId)->get();

        // Compare if the rubricAssesment of Project I for student exists
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

        // Get moderator marks in Rubric_Criteria table if record exists
        $artifactDetail = Artifact::where('rubricId', $rubricId)->where('CLO', 1)->first();
        $studentAssessmentDetail = Rubric_Assessment::where('studentId', $studentId)->where('rubricId', $rubricId)->first();
        $studentArtifactDetail = Rubric_Artifact::where('rubricAssessId', $studentAssessmentDetail['rubricAssessId'])->where('artifactId', $artifactDetail['artifactId'])->first();
        $studentCriteriaDetails = Rubric_Criteria::where('rubricArtifactId', $studentArtifactDetail['rubricArtifactId'])->get();

        // Calculate total marks
        $finalMark = 0;

        foreach ($studentCriteriaDetails as $studentCriteriaDetail) {
            if ($studentCriteriaDetail['markModerator'] !== null) {
                $finalMark += $studentCriteriaDetail['markModerator'];
            } else {
                $finalMark += 0;
            }
        }

        $form3PdfArray = array();
        $form3PdfArray[0] = $staffDetails;
        $form3PdfArray[1] = $role;
        $form3PdfArray[2] = $cohortId;
        $form3PdfArray[3] = $supervisorName;
        $form3PdfArray[4] = $moderatorName;
        $form3PdfArray[5] = $studentDetail;
        $form3PdfArray[6] = $programmeName;
        $form3PdfArray[7] = $projectDetail;
        $form3PdfArray[8] = $projectRequirementLists;
        $form3PdfArray[9] = $form3AssessmentDetail;
        $form3PdfArray[10] = $form3RequirementDetails;
        $form3PdfArray[11] = $criteriaLists;
        $form3PdfArray[12] = $studentCriteriaDetails;
        $form3PdfArray[13] = $finalMark;
        Session::put('form3PdfArray', $form3PdfArray);

        return view ('form.form3', compact('staffDetails', 'role', 'cohortId', 'supervisorName', 'moderatorName',
        'studentDetail', 'programmeName', 'projectDetail', 'projectRequirementLists', 'form3AssessmentDetail',
        'form3RequirementDetails', 'criteriaLists', 'studentCriteriaDetails', 'finalMark'));

    }

    public function storeForm3(Request $request, $role) {
        $requirementIdArray = Session::get('requirementIdArray');

        $requirementIds = array();
        $a = 0;

        $form3RequirementLists = Form3_Requirement::all();

        for ($i = 0; $i < count($requirementIdArray); $i++) {
            $id = $requirementIdArray[$i];

            if ($request->get($id) == '') {
                $requirementIds[$i] = "Not Complied";
            } else {
                $requirementIds[$i] = "Complied";
            }
        }

        foreach ($requirementIdArray as $requirementId) {
            foreach ($form3RequirementLists as $form3RequirementList) {
                if ($requirementId == $form3RequirementList['form3RequirementId']) {
                    $form3RequirementList->comply = $requirementIds[$a];
                    $form3RequirementList->save();
                    $a++;
                }
            }
        }

        $form3AssessmentDetail = Session::get('form3AssessmentDetail');

        $form3AssessmentDetail->feedbackComment = $request->get('changesRecommended');
        $form3AssessmentDetail->feedbackAction = $request->get('actionTaken');
        if ($role == "supervisor") {
            $form3AssessmentDetail->dateBySupervisor = date("Y-m-d H:i:s", strtotime('+8 hours'));
        } else {
            $form3AssessmentDetail->dateByModerator = date("Y-m-d H:i:s", strtotime('+8 hours'));
        }
        $form3AssessmentDetail->save();

        $proposalCriteriaArray = Session::get('proposalCriteriaArray');
        $proposalCriteriaMark = array();
        $rubricCriteriaLists = Rubric_Criteria::all();
        $p = 0;

        for ($i = 0; $i < count($proposalCriteriaArray); $i++) {
            $proposalCriteriaMark[$i] = $request->get($proposalCriteriaArray[$i]);
        }

        foreach ($proposalCriteriaArray as $proposalCriteriaId) {
            foreach ($rubricCriteriaLists as $rubricCriteriaList) {
                if ($proposalCriteriaId == $rubricCriteriaList['rubricCriteriaId']) {
                    $rubricCriteriaList['markModerator'] = $proposalCriteriaMark[$p];
                    $rubricCriteriaList->save();
                    $p++;
                }
            }
        }

        // To differentiate if the student is in the Session
        Session::put('identifier2', 'identifier2');

		Session::flash('success', 'Form 3 details are saved.');
        return self::createForm3($request);
    }

    public function printForm3PDF() {
        require_once('tcpdf/tcpdf.php');

        $view = View::make('form.form3PDF');
        $html = $view->render();

        $title = "Form 3";
        $pdfName = "Form 3";

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

    public function getStudentProposal() {
        $staffId = Session::get('staffId');
        $cohortId = Session::get('cohortId');

        $supervisorTeams = array();
        $moderatorTeams = array();
        $a = 0;
        $b = 0;

        $supervisorCohort = Supervisor_Cohort::where('staffId', $staffId)->where('cohortId', $cohortId)->first();
		
		if(empty($supervisorCohort)) {
			Session::flash('fail', 'Student Proposal is not ready to be accessed (Staff Pairing is not done) Please refer to Faculty Admin for further action.');
			return view('form.formMain');
		}

        $superviseTeamLists = Team::where('supervisor', $supervisorCohort['staffId'])->get();
        $moderateTeamLists = Team::where('moderator', $supervisorCohort['staffId'])->get();

        $projectLists = Project::all();
        $studentLists = Student::where('cohortId', $cohortId)->get();
        $form2Proposals = Form2::all();

        foreach($superviseTeamLists as $superviseTeamList) {
            foreach($studentLists as $studentList) {
                if ($superviseTeamList['teamId'] == $studentList['teamId']) {
                    $supervisorTeams[$a] = $superviseTeamList;
                    $a++;
                    break;
                }
            }
        }

        foreach($moderateTeamLists as $moderateTeamList) {
            foreach($studentLists as $studentList) {
                if ($moderateTeamList['teamId'] == $studentList['teamId']) {
                    $moderatorTeams[$b] = $moderateTeamList;
                    $b++;
                    break;
                }
            }
        }

        return view ('form.downloadProposal', compact('supervisorTeams', 'moderatorTeams', 'form2Proposals', 'studentLists', 'projectLists'));
    }

    public function downloadSuperviseProposal(Request $request) {
        $superviseStudentArray = Session::get('superviseStudentArray');
        $form2Lists = Form2::all();
        $fileArray = array();
        $a = 0;

        for ($i = 0; $i < count($superviseStudentArray); $i++) {
            foreach ($form2Lists as $form2List) {
                if ($superviseStudentArray[$i] == $form2List['studentId']) {
                    if ($request->get($superviseStudentArray[$i]) == 'on') {
                        $fileArray[$a] = $form2List['fileName'];
                        $a++;
                    }
                }
            }
        }

        if ($fileArray == "") {
            return self::getStudentProposal();
        }

        return self::createZipFile($fileArray);

    }

    public function downloadModerateProposal(Request $request) {
        $moderateStudentArray = Session::get('moderateStudentArray');
        $form2Lists = Form2::all();
        $fileArray = array();
        $a = 0;

        for ($i = 0; $i < count($moderateStudentArray); $i++) {
            foreach ($form2Lists as $form2List) {
                if ($moderateStudentArray[$i] == $form2List['studentId']) {
                    if ($request->get($moderateStudentArray[$i]) == 'on') {
                        $fileArray[$a] = $form2List['fileName'];
                        $a++;
                    }
                }
            }
        }

        if ($fileArray == "") {
            return self::getStudentProposal();
        }

        return self::createZipFile($fileArray);
    }

    public function createZipFile($fileArray) {
        $currentTime = date("d-m-Y H.i.s", strtotime('+8 hours'));

        $zipName = $currentTime . '.zip';
        $zipFilePath = 'C:\proposal\\' . $zipName;

        $zipper = new \Chumper\Zipper\Zipper;
        $zipper->make($zipFilePath);

        foreach ($fileArray as $file) {
            $zipper->add(storage_path() . '\app\\' .  str_replace( '\\', '/',$file));
        }

        $zipper->close();

        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );

        return response()->download($zipFilePath, $zipName, $headers);
    }

    public function getFormTemplate() {
        $formTemplate = Form_Template::orderBy('created_at', 'desc')->first();

        // $sysDate = new Carbon (date("Y-m-d H:i:s", strtotime('+8 hours')));
        // $formTemplateDate = new Carbon ($formTemplate['created_at']);
        // $diff = $sysDate->diffInHours($formTemplateDate) . ':' . $sysDate->diff($formTemplateDate)->format('%I:%S');
        // $diffHours = explode(":", $diff);
        // $HoursValue = $diffHours[0];

        // if ($HoursValue <= 24) {
        //     Session::put('formTemplate', $formTemplate);
        // } else {
        //     Session::forget('formTemplate');
        // }
        Session::put('formTemplate', $formTemplate);

        return view('form.uploadFormTemplate');
    }

    public function storeFormTemplate(Request $request) {
        $staffId = Session::get('staffId');

        if ($request->get('formType') == "form2") {
            $formType = "Form 2";
        } elseif ($request->get('formType') == "form3") {
            $formType = "Form 3";
        } elseif ($request->get('formType') == "form4i") {
            $formType = "Form 4 (i)";
        } else {
            $formType = "Form 4 (ii)";
        }

        if ($formType == "Form 3") {
            $validator = Validator::make($request->all(), [
                'form'   => 'mimes:xls,xlsx'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'form'   => 'mimes:doc,docx,pdf'
            ]);
        }

        if ($validator->fails()) {
            return redirect('invalidFormUpload')
                        ->withErrors($validator)
                        ->withInput();
        }

        $latestForm = Form_Template::where('type', $formType)->orderBy('created_at', 'desc')->first();

        if ($latestForm == "") {
            $version = 1;
        } else {
            $version = $latestForm['version'] + 1;
        }

        $folderName = "Form Template/".$formType;
        $path = $request->file('form')->storeAs($folderName, $version . "_" . $request->form->getClientOriginalName());

        $formTemplate = new Form_Template();
        $formTemplate->fileName = $path;
        $formTemplate->version = $version;
        $formTemplate->type = $formType;
        $formTemplate->staffId = $staffId;
        $formTemplate->save();

        if ($formType == "Form 3") {
            $formTemplateDetail = Form_Template::where('type', $formType)->where('fileName', $path)->orderBy('created_at', 'desc')->first();
            $cols = Excel::toArray(new UsersImport, $formTemplate->fileName);

            foreach ($cols as $col) {
                foreach ($col as $c) {
                    if ($c[0] != 'Project Requirement' && isset($c[0])) {
                        $projectRequirement = new Project_Requirement();
                        $projectRequirement->requirementName = $c[0];
                        $projectRequirement->description = $c[1];
                        $projectRequirement->formTemplateId = $formTemplateDetail['formTemplateId'];
                        $projectRequirement->save();
                    }
                }
            }
        }

        $formDetail = Form_Template::orderBy('created_at', 'desc')->first();
        Session::put('formTemplate', $formDetail);

        return redirect ('uploadFormTemplate')->with('uploadFormSuccess', 'Form has been uploaded');
    }

    public function deleteFormTemplate(Request $request) {
        $formTemplate = Session::get('formTemplate');
        $formTemplateDetail = Form_Template::find($formTemplate['formTemplateId']);
		
		$form3Detail = Form3::where('formTemplateId', $formTemplate['formTemplateId'])->first();
		
		if (!empty($form3Detail)) {
			return redirect('removeFormTemplate')->with('removeFormTemplateFail', 'Form 3 Template cannot be removed.');
		}

        if($formTemplate['type'] == "Form 3") {
            $projectRequirements = Project_Requirement::where('formTemplateId', $formTemplate['formTemplateId'])->delete();
        }

        $formTemplateDetail->delete();

        Session::forget('formTemplate');

        return redirect('removeFormTemplate')->with('removeFormTemplateSuccess', 'Form Template has been removed.');
    }

}
