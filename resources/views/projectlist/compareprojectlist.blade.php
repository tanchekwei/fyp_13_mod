@extends('layouts.app')
@section('title', 'Compare Project List')
@section('module', 'ProjectList Page')
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
        width: 2200px;
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
        width: 300px;
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
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    <h3 align="center">View Project List</h3><br />
    <h5 align="left">Project Code: {{$projectCode}}</h5>

    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-table"></i>
            Team Details</div>
        <div class="card-body">
            <div class="table-responsive" style="height:400px">
                <table class="table table-bordered" id="dataTable" style="width:2200px;font-size:14px" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th width="8%">Project Generation</th>
                            <th width="8%">Advisor</th>
                            <th width="8%">Supervisor</th>
                            <th width="8%">Student Name</th>
                            <th width="6%">Team Size</th>
                            <th width="14%">Project Scope</th>
                            <th width="14%">Project Enhancement</th>
							<th width="14%">Team Scope</th>
                            <th width="8%">Competition Name</th>
                            <th width="10%">Project Status</th>

                        </tr>
                    </thead>
                    <tbody>          
                        <?php
                        $i = 1;
                        ?>
                        @foreach($team as $teamdetail)
                        <tr>
                            <td>{{$i}}</td>
                            <td class="hover">
										<?php
										$connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
										$sql = mysqli_query($connect, 'SELECT * From project WHERE projectCode ="' . $teamdetail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
											$advisorName = str_replace("<br />", ", ", $row['advisor']);
										?>
										<a href="#">{{$row['projectCode']}}</a><div>
                                                Project Code: {{$row['projectCode']}}<br />Title: {{$row['title']}}<br />Cluster: {{$row['cluster']}}<br />Group: {{$row['projectGroup']}}<br />Status: {{$row['status']}}<br />Advisor: {{$advisorName}}<br />Team Size: {{$row['teamSize']}}
                                            </div>
											
											<?php
										}
										?>
                                    </td>
                            <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT advisor From project WHERE projectCode ="' . $teamdetail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										$advisorName = str_replace("<br />", ", ", $row['advisor']);
										while ($row = mysqli_fetch_array($sql)) {
											$advisorName = str_replace("<br />", ", ", $row['advisor']);
										?>{{$advisorName}}
											<?php
										}
										?>
                                    </td>
							<td class="hover">
                            <?php
							$sql = mysqli_query($connect, 'SELECT * From staff WHERE staffId ="' . $teamdetail['supervisor'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
                            if ($row['staffId'] == $teamdetail['supervisor']) {
                                ?>
                                <a href="#">{{$row['title']}}. {{$row['staffName']}}</a><div>
                                                Staff ID: {{$row['staffId']}}<br />Name: {{$row['staffName']}}<br />Email: {{$row['email']}}<br />Department: {{$row['departmentId']}}<br />Specialization: {{$row['specialization']}}
                                            </div>
                                <?php
                            }
										}
                            ?>
                            
							</td>
                            <td class="hover">
                            <?php
							$sql = mysqli_query($connect, 'SELECT * From student WHERE teamId ="' . $teamdetail['teamId'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
                            if ($row['teamId'] == $teamdetail['teamId']) {
                                ?>
                                <a href="#">{{$row['studentName']}}</a><div>
                                                Student ID: {{$row['studentId']}}<br />Student Name: {{$row['studentName']}}<br />Programme: {{$row['programmeId']}}<br />Tutorial Group: {{$row['tutorialGroup']}}@auth('staff')<br />Phone: {{$row['phoneNo']}}@endauth<br />TARUC Email: {{$row['TARCemail']}}
                                            </div><br />
                                <?php
                            }
										}
                            ?>
                            
							</td>
                            <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT teamSize From project WHERE projectCode ="' . $teamdetail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['teamSize']}}
											<?php
										}
										?>
                                    </td>
                            <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT scope From project WHERE projectCode ="' . $teamdetail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['scope']}}
											<?php
										}
										?>
                                    </td>
                            <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT enhancement From project WHERE projectCode ="' . $teamdetail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['enhancement']}}
											<?php
										}
										?>
                                    </td>
							<td>{{$teamdetail['teamScope']}}</td>
                            <td>{{$teamdetail['competitionName']}}</td>
                            <td>{{$teamdetail['status']}}</td>
                            <?php
                            $i++;
                            ?>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <h5 align="center">
            <button onClick="history.go(-1);" class="btn btn-info" style="width:300px;font-size:20px">Back</button>
        </h5>
    </div>
    @endsection
