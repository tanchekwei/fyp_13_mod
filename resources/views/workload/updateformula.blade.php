@extends('layouts.app')
@section('title', 'Update Formula Page')
@section('module', 'Workload Page')
@section('content')
<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();
$GLOBALS['$databaseName'] = getenv('DB_DATABASE');
$GLOBALS['$usernameName'] = getenv('DB_USERNAME');
$GLOBALS['$passwordName'] = getenv('DB_PASSWORD');
?>
<style>
    .error {color: #FF0000;}
</style>


<div class="container">
    <br /><br />
    <h3 align="center">Update Workload Formula</h3><br />
    <?php
    $connect = mysqli_connect("localhost", $GLOBALS['$usernameName'], $GLOBALS['$passwordName'], $GLOBALS['$databaseName']);
    $sql = mysqli_query($connect, "SELECT * From wl_formula");
    $row = mysqli_num_rows($sql);
    while ($row = mysqli_fetch_array($sql)) {
        ?>
        <form method="post" action="{{action('WorkloadController@update', $row['formulaId'])}}">
            @csrf 
            <input name="_method" type="hidden" value="PATCH">
            <p><span class="error">* required field</span></p>
            <table class="table table-striped" border="">

                <tr>
                    <td><label for="totalMinutes">Total Minutes Per Student Per Week</label></td>
                    <td>:</td>
                    <td><input type="number" name="totalMinutes" value="{{$row['totalMinutes']}}" required="required"></td>
                    <td><span class="error">* This field is required</span></td>
                </tr>

                <tr>
                    <td><label for="totalWeeks">Total Weeks</label></td>
                    <td>:</td>
                    <td><input type="number" name="totalWeeks" value="{{$row['totalWeeks']}}" required="required"></td>
                    <td><span class="error">* This field is required</span></td>
                </tr>

                <tr>
                    <td><label for="ptclaims">Part Time Rate Per Student (RM)</label></td>
                    <td>:</td>
                    <td><input type="number" name="ptclaims" value="{{$row['PTClaims']}}" min="40" max="160" required="required"></td>
                    <td><span class="error">* This field is required</span></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <br /><h5 align="center" ><button type="submit" class="btn btn-info" style="width:300px;font-size:20px">Update Workload Formula</button></h5><br />

    </form> 
</div>
@endsection
