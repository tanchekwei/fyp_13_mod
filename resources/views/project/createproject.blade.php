@extends('layouts.app')
@section('title', 'Create Project')
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
            document.cookie = 'selectedCluster=' + name;
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
        <h3 align="center">Create Project</h3><br />
        <form method="post" action="{{action('ProjectController@store')}}">
            @csrf 
            <p><span class="error">* required field</span></p>
            <table class="table table-striped" border="">
                <tr>
                    <td style="width:20%"><label for="cluster">Project Cluster</label></td>
                    <td>:</td>
                    <td><select style="width:300px" name="cluster" onchange="showfield(this.options[this.selectedIndex].value)">
                            <option>Please select cluster</option>
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT cluster From project GROUP BY cluster");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                if (isset($_COOKIE['selectedCluster'])) {
                                    if ($_COOKIE['selectedCluster'] == $row['cluster']) {
                                        if($row['cluster'] != NULL){
                                        echo "<option selected='selected' value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                                        }
                                    } else {
                                        if($row['cluster'] != NULL){
                                        echo "<option  value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
                                        }
                                    }
                                } else {
                                    if($row['cluster'] != NULL){
                                    echo "<option  value='" . $row['cluster'] . "'>" . $row['cluster'] . "</option>";
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
                    <td><select style="width:300px" name="group"onchange="showfield1(this.options[this.selectedIndex].value)">
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT cluster, projectGroup From project GROUP BY cluster, projectGroup");
                            $row = mysqli_num_rows($sql);
                            if (isset($_COOKIE['selectedCluster'])) {
                                if ($_COOKIE['selectedCluster'] == "Please select cluster") {
                                    echo "<option value='Other'>Please select cluster first</option>";
                                } else {
                                    while ($row = mysqli_fetch_array($sql)) {
                                        if ($_COOKIE['selectedCluster'] == $row['cluster']) {
                                            if($row['projectGroup'] != NULL){
                                            echo "<option  value='" . $row['projectGroup'] . "'>" . $row['projectGroup'] . "</option>";
                                            }
                                        }
                                    }
                                    echo "<option value='Other'>Other</option>";
                                }
                            } else {
                                echo "<option value='all'>Please select cluster first</option>";
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
                    <td><input style="width:300px" type="text" name="code" required="required"> </td>
                    <td style="width:30%"><span class="error">* Project Code is required</span></td>
                </tr>

                <tr>
                    <td style="width:20%"><label for="title">Project Title</label></td>
                    <td>:</td>
                    <td><input style="width:300px" type="text" name="title" required="required"></td>
                    <td style="width:30%"><span class="error">* Project Title is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="desc">Project Description</label></td>
                    <td>:</td>
                    <td><input style="width:440px" type="text" name="desc" required="required"></td>
                    <td style="width:30%"><span class="error">* Project Description is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="scope">Project Scope</label></td>
                    <td>:</td>
                    <td><input style="width:440px" type="text" name="scope"></td>
                    <td style="width:30%"></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="enhancement">Project Enhancement</label></td>
                    <td>:</td>
                    <td><input style="width:440px" type="text" name="enhancement"></td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="teamsize">Team Size</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="number" min="1" max="6" name="teamsize" required="required"></td>
                    <td style="width:30%"><span class="error">* Team Size is required</span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="advisor">Project Advisor</label></td>
                    <td>:</td>
                    <td><ul></ul><select style="width:200px" onchange="selectIngredient(this);">
                            <option value="" disabled selected>Select project advisor</option>
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffName, staffId");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                echo "<option value='" . $row['staffName'] . "'>" . $row['staffName'] . "</option>";
                            }
                            ?>
                        </select> (Click again to remove)</td>
                    <td style="width:30%"><span class="error">* Project Advisor is required</span></td>
                </tr>
				<tr>
                    <td style="width:20%"><label for="supervisor">Project Supervisor</label></td>
                    <td>:</td>
                    <td><ul></ul><select style="width:200px" onchange="selectSupervisor(this);">
                            <option value="" disabled selected>Select project supervisor</option>
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffName, staffId");
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
                    <td><input style="width:200px" type="text" name="clientName"></td>
                    <td style="width:30%"></td>
                </tr>

                <tr>
                    <td style="width:20%"><label for="level">Project Level</label></td>
                    <td>:</td>
                    <td><select style="width:200px" name="level">
                            <option>Bachelor</option>
                            <option>Master</option>
                            <option>PhD</option>
                        </select></td>
                    <td style="width:30%"><span class="error">* Project Level is required</span></td>
                </tr>
            </table>
            <br /><h5 align="center" ><button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Create Project</button></h5><br />

        </form> 
    </div>
</body>
@endsection