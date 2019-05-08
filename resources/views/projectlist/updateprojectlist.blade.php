@extends('layouts.app')
@section('title', 'Update Project List Page')
@section('module', 'ProjectList Page')
@section('content')
<?php
$GLOBALS['$databaseName'] = getenv('DB_DATABASE');
$GLOBALS['$usernameName'] = getenv('DB_USERNAME');
$GLOBALS['$passwordName'] = getenv('DB_PASSWORD');
$staffName = "";
?>
<script type="text/javascript">
    function showfield(name) {
        if (name == 'Other')
            document.getElementById('div1').style.display = "block";
        else
            document.getElementById('div1').style.display = "none";
    }

    function hidefield() {
        document.getElementById('div1').style.display = 'none';
    }
</script>

<style>
    .error {color: #FF0000;}
</style>

<body onload="hidefield()">
    <div class="container">
        <br />
        <h3 align="center">Update Project List</h3><br />
        @foreach($team as $teamdetail)
        <form method="post" action="{{action('ProjectListController@update', $teamdetail->teamId)}}">
            @csrf 
            <input name="_method" type="hidden" value="PATCH">
            <p><span class="error">* required field</span></p>
            <table class="table table-striped" border="">
                <tr>
                    <td style="width:20%"><label for="teamID">Team ID</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="text" name="teamID" value="{{$teamdetail->teamId}}" disabled="disabled"></td>
                    <td style="width:30%"><span class="error"></span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="code">Project Code</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="text" name="code" value="{{$teamdetail->projectCode}}" disabled="disabled"></td>
                    <td style="width:30%"><span class="error"></span></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="supervisor">Supervisor</label></td>
                    <td>:</td>
                    @auth('staff')
                    <td><select style="width:300px" name="supervisor">
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                if ($teamdetail->supervisor == $row['staffId']) {
                                    echo "<option selected='selected' value='" . $row['staffId'] . "'>" . $row['staffName'] . "</option>";
                                } else {
                                    echo "<option value='" . $row['staffId'] . "'>" . $row['staffName'] . "</option>";
                                }
                            }
                            ?>
                        </select></td>
						<td style="width:30%"><span class="error">* Supervisor is required</span></td>
                    @endauth
                    @auth('student')
                    @foreach($fypstaff as $fypstaffdetail)
                    <?php
                    if($fypstaffdetail['staffId'] == $teamdetail->supervisor){
                        $staffName = $fypstaffdetail['staffName'];
                    }
                    ?>
                    @endforeach
                    <td><input style="width:300px" type="text" name="supervisor" value="{{$staffName}}" disabled="disabled"></td>
					<td style="width:30%"><span class="error"></span></td>
                    @endauth
                    
                </tr>
                <tr>
                    <td style="width:20%"><label for="moderator">Moderator</label></td>
                    <td>:</td>
                    @auth('staff')
                    <td><select style="width:300px" name="moderator">
                            <?php
                            $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
                            $sql = mysqli_query($connect, "SELECT staffId, staffName From staff GROUP BY staffId, staffName");
                            $row = mysqli_num_rows($sql);
                            while ($row = mysqli_fetch_array($sql)) {
                                if ($teamdetail->moderator == $row['staffId']) {
                                    echo "<option selected='selected' value='" . $row['staffId'] . "'>" . $row['staffName'] . "</option>";
                                } else {
                                    echo "<option value='" . $row['staffId'] . "'>" . $row['staffName'] . "</option>";
                                }
                            }
                            ?>
                        </select></td>
						<td style="width:30%"><span class="error">* Moderator is required</span></td>
                    @endauth
                    @auth('student')
                    @foreach($fypstaff as $fypstaffdetail)
                    <?php
                    if($fypstaffdetail['staffId'] == $teamdetail->moderator){
                        $staffName = $fypstaffdetail['staffName'];
                    }
                    ?>
                    @endforeach
                    <td><input style="width:300px" type="text" name="moderator" value="{{$staffName}}" disabled="disabled"></td>
					<td style="width:30%"><span class="error"></span></td>
                    @endauth
                    
                </tr>
				<tr>
                    <td style="width:20%"><label for="teamScope">Team Scope</label></td>
                    <td>:</td>
                    <td><input style="width:480px" type="text" name="teamScope" value="{{$teamdetail->teamScope}}"></td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="competitionName">Competition Name</label></td>
                    <td>:</td>
                    <td><input style="width:200px" type="text" name="competitionName" value="{{$teamdetail->competitionName}}"></td>
                    <td style="width:30%"></td>
                </tr>
                <tr>
                    <td style="width:20%"><label for="status">Status</label></td>
                    <td>:</td>
                    @auth('staff')
                    <td><select style="width:200px" name="status">
                            <?php
                            $select = "selected";
                            if ($teamdetail->status == 'Assigned') {
                                echo "<option selected='" . $select . "'>Assigned</option>";
                            } else {
                                echo "<option>Assigned</option>";
                            }
                            if ($teamdetail->status == 'Unassigned') {
                                echo "<option selected='" . $select . "'>Unassigned</option>";
                            } else {
                                echo "<option>Unassigned</option>";
                            }
                            ?>
                        </select></td>
						<td style="width:30%"><span class="error">* Status is required</span></td>
                    @endauth
                    @auth('student')
                    <td><input style="width:200px" type="text" value="{{$teamdetail->status}}" disabled="disabled"><input type="hidden" type="text" name="status" value="{{$teamdetail->status}}"></td>
					<td style="width:30%"><span class="error"></span></td>
                    @endauth
                    
                </tr>
            </table>
            @endforeach
            <br /><h5 align="center" ><button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Update Project List</button></h5><br />
        </form>
    </div>
</body>
@endsection
