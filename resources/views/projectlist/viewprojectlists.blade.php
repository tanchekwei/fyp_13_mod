@extends('layouts.app')
@section('title', 'Project List Page')
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
if (isset($_POST['cluster'])) {
    if (isset($_SESSION['cluster'])) {
        if ($_SESSION['cluster'] == $_POST['cluster']) {
            if ($_POST['group'] == 'all') {
                if ($_POST['cluster'] == 'all') {
                    $_SESSION['cluster'] = $_POST['cluster'];
                    $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
					?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
                    $search_result = filterTable($query);
                } else {
                    $_SESSION['cluster'] = $_POST['cluster'];
                    $valueToSearch = $_POST['cluster'];
                    $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.cluster = '$valueToSearch'";
					?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed' AND project.cluster = '$valueToSearch'";
					?>
					@endauth
					<?php
                    $search_result = filterTable($query);
                }
            } else {
                if ($_POST['cluster'] == 'all') {
                    $_SESSION['cluster'] = $_POST['cluster'];
                    $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
					?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
                    $search_result = filterTable($query);
                } else {
                    $_SESSION['cluster'] = $_POST['cluster'];
                    $_SESSION['group'] = $_POST['group'];
                    $valueToSearch = $_POST['cluster'];
                    $selectedGroup = $_POST['group'];
                    if ($selectedGroup == 'all') {
                        $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
						?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
                        $search_result = filterTable($query);
                    } else {
                        $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.projectGroup = '$selectedGroup'";
                        ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed' AND project.projectGroup = '$selectedGroup'";
					?>
					@endauth
					<?php
						$search_result = filterTable($query);
                    }
                }
            }
        } else {
            if ($_POST['cluster'] == 'all') {
                $_SESSION['cluster'] = $_POST['cluster'];
                $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
                ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
				
				$search_result = filterTable($query);
            } else {
                $_SESSION['cluster'] = $_POST['cluster'];
                $valueToSearch = $_POST['cluster'];
                $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.cluster = '$valueToSearch'";
                ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed' AND project.cluster = '$valueToSearch'";
					?>
					@endauth
					<?php
				$search_result = filterTable($query);
            }
        }
    } else {
        if ($_POST['cluster'] == 'all') {
            $_SESSION['cluster'] = $_POST['cluster'];
            $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
            ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
			$search_result = filterTable($query);
        } else {
            $_SESSION['cluster'] = $_POST['cluster'];
            $valueToSearch = $_POST['cluster'];
            $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.cluster = '$valueToSearch'";
            ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed' AND project.cluster = '$valueToSearch'";
					?>
					@endauth
					<?php
			$search_result = filterTable($query);
        }
    }
} else {
    if (isset($_POST['group'])) {
        if ($_POST['group'] == 'all') {
            $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
            ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
			$search_result = filterTable($query);
        } else {
            $selectedGroup = $_POST['group'];
            $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.projectGroup = '$selectedGroup'";
            ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed' AND project.projectGroup = '$selectedGroup'";
					?>
					@endauth
					<?php
			$search_result = filterTable($query);
        }
    } else {
        $query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode";
        ?>
					@auth('student')
					<?php
					$query = "SELECT team.*, project.projectGroup FROM `team`, `project` WHERE project.projectCode = team.projectCode AND project.status != 'Completed'";
					?>
					@endauth
					<?php
		$search_result = filterTable($query);
    }
}

// function to connect and execute the query
function filterTable($query) {
    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $filter_Result = mysqli_query($connect, $query);
    return $filter_Result;
}
?>
<style>
    .vertical-menu {
        width: 2200px;
    }

    .vertical-menu2 {
        width: 100%;
        height: 520px;
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
        width: 350px;
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
<meta charset="utf-8">
<div class="container">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    <h3 align="center">Project List Management</h3><br />
    <form action="{{ route('projectlist.viewprojectlists') }}" method="post">
        @csrf
        <h5 align="left">Cluster: <select name="cluster" onchange="this.form.submit()">

                <?php
                $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                $sql = mysqli_query($connect, "SELECT cluster From project GROUP BY cluster");
                $row = mysqli_num_rows($sql);
                echo "<option value='all'>All</option>";
                while ($row = mysqli_fetch_array($sql)) {
                    if (isset($_POST['cluster'])) {
                        if ($_POST['cluster'] == $row['cluster']) {
                            if ($row['cluster'] != NULL) {
                                echo "<option selected='selected' value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                            }
                        } else {
                            if ($row['cluster'] != NULL) {
                                echo "<option  value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                            }
                        }
                    } else {
                        if ($row['cluster'] != NULL) {
                            echo "<option  value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                        }
                    }
                }
                ?>
            </select>
            Project Group: <select style="width:300px" name="group"onchange="this.form.submit()">
                <?php
                $select = "selected";
                $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                $sql = mysqli_query($connect, "SELECT cluster, projectGroup From project GROUP BY cluster, projectGroup");
                $row = mysqli_num_rows($sql);
                if (isset($_POST['group'])) {
                    if (isset($_POST['cluster'])) {
                        if ($_POST['cluster'] == 'all') {
                            echo "<option value='all'>All</option>";
                        } else {
                            echo "<option value='all'>All</option>";
                            while ($row = mysqli_fetch_array($sql)) {
                                if ($_POST['cluster'] == $row['cluster']) {
                                    if ($_POST['group'] == $row['projectGroup']) {
                                        if ($row['projectGroup'] != NULL) {
                                            echo "<option selected='" . $select . " value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                        }
                                    } else {
                                        if ($row['projectGroup'] != NULL) {
                                            echo "<option value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        echo "<option value='all'>All</option>";
                    }
                } else {
                    if (isset($_POST['cluster'])) {
                        if ($_POST['cluster'] == 'all') {
                            echo "<option value='all'>All</option>";
                        } else {
                            echo "<option value='all'>All</option>";
                            while ($row = mysqli_fetch_array($sql)) {
                                if ($_POST['cluster'] == $row['cluster']) {
                                    if ($row['projectGroup'] != NULL) {
                                        echo "<option  value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                    }
                                }
                            }
                        }
                    } else {
                        echo "<option value='all'>All</option>";
                    }
                }
                ?>
            </select>
        </h5>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table"></i>
                Project List Details</div>
            <div class="card-body">
                <div class="table-responsive" style="height:500px">
                    <table class="table table-bordered" id="dataTable" style="width:2000px;font-size:14px" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="9%">Project Code</th>
                                <th width="6%">Generation</th>
								<th width="7%">Supervisor</th>
								<th width="9%">Student Name</th>
                                <th width="18%">Project Scope</th>
                                <th width="18%">Project Enhancement</th>
								<th width="18%">Team Scope</th>
                                <th width="10%">Project Status</th>
								<th>Action</th>
                                <th>Action</th>
								<th>Action</th>
                            </tr>
                        </thead>
						<tbody>
                            <?php
                            $i = 1;

                            foreach ($search_result as $team_detail) {
                                ?>
                                <tr>
									<td>{{$i}}</td>
                                    <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT * From project WHERE projectCode ="' . $team_detail['projectCode'] . '"');
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
										$sql = mysqli_query($connect, 'SELECT generation From project WHERE projectCode ="' . $team_detail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['generation']}}
											<?php
										}
										?>
                                    </td>
									<td class="hover">
                                        <?php
                                        $staffExist = 0;
										$staffsExist = 0;
										?>
										@auth('staff')
										<?php
										if(Auth::guard('staff')->user()->role == 'admin' OR Auth::guard('staff')->user()->role == 'facultyadmin' OR Auth::guard('staff')->user()->role == 'fypcommittee'){
										$staffsExist++;
										}
										?>
										@endauth
										<?php
										$sql = mysqli_query($connect, 'SELECT * From staff WHERE staffId ="' . $team_detail['supervisor'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
                                        ?>
                                        @auth('staff')
                                        <?php
                                        if ($row['staffId'] == $team_detail['supervisor']) {
                                            if ($row['staffId'] == Auth::guard('staff')->user()->staffId) {
                                                $staffExist++;
                                            }
                                            ?>
                                            <a href="#">{{$row['title']}}. {{$row['staffName']}}</a><div>
                                                Staff ID: {{$row['staffId']}}<br />Name: {{$row['staffName']}}<br />Email: {{$row['email']}}<br />Department: {{$row['departmentId']}}<br />Specialization: {{$row['specialization']}}
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        @endauth
                                        @auth('student')
                                        <?php
                                        if ($row['staffId'] == $team_detail['supervisor']) {
                                            ?>
                                            <a href="#">{{$row['title']}}. {{$row['staffName']}}</a><div>
                                                Staff ID: {{$row['staffId']}}<br />Name: {{$row['staffName']}}<br />Email: {{$row['email']}}<br />Department: {{$row['departmentId']}}<br />Specialization: {{$row['specialization']}}
                                            </div><br />
                                            <?php
                                        }
                                        ?>
                                        @endauth
                                        <?php
										}
										?>
                                    </td>
									<td class="hover">
                                        <?php
                                        $countStudentperteam = 0;
                                        $studentExist = 0;
										$sql = mysqli_query($connect, 'SELECT * From student WHERE teamId ="' . $team_detail['teamId'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
                                        ?>
                                        @auth('student')
                                        <?php
                                        if ($row['teamId'] == $team_detail['teamId']) {
                                            if ($row['studentName'] == Auth::guard('student')->user()->studentName) {
                                                $studentExist++;
                                            }
                                            ?>
                                            <a href="#">{{$row['studentName']}}</a><div>
                                                Student ID: {{$row['studentId']}}<br />Student Name: {{$row['studentName']}}<br />Programme: {{$row['programmeId']}}<br />Tutorial Group: {{$row['tutorialGroup']}}@auth('staff')<br />Phone: {{$row['phoneNo']}}@endauth<br />TARUC Email: {{$row['TARCemail']}}
                                            </div><br />
                                            <?php
                                            $countStudentperteam++;
                                        }
                                        ?>
                                        @endauth
                                        @auth('staff')
                                        <?php
                                        if ($row['teamId'] == $team_detail['teamId']) {
                                            ?>
                                            <a href="#">{{$row['studentName']}}</a><div>
                                                Student ID: {{$row['studentId']}}<br />Student Name: {{$row['studentName']}}<br />Programme: {{$row['programmeId']}}<br />Tutorial Group: {{$row['tutorialGroup']}}@auth('staff')<br />Phone: {{$row['phoneNo']}}@endauth<br />TARUC Email: {{$row['TARCemail']}}
                                            </div><br />
                                            <?php
                                            $countStudentperteam++;
                                        }
                                        ?>
                                        @endauth
										<?php
										}
										?>
                                    </td>
                                    <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT scope From project WHERE projectCode ="' . $team_detail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['scope']}}
											<?php
										}
										?>
                                    </td>
									<td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT enhancement From project WHERE projectCode ="' . $team_detail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['enhancement']}}
											<?php
										}
										?>
                                    </td>
                                    <td>{{$team_detail['teamScope']}}</td>
                                    <td class="hover">
										<?php
										$sql = mysqli_query($connect, 'SELECT status From project WHERE projectCode ="' . $team_detail['projectCode'] . '"');
										$row = mysqli_num_rows($sql);
										while ($row = mysqli_fetch_array($sql)) {
										?>{{$row['status']}}
											<?php
										}
										?>
                                    </td>
									@auth('staff')
                                    <td>
										<?php
                                        if ($staffExist != 0 OR $staffsExist != 0) {
                                            ?>
                                        <form></form>
                                        <form action="{{action('ProjectListController@view', $team_detail['projectCode'])}}" method="post">
                                            @csrf
                                            <button class="btn btn-warning" type="submit">View</button>
                                        </form>
										<?php
                                        } else {
                                            ?>
                                            <form action="{{action('ProjectListController@view', $team_detail['projectCode'])}}" method="post">
                                            @csrf
                                            <button class="btn btn-warning" type="submit" disabled="disabled">View</button>
                                        </form>
                                            <?php
                                        }
                                        ?>
										
                                    </td>
                                    @endauth
									@auth('student')
                                    <td>
										<?php
                                        if ($studentExist != 0) {
                                            ?>
                                        <form></form>
                                        <form action="{{action('ProjectListController@view', $team_detail['projectCode'])}}" method="post">
                                            @csrf
                                            <button class="btn btn-warning" type="submit">View</button>
                                        </form>
										<?php
                                        } else {
                                            ?>
                                            <form action="{{action('ProjectListController@view', $team_detail['projectCode'])}}" method="post">
                                            @csrf
                                            <button class="btn btn-warning" type="submit" disabled="disabled">View</button>
                                        </form>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    @endauth
                                    <td>
                                        <?php
                                        if ($studentExist != 0 OR $staffExist != 0 OR $staffsExist != 0) {
                                            ?>
                                            <form></form>
                                            <form action="{{action('ProjectListController@edit', $team_detail['teamId'])}}" method="post">
                                                @csrf
                                                <button class="btn btn-danger" type="submit">Update</button>
                                            </form>
                                            <?php
                                        } else {
                                            ?>
                                            <form action="{{action('ProjectListController@edit', $team_detail['teamId'])}}" method="post">
                                                @csrf
                                                <button class="btn btn-danger" disabled="disabled" type="submit">Update</button>
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </td>
									@auth('staff')
									<td>
									
										<?php
                                        if ($staffExist != 0 OR $staffsExist != 0) {
                                            ?>
                                        <form></form>
                                        <form action="{{route('project_repository', ['id'=>$team_detail['projectCode']])}}" method="get">
                                            @csrf
                                            <button class="btn btn-info" type="submit">Repository</button>
                                        </form>
										<?php
                                        } else {
                                            ?>
                                            <form action="{{route('project_repository', ['id'=>$team_detail['projectCode']])}}" method="get">
                                            @csrf
                                            <button class="btn btn-info" type="submit" disabled="disabled">Repository</button>
                                        </form>
                                       
                                            <?php
                                        }
                                        ?>
										
									</td> 
									@endauth
									
									@auth('student')
									<td>
										<?php
                                        if ($studentExist != 0) {
                                            ?>
                                        <form></form>
                                        <form action="{{ route('student_home') }}" method="get">
                                            @csrf
                                            <button class="btn btn-info" type="submit">Repository</button>
                                        </form>
										<?php
                                        } else {
                                            ?>
                                            <form action="{{route('student_home') }}" method="get">
                                            @csrf
                                            <button class="btn btn-info" type="submit" disabled="disabled">Repository</button>
                                        </form>
                                            <?php
                                        }
                                        ?>
                                    </td>
									@endauth

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

    </form>
</div>
@endsection                             