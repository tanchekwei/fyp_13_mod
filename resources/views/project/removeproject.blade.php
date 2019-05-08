@extends('layouts.app')
@section('title', 'Delete Project')
@section('module', 'Project Page')
@section('content')
<style>
    .imgcontainer{
        text-align: center;
        margin: 24px 0 12px 0;
        position: relative;
    }

    .modal {
        display:none;
        position:fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content{
        background-color: #fefefe;
        margin: 4% auto 15% auto;
        border: 1px solid #888;
        width: 60%;
        padding-bottom: 30px;
    }

    .close{
        position: absolute;
        right: 25px;
        top: 0;
        color: #000;
        font-size: 35px;
        font-weight: bold;
    }

    .close:hover,.close:focus{
        color: red;
        cursor: pointer;
    }

    .animate{
        animation: zoom 0.6s
    }

    @keyframes zoom {
        from {transform: scale(0)}
        to {transform: scale(1)}
    }
</style>
<div class="container">
    <br />
    <h3 align="center">Delete Project</h3><br />
    @foreach($project as $projectdetail)
    <?php
    $advisorName = str_replace("<br />",", ",$projectdetail->advisor);
	if($projectdetail->description == ""){
	$projectdetail->description = "N/A";
	}
	if($projectdetail->teamSize == ""){
	$projectdetail->teamSize = "N/A";
	}
	if($projectdetail->teamSize == ""){
	$projectdetail->teamSize = "N/A";
	}
	if($projectdetail->scope == ""){
	$projectdetail->scope = "N/A";
	}
	if($projectdetail->enhancement == ""){
	$projectdetail->enhancement = "N/A";
	}
	if($projectdetail->clientName == ""){
	$projectdetail->clientName = "N/A";
	}
	if($projectdetail->advisor == ""){
	$projectdetail->advisor = "N/A";
	}
    ?>

    <table class="table table-striped" border="">
        <tr>
            <td><label for="code">Project Code</label></td>
            <td>:</td>
            <td><input type="text" name="code" value="{{$projectdetail->projectCode}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="title">Project Title</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="title" value="{{$projectdetail->title}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="desc">Project Description</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="desc" value="{{$projectdetail->description}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="teamsize">Team Size</label></td>
            <td>:</td>
            <td><input type="text" name="teamsize" value="{{$projectdetail->teamSize}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="cluster">Project Cluster</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="cluster" value="{{$projectdetail->cluster}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="group">Project Group</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="group" value="{{$projectdetail->projectGroup}}" disabled="disabled"></td>
        </tr>
		<tr>
            <td><label for="scope">Project Scope</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="scope" value="{{$projectdetail->scope}}" disabled="disabled"></td>
        </tr>
		<tr>
            <td><label for="enhancement">Project Enhancement</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="enhancement" value="{{$projectdetail->enhancement}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="advisor">Project Advisor</label></td>
            <td>:</td>
            <td><input style="width:600px" type="text" name="advisor" value="{{$advisorName}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="clientName">Client Name</label></td>
            <td>:</td>
            <td><input type="text" name="clientName" value="{{$projectdetail->clientName}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="level">Project Level</label></td>
            <td>:</td>
            <td><input type="text" name="level" value="{{$projectdetail->level}}" disabled="disabled"></td>
        </tr>
        <tr>
            <td><label for="status">Project Status</label></td>
            <td>:</td>
            <td><input type="text" name="status" value="{{$projectdetail->status}}" disabled="disabled"></td>
        </tr>
    </table>
    @endforeach
    <br /><h5 align="center" ><button class="btn btn-danger" style="width:300px;font-size:20px" onclick="document.getElementById('modal-wrapper').style.display = 'block'">Delete Project</button>
        <div id="modal-wrapper" class="modal" >
            <form method="post" class="modal-content animate" action="{{action('ProjectController@destroy', $projectdetail->projectCode)}}">
                @csrf 
                <input name="_method" type="hidden" value="DELETE">
                <div class="imgcontainer">
                    <span onclick="document.getElementById('modal-wrapper').style.display = 'none'" class="close" title="Close PopUp">&times;</span>  
                    <h3 style="text-align:center">Do you really want to delete this?</h3>
                </div>
                <div class="container">
                    <button type="submit" class="btn btn-danger" style="width:300px;font-size:20px">Yes</button>
                </div>
            </form>
        </div>
        <script>
            var modal = document.getElementById('modal-wrapper');
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script></h5><br />
</form>  
</div>
@endsection
