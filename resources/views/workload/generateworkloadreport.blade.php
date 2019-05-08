@extends('layouts.app')
@section('title', 'Workload Report Page')
@section('module', 'Workload Page')
@section('content')
<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_cache_limiter('private, must-revalidate');
session_cache_expire(60);
session_start();
$GLOBALS['$databaseName'] = getenv('DB_DATABASE');
$GLOBALS['$usernameName'] = getenv('DB_USERNAME');
$GLOBALS['$passwordName'] = getenv('DB_PASSWORD');
date_default_timezone_set("Asia/Kuala_Lumpur");
if (isset($_POST["generatebysession_pdf"])) {
    require_once('tcpdf/tcpdf.php');
    $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("Generate Workload Report");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(TRUE, 10);
    $obj_pdf->SetFont('helvetica', '', 11);
    $obj_pdf->AddPage();
    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $sessionProjectI = "";
    $sessionProjectII = "";
    if (isset($_COOKIE['sessionProjectI'])) {
        $sessionProjectI = $_COOKIE['sessionProjectI'];
    } else {
        $sessionProjectI = "";
    }
    if (isset($_COOKIE['sessionProjectII'])) {
        $sessionProjectII = $_COOKIE['sessionProjectII'];
    } else {
        $sessionProjectII = "";
    }
    $totalStudentI = 0;
    $totalSupervisorI = 0;
    $totalStudentII = 0;
    $totalSupervisorII = 0;
    $content = '<h1 align="center">Workload Report</h1>'
            . '<h3 align="left">Generated Date: ' . date("Y/m/d") . ', Generated Time: ' . date("h:i:sa") . '</h3>'
            . '<h3 align="left">Workload Summary for</h3>'
            . '<h3 align="left">Project I In Session ' . $sessionProjectI . ' And Project II In Session ' . $sessionProjectII . ':</h3>';
    if (isset($_COOKIE['sessionProjectI'])) {
        $content .= '<h5>Total Workload of Project I In Session ' . $_COOKIE['sessionProjectI'] . '</h5>';

        $content .= '<table>'
                . '<thead>';
        $content .= '<tr>'
                . '<th width="5%">No</th>'
                . '<th width="15%">Staff ID</th>'
                . '<th width="50%">Staff Name</th>'
                . '<th width="10%">Total Students</th>'
                . '<th width="10%">Total Weeks</th>'
                . '<th width="10%">Total Hours</th>'
                . '</tr>';
        $content .= '</thead>'
                . '<tbody>';
        $countStaff = 1;
        $countcohort = 0;

        if (isset($_COOKIE['selectedStatus'])) {
            $selectedStatus = $_COOKIE['selectedStatus'];
            foreach ($fypstaff as $fypstaffdetail) {
                $completedStudent = 0;
                $countStudent = 0;
                //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                //$rowformula = mysqli_num_rows($sqlformula);
                $selectedstaffName = $fypstaffdetail['staffId'];

                if (isset($_SESSION['cohort'])) {
                    $_POST['cohort'] = $_SESSION['cohort'];


                    foreach ($_POST["cohort"] as $cohorts) {
                        if ($selectedStatus == 'All') {
                            $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='Completed' OR project1Session='New' OR project1Session='InProgress'");
                        } else {
                            $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='$selectedStatus'");
                        }
                        $rowcohort = mysqli_num_rows($sqlcohort);
                        $sqlstudent = mysqli_query($connect, "SELECT student.*, team.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$cohorts'");
                        $rowstudent = mysqli_num_rows($sqlstudent);
                        while ($rowcohort = mysqli_fetch_array($sqlcohort)) {
                            if ($rowcohort['cohortId'] == $cohorts) {
                                while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                    if ($fypstaffdetail['staffId'] == $rowstudent['supervisor']) {
                                        $countStudent++;
                                    }
                                    $totalStudentI++;
                                    if ($rowcohort['project1Session'] == 'Completed') {
                                        $completedStudent++;
                                    }
                                }
                            }
                            $countcohort++;
                        }
                    }
                }
                foreach ($workloadformula as $formula) {
                    $totalMinutes = $completedStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                    $totalWeeks = $formula['totalWeeks'];
                }

                if ($countStudent != 0) {
                    $totalSupervisorI++;
                }
                if ($countcohort != 0) {
                    if ($countStudent != 0) {
                        $content .= '<tr>'
                                . '<td width="5%">' . $countStaff . '</td>'
                                . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                                . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                                . '<td width="10%">' . $countStudent . '</td>'
                                . '<td width="10%">' . $totalWeeks . '</td>'
                                . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                                . '</tr>';
                        $countStaff++;
                    }
                }
            }
        }
        $content .= '</tbody>'
                . '</table>';
        $content .= '<h5>Total Supervisor: ' . $totalSupervisorI . ' Total Student: ' . $totalStudentI . '</h5>';
    }
    if (isset($_COOKIE['sessionProjectII'])) {
        $content .= '<h5>Total Workload of Project II In Session ' . $_COOKIE['sessionProjectII'] . '</h5>';
        $content .= '<table>'
                . '<thead>';
        $content .= '<tr>'
                . '<th width="5%">No</th>'
                . '<th width="15%">Staff ID</th>'
                . '<th width="50%">Staff Name</th>'
                . '<th width="10%">Total Students</th>'
                . '<th width="10%">Total Weeks</th>'
                . '<th width="10%">Total Hours</th>'
                . '</tr>';
        $content .= '</thead>'
                . '<tbody>';
        $countStaff = 1;

        if (isset($_COOKIE['selectedStatus'])) {
            $selectedStatus = $_COOKIE['selectedStatus'];
            foreach ($fypstaff as $fypstaffdetail) {
                $completedStudentII = 0;
                $countStudentII = 0;
                //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                //$rowformula = mysqli_num_rows($sqlformula);
                $selectedstaffName = $fypstaffdetail['staffId'];
                if (isset($_SESSION['cohort'])) {
                    $_POST['cohort'] = $_SESSION['cohort'];

                    foreach ($_POST["cohort"] as $cohorts) {
                        if ($selectedStatus == 'All') {
                            $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='Completed' OR project2Session='New' OR project2Session='InProgress'");
                        } else {
                            $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='$selectedStatus'");
                        }
                        $rowcohort = mysqli_num_rows($sqlcohort);
                        $sqlstudent = mysqli_query($connect, "SELECT student.*, team.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$cohorts'");
                        $rowstudent = mysqli_num_rows($sqlstudent);
                        while ($rowcohort = mysqli_fetch_array($sqlcohort)) {
                            if ($rowcohort['cohortId'] == $cohorts) {
                                while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                    if ($fypstaffdetail['staffId'] == $rowstudent['supervisor']) {
                                        $countStudentII++;
                                    }
                                    $totalStudentII++;
                                    if ($rowcohort['project2Session'] == 'Completed') {
                                        $completedStudentII++;
                                    }
                                }
                            }
                        }
                    }
                }
                foreach ($workloadformula as $formula) {
                    $totalMinutes = $completedStudentII * $formula['totalMinutes'] * $formula['totalWeeks'];
                    $totalWeeks = $formula['totalWeeks'];
                }

                if ($countStudentII != 0) {
                    $totalSupervisorII++;
                }
                if ($countStudentII != 0) {
                    $content .= '<tr>'
                            . '<td width="5%">' . $countStaff . '</td>'
                            . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                            . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                            . '<td width="10%">' . $countStudentII . '</td>'
                            . '<td width="10%">' . $totalWeeks . '</td>'
                            . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                            . '</tr>';
                    $countStaff++;
                }
            }
        }
        $content .= '</tbody>'
                . '</table>';
        $content .= '<h5>Total Supervisor: ' . $totalSupervisorII . ' Total Student: ' . $totalStudentII . '</h5>';
    }
    $content .= '<h5 align="left">=================================================================================================</h5>';
    $content .= '<br /><h3 align="left">Workload According to Project I and Project II:</h3>';
    $totalStudentI = 0;
    $totalSupervisorI = 0;
    $totalStudentII = 0;
    $totalSupervisorII = 0;
    $content .= '<h5 align="left">Project I:</h5>';
    if (isset($_COOKIE['selectedStatus'])) {
        if ($_COOKIE['selectedStatus'] == "All") {
            $sql = mysqli_query($connect, "SELECT * From cohort");
        } else {
            $selectedStatus = $_COOKIE['selectedStatus'];
            $sql = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='$selectedStatus'");
        }
        while ($row = mysqli_fetch_array($sql)) {
            if (isset($_SESSION['cohort'])) {
                $_POST['cohort'] = $_SESSION['cohort'];
                foreach ($_POST["cohort"] as $cohorts) {
                    $convertoString = $row['project1startingDate'];
                    $date = date_create($convertoString);
                    $result = $date->format('Y/m');
                    $row['project1startingDate'] = str_replace('/', '', $result);
                    if ($row['cohortId'] == $cohorts) {
                        $content .= '<h5 align="left">Cohort ID: ' . $row['cohortId'] . ' (Session: ' . $row['project1startingDate'] . ')</h5>'
                                . '<table>'
                                . '<thead>';
                        if (isset($_COOKIE['selectedCohort'])) {
                            unset($_COOKIE['selectedCohort']);
                            setcookie('selectedCohort', '', time() - 3600);
                        }
                        if (isset($_COOKIE['selectedSection'])) {
                            unset($_COOKIE['selectedSection']);
                            setcookie('selectedSection', '', time() - 3600);
                        }
                        $cookie_name = "selectedCohort";
                        $cookie_value = $row['cohortId'];
                        $cookie_section = "selectedSection";
                        $cookie_sectionvalue = "Project I";
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                        setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                        $content .= '<tr>'
                                . '<th width="5%">No</th>'
                                . '<th width="15%">Staff ID</th>'
                                . '<th width="50%">Staff Name</th>'
                                . '<th width="10%">Total Students</th>'
                                . '<th width="10%">Total Weeks</th>'
                                . '<th width="10%">Total Hours</th>'
                                . '</tr>';
                        $content .= '</thead>'
                                . '<tbody>';
                        $i = 1;
                        $ttlStudentI = 0;
                        $ttlSupervisorI = 0;
                        foreach ($fypstaff as $fypstaffdetail) {
                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                            //$rowformula = mysqli_num_rows($sqlformula);
                            $selectedcohortID = $row['cohortId'];
                            $selectedstaffName = $fypstaffdetail['staffId'];
                            $countStudent = 0;
                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                            $rowstudent = mysqli_num_rows($sqlstudent);
                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                $countStudent++;
                                $totalStudentI++;
                                $ttlStudentI++;
                            }
                            if ($row['project1Session'] == 'Completed') {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            } else {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = 0;
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            }

                            if ($countStudent != 0) {
                                $totalSupervisorI++;
                                $ttlSupervisorI++;
                            }
                            if ($countStudent != 0) {
                                $content .= '<tr>'
                                        . '<td width="5%">' . $i . '</td>'
                                        . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                                        . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                                        . '<td width="10%">' . $countStudent . '</td>'
                                        . '<td width="10%">' . $totalWeeks . '</td>'
                                        . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                                        . '</tr>';
                                $i++;
                            }
                        }
                        $content .= '</tbody>'
                                . '</table>'
                                . '<h5>Total Supervisor: ' . $ttlSupervisorI . ' Total Student: ' . $ttlStudentI . '</h5>';
                    }
                }
            }
        }
    }
    $content .= '<h5>Overall Total Supervisor: ' . $totalSupervisorI . ' Overall Total Student: ' . $totalStudentI . '</h5>';
    $content .= '<br /><h5 align="left">Project II:</h5>';
    if (isset($_COOKIE['selectedStatus'])) {
        if ($_COOKIE['selectedStatus'] == "All") {
            $sql = mysqli_query($connect, "SELECT * From cohort");
        } else {
            $selectedStatus = $_COOKIE['selectedStatus'];
            $sql = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='$selectedStatus'");
        }
        while ($row = mysqli_fetch_array($sql)) {
            if (isset($_SESSION['cohort'])) {
                $_POST['cohort'] = $_SESSION['cohort'];
                foreach ($_POST["cohort"] as $cohorts) {
                    $convertoStringII = $row['project2startingDate'];
                    $date = date_create($convertoStringII);
                    $result = $date->format('Y/m');
                    $row['project2startingDate'] = str_replace('/', '', $result);
                    if ($row['cohortId'] == $cohorts) {
                        $content .= '<h5 align="left">Cohort ID: ' . $row['cohortId'] . ' (Session: ' . $row['project2startingDate'] . ')</h5>'
                                . '<table>'
                                . '<thead>';
                        if (isset($_COOKIE['selectedCohort'])) {
                            unset($_COOKIE['selectedCohort']);
                            setcookie('selectedCohort', '', time() - 3600);
                        }
                        if (isset($_COOKIE['selectedSection'])) {
                            unset($_COOKIE['selectedSection']);
                            setcookie('selectedSection', '', time() - 3600);
                        }
                        $cookie_name = "selectedCohort";
                        $cookie_value = $row['cohortId'];
                        $cookie_section = "selectedSection";
                        $cookie_sectionvalue = "Project II";
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                        setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                        $content .= '<tr>'
                                . '<th width="5%">No</th>'
                                . '<th width="15%">Staff ID</th>'
                                . '<th width="50%">Staff Name</th>'
                                . '<th width="10%">Total Students</th>'
                                . '<th width="10%">Total Weeks</th>'
                                . '<th width="10%">Total Hours</th>'
                                . '</tr>';
                        $content .= '</thead>'
                                . '<tbody>';
                        $i = 1;
                        $ttlStudentII = 0;
                        $ttlSupervisorII = 0;
                        foreach ($fypstaff as $fypstaffdetail) {
                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                            //$rowformula = mysqli_num_rows($sqlformula);
                            $selectedcohortID = $row['cohortId'];
                            $selectedstaffName = $fypstaffdetail['staffId'];
                            $countStudent = 0;
                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                            $rowstudent = mysqli_num_rows($sqlstudent);
                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                $countStudent++;
                                $totalStudentII++;
                                $ttlStudentII++;
                            }

                            if ($row['project2Session'] == 'Completed') {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            } else {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = 0;
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            }

                            if ($countStudent != 0) {
                                $totalSupervisorII++;
                                $ttlSupervisorII++;
                            }
                            if ($countStudent != 0) {
                                $content .= '<tr>'
                                        . '<td width="5%">' . $i . '</td>'
                                        . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                                        . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                                        . '<td width="10%">' . $countStudent . '</td>'
                                        . '<td width="10%">' . $totalWeeks . '</td>'
                                        . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                                        . '</tr>';
                                $i++;
                            }
                        }
                        $content .= '</tbody>'
                                . '</table>'
                                . '<h5>Total Supervisor: ' . $ttlSupervisorII . ' Total Student: ' . $ttlStudentII . '</h5>';
                    }
                }
            }
        }
    }
    $content .= '<h5>Overall Total Supervisor: ' . $totalSupervisorII . ' Overall Total Student: ' . $totalStudentII . '</h5>';
    ob_end_clean();
    $obj_pdf->writeHTML($content, true, false, true, false, '');
    $obj_pdf->Output('workloadreportbysession.pdf', 'I');
    exit;
}
if (isset($_POST["generatebysupervisor_pdf"])) {
    require_once('tcpdf/tcpdf.php');
    $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("Generate Workload Report");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(TRUE, 10);
    $obj_pdf->SetFont('helvetica', '', 11);
    $obj_pdf->AddPage();
    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $sessionProjectI = "";
    $sessionProjectII = "";
    if (isset($_COOKIE['sessionProjectI'])) {
        $sessionProjectI = $_COOKIE['sessionProjectI'];
    } else {
        $sessionProjectI = "";
    }
    if (isset($_COOKIE['sessionProjectII'])) {
        $sessionProjectII = $_COOKIE['sessionProjectII'];
    } else {
        $sessionProjectII = "";
    }
    $totalStudentI = 0;
    $totalSupervisorI = 0;
    $totalStudentII = 0;
    $totalSupervisorII = 0;
    $content = '<h1 align="center">Workload Report</h1>'
            . '<h3 align="left">Generated Date: ' . date("Y/m/d") . ', Generated Time: ' . date("h:i:sa") . '</h3>'
            . '<h3 align="left">Workload Summary for</h3>'
            . '<h3 align="left">Project I In Session ' . $sessionProjectI . ' And Project II In Session ' . $sessionProjectII . ':</h3>';
    $content .= '<h5>Total Workload for Project I & II</h5>'
            . '<table>'
            . '<thead>';
    $content .= '<tr>'
            . '<td width="5%">No</td>'
            . '<td width="15%">Staff ID</td>'
            . '<td width="42%">Staff Name</td>'
            . '<td width="12%">Project I (Hours)</td>'
            . '<td width="12%">Project II (Hours)</td>'
            . '<td width="16%">Total Workload (Hours)</td>'
            . '</tr>';
    $content .= '</thead>'
            . '<tbody>';
    $countStaff = 1;
    if (isset($_COOKIE['selectedStatus'])) {
        $selectedStatus = $_COOKIE['selectedStatus'];
        foreach ($fypstaff as $fypstaffdetail) {
            $completedStudent = 0;
            $completedStudentII = 0;
            $countStudent = 0;
            $countStudentII = 0;
            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
            //$rowformula = mysqli_num_rows($sqlformula);
            $selectedstaffName = $fypstaffdetail['staffId'];
            if (isset($_SESSION['cohort'])) {
                $_POST['cohort'] = $_SESSION['cohort'];
                foreach ($_POST["cohort"] as $cohorts) {
                    if ($selectedStatus == 'All') {
                        $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='Completed' OR project1Session='New' OR project1Session='InProgress' OR project2Session='Completed' OR project2Session='New' OR project2Session='InProgress'");
                    } else {
                        $sqlcohort = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='$selectedStatus' OR project2Session = '$selectedStatus'");
                    }
                    $rowcohort = mysqli_num_rows($sqlcohort);
                    $sqlstudent = mysqli_query($connect, "SELECT student.*, team.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$cohorts'");
                    $rowstudent = mysqli_num_rows($sqlstudent);
                    while ($rowcohort = mysqli_fetch_array($sqlcohort)) {
                        if ($rowcohort['cohortId'] == $cohorts) {
                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                if ($rowstudent['supervisor'] == $fypstaffdetail['staffId']) {
                                    if ($selectedStatus == 'All') {
                                        $countStudent++;
                                    } else {
                                        if ($rowcohort['project1Session'] == $selectedStatus) {
                                            $countStudent++;
                                        }
                                    }
                                }
                                if ($selectedStatus == 'All') {
                                    $totalStudentI++;
                                } else {
                                    if ($rowcohort['project1Session'] == $selectedStatus) {
                                        $totalStudentI++;
                                    }
                                }
                                if ($selectedStatus == 'All' || $selectedStatus == 'Completed') {
                                    if ($rowcohort['project1Session'] == 'Completed') {
                                        $completedStudent++;
                                    }
                                }
                            }
                        }
                    }
                    if ($selectedStatus == 'All') {
                        $sqlcohortII = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='Completed' OR project2Session='New' OR project2Session='InProgress'");
                    } else {
                        $sqlcohortII = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='$selectedStatus'");
                    }
                    $rowcohortII = mysqli_num_rows($sqlcohortII);
                    $sqlstudentII = mysqli_query($connect, "SELECT student.*, team.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$cohorts'");
                    $rowstudentII = mysqli_num_rows($sqlstudentII);
                    while ($rowcohortII = mysqli_fetch_array($sqlcohortII)) {
                        if ($rowcohortII['cohortId'] == $cohorts) {
                            while ($rowstudentII = mysqli_fetch_array($sqlstudentII)) {
                                if ($rowstudentII['supervisor'] == $fypstaffdetail['staffId']) {
                                    if ($selectedStatus == 'All') {
                                        $countStudentII++;
                                    } else {
                                        if ($rowcohortII['project2Session'] == $selectedStatus) {
                                            $countStudentII++;
                                        }
                                    }
                                }
                                if ($selectedStatus == 'All') {
                                    $totalStudentII++;
                                } else {
                                    if ($rowcohortII['project2Session'] == $selectedStatus) {
                                        $totalStudentII++;
                                    }
                                }
                                if ($selectedStatus == 'All' || $selectedStatus == 'Completed') {
                                    if ($rowcohortII['project2Session'] == 'Completed') {
                                        $completedStudentII++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($workloadformula as $formula) {
                $totalMinutesInP1 = $completedStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                $totalMinutesInP2 = $completedStudentII * $formula['totalMinutes'] * $formula['totalWeeks'];
            }

            if ($countStudent != 0) {
                $totalSupervisorI++;
            }

            if ($countStudentII != 0) {
                $totalSupervisorII++;
            }
            if ($countStudent != 0 || $countStudentII != 0) {
                $content .= '<tr>'
                        . '<td width="5%">' . $countStaff . '</td>'
                        . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                        . '<td width="42%">' . $fypstaffdetail['staffName'] . '</td>'
                        . '<td width="12%">' . $totalMinutesInP1 / 60 . '</td>'
                        . '<td width="12%">' . $totalMinutesInP2 / 60 . '</td>'
                        . '<td width="16%">' . ($totalMinutesInP1 + $totalMinutesInP2) / 60 . '</td>'
                        . '</tr>';
                $countStaff++;
            }
        }
    }
    $content .= '</tbody>'
            . '</table>';
    $content .= '<h5 align="left">(Project I)  Total Supervisor: ' . $totalSupervisorI . ' Total Student: ' . $totalStudentI . '</h5>';
    $content .= '<h5 align="left">(Project II) Total Supervisor: ' . $totalSupervisorII . ' Total Student: ' . $totalStudentII . '</h5>';
    $content .= '<h5 align="left">=================================================================================================</h5>';
    $totalStudentI = 0;
    $totalSupervisorI = 0;
    $totalStudentII = 0;
    $totalSupervisorII = 0;
    $content .= '<br /><h3 align="left">Workload According to Project I and Project II:</h3>';
    $content .= '<h5 align="left">Project I:</h5>';
    if (isset($_COOKIE['selectedStatus'])) {
        if ($_COOKIE['selectedStatus'] == "All") {
            $sql = mysqli_query($connect, "SELECT * From cohort");
        } else {
            $selectedStatus = $_COOKIE['selectedStatus'];
            $sql = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='$selectedStatus'");
        }
        while ($row = mysqli_fetch_array($sql)) {
            if (isset($_SESSION['cohort'])) {
                $_POST['cohort'] = $_SESSION['cohort'];
                foreach ($_POST["cohort"] as $cohorts) {
                    $convertoString = $row['project1startingDate'];
                    $date = date_create($convertoString);
                    $result = $date->format('Y/m');
                    $row['project1startingDate'] = str_replace('/', '', $result);
                    if ($row['cohortId'] == $cohorts) {
                        $content .= '<h5 align="left">Cohort ID: ' . $row['cohortId'] . ' (Session: ' . $row['project1startingDate'] . ')</h5>'
                                . '<table>'
                                . '<thead>';
                        if (isset($_COOKIE['selectedCohort'])) {
                            unset($_COOKIE['selectedCohort']);
                            setcookie('selectedCohort', '', time() - 3600);
                        }
                        if (isset($_COOKIE['selectedSection'])) {
                            unset($_COOKIE['selectedSection']);
                            setcookie('selectedSection', '', time() - 3600);
                        }
                        $cookie_name = "selectedCohort";
                        $cookie_value = $row['cohortId'];
                        $cookie_section = "selectedSection";
                        $cookie_sectionvalue = "Project I";
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                        setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                        $content .= '<tr>'
                                . '<th width="5%">No</th>'
                                . '<th width="15%">Staff ID</th>'
                                . '<th width="50%">Staff Name</th>'
                                . '<th width="10%">Total Students</th>'
                                . '<th width="10%">Total Weeks</th>'
                                . '<th width="10%">Total Hours</th>'
                                . '</tr>';
                        $content .= '</thead>'
                                . '<tbody>';
                        $i = 1;
                        $ttlStudentI = 0;
                        $ttlSupervisorI = 0;
                        foreach ($fypstaff as $fypstaffdetail) {
                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                            //$rowformula = mysqli_num_rows($sqlformula);
                            $selectedcohortID = $row['cohortId'];
                            $selectedstaffName = $fypstaffdetail['staffId'];
                            $countStudent = 0;
                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                            $rowstudent = mysqli_num_rows($sqlstudent);
                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                $countStudent++;
                                $totalStudentI++;
                                $ttlStudentI++;
                            }
                            if ($row['project1Session'] == 'Completed') {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            } else {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = 0;
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            }

                            if ($countStudent != 0) {
                                $totalSupervisorI++;
                                $ttlSupervisorI++;
                            }
                            if ($countStudent != 0) {
                                $content .= '<tr>'
                                        . '<td width="5%">' . $i . '</td>'
                                        . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                                        . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                                        . '<td width="10%">' . $countStudent . '</td>'
                                        . '<td width="10%">' . $totalWeeks . '</td>'
                                        . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                                        . '</tr>';
                                $i++;
                            }
                        }
                        $content .= '</tbody>'
                                . '</table>'
                                . '<h5>Total Supervisor: ' . $ttlSupervisorI . ' Total Student: ' . $ttlStudentI . '</h5>';
                    }
                }
            }
        }
    }
    $content .= '<h5>Overall Total Supervisor: ' . $totalSupervisorI . ' Overall Total Student: ' . $totalStudentI . '</h5>';
    $content .= '<br /><h5 align="left">Project II:</h5>';
    if (isset($_COOKIE['selectedStatus'])) {
        if ($_COOKIE['selectedStatus'] == "All") {
            $sql = mysqli_query($connect, "SELECT * From cohort");
        } else {
            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
            $selectedStatus = $_COOKIE['selectedStatus'];
            $sql = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='$selectedStatus'");
        }
        while ($row = mysqli_fetch_array($sql)) {
            if (isset($_SESSION['cohort'])) {
                $_POST['cohort'] = $_SESSION['cohort'];
                foreach ($_POST["cohort"] as $cohorts) {
                    $convertoStringII = $row['project2startingDate'];
                    $date = date_create($convertoStringII);
                    $result = $date->format('Y/m');
                    $row['project2startingDate'] = str_replace('/', '', $result);
                    if ($row['cohortId'] == $cohorts) {
                        $content .= '<h5 align="left">Cohort ID: ' . $row['cohortId'] . ' (Session: ' . $row['project2startingDate'] . ')</h5>'
                                . '<table>'
                                . '<thead>';
                        if (isset($_COOKIE['selectedCohort'])) {
                            unset($_COOKIE['selectedCohort']);
                            setcookie('selectedCohort', '', time() - 3600);
                        }
                        if (isset($_COOKIE['selectedSection'])) {
                            unset($_COOKIE['selectedSection']);
                            setcookie('selectedSection', '', time() - 3600);
                        }
                        $cookie_name = "selectedCohort";
                        $cookie_value = $row['cohortId'];
                        $cookie_section = "selectedSection";
                        $cookie_sectionvalue = "Project II";
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                        setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                        $content .= '<tr>'
                                . '<th width="5%">No</th>'
                                . '<th width="15%">Staff ID</th>'
                                . '<th width="50%">Staff Name</th>'
                                . '<th width="10%">Total Students</th>'
                                . '<th width="10%">Total Weeks</th>'
                                . '<th width="10%">Total Hours</th>'
                                . '</tr>';
                        $content .= '</thead>'
                                . '<tbody>';
                        $i = 1;
                        $ttlStudentII = 0;
                        $ttlSupervisorII = 0;
                        foreach ($fypstaff as $fypstaffdetail) {
                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                            //$rowformula = mysqli_num_rows($sqlformula);
                            $selectedcohortID = $row['cohortId'];
                            $selectedstaffName = $fypstaffdetail['staffId'];
                            $countStudent = 0;
                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                            $rowstudent = mysqli_num_rows($sqlstudent);
                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                $countStudent++;
                                $totalStudentII++;
                                $ttlStudentII++;
                            }

                            if ($row['project2Session'] == 'Completed') {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            } else {
                                foreach ($workloadformula as $formula) {
                                    $totalMinutes = 0;
                                    $totalWeeks = $formula['totalWeeks'];
                                }
                            }

                            if ($countStudent != 0) {
                                $totalSupervisorII++;
                                $ttlSupervisorII++;
                            }
                            if ($countStudent != 0) {
                                $content .= '<tr>'
                                        . '<td width="5%">' . $i . '</td>'
                                        . '<td width="15%">' . $fypstaffdetail['staffId'] . '</td>'
                                        . '<td width="50%">' . $fypstaffdetail['staffName'] . '</td>'
                                        . '<td width="10%">' . $countStudent . '</td>'
                                        . '<td width="10%">' . $totalWeeks . '</td>'
                                        . '<td width="10%">' . $totalMinutes / 60 . '</td>'
                                        . '</tr>';
                                $i++;
                            }
                        }
                        $content .= '</tbody>'
                                . '</table>'
                                . '<h5>Total Supervisor: ' . $ttlSupervisorII . ' Total Student: ' . $ttlStudentII . '</h5>';
                    }
                }
            }
        }
    }
    $content .= '<h5>Overall Total Supervisor: ' . $totalSupervisorII . ' Overall Total Student: ' . $totalStudentII . '</h5>';
    ob_end_clean();
    $obj_pdf->writeHTML($content, true, false, true, false, '');
    $obj_pdf->Output('workloadreportbysupervisor.pdf', 'I');
    exit;
}
?>
<style>
    .error {color: #FF0000;}

    .vertical-menu {
        width: 100%;
    }

    .vertical-menu2 {
        width: 100%;
        height: 800px;
        overflow-x: scroll;
        overflow-y: scroll;
    }
</style>

<div class="container">
    <br />
    <h3 align="center">Workload Report</h3><br />
    <div class="vertical-menu2"><div class="vertical-menu" >
            <?php
            if (isset($_COOKIE['sessionProjectI']) AND $_COOKIE['sessionProjectI'] != null) {
                ?>
                <h4 align="left">Session Displayed In Project I: {{$_COOKIE['sessionProjectI']}}</h4>
                <?php
                ?>
                <h5 align="left">Project I:</h5>
                <?php
                $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);

                if (isset($_COOKIE['selectedStatus'])) {
                    if ($_COOKIE['selectedStatus'] == "All") {
                        $sql = mysqli_query($connect, "SELECT * From cohort");
                    } else {
                        $selectedStatus = $_COOKIE['selectedStatus'];
                        $sql = mysqli_query($connect, "SELECT * From cohort WHERE project1Session='$selectedStatus'");
                    }

                    $row = mysqli_num_rows($sql);
                    $totalStudentI = 0;
                    $totalSupervisorI = 0;
                    while ($row = mysqli_fetch_array($sql)) {
                        if (isset($_SESSION['cohort'])) {
                            $_POST['cohort'] = $_SESSION['cohort'];
                            foreach ($_POST["cohort"] as $cohorts) {
                                if ($row['cohortId'] == $cohorts) {
                                    $convertoString = $row['project1startingDate'];
                                    $date = date_create($convertoString);
                                    $result = $date->format('Y/m');
                                    $row['project1startingDate'] = str_replace('/', '', $result);
                                    ?>   
                                    <h5 align="left">Cohort ID: {{$row['cohortId']}} (Session: {{$row['project1startingDate']}})</h5>
                                    <table class="table table-striped" border="">
                                        <thead>
                                            <?php
                                            if (isset($_COOKIE['selectedCohort'])) {
                                                unset($_COOKIE['selectedCohort']);
                                                setcookie('selectedCohort', '', time() - 3600);
                                            }
                                            if (isset($_COOKIE['selectedSection'])) {
                                                unset($_COOKIE['selectedSection']);
                                                setcookie('selectedSection', '', time() - 3600);
                                            }
                                            $cookie_name = "selectedCohort";
                                            $cookie_value = $row['cohortId'];
                                            $cookie_section = "selectedSection";
                                            $cookie_sectionvalue = "Project I";
                                            setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                                            setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                                            ?>
                                            <tr>
                                                <th width="5%">NO</th>
                                                <th width="15%">Staff ID</th>
                                                <th width="35%">Staff Name</th>
                                                <th width="15%">Total Students</th>
                                                <th width="15%">Total Weeks</th>
                                                <th width="15%">Total Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ttlStudentI = 0;
                                            $ttlSupervisorI = 0;
                                            $i = 1;
                                            ?>
                                            @foreach($fypstaff as $fypstaffdetail)
                                            <?php
                                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                                            //$rowformula = mysqli_num_rows($sqlformula);
                                            $selectedcohortID = $row['cohortId'];
                                            $selectedstaffName = $fypstaffdetail['staffId'];
                                            $countStudent = 0;
                                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                            $rowstudent = mysqli_num_rows($sqlstudent);
                                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                $countStudent++;
                                                $totalStudentI++;
                                                $ttlStudentI++;
                                            }
                                            if ($row['project1Session'] == 'Completed') {
                                                ?>
                                                @foreach($workloadformula as $formula)
                                                <?php
                                                $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                                $totalWeeks = $formula['totalWeeks'];
                                                ?>
                                                @endforeach
                                                <?php
                                            } else {
                                                ?>
                                                @foreach($workloadformula as $formula)
                                                <?php
                                                $totalMinutes = 0;
                                                $totalWeeks = $formula['totalWeeks'];
                                                ?>
                                                @endforeach
                                                <?php
                                            }

                                            if ($countStudent != 0) {
                                                $totalSupervisorI++;
                                                $ttlSupervisorI++;
                                            }
                                            if ($countStudent != 0) {
                                                ?>
                                                <tr>
                                                    <td width="5%">{{$i}}</td>
                                                    <td width="15%">{{$fypstaffdetail['staffId']}}</td>
                                                    <td width="35%">{{$fypstaffdetail['staffName']}}</td>
                                                    <td width="15%">{{$countStudent}}</td>
                                                    <td width="15%">{{$totalWeeks}}</td>
                                                    <td width="15%">{{$totalMinutes/60}}</td>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <h5>Total Supervisor: {{$ttlSupervisorI}} Total Student: {{$ttlStudentI}}</h5>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    <h5>Overall Total Supervisor: {{$totalSupervisorI}} Overall Total Student: {{$totalStudentI}}</h5><br />
                    <?php
                }
            }
            ?>
            <?php
            if (isset($_COOKIE['sessionProjectII']) AND $_COOKIE['sessionProjectII'] != null) {
                ?>
                <br /><h4 align="left">Session Displayed In Project II: {{$_COOKIE['sessionProjectII']}}</h4>
                <?php
                ?>    
                <h5 align="left" >Project II:</h5>
                <?php
                if (isset($_COOKIE['selectedStatus'])) {
                    if ($_COOKIE['selectedStatus'] == "All") {
                        $sql = mysqli_query($connect, "SELECT * From cohort");
                    } else {
                        $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                        $selectedStatus = $_COOKIE['selectedStatus'];
                        $sql = mysqli_query($connect, "SELECT * From cohort WHERE project2Session='$selectedStatus'");
                    }
                    $totalStudentII = 0;
                    $totalSupervisorII = 0;
                    $row = mysqli_num_rows($sql);
                    while ($row = mysqli_fetch_array($sql)) {
                        if (isset($_SESSION['cohort'])) {
                            $_POST['cohort'] = $_SESSION['cohort'];
                            foreach ($_POST["cohort"] as $cohorts) {
                                if ($row['cohortId'] == $cohorts) {
                                    $convertoStringII = $row['project2startingDate'];
                                    $date = date_create($convertoStringII);
                                    $result = $date->format('Y/m');
                                    $row['project2startingDate'] = str_replace('/', '', $result);
                                    ?>   
                                    <h5 align="left">Cohort ID: {{$row['cohortId']}} (Session: {{$row['project2startingDate']}})</h5>
                                    <table class="table table-striped" border="">
                                        <thead>
                                            <?php
                                            if (isset($_COOKIE['selectedCohort'])) {
                                                unset($_COOKIE['selectedCohort']);
                                                setcookie('selectedCohort', '', time() - 3600);
                                            }
                                            if (isset($_COOKIE['selectedSection'])) {
                                                unset($_COOKIE['selectedSection']);
                                                setcookie('selectedSection', '', time() - 3600);
                                            }
                                            $cookie_name = "selectedCohort";
                                            $cookie_value = $row['cohortId'];
                                            $cookie_section = "selectedSection";
                                            $cookie_sectionvalue = "Project II";
                                            setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                                            setcookie($cookie_section, $cookie_sectionvalue, time() + (86400 * 30), "/");
                                            ?>
                                            <tr>
                                                <th width="5%">NO</th>
                                                <th width="15%">Staff ID</th>
                                                <th width="35%">Staff Name</th>
                                                <th width="15%">Total Students</th>
                                                <th width="15%">Total Weeks</th>
                                                <th width="15%">Total Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $ttlStudentII = 0;
                                            $ttlSupervisorII = 0;
                                            ?>
                                            @foreach($fypstaff as $fypstaffdetail)
                                            <?php
                                            //$connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                                            //$rowformula = mysqli_num_rows($sqlformula);
                                            $selectedcohortID = $row['cohortId'];
                                            $selectedstaffName = $fypstaffdetail['staffId'];
                                            $countStudent = 0;
                                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                            $rowstudent = mysqli_num_rows($sqlstudent);
                                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                $countStudent++;
                                                $totalStudentII++;
                                                $ttlStudentII++;
                                            }

                                            if ($row['project2Session'] == 'Completed') {
                                                ?>
                                                @foreach($workloadformula as $formula)
                                                <?php
                                                $totalMinutes = $countStudent * $formula['totalMinutes'] * $formula['totalWeeks'];
                                                $totalWeeks = $formula['totalWeeks'];
                                                ?>
                                                @endforeach
                                                <?php
                                            } else {
                                                ?>
                                                @foreach($workloadformula as $formula)
                                                <?php
                                                $totalMinutes = 0;
                                                $totalWeeks = $formula['totalWeeks'];
                                                ?>
                                                @endforeach
                                                <?php
                                            }

                                            if ($countStudent != 0) {
                                                $totalSupervisorII++;
                                                $ttlSupervisorII++;
                                            }
                                            if ($countStudent != 0) {
                                                ?>
                                                <tr>
                                                    <td width="5%">{{$i}}</td>
                                                    <td width="15%">{{$fypstaffdetail['staffId']}}</td>
                                                    <td width="35%">{{$fypstaffdetail['staffName']}}</td>
                                                    <td width="15%">{{$countStudent}}</td>
                                                    <td width="15%">{{$totalWeeks}}</td>
                                                    <td width="15%">{{$totalMinutes/60}}</td>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <h5>Total Supervisor: {{$ttlSupervisorII}} Total Student: {{$ttlStudentII}}</h5>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    <h5>Overall Total Supervisor: {{$totalSupervisorII}} Overall Total Student: {{$totalStudentII}}</h5>
                    <?php
                }
            }
            ?>
        </div></div>
    <br /><form method="post">
        @csrf
        <h5 align="center">
            <button type="submit" name="generatebysession_pdf" class="btn btn-info" style="width:300px;font-size:20px">Generate(By Session) PDF File</button>
            <button type="submit" name="generatebysupervisor_pdf" class="btn btn-info" style="width:350px;font-size:20px">Generate(By Supervisor) PDF File</button>
        </h5>
    </form><br />
</div>
@endsection