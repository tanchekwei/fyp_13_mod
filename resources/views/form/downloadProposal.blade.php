@extends('layouts.app')
@section('title', 'Download Proposal')
@section('module', 'Form Page')
@section('content')

@if (Session::has('fail'))
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <strong>{{ Session::get('fail') }}</strong>
    </div>
@endif

<script>
    function selectType_s() {
        var select_all = document.getElementById("select_all_s"); //select all checkbox
        var checkboxes = document.getElementsByClassName("checkbox"); //checkbox items

        //select all checkboxes
        select_all.addEventListener("change", function(e){
            for (i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = select_all.checked;
                document.getElementById("superviseDLbtn").disabled = false;
            }

            if (select_all.checked == false) {
                document.getElementById("superviseDLbtn").disabled = true;
            }

        });

        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function(e){ //".checkbox" change
                //uncheck "select all", if one of the listed checkbox item is unchecked
                if(this.checked == false){
                    select_all.checked = false;
                }
                //check "select all" if all checkbox items are checked
                if(document.querySelectorAll('.checkbox:checked').length == checkboxes.length){
                    select_all.checked = true;
                }

                if(document.querySelectorAll('.checkbox:checked').length == 0){
                    document.getElementById("superviseDLbtn").disabled = true;
                } else {
                    document.getElementById("superviseDLbtn").disabled = false;
                }
            });
        }
    }

    function selectType_m() {
        var select_all = document.getElementById("select_all_m"); //select all checkbox
        var checkboxes = document.getElementsByClassName("checkbox_m"); //checkbox items

        //select all checkboxes
        select_all.addEventListener("change", function(e){
            for (i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = select_all.checked;
                document.getElementById("moderateDLbtn").disabled = false;
            }

            if (select_all.checked == false) {
                document.getElementById("moderateDLbtn").disabled = true;
            }
        });


        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function(e){ //".checkbox" change
                //uncheck "select all", if one of the listed checkbox item is unchecked
                if(this.checked == false){
                    select_all.checked = false;
                }
                //check "select all" if all checkbox items are checked
                if(document.querySelectorAll('.checkbox_m:checked').length == checkboxes.length){
                    select_all.checked = true;
                }

                if(document.querySelectorAll('.checkbox_m:checked').length == 0){
                    document.getElementById("moderateDLbtn").disabled = true;
                } else {
                    document.getElementById("moderateDLbtn").disabled = false;
                }
            });
        }
    }
</script>

@php
    $superviseStudentArray = array();
    $moderateStudentArray = array();
    $a = 0;
    $b = 0;
@endphp

<div class="container">
    <div class="col-md-12" style="padding-top:25px">
        <h2 style="text-align:center;">Proposals</h2><br/>

        <div class="downloadProposal">
            <div class="card">
                <div class="card-header bg-primary" style="color:white"><b>Supervise Student Proposal List</b></div>
                <div class="card-body">

                    <div style="text-align:right">
                        <input type="checkbox" id="select_all_s" name="superviseSelect" onclick="selectType_s()" style="width:15px; height:15px;">
                        <label for="select_all_s">Select All</label>
                    </div><br/>

                    <form action="downloadSuperviseProposal" method="POST">
                        @csrf

                        @foreach ($supervisorTeams as $supervisorTeam)
                            @foreach ($projectLists as $projectList)
                                @if ($projectList['projectCode'] == $supervisorTeam['projectCode'])
                                    <table class="table table-bordered">
                                        <tbody>

                                            <tr>
                                                <th colspan='4' style="background-color:black; color:white; text-align:left;">{{ $projectList['title'] }}</th>
                                            </tr>

                                            <tr>
                                                <th style="width:5em;">Select</th>
                                                <th style="width:20em;">StudentName</th>
                                                <th style="width:15em;">Student ID</th>
                                                <th style="width:30em;">Proposal</th>
                                            </tr>

                                            @foreach ($studentLists as $studentList)
                                                @if ($supervisorTeam['teamId'] == $studentList['teamId'])
                                                    @foreach ($form2Proposals as $form2Proposal)
                                                        @if ($studentList['studentId'] == $form2Proposal['studentId'])
                                                            <tr>
                                                                <td><input type="checkbox" class="checkbox" name="{{ $form2Proposal['studentId'] }}" onclick="selectType_s()" style="width:15px; height:15px;"></td>
                                                                <td>{{ $studentList['studentName'] }}</td>
                                                                <td>{{ $form2Proposal['studentId'] }}</td>
                                                                <td>{{ substr($form2Proposal['fileName'], strrpos($form2Proposal['fileName'], '/')+1) }}</td>
                                                            </tr>
                                                            @php
                                                                $superviseStudentArray[$a] = $form2Proposal['studentId'];
                                                                $a++;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach

                                            @php Session::put('superviseStudentArray', $superviseStudentArray); @endphp

                                        </tbody>
                                    </table>

                                @endif
                            @endforeach
                        @endforeach


                        <div class="col-md-12" style="text-align:center">
                            <button id="superviseDLbtn" class="btn btn-success" type="submit" disabled>Download</button>
                        </div>

                    </form>

                </div>
            </div>

            <br/>

            <div class="card">
                <div class="card-header bg-danger" style="color:white"><b>Moderate Student Proposal List</b></div>
                <div class="card-body">

                    <div style="text-align:right">
                        <input type="checkbox" id="select_all_m" name="moderateSelect" onclick="selectType_m()" style="width:15px; height:15px;">
                        <label for="select_all_m">Select All</label>
                    </div><br/>

                    <form action="downloadModerateProposal" method="POST">
                        @csrf

                        @foreach ($moderatorTeams as $moderatorTeam)
                            @foreach ($projectLists as $projectList)
                                @if ($projectList['projectCode'] == $moderatorTeam['projectCode'])
                                    <table class="table table-bordered">
                                        <tbody>

                                            <tr>
                                                <th colspan='4' style="background-color:black; color:white; text-align:left;">{{ $projectList['title'] }}</th>
                                            </tr>

                                            <tr>
                                                <th style="width:5em;">Select</th>
                                                <th style="width:20em;">StudentName</th>
                                                <th style="width:15em;">Student ID</th>
                                                <th style="width:30em;">Proposal</th>
                                            </tr>

                                            @foreach ($studentLists as $studentList)
                                                @if ($moderatorTeam['teamId'] == $studentList['teamId'])
                                                    @foreach ($form2Proposals as $form2Proposal)
                                                        @if ($studentList['studentId'] == $form2Proposal['studentId'])
                                                            <tr>
                                                                <td><input type="checkbox" class="checkbox_m" name="{{ $form2Proposal['studentId'] }}" onclick="selectType_m()" style="width:15px; height:15px;"></td>
                                                                <td>{{ $studentList['studentName'] }}</td>
                                                                <td>{{ $form2Proposal['studentId'] }}</td>
                                                                <td>{{ substr($form2Proposal['fileName'], strrpos($form2Proposal['fileName'], '/')+1) }}</td>
                                                            </tr>
                                                            @php
                                                                $moderateStudentArray[$b] = $form2Proposal['studentId'];
                                                                $b++;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach

                                            @php Session::put('moderateStudentArray', $moderateStudentArray); @endphp

                                        </tbody>
                                    </table>

                                @endif
                            @endforeach
                        @endforeach

                        <div class="col-md-12" style="text-align:center">
                            <button id="moderateDLbtn" class="btn btn-success" type="submit" disabled>Download</button>
                        </div>

                    </form>

                </div>
            </div>
            <br/>

        </div>

    </div>
</div>

@endsection
