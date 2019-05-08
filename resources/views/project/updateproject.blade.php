@extends('layouts.app')
@section('title', 'Update Project')
@section('module', 'Project Page')
@section('content')
<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();
$GLOBALS['$databaseName'] = getenv('DB_DATABASE');
$GLOBALS['$usernameName'] = getenv('DB_USERNAME');
$GLOBALS['$passwordName'] = getenv('DB_PASSWORD');
?>
<script type="text/javascript">
    function showfield(name) {
        if (name == 'Other')
            document.getElementById('div1').style.display = "block";
        else {
            document.getElementById('div1').style.display = "none";
            document.cookie = 'updatedCluster=' + name;
            location.reload();
        }
    }

    function showfield1(name) {
        if (name == 'Other')
            document.getElementById('div2').style.display = "block";
        else
            document.getElementById('div2').style.display = "none";
    }
    
    function showfield2(name) {
        if (name == 'Other')
            document.getElementById('div3').style.display = "block";
        else
            document.getElementById('div3').style.display = "none";
    }

    function hidefield() {
        document.getElementById('div1').style.display = 'none';
        document.getElementById('div2').style.display = 'none';
        document.getElementById('div3').style.display = 'none';

//        document.getElementById("cluster").value = 'Testing';
//        document.getElementById("group").value = 'Testing';
    }

    function selectIngredient(select)
    {
        var option = select.options[select.selectedIndex];
        var ul = select.parentNode.getElementsByTagName('ul')[0];

        var choices = ul.getElementsByTagName('input');
        for (var i = 0; i < choices.length; i++)
            if (choices[i].value == option.value)
                return;

        var li = document.createElement('li');
        var input = document.createElement('input');
        var text = document.createTextNode(option.firstChild.data);

        input.type = 'hidden';
        input.name = 'ingredients[]';
        input.value = option.value;

        li.appendChild(input);
        li.appendChild(text);
        li.setAttribute('onclick', 'this.parentNode.removeChild(this);');

        ul.appendChild(li);
    }
	
	function selectSupervisor(select)
    {
        var option = select.options[select.selectedIndex];
        var ul = select.parentNode.getElementsByTagName('ul')[0];

        var choices = ul.getElementsByTagName('input');
        for (var i = 0; i < choices.length; i++)
            if (choices[i].value == option.value)
                return;

        var li = document.createElement('li');
        var input = document.createElement('input');
        var text = document.createTextNode(option.firstChild.data);

        input.type = 'hidden';
        input.name = 'supervisors[]';
        input.value = option.value;

        li.appendChild(input);
        li.appendChild(text);
        li.setAttribute('onclick', 'this.parentNode.removeChild(this);');

        ul.appendChild(li);
    }

</script>

