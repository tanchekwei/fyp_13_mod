@extends('layouts.app')
@section('title', 'View Workload Details Page')
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
?>
<style>
    .vertical-menu {
        width: 1200px;
    }

    .vertical-menu2 {
        width: 100%;
        height: 400px;
        overflow-x: scroll;
        overflow-y: scroll;
    }

    .hover {
        position: relative;
    }

    .hover div {
        /*visiblity: hidden; */

        display: none;
        position: absolute;
        top: 0;
        left:100%;
        margin-top: -30px; /* approx adjustment for arrow */
        margin-left: 25px; /* approx adjustment for arrow */
    }

    .hover a:hover + div {
        cursor: pointer;
        display: block;
        /*visibility: visible; */
        width: 275px;
        line-height: 20px;
        padding: 8px;
        font-size: 16px;
        text-align: left;
        color: black;
        background: rgb(255, 255, 255);
        border: 4px solid rgb(255, 255, 255);
        border-radius: 5px;
        text-shadow: rgba(0, 0, 0, 0.0980392) 1px 1px 1px;
        box-shadow: #333 -4px 4px 16px 2px;
        -webkit-transition: opacity 100ms ease-in;
        -o-transition: opacity 100ms ease-in;
        -moz-transition: opacity 100ms ease-in;
        transition: opacity 100ms ease-in;
        pointer-events: none;
        z-index: 3000;
    }

    .hover div:after {
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-width: 10px;
        border-style: solid;
        border-color: transparent #FFFFFF transparent transparent;
        top: 40px;
        left: -23px;
    }

</style>
<div class="container">
    <br />
    <h3 align="center">View Workload Details</h3><br />
    <?php
    $selectedCohort = $cohortID;
    $selectedSection = $selectedSection;

    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $sqlstaff = mysqli_query($connect, "SELECT staff.* From staff WHERE staffId = '$staffName'");
    $rowstaff = mysqli_num_rows($sqlstaff);
    while ($rowstaff = mysqli_fetch_array($sqlstaff)) {
        $staffName = $rowstaff['staffName'];
        $staffTitle = $rowstaff['title'];
        $staffId = $rowstaff['staffId'];
    }
    ?>
    <h5 align="left">{{$staffTitle}} {{$staffName}} 's Workload Details In Cohort ID: {{$cohortID}} ({{$selectedSection}})</h5>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-table"></i>
            Workload Details</div>
        <div class="card-body">
            <div class="table-responsive" style="height:400px">
                <table class="table table-bordered" id="dataTable" style="width:1200px;font-size:14px" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th width="10%">Team ID</th>
                            <th width="15%">Project Code</th>
                            <th width="24%">Project Scope</th>
                            <th width="15%">Student Name</th>
                            <th width="13%">Moderator</th>
                            <th width="14%">Project Status</th>
                        </tr>
                    </thead>
                    <tbody>          
                        <?php
                        $i = 1;
                        ?>
                        <?php
                        $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                        $sql = mysqli_query($connect, "SELECT team.*, student.cohortId From team, student WHERE team.teamId = student.teamId AND student.cohortId = '$selectedCohort' AND team.supervisor = '$staffId' GROUP BY team.teamId");
                        $row = mysqli_num_rows($sql);
                        while ($row = mysqli_fetch_array($sql)) {
                            ?>
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$row['teamId']}}</td>
                                <td>{{$row['projectCode']}}</td>
                                <td>{{$row['teamScope']}}</td>
                                <td class = "hover">
                                    @foreach($student as $studentdetail)

                                    <?php
                                    if ($studentdetail['teamId'] == $row['teamId']) {
                                        ?>
                                        <a href="#">{{$studentdetail['studentName']}}</a><div>
                                            Student ID: {{$studentdetail['studentId']}}<br />Student Name: {{$studentdetail['studentName']}}<br />Programme: {{$studentdetail['programmeId']}}<br />Tutorial Group: {{$studentdetail['tutorialGroup']}}<br />Phone: {{$studentdetail['phoneNo']}}<br />TARUC Email: {{$studentdetail['TARCemail']}}<br />Team ID: {{$studentdetail['teamId']}}
                                        </div><br />
                                        <?php
                                    }
                                    ?>
                                    @endforeach
                                </td>
                                <td>{{$row['moderator']}}</td>
                                <td>{{$row['status']}}</td>
                                <?php
                                $i++;
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 align="center">
        <button onClick="history.go(-1);" class="btn btn-info" style="width:300px;font-size:20px">Back</button>
    </h5>
</div>
@endsection