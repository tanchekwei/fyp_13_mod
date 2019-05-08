@extends('layouts.app')
@section('title', 'Project Page')
@section('module', 'Project Page')
@section('content')
<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();
$GLOBALS['$databaseName'] = getenv('DB_DATABASE');
$GLOBALS['$usernameName'] = getenv('DB_USERNAME');
$GLOBALS['$passwordName'] = getenv('DB_PASSWORD');

if (isset($_COOKIE['selectedCluster'])) {
    unset($_COOKIE['selectedCluster']);
    setcookie('selectedCluster', '', time() - 3600);
}
if (isset($_COOKIE['updatedCluster'])) {
    unset($_COOKIE['updatedCluster']);
    setcookie('updatedCluster', '', time() - 3600);
}

if (isset($_POST['cluster'])) {
    if ($_POST['status'] == 'All') {
        if ($_POST['cluster'] == 'all') {
            $query = "SELECT * FROM `project`";
            $search_result = filterTable($query);
        } else {
            $_SESSION['cluster'] = $_POST['cluster'];
            $valueToSearch = $_POST['cluster'];
            $query = "SELECT * FROM `project` WHERE cluster = '$valueToSearch'";
            $search_result = filterTable($query);
        }
    } else {
        if ($_POST['cluster'] == 'all') {
            $selectedStatus = $_POST['status'];
            $query = "SELECT * FROM `project` WHERE status ='$selectedStatus'";
            $search_result = filterTable($query);
        } else {
            $_SESSION['cluster'] = $_POST['cluster'];
            $_SESSION['status'] = $_POST['status'];
            $valueToSearch = $_POST['cluster'];
            $selectedStatus = $_POST['status'];
            if ($selectedStatus == 'All') {
                $query = "SELECT * FROM `project` WHERE cluster = '$valueToSearch'";
                $search_result = filterTable($query);
            } else {
                $query = "SELECT * FROM `project` WHERE cluster = '$valueToSearch' AND status ='$selectedStatus'";
                $search_result = filterTable($query);
            }
        }
    }
} else {
    if (isset($_POST['status'])) {
        if ($_POST['status'] == 'All') {
            $query = "SELECT * FROM `project`";
            $search_result = filterTable($query);
        } else {
            $selectedStatus = $_POST['status'];
            $query = "SELECT * FROM `project` WHERE status ='$selectedStatus'";
            $search_result = filterTable($query);
        }
    } else {
        $query = "SELECT * FROM `project`";
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
        width: 1800px;
    }

    .vertical-menu2 {
        width: 100%;
        height: 400px;
        overflow-x: scroll;
        overflow-y: scroll;
        overflow: auto;
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
<script>
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
</script>
<script>$(document).ready(function () {
        $('.table-responsive').doubleScroll();
    });</script>
<div class="container">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div>
    @endif
    @if (\Session::has('fail'))
    <div class="alert alert-danger">
        <p>{{ \Session::get('fail') }}</p>
    </div>
    @endif
    <h3 align="center">Project Management</h3><br />
    <form action="{{ route('project.viewproject') }}" method="post">
        @csrf
        <h6 align="left">Cluster: <select name="cluster" onchange="this.form.submit()">
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
            Status: <select name="status" onchange="this.form.submit()">
                <?php
                $select = "selected";
                if (isset($_POST['status'])) {
                    $selectedStatus = $_POST['status'];
					echo "<option  value='All'>All</option>";
					if($selectedStatus == "New"){
						echo "<option  selected='selected' value='New'>New</option>";	
					} else {
						echo "<option value='New'>New</option>";	
					}
					if($selectedStatus == "Continued"){
						echo "<option  selected='selected' value='Continued'>Continued</option>";	
					} else {
						echo "<option value='Continued'>Continued</option>";	
					}
					if($selectedStatus == "Ongoing"){
						echo "<option  selected='selected' value='Ongoing'>Ongoing</option>";	
					} else {
						echo "<option value='Ongoing'>Ongoing</option>";	
					}
					if($selectedStatus == "Completed"){
						echo "<option  selected='selected' value='Completed'>Completed</option>";	
					} else {
						echo "<option value='Completed'>Completed</option>";	
					}
                } else {
                    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                    $sql = mysqli_query($connect, "SELECT status From project GROUP BY status");
                    $row = mysqli_num_rows($sql);
                    echo "<option  value='All'>All</option>";
					echo "<option  value='New'>New</option>";
					echo "<option  value='Continued'>Continued</option>";
					echo "<option  value='Ongoing'>Ongoing</option>";
					echo "<option  value='Completed'>Completed</option>";
                    
                }
                ?>
            </select></h6>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table"></i>
                Project Details</div>
            <div class="card-body">
                <div class="table-responsive" style="height:500px">
                    <table class="table table-bordered" id="dataTable" style="width:3000px;font-size:14px" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="5%">Project Code</th>
                                <th width="6%">Project Title</th>
                                <th width="12%">Project Description</th>
                                <th width="5%">Project Cluster</th>
                                <th width="6%">Project Group</th>
								<th width="4%">Generation</th>
								<th width="12%">Scope</th>
								<th width="12%">Enhancement</th>
                                <th width="7%">Project Advisor</th>
                                <th width="6%">Client Name</th>
                                <th width="8%">Project Supervisor</th>
                                <th width="6%">Project Team Size</th>
                                <th width="5%">Project Level</th>
                                <th width="6%">Project Status</th>
                                <th>Action</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = mysqli_fetch_array($search_result)):
                                ?>

                                <tr>
                                    <td>{{$i}}</td>
                                    <td><?php echo $row['projectCode']; ?></td>
                                    <td><?php echo $row['title']; ?></td>
									<?php
									if($row['description'] == ""){
									$row['description'] = "N/A";
									}
									if($row['scope'] == ""){
									$row['scope'] = "N/A";
									}
									if($row['enhancement'] == ""){
									$row['enhancement'] = "N/A";
									}
									if($row['clientName'] == ""){
									$row['clientName'] = "N/A";
									}
									if($row['generation'] == ""){
									$row['generation'] = "N/A";
									}
									if($row['level'] == ""){
									$row['level'] = "N/A";
									}
									if($row['advisor'] == ""){
									$row['advisor'] = "N/A";
									}
									if($row['teamSize'] == ""){
									$row['teamSize'] = "N/A";
									}
									?>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo $row['cluster']; ?></td>
                                    <td><?php echo $row['projectGroup']; ?></td>
									<td><?php echo $row['generation']; ?></td>
									<td><?php echo $row['scope']; ?></td>
									<td><?php echo $row['enhancement']; ?></td>
                                    <td><?php echo $row['advisor']; ?></td>
                                    <td><?php echo $row['clientName']; ?></td>
                                    <td class="hover">
									<?php
									$supervisorsExist = 0;
									$supervisorExist = 0;
									if (Auth::guard('staff')->user()->role == "admin" OR Auth::guard('staff')->user()->role == "fypcommittee" OR Auth::guard('staff')->user()->role == "facultyadmin") {
                                                $supervisorExist++;
                                    }
									$sqlstaff = mysqli_query($connect, 'SELECT staff.* From `staff`, `project_supervisor` WHERE staff.staffId = project_supervisor.supervisorId AND project_supervisor.projectCode = "' . $row['projectCode'] . '"');
										$rowstaff = mysqli_num_rows($sqlstaff);
										while ($rowstaff = mysqli_fetch_array($sqlstaff)) {
									?>
                                            <a href="#">{{$rowstaff['title']}}. {{$rowstaff['staffName']}}</a><div>
                                                Name: {{$rowstaff['staffName']}}<br />Email: {{$rowstaff['email']}}<br />Department: {{$rowstaff['departmentId']}}<br />Specialization: {{$rowstaff['specialization']}}
                                            </div><br />
                                            <?php
											if($rowstaff['staffId'] == Auth::guard('staff')->user()->staffId){ 
											$supervisorExist++;
											}
											$supervisorsExist++;
                                        }
										
                                        ?>

										<?php
										
										if($supervisorsExist == 0){
										echo "N/A";
										}
										?>
                                    </td>
                                    <td><?php echo $row['teamSize']; ?></td>
                                    <td><?php echo $row['level']; ?></td>
                                    <td><?php echo $row['status']; ?></td>

                                    <?php
                                    $i++;
                                    ?>
                                    <td>
                                        <form></form>
										<?php
                                        if ($supervisorExist != 0) {
                                            ?>
                                        <form action="{{action('ProjectController@updateproject', $row['projectCode'])}}"
                                              method="post">
                                            {{ csrf_field() }}
                                            <button class="btn btn-warning" type="submit">Update</button>
                                        </form>
										<?php
                                        } else {
                                            ?>
                                            <form action="{{action('ProjectController@updateproject', $row['projectCode'])}}"
                                              method="post">
                                            {{ csrf_field() }}
                                            <button class="btn btn-warning" disabled="disabled" type="submit">Update</button>
                                        </form>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $teamExist = 0;
                                        ?>
                                        <?php
                                        if ($row['status'] == "New") {
                                            ?>
                                            <form action="{{action('ProjectController@removeproject', $row['projectCode'])}}" method="get">
                                                {{ csrf_field() }}
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                            </form>
                                            <?php
                                        } else {
                                            ?>
                                            <form action="{{action('ProjectController@removeproject', $row['projectCode'])}}" method="get">
                                                {{ csrf_field() }}
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger" disabled="disabled" type="submit">Delete</button>
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br /><form action="{{ route('project.createproject') }}" method="post">
                {{ csrf_field() }}
                <h5 align="center">
                    <button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Create Project</button>
                </h5>
            </form>
    </form>
</div>
@endsection