<style>
    .error {color: #FF0000;}
</style>

<meta charset="UTF-8">

<body onload="hidefield()">
    <div class="container">
        <br />
        @if (\Session::has('fail'))
        <div class="alert alert-danger">
            <p>{{ \Session::get('fail') }}</p>
        </div><br />
        @endif
        <h3 align="center">Update Project</h3><br />
        @foreach($project as $projectdetail)
        <form method="post" action="{{action('ProjectController@update', $projectdetail->projectCode)}}">
            @csrf 
            <input name="_method" type="hidden" value="PATCH">
            <p><span class="error">* required field</span></p>

            <table class="table table-striped" border="">
                
                <tr>
                    <td style="width:20%"><label for="cluster">Project Cluster</label></td>
                    <td>:</td>
                    <td><select style="width:300px" value="{{$projectdetail->cluster}}" name="cluster" id="cluster" onchange="showfield(this.options[this.selectedIndex].value)">
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT cluster From project GROUP BY cluster");
                            $row = mysqli_num_rows($sql);
                            $select = "selected";
                            while ($row = mysqli_fetch_array($sql)) {
                                if (isset($_COOKIE['updatedCluster'])) {
                                    if ($_COOKIE['updatedCluster'] == $row['cluster']) {
                                        if ($row['cluster'] != NULL) {
                                        echo "<option selected='selected' value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                                        }
                                    } else {
                                        if ($row['cluster'] != NULL) {
                                        echo "<option  value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                                        }
                                    }
                                } else {
                                    if ($projectdetail->cluster == $row['cluster']) {
                                        if ($row['cluster'] != NULL) {
                                        echo "<option value='" . $row['cluster'] . "' selected='" . $select . "'>" . $row['cluster'] . "</option>";
                                        }
                                    } else {
                                        if ($row['cluster'] != NULL) {
                                        echo "<option value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                                        }
                                    }
                                }
                            }
                            ?>
                            <option value="Other">Other</option>
                        </select>
                        <div id="div1">New Cluster: <input type="text" name="newcluster"/></div></td>
                    <td style="width:30%"><span class="error">* Project Cluster is required</span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="group">Project Group</label></td>
                    <td>:</td>
                    <td><select style="width:300px" value="{{$projectdetail->projectGroup}}" name="group" id="group" onchange="showfield1(this.options[this.selectedIndex].value)">
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT cluster, projectGroup From project GROUP BY cluster, projectGroup");
                            $row = mysqli_num_rows($sql);
                            if (isset($_COOKIE['updatedCluster'])) {
                                while ($row = mysqli_fetch_array($sql)) {

                                    if ($_COOKIE['updatedCluster'] == $row['cluster']) {
                                        if ($row['projectGroup'] != NULL) {
                                        echo "<option  value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                        }
                                    }
                                }
                                echo "<option value='Other'>Other</option>";
                            } else {
                                while ($row = mysqli_fetch_array($sql)) {
                                    if ($projectdetail->projectGroup == $row['projectGroup']) {
                                        if ($row['projectGroup'] != NULL) {
                                        echo "<option value='" . $row['projectGroup'] . "' selected='" . $select . "'>" . $row['projectGroup'] . "</option>";
                                        }
                                    } else {
                                        if ($row['projectGroup'] != NULL) {
                                        echo "<option value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                        }
                                    }
                                }
                                echo "<option value='Other'>Other</option>";
                            }
                            ?>
                        </select>
                        <div id="div2">New Group: <input type="text" name="newgroup"/></div></td>
                    <td style="width:30%"><span class="error">* Project Group is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="code">Project Code</label></td>
                    <td>:</td>
                    <td><input style="width:300px" type="text" name="code" value="{{$projectdetail->projectCode}}" required="required"></td>
                    <td style="width:30%"><span class="error">* Project Code is required</span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="title">Project Title</label></td>
                    <td>:</td>
                    <td><input style="width:460px" type="text" name="title" value="{{$projectdetail->title}}" required="required"></td>
                    <td style="width:30%"><span class="error">* Project Title is required</span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="desc">Project Description</label></td>
                    <td>:</td>
                    <td><input style="width:460px" type="text" id="desc" name="desc" value="{{$projectdetail->description}}" required="required"></td>
                    <td style="width:30%"><span class="error">* Project Description is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="scope">Project Scope</label></td>
                    <td>:</td>
                    <td><input style="width:460px" type="text" id="scope" name="scope" value="{{$projectdetail->scope}}"></td>
                    <td style="width:30%"></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="enhancement">Project Enhancement</label></td>
                    <td>:</td>
                    <td><input style="width:460px" type="text" id="enhancement" name="enhancement" value="{{$projectdetail->enhancement}}"></td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="teamsize">Team Size</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="number" name="teamsize" value="{{$projectdetail->teamSize}}" min="1" max="6" required="required"></td>
                    <td style="width:30%"><span class="error">* Team Size is required</span></td>
                </tr>

                <tr>
                    <td style="width:20%"><label for="advisor">Project Advisor</label></td>
                    <td>:</td>
                    <td><ul><?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                $checkStaff = $row['staffName'];
                                //$checkStaff = $row['staffName'] . '<br />';
                                if (strpos($projectdetail->advisor, $checkStaff) !== false) {
                                    echo "<li onclick='this.parentNode.removeChild(this);'><input type='hidden' name='ingredients[]' value='" . $row['staffName'] . "' />" . $row['staffName'] . "</li>";
                                }
                            }
                            ?></ul><select style="width:200px" onchange="selectIngredient(this);">
                            <option value="" disabled selected>Select project advisor</option>
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                echo "<option value='" . $row['staffName'] . "'>" . $row['staffName'] . "</option>";
                            }
                            ?>
                        </select> (Click again to remove)</td>
                    <td style="width:30%"><span class="error">* Project Advisor is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="advisor">Project Supervisor</label></td>
                    <td>:</td>
                    <td><ul><?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                $checkStaff = $row['staffId'];
                                //$checkStaff = $row['staffName'] . '<br />';
								foreach($project_supervisor as $project_supervisord){
                                if ($project_supervisord->projectCode == $projectdetail->projectCode && $project_supervisord->supervisorId == $checkStaff) {
                                    echo "<li onclick='this.parentNode.removeChild(this);'><input type='hidden' name='supervisors[]' value='" . $row['staffId'] . "' />" . $row['staffName'] . "</li>";
                                }
								}
                            }
                            ?></ul><select style="width:200px" onchange="selectSupervisor(this);">
                            <option value="" disabled selected>Select project supervisor</option>
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                echo "<option value='" . $row['staffId'] . "'>" . $row['staffName'] . "</option>";
                            }
                            ?>
                        </select> (Click again to remove)</td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="clientName">Client Name</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="text" name="clientName" value="{{$projectdetail->clientName}}"></td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="level">Project Level</label></td>
                    <td>:</td>
                    <td><select style="width:200px" name="level">
                            <?php
                            $select = "selected";
                            if ($projectdetail->level == 'Bachelor') {
                                echo "<option selected='" . $select . "'>Bachelor</option>";
                            } else {
                                echo "<option>Bachelor</option>";
                            }
                            if ($projectdetail->level == 'Master') {
                                echo "<option selected='" . $select . "'>Master</option>";
                            } else {
                                echo "<option>Master</option>";
                            }
                            if ($projectdetail->level == 'PhD') {
                                echo "<option selected='" . $select . "'>PhD</option>";
                            } else {
                                echo "<option>PhD</option>";
                            }
                            ?>
                        </select></td>
                    <td style="width:30%"><span class="error">* Project Level is required</span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="status">Project Status</label></td>
                    <td>:</td>
                    <td><select style="width:200px" name="status">
                        <?php
                            $select = "selected";
                            if ($projectdetail->status == 'New') {
                                echo "<option selected='" . $select . "'>New</option>";
                            } else {
                                echo "<option>New</option>";
                            }
                            if ($projectdetail->status == 'Continued') {
                                echo "<option selected='" . $select . "'>Continued</option>";
                            } else {
                                echo "<option>Continued</option>";
                            }
                            if ($projectdetail->status == 'Ongoing') {
                                echo "<option selected='" . $select . "'>Ongoing</option>";
                            } else {
                                echo "<option>Ongoing</option>";
                            }
							if ($projectdetail->status == 'Completed') {
                                echo "<option selected='" . $select . "'>Completed</option>";
                            } else {
                                echo "<option>Completed</option>";
                            }
                            ?> 
                        </select></td>
                    <td style="width:30%"><span class="error">* Project Status is required</span></td>
                </tr>
            </table>
            @endforeach
            <br /><h5 align="center" ><button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Update Project</button></h5><br />
        </form> 
    </div>
</body>
@endsection