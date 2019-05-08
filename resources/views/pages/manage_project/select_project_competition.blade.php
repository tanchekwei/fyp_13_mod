@extends('layouts.app')
<style>
    .tab {
    }
    /* Style the buttons inside the tab */

    .tab button {
        background-color: inherit;
        float: left;
        outline: none;
        border:none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */

    .tab button:hover {
        background-color: #eee;
    }

    /* Create an active/current tablink class */

    .tab button.active {
        border-bottom: 2px solid black;
    }

    /* Style the tab content */

    .tabcontent {
        display: none;
        padding: 6px 12px;
        border-top: none;
    }
</style>
@section('content')
    <h4>Select Project for Competition Submission
	@if($cohortId!=null)
		(Cohort {{$cohortId}})
	@else
		<font class='text-danger'>(Cohort not selected)</font>
	@endif
	</h4>
    <br>
	<div class="text-info"><b>Note: </b>Only projects which have been assigned to a team will appear below.</div>
    <div class="tab"  style="clear: both" >
        <button class="tablinks" onclick="openTab(event, 'Select New Project')">Project</button>
        <button class="tablinks" onclick="openTab(event, 'Previously Selected Project')">Selected Project</button>
    </div>

    <div id = "Select New Project" class="tabcontent">
        <form id="unselected_project_form" action="{{action('SelectProjectCompetitionContoller@select')}}" method="post" enctype="multipart/form-data">
            <table class="table" id="unselected_project_table">
                <thead>
                <tr class="row">
					<td class="col"><b>Team ID</b></td>
                    <td class="col"><b>Project ID</b></td>
                    <td class="col"><b>Project Name</b></td>
                    <td class="col"><b>Select</b></td>
                </tr>
                </thead>
                <tbody>
                <!--Table content is generated using javascript function-->
                </tbody>
            </table>
            {{ csrf_field() }}
        </form>
    </div>

    <div id = "Previously Selected Project" class="tabcontent">
        <form id="previously_selected_project_form" action="{{action('SelectProjectCompetitionContoller@unselect')}}" method="post" enctype="multipart/form-data">
            <table class="table" id="previously_selected_project_table">
                <thead>
                <tr class="row">
					<td class="col"><b>Team ID</b><td>
                    <td class="col"><b>Project ID</b></td>
                    <td class="col"><b>Project Name</b></td>
                    <td class="col"><b>Submission status</b></td>
                    <td class="col"><b>Remove</b></td>
                </tr>
                </thead>
                <tbody>
                <!--Table content is generated using javascript function-->
                </tbody>
            </table>
            {{ csrf_field() }}
        </form>
    </div>

    <script>

            document.getElementsByClassName('tablinks')['{{session('active_tab')}}'].click();
            var project_name = [];

            function openTab(evt, tabName) {

                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";

                //populate select new project table
                if(tabName === "Select New Project"){
                    $.ajax({
                        type: 'get',
                        url: '{{action('SelectProjectCompetitionContoller@ajax_load_unselected')}}',
                        dataType: "JSON",
                        success: function (response) {
                            var projects = response;
                            var string = "";
                            if(projects.length >0 ) {
                                project_name = [];
                                $(projects).each(function (i) {
                                    string += '<tr class="row"><td class="col">'+projects[i].teamID+'</td><td class="col">' + projects[i].projectCode + '</td><td class="col">' + projects[i].title + '<td class="col"><input name="unselected_student[]" class="unselected_student" type="checkbox" value="' + projects[i].teamID + '"></td></tr>';
                                    project_name.push(projects[i].title);
                                });
                                string += '<tr class="row"><td class="col"><td class="col"></col></td><td class="col"></td><td class="col"><input class="btn btn-success" id="select_button" type="submit" value="Select"  onclick="select_project()"></td></tr>';
                                $('#unselected_project_table tbody').html(string);
                            }
                            else{
                                $('#previously_selected_project_table tbody').html("<br>No project selected.");
                            }
                        }
                    });
                }
                //populate previously selected project table
                else{
                    $.ajax({
                        type: 'get',
                        url: '{{action('SelectProjectCompetitionContoller@ajax_load_previously_selected')}}',
                        dataType: "JSON",
                        success: function (response) {
                            var projects = response.projects;
                            var submission_status = response.submission_status;
                            var string = "";
                            if(projects.length >0 ) {
                                project_name = [];
                                $(projects).each(function (i) {
                                    string += '<tr class="row"><td class="col">'+projects[i].teamID+'</td><td class="col">' + projects[i].projectCode + '</td><td class="col">' + projects[i].title + '</td><td class="col">'+submission_status[i]+'</td><td class="col"><input name="previous_selected_student[]" class="previous_selected_student" type="checkbox" value="' + projects[i].teamID + '"></td></tr>';
                                    project_name.push(projects[i].title);
                                });
                                string += '<tr class="row"><td class="col"></td><td class="col"></td><td class="col"></td><td class="col"></td><td class="col"><input class="btn btn-danger" id="remove_button" type="submit" value="Remove" class="remove" onclick="remove_project()"></td></tr>';
                                $('#previously_selected_project_table tbody').html(string);
                            }
                            else{
                                $('#previously_selected_project_table tbody').html("<br>No project selected.");
                            }
                        }

                    });
                }
            }

            function select_project(action) {
                event.preventDefault();
                var selected_project = [];
                var inputElements = document.getElementsByClassName("unselected_student");

                for (var i = 0; i < inputElements.length; i++) {
                    if (inputElements[i].checked) {
                        selected_project.push(project_name[i]);
                    }
                }
                if(selected_project.length > 0) {
                    if (confirm("Are you sure to select " + selected_project + " ?")) {

                        //disable select button after submit
                        $("#unselected_project_form").submit(function () {
                            $('#select_button').attr("disabled", true);
                            return true;
                        });
                        $('#unselected_project_form').submit();

                    } else {

                    }
                }
                else{
                    alert("No projects were selected.");
                }
            }

            function remove_project() {
                event.preventDefault();
                var selected_project = [];
                var inputElements = document.getElementsByClassName("previous_selected_student");
                for (var i = 0; i < inputElements.length; i++) {
                    if (inputElements[i].checked) {
                        selected_project.push(project_name[i]);
                    }
                }
                if(selected_project.length > 0) {
                    if (confirm("Are you sure to remove " + selected_project + " ?")) {
                        
                        //disable unselect button after submit
                        $("#previously_selected_project_form").submit(function () {
                            $('#remove_button').attr("disabled", true);
                            return true;
                        });
                        $('#previously_selected_project_form').submit();

                    } else {

                    }
                }
                else{
                    alert("No projects were selected.");
                }
            }
    </script>
@endsection
