@extends('layouts.app')
@section('title', 'Workload Page')
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
foreach ($cohort as $cohortdetail) {
    $conn = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $convertoString = $cohortdetail['project1startingDate'];
    $date = date_create($convertoString);
    $afterDate = date_add($date, date_interval_create_from_date_string('98 days'));
    $result = $afterDate->format('Y/m/d H:i:s');
    if ((time() - (60 * 60 * 24)) > strtotime($result)) {
        $sqlupdateCohort = ' UPDATE cohort SET project1Session="Completed" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
        mysqli_query($conn, $sqlupdateCohort);
    } else {
        if ((time() - (60 * 60 * 24)) > strtotime($convertoString)) {
            $sqlupdateCohort = ' UPDATE cohort SET project1Session="InProgress" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
            mysqli_query($conn, $sqlupdateCohort);
        } else {
            $sqlupdateCohort = ' UPDATE cohort SET project1Session="New" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
            mysqli_query($conn, $sqlupdateCohort);
        }
    }
    $convertoStringII = $cohortdetail['project2startingDate'];
    $dateII = date_create($convertoStringII);
    $afterDateII = date_add($dateII, date_interval_create_from_date_string('98 days'));
    $resultII = $afterDateII->format('Y/m/d H:i:s');
    if ((time() - (60 * 60 * 24)) > strtotime($resultII)) {
        $sqlupdateCohort = ' UPDATE cohort SET project2Session="Completed" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
        mysqli_query($conn, $sqlupdateCohort);
    } else {
        if ((time() - (60 * 60 * 24)) > strtotime($convertoStringII)) {
            $sqlupdateCohort = ' UPDATE cohort SET project2Session="InProgress" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
            mysqli_query($conn, $sqlupdateCohort);
        } else {
            $sqlupdateCohort = ' UPDATE cohort SET project2Session="New" WHERE cohortId="' . $cohortdetail['cohortId'] . '"';
            mysqli_query($conn, $sqlupdateCohort);
        }
    }
}
?>
<script type="text/javascript">
    function updateTable() {
        document.cookie = cohort + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
</script>

<style>
    .error {color: #FF0000;}

    .vertical-menu {
        width: 1000px;
    }

    .vertical-menu2 {
        width: 100%;
        height: 800px;
        overflow-x: scroll;
        overflow-y: scroll;
    }

    .vertical-menucohort {
        width: 1085px;
    }

    .vertical-menucohort2 {
        width: 100%;
        height: 200px;
        overflow-y: scroll;
    }
</style>
<div class="container">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    <h3 align="center">Workload Management</h3><br />
    <h5 align="left">Cohort:</h5>
    <form action="{{ route('workload.viewworkload') }}" method="post">
        @csrf
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table"></i>
                Cohort Details</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" style="width:1600px;font-size:14px" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>NO</th>
                                <th>Cohort ID</th>
                                <th>Project I's Starting Date</th>
                                <th>Project I's End Date</th>
                                <th>Project II's Starting Date</th>
                                <th>Project II's End Date</th>
                                <th>Session of Project I's Status</th>
                                <th>Session of Project II's Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $totalStudentI = 0;
                            $totalSupervisorI = 0;
                            $totalStudentII = 0;
                            $totalSupervisorII = 0;
                            $sessionProjectI = '';
                            $sessionProjectII = '';
                            ?>
                            @foreach($cohort as $cohortdetail)
                            <tr>
                                <td>
                                    <?php
                                    if (isset($_POST['cohort'])) {
                                        $_SESSION['cohort'] = $_POST['cohort'];
                                        foreach ($_POST["cohort"] as $cohorts) {
                                            if ($cohortdetail['cohortId'] == $cohorts) {
                                                $cohorts = "Exist";
                                                break;
                                            }
                                        }
                                        if ($cohorts == "Exist") {
                                            echo "<input type='checkbox' name='cohort[]' selected='selected' value='" . $cohortdetail['cohortId'] . "' checked>";
                                        } else {
                                            echo "<input type='checkbox' name='cohort[]' selected='selected' value='" . $cohortdetail['cohortId'] . "'>";
                                        }
                                    } else if (isset($_POST['status'])) {
                                        foreach ($_SESSION["cohort"] as $cohorts) {
                                            if ($cohortdetail['cohortId'] == $cohorts) {
                                                $cohorts = "Exist";
                                                break;
                                            }
                                        }
                                        if ($cohorts == "Exist") {
                                            echo "<input type='checkbox' name='cohort[]' selected='selected' value='" . $cohortdetail['cohortId'] . "' checked>";
                                        } else {
                                            echo "<input type='checkbox' name='cohort[]' selected='selected' value='" . $cohortdetail['cohortId'] . "'>";
                                        }
                                    } else {
                                        unset($_SESSION['cohort']);
                                        echo "<input type='checkbox' name='cohort[]' selected='selected' value='" . $cohortdetail['cohortId'] . "'>";
                                    }
                                    $convertoStringI = $cohortdetail['project1startingDate'];
                                    $dateI = date_create($convertoStringI);
                                    $resultI = $dateI->format('d/m/Y');
                                    $convertoStringII = $cohortdetail['project1endDate'];
                                    $dateII = date_create($convertoStringII);
                                    $resultII = $dateII->format('d/m/Y');
                                    $convertoStringIII = $cohortdetail['project2startingDate'];
                                    $dateIII = date_create($convertoStringIII);
                                    $resultIII = $dateIII->format('d/m/Y');
                                    $convertoStringIV = $cohortdetail['project2endDate'];
                                    $dateIV = date_create($convertoStringIV);
                                    $resultIV = $dateIV->format('d/m/Y');
                                    ?>

                                </td>
                                <td width="4%">{{$i}}</td>
                                <td width="8%">{{$cohortdetail['cohortId']}}</td>
                                <td width="13%">{{$resultI}}</td>
                                <td width="13%">{{$resultII}}</td>
                                <td width="14%">{{$resultIII}}</td>
                                <td width="14%">{{$resultIV}}</td>
                                <td width="16%">{{$cohortdetail['project1Session']}}</td>
                                <td width="16%">{{$cohortdetail['project2Session']}}</td>
                                <?php
                                $i++;
                                ?>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            <p><button type="submit" class="btn btn-info" style="width:220px;font-size:16px" onchange="updateTable()">Generate Workload Result</button>
            </p>
    </form>

    <br /><h5 align="left">Workload Result:</h5>
    <form action="{{ route('workload.viewworkload') }}" method="post">
        @csrf
        <h5 align="left">Status: <select name="status" onchange="this.form.submit()">
                <?php
                if (isset($_POST['status'])) {
                    if ($_POST['status'] == "All") {
                        echo "<option value='All' selected='selected'>All</option>";
                        echo "<option value='Completed'>Completed</option>";
                        echo "<option value='New'>New</option>";
                        echo "<option value='InProgress'>InProgress</option>";
                    } else if ($_POST['status'] == "Completed") {
                        echo "<option value='All' selected='selected'>All</option>";
                        echo "<option value='Completed' selected='selected'>Completed</option>";
                        echo "<option value='New'>New</option>";
                        echo "<option value='InProgress'>InProgress</option>";
                    } else if ($_POST['status'] == "New") {
                        echo "<option value='All' selected='selected'>All</option>";
                        echo "<option value='Completed'>Completed</option>";
                        echo "<option value='New' selected='selected'>New</option>";
                        echo "<option value='InProgress'>InProgress</option>";
                    } else if ($_POST['status'] == "InProgress") {
                        echo "<option value='All' selected='selected'>All</option>";
                        echo "<option value='Completed'>Completed</option>";
                        echo "<option value='New'>New</option>";
                        echo "<option value='InProgress' selected='selected'>InProgress</option>";
                    }
                    if (isset($_COOKIE['selectedStatus'])) {
                        unset($_COOKIE['selectedStatus']);
                        setcookie('selectedStatus', '', time() - 3600);
                    }
                    $cookie_status = "selectedStatus";
                    $cookie_statusvalue = $_POST['status'];
                    setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                } else {
                    echo "<option value='All'>All</option>";
                    echo "<option value='Completed'>Completed</option>";
                    echo "<option value='New'>New</option>";
                    echo "<option value='InProgress'>InProgress</option>";
                    if (isset($_COOKIE['selectedStatus'])) {
                        unset($_COOKIE['selectedStatus']);
                        setcookie('selectedStatus', '', time() - 3600);
                    }
                    $cookie_status = "selectedStatus";
                    $cookie_statusvalue = "All";
                    setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                }
                ?>
            </select></h5>
    </form>
    <h5 align="left">Project I:</h5>
    <?php
    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    if (isset($_POST['status'])) {
        $selectedStatus = $_POST['status'];
        if ($selectedStatus == "All") {
            //$sql = mysqli_query($connect, "SELECT * From cohort");
            //$row = mysqli_num_rows($sql);
            ?>
            @foreach($cohort as $cohortdetail)
            <?php
            if (isset($_SESSION['cohort'])) {
                foreach ($_SESSION["cohort"] as $cohorts) {
                    if ($cohortdetail['cohortId'] == $cohorts) {
                        $convertoString = $cohortdetail['project1startingDate'];
                        $date = date_create($convertoString);
                        $result = $date->format('Y/m');
                        $cohortdetail['project1startingDate'] = str_replace('/', '', $result);
                        $sessionProjectI .= "(" . $cohortdetail['project1startingDate'] . ") ";
                        ?>
                        <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project1startingDate']}})</h5>
                        <div class="card mb-3" style='width:100%'>
                            <div class="card-header">
                                <i class="fas fa-table"></i>
                                Workload Details</div>
                            <div class="card-body">
                                <div class="table-responsive" style="height:500px">
                                    <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>Staff ID</th>
                                                <th>Staff Name</th>
                                                <th>Total Students</th>
                                                <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                <th>Action</th>
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
                                            $selectedcohortID = $cohortdetail['cohortId'];
                                            $selectedstaffName = $fypstaffdetail['staffId'];
                                            $countStudent = 0;
                                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                            $rowstudent = mysqli_num_rows($sqlstudent);
                                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                $countStudent++;
                                                $totalStudentI++;
                                                $ttlStudentI++;
                                            }
                                            if ($cohortdetail['project1Session'] == 'Completed') {
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
                                            ?>
                                            <tr>
                                                <td>{{$i}}</td>
                                                <td>{{$fypstaffdetail['staffId']}}</td>
                                                <td>{{$fypstaffdetail['staffName']}}</td>
                                                <td>{{$countStudent}}</td>
                                                <td>{{$totalWeeks}}</td>
                                                <td>{{$totalMinutes/60}}</td>
                                                <td>
                                                    <?php
                                                    $selectedSection = "Project I";
                                                    ?>
                                                    <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                        @csrf
                                                        <button class="btn btn-warning" type="submit">View Details</button>
                                                    </form>
                                                </td>
                                                <?php
                                                $i++;
                                                ?>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                            <h5>Total Supervisor: {{$ttlSupervisorI}} Total Student: {{$ttlStudentI}}</h5>
                            <?php
                        }
                    }
                    if (isset($_COOKIE['sessionProjectI'])) {
                        unset($_COOKIE['sessionProjectI']);
                        setcookie('sessionProjectI', '', time() - 3600);
                    }
                    $cookie_status = "sessionProjectI";
                    $cookie_statusvalue = $sessionProjectI;
                    setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                }
                ?>
                @endforeach
                <?php
            } else {
                //$sql = mysqli_query($connect, "SELECT * From cohort");
                //$row = mysqli_num_rows($sql);
                ?>
                @foreach($cohort as $cohortdetail)
                <?php
                if (isset($_SESSION['cohort'])) {
                    foreach ($_SESSION["cohort"] as $cohorts) {
                        if ($cohortdetail['project1Session'] == $selectedStatus) {
                            if ($cohortdetail['cohortId'] == $cohorts) {
                                $convertoString = $cohortdetail['project1startingDate'];
                                $date = date_create($convertoString);
                                $result = $date->format('Y/m');
                                $cohortdetail['project1startingDate'] = str_replace('/', '', $result);
                                $sessionProjectI .= "(" . $cohortdetail['project1startingDate'] . ") ";
                                ?>
                                <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project1startingDate']}})</h5>
                                <div class="card mb-3" style='width:100%'>
                                    <div class="card-header">
                                        <i class="fas fa-table"></i>
                                        Workload Details</div>
                                    <div class="card-body">
                                        <div class="table-responsive" style="height:400px">
                                            <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>NO</th>
                                                        <th>Staff ID</th>
                                                        <th>Staff Name</th>
                                                        <th>Total Students</th>
                                                        <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                        <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                        <th>Action</th>
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
                                                    $selectedcohortID = $cohortdetail['cohortId'];
                                                    $selectedstaffName = $fypstaffdetail['staffId'];
                                                    $countStudent = 0;
                                                    $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                                    $rowstudent = mysqli_num_rows($sqlstudent);
                                                    while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                        $countStudent++;
                                                        $totalStudentI++;
                                                        $ttlStudentI++;
                                                    }
                                                    if ($cohortdetail['project1Session'] == 'Completed') {
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
                                                    ?>
                                                    <tr>
                                                        <td>{{$i}}</td>
                                                        <td>{{$fypstaffdetail['staffId']}}</td>
                                                        <td>{{$fypstaffdetail['staffName']}}</td>
                                                        <td>{{$countStudent}}</td>
                                                        <td>{{$totalWeeks}}</td>
                                                        <td>{{$totalMinutes/60}}</td>
                                                        <td>
                                                            <?php
                                                            $selectedSection = "Project I";
                                                            ?>
                                                            <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                                @csrf
                                                                <button class="btn btn-warning" type="submit">View Details</button>
                                                            </form>
                                                        </td>
                                                        <?php
                                                        $i++;
                                                        ?>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                    <h5>Total Supervisor: {{$ttlSupervisorI}} Total Student: {{$ttlStudentI}}</h5>
                                    <?php
                                }
                            }
                        }
                        if (isset($_COOKIE['sessionProjectI'])) {
                            unset($_COOKIE['sessionProjectI']);
                            setcookie('sessionProjectI', '', time() - 3600);
                        }
                        $cookie_status = "sessionProjectI";
                        $cookie_statusvalue = $sessionProjectI;
                        setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                    }
                    ?>
                    @endforeach
                    <?php
                }
            } else {
                //$sql = mysqli_query($connect, "SELECT * From cohort");
                //$row = mysqli_num_rows($sql);
                ?>
                @foreach($cohort as $cohortdetail)
                <?php
                if (isset($_SESSION['cohort'])) {
                    foreach ($_SESSION["cohort"] as $cohorts) {
                        if ($cohortdetail['cohortId'] == $cohorts) {
                            $convertoString = $cohortdetail['project1startingDate'];
                            $date = date_create($convertoString);
                            $result = $date->format('Y/m');
                            $cohortdetail['project1startingDate'] = str_replace('/', '', $result);
                            $sessionProjectI .= "(" . $cohortdetail['project1startingDate'] . ") ";
                            ?>
                            <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project1startingDate']}})</h5>
                            <div class="card mb-3" style='width:100%'>
                                <div class="card-header">
                                    <i class="fas fa-table"></i>
                                    Workload Details</div>
                                <div class="card-body">
                                    <div class="table-responsive" style="height:500px">
                                        <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>Staff ID</th>
                                                    <th>Staff Name</th>
                                                    <th>Total Students</th>
                                                    <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                    <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                    <th>Action</th>
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
                                                $selectedcohortID = $cohortdetail['cohortId'];
                                                $selectedstaffName = $fypstaffdetail['staffId'];
                                                $countStudent = 0;
                                                $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                                $rowstudent = mysqli_num_rows($sqlstudent);
                                                while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                    $countStudent++;
                                                    $totalStudentI++;
                                                    $ttlStudentI++;
                                                }
                                                if ($cohortdetail['project1Session'] == 'Completed') {
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
                                                ?>
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$fypstaffdetail['staffId']}}</td>
                                                    <td>{{$fypstaffdetail['staffName']}}</td>
                                                    <td>{{$countStudent}}</td>
                                                    <td>{{$totalWeeks}}</td>
                                                    <td>{{$totalMinutes/60}}</td>
                                                    <td>
                                                        <?php
                                                        $selectedSection = "Project I";
                                                        ?>
                                                        <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                            @csrf
                                                            <button class="btn btn-warning" type="submit">View Details</button>
                                                        </form>
                                                    </td>
                                                    <?php
                                                    $i++;
                                                    ?>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                <h5>Total Supervisor: {{$ttlSupervisorI}} Total Student: {{$ttlStudentI}}</h5>
                                <?php
                            }
                        }
                        if (isset($_COOKIE['sessionProjectI'])) {
                            unset($_COOKIE['sessionProjectI']);
                            setcookie('sessionProjectI', '', time() - 3600);
                        }
                        $cookie_status = "sessionProjectI";
                        $cookie_statusvalue = $sessionProjectI;
                        setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                    }
                    ?>
                    @endforeach
                    <?php
                }
                ?>
                <h5>Overall Total Supervisor: {{$totalSupervisorI}} Overall Total Student: {{$totalStudentI}}</h5><br />
                <h5 align="left">Project II:</h5>
                <?php
                if (isset($_POST['status'])) {
                    $selectedStatus = $_POST['status'];
                    if ($selectedStatus == "All") {
                        //$sql = mysqli_query($connect, "SELECT * From cohort");
                        //$row = mysqli_num_rows($sql);
                        ?>
                        @foreach($cohort as $cohortdetail)
                        <?php
                        if (isset($_SESSION['cohort'])) {
                            foreach ($_SESSION["cohort"] as $cohorts) {
                                if ($cohortdetail['cohortId'] == $cohorts) {
                                    $convertoStringII = $cohortdetail['project2startingDate'];
                                    $date2 = date_create($convertoStringII);
                                    $result2 = $date2->format('Y/m');
                                    $cohortdetail['project2startingDate'] = str_replace('/', '', $result2);
                                    $sessionProjectII .= "(" . $cohortdetail['project2startingDate'] . ") ";
                                    ?>
                                    <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project2startingDate']}})</h5>
                                    <div class="card mb-3" style='width:100%'>
                                        <div class="card-header">
                                            <i class="fas fa-table"></i>
                                            Workload Details</div>
                                        <div class="card-body">
                                            <div class="table-responsive" style="height:400px">
                                                <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Staff ID</th>
                                                            <th>Staff Name</th>
                                                            <th>Total Students</th>
                                                            <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                            <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $ttlStudentII = 0;
                                                        $ttlSupervisorII = 0;
                                                        $i = 1;
                                                        ?>
                                                        @foreach($fypstaff as $fypstaffdetail)
                                                        <?php
                                                        //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                                                        //$rowformula = mysqli_num_rows($sqlformula);
                                                        $selectedcohortID = $cohortdetail['cohortId'];
                                                        $selectedstaffName = $fypstaffdetail['staffId'];
                                                        $countStudent = 0;
                                                        $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                                        $rowstudent = mysqli_num_rows($sqlstudent);
                                                        while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                            $countStudent++;
                                                            $totalStudentII++;
                                                            $ttlStudentII++;
                                                        }
                                                        if ($cohortdetail['project2Session'] == 'Completed') {
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
                                                        ?>
                                                        <tr>
                                                            <td>{{$i}}</td>
                                                            <td>{{$fypstaffdetail['staffId']}}</td>
                                                            <td>{{$fypstaffdetail['staffName']}}</td>
                                                            <td>{{$countStudent}}</td>
                                                            <td>{{$totalWeeks}}</td>
                                                            <td>{{$totalMinutes/60}}</td>
                                                            <td>
                                                                <?php
                                                                $selectedSection = "Project II";
                                                                ?>
                                                                <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                                    @csrf
                                                                    <button class="btn btn-warning" type="submit">View Details</button>
                                                                </form>
                                                            </td>
                                                            <?php
                                                            $i++;
                                                            ?>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </div>
                                        <h5>Total Supervisor: {{$ttlSupervisorII}} Total Student: {{$ttlStudentII}}</h5>
                                        <?php
                                    }
                                }
                                if (isset($_COOKIE['sessionProjectII'])) {
                                    unset($_COOKIE['sessionProjectII']);
                                    setcookie('sessionProjectII', '', time() - 3600);
                                }
                                $cookie_status = "sessionProjectII";
                                $cookie_statusvalue = $sessionProjectII;
                                setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                            }
                            ?>
                            @endforeach
                            <?php
                        } else {
                            //$sql = mysqli_query($connect, "SELECT * From cohort");
                            //$row = mysqli_num_rows($sql);
                            ?>
                            @foreach($cohort as $cohortdetail)
                            <?php
                            if (isset($_SESSION['cohort'])) {
                                foreach ($_SESSION["cohort"] as $cohorts) {
                                    if ($cohortdetail['project2Session'] == $selectedStatus) {
                                        if ($cohortdetail['cohortId'] == $cohorts) {
                                            $convertoStringII = $cohortdetail['project2startingDate'];
                                            $date2 = date_create($convertoStringII);
                                            $result2 = $date2->format('Y/m');
                                            $cohortdetail['project2startingDate'] = str_replace('/', '', $result2);
                                            $sessionProjectII .= "(" . $cohortdetail['project2startingDate'] . ") ";
                                            ?>
                                            <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project2startingDate']}})</h5>
                                            <div class="card mb-3" style='width:100%'>
                                                <div class="card-header">
                                                    <i class="fas fa-table"></i>
                                                    Workload Details</div>
                                                <div class="card-body">
                                                    <div class="table-responsive" style="height:500px">
                                                        <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>NO</th>
                                                                    <th>Staff ID</th>
                                                                    <th>Staff Name</th>
                                                                    <th>Total Students</th>
                                                                    <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                                    <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $ttlStudentII = 0;
                                                                $ttlSupervisorII = 0;
                                                                $i = 1;
                                                                ?>
                                                                @foreach($fypstaff as $fypstaffdetail)
                                                                <?php
                                                                //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                                                                //$rowformula = mysqli_num_rows($sqlformula);
                                                                $selectedcohortID = $cohortdetail['cohortId'];
                                                                $selectedstaffName = $fypstaffdetail['staffId'];
                                                                $countStudent = 0;
                                                                $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                                                $rowstudent = mysqli_num_rows($sqlstudent);
                                                                while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                                    $countStudent++;
                                                                    $totalStudentII++;
                                                                    $ttlStudentII++;
                                                                }
                                                                if ($cohortdetail['project2Session'] == 'Completed') {
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
                                                                ?>
                                                                <tr>
                                                                    <td>{{$i}}</td>
                                                                    <td>{{$fypstaffdetail['staffId']}}</td>
                                                                    <td>{{$fypstaffdetail['staffName']}}</td>
                                                                    <td>{{$countStudent}}</td>
                                                                    <td>{{$totalWeeks}}</td>
                                                                    <td>{{$totalMinutes/60}}</td>
                                                                    <td>
                                                                        <?php
                                                                        $selectedSection = "Project II";
                                                                        ?>
                                                                        <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                                            @csrf
                                                                            <button class="btn btn-warning" type="submit">View Details</button>
                                                                        </form>
                                                                    </td>
                                                                    <?php
                                                                    $i++;
                                                                    ?>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                                <h5>Total Supervisor: {{$ttlSupervisorII}} Total Student: {{$ttlStudentII}}</h5>
                                                <?php
                                            }
                                        }
                                    }
                                    if (isset($_COOKIE['sessionProjectII'])) {
                                        unset($_COOKIE['sessionProjectII']);
                                        setcookie('sessionProjectII', '', time() - 3600);
                                    }
                                    $cookie_status = "sessionProjectII";
                                    $cookie_statusvalue = $sessionProjectII;
                                    setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                                }
                                ?>
                                @endforeach
                                <?php
                            }
                        } else {
                            //$sql = mysqli_query($connect, "SELECT * From cohort");
                            //$row = mysqli_num_rows($sql);
                            ?>
                            @foreach($cohort as $cohortdetail)
                            <?php
                            if (isset($_SESSION['cohort'])) {
                                foreach ($_SESSION["cohort"] as $cohorts) {
                                    if ($cohortdetail['cohortId'] == $cohorts) {
                                        $convertoStringII = $cohortdetail['project2startingDate'];
                                        $date2 = date_create($convertoStringII);
                                        $result2 = $date2->format('Y/m');
                                        $cohortdetail['project2startingDate'] = str_replace('/', '', $result2);
                                        $sessionProjectII .= "(" . $cohortdetail['project2startingDate'] . ") ";
                                        ?>
                                        <h5 align="left">Cohort ID: {{$cohortdetail['cohortId']}} (Session: {{$cohortdetail['project2startingDate']}})</h5>
                                        <div class="card mb-3" style='width:100%'>
                                            <div class="card-header">
                                                <i class="fas fa-table"></i>
                                                Workload Details</div>
                                            <div class="card-body">
                                                <div class="table-responsive" style="height:500px">
                                                    <table class="table table-bordered" id="dataTable" style="width:1000px;font-size:14px" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th>NO</th>
                                                                <th>Staff ID</th>
                                                                <th>Staff Name</th>
                                                                <th>Total Students</th>
                                                                <th>Total Weeks [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                                <th>Total Hours [<a href="{{ action('WorkloadController@edit') }}">Edit</a>]</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $ttlStudentII = 0;
                                                            $ttlSupervisorII = 0;
                                                            $i = 1;
                                                            ?>
                                                            @foreach($fypstaff as $fypstaffdetail)
                                                            <?php
                                                            //$sqlformula = mysqli_query($connect, "SELECT * From wl_formula WHERE formulaId = '1'");
                                                            //$rowformula = mysqli_num_rows($sqlformula);
                                                            $selectedcohortID = $cohortdetail['cohortId'];
                                                            $selectedstaffName = $fypstaffdetail['staffId'];
                                                            $countStudent = 0;
                                                            $sqlstudent = mysqli_query($connect, "SELECT student.* FROM `team`, `student` WHERE student.teamId = team.teamId AND team.supervisor = '$selectedstaffName' AND student.cohortId = '$selectedcohortID'");
                                                            $rowstudent = mysqli_num_rows($sqlstudent);
                                                            while ($rowstudent = mysqli_fetch_array($sqlstudent)) {
                                                                $countStudent++;
                                                                $totalStudentII++;
                                                                $ttlStudentII++;
                                                            }
                                                            if ($cohortdetail['project2Session'] == 'Completed') {
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
                                                            ?>
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>{{$fypstaffdetail['staffId']}}</td>
                                                                <td>{{$fypstaffdetail['staffName']}}</td>
                                                                <td>{{$countStudent}}</td>
                                                                <td>{{$totalWeeks}}</td>
                                                                <td>{{$totalMinutes/60}}</td>
                                                                <td>
                                                                    <?php
                                                                    $selectedSection = "Project II";
                                                                    ?>
                                                                    <form action="{{ route('workload.viewworkloaddetails',['staffName'=>$fypstaffdetail['staffId'],'cohortID'=>$cohortdetail['cohortId'],'selectedSec'=>$selectedSection]) }}" method="post">
                                                                        @csrf
                                                                        <button class="btn btn-warning" type="submit">View Details</button>
                                                                    </form>
                                                                </td>
                                                                <?php
                                                                $i++;
                                                                ?>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                            <h5>Total Supervisor: {{$ttlSupervisorI}} Total Student: {{$ttlStudentI}}</h5>
                                            <?php
                                        }
                                    }
                                    if (isset($_COOKIE['sessionProjectII'])) {
                                        unset($_COOKIE['sessionProjectII']);
                                        setcookie('sessionProjectII', '', time() - 3600);
                                    }
                                    $cookie_status = "sessionProjectII";
                                    $cookie_statusvalue = $sessionProjectII;
                                    setcookie($cookie_status, $cookie_statusvalue, time() + (86400 * 30), "/");
                                }
                                ?>
                                @endforeach
                                <?php
                            }
                            ?>
                            <h5>Overall Total Supervisor: {{$totalSupervisorII}} Overall Total Student: {{$totalStudentII}}</h5><br />
                        </div></div></form>
                    <br /><form method="post" action="{{action('WorkloadController@generate')}}">
                        @csrf
                        <h5 align="center">
                            <button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Generate Report</button>
                        </h5>
                    </form><br />
</div>
                @endsection
