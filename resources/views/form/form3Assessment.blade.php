@extends('layouts.app')
@section('title', 'Form 3 Assessment Page')
@section('module', 'Form Page')
@section('content')

<script>
    function roleType() {

        //Project title part
        var superviseProject = document.getElementById('superviseProject');
        var moderateProject = document.getElementById('moderateProject');
        var supervise1 = document.getElementById('supervise1');
        var supervise2 = document.getElementById('supervise2');
        var moderate1 = document.getElementById('moderate1');
        var moderate2 = document.getElementById('moderate2');

        //Student name Part
        var superviseStudent = document.getElementById('superviseStudent');
        var moderateStudent = document.getElementById('moderateStudent');
        var supervise3 = document.getElementById('supervise3');
        var supervise4 = document.getElementById('supervise4');
        var moderate3 = document.getElementById('moderate3');
        var moderate4 = document.getElementById('moderate4');

        if(document.getElementById('supervisor').checked) {
            //Project fields
            superviseProject.disabled = false;
            moderateProject.disabled = true;
            supervise1.style.backgroundColor = "white";
            moderate1.style.backgroundColor = "lightgrey";
            supervise2.style.backgroundColor = "white";
            moderate2.style.backgroundColor = "lightgrey";
            superviseProject.style.backgroundColor = "white";
            moderateProject.style.backgroundColor = "lightgrey";

            //Student fields
            superviseStudent.disabled = false;
            moderateStudent.disabled = true;
            supervise3.style.backgroundColor = "white";
            moderate3.style.backgroundColor = "lightgrey";
            supervise4.style.backgroundColor = "white";
            moderate4.style.backgroundColor = "lightgrey";
            superviseStudent.style.backgroundColor = "white";
            moderateStudent.style.backgroundColor = "lightgrey";
        } else {
            //Project fields
            superviseProject.disabled = true;
            moderateProject.disabled = false;
            supervise1.style.backgroundColor = "lightgrey";
            moderate1.style.backgroundColor = "white";
            supervise2.style.backgroundColor = "lightgrey";
            moderate2.style.backgroundColor = "white";
            superviseProject.style.backgroundColor = "lightgrey";
            moderateProject.style.backgroundColor = "white";

            //Student fields
            superviseStudent.disabled = true;
            moderateStudent.disabled = false;
            supervise3.style.backgroundColor = "lightgrey";
            moderate3.style.backgroundColor = "white";
            supervise4.style.backgroundColor = "lightgrey";
            moderate4.style.backgroundColor = "white";
            superviseStudent.style.backgroundColor = "lightgrey";
            moderateStudent.style.backgroundColor = "white";
        }
    }

    jQuery(document).ready(function() {
        roleType();

        jQuery.ajax({
            url: "{{ url('getStudents') }}",
			headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            method: 'post',
            data: {
                teamId: jQuery('#superviseProject').val()
            },
            success: function(response){
                let studentList = response;
                let dropDownList = $('#superviseStudent');
                dropDownList.empty();

                for (var i in studentList) {
                    dropDownList.append($('<option></option>')
                        .val(studentList[i]['studentId'])
                        .html(studentList[i]['teamId'] + ' - ' + studentList[i]['studentName']));
                }

            },
            error: function(result){
                console.log(result);
            }
        });

        jQuery('#superviseProject').change(function(e) {
            e.preventDefault();

            jQuery.ajax({
                url: "{{ url('getStudents') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				},
                method: 'post',
                data: {
                    teamId: jQuery('#superviseProject').val()
                },
                success: function(response){
                    let studentList = response;
                    let dropDownList = $('#superviseStudent');
                    dropDownList.empty();

                    for (var i in studentList) {
                        dropDownList.append($('<option></option>')
                            .val(studentList[i]['studentId'])
                            .html(studentList[i]['teamId'] + ' - ' + studentList[i]['studentName']));
                    }

                },
                error: function(result){
                    console.log(result);
                }
            });
        });

        jQuery.ajax({
            url: "{{ url('getStudents') }}",
			headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            method: 'post',
            data: {
                teamId: jQuery('#moderateProject').val()
            },
            success: function(response){
                let studentList = response;
                let dropDownList = $('#moderateStudent');
                dropDownList.empty();

                for (var i in studentList) {
                    dropDownList.append($('<option></option>')
                        .val(studentList[i]['studentId'])
                        .html(studentList[i]['teamId'] + ' - ' + studentList[i]['studentName']));
                }

            },
            error: function(result){
                console.log(result);
            }
        });

        jQuery('#moderateProject').change(function(e) {
            e.preventDefault();

            jQuery.ajax({
                url: "{{ url('getStudents') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				},
                method: 'post',
                data: {
                    teamId: jQuery('#moderateProject').val()
                },
                success: function(response){
                    let studentList = response;
                    let dropDownList = $('#moderateStudent');
                    dropDownList.empty();

                    for (var i in studentList) {
                        dropDownList.append($('<option></option>')
                            .val(studentList[i]['studentId'])
                            .html(studentList[i]['teamId'] + ' - ' + studentList[i]['studentName']));
                    }

                },
                error: function(result){
                    console.log(result);
                }
            });
        });
    });
</script>

<div class="container" style="padding-top:50px">
    <div class="col-md-12">
    <h2 style="text-align:center;">Form 3 - Project Proposal Moderation</h2><br/>

        <form action="createForm3" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">

                    @php
                        $staffDetail = $form3Array[0];
                        $superviseProjectLists = $form3Array[1];
                        $moderateProjectLists = $form3Array[2];
                    @endphp

                    <table class="table table-bordered" style="font-size:14px">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                @foreach ($staffDetail as $staff)
                                    <td colspan='3'>{{ $staff['title'] . " " . $staff['staffName'] }}</td>
                                @endforeach
                            </tr>

                            <tr>
                                <th>Role</th>
                                <td colspan='3'>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="supervisor" name="role" value="supervisor" onclick="roleType()" checked>
                                        <label class="form-check-label" for="supervisor">Supervisor</label>
                                    </div>

                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="moderator" name="role" value="moderator" onclick="roleType()">
                                        <label class="form-check-label" for="moderator">Moderator</label>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th id="supervise1" style="width:3.5cm">Supervise Project</th>
                                <td id="supervise2" style="width:8cm">
                                    <label for="superviseProject"></label>
                                    <select id="superviseProject" name="superviseProject" class="form-control" style="font-size:14px">
                                        @foreach ($superviseProjectLists as $superviseProjectList)
                                            <option value="{{ $superviseProjectList }}">{{ $superviseProjectList }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th id="moderate1" style="background-color:lightgrey; width:3.5cm">Moderate Project</th>
                                <td id="moderate2" style="background-color:lightgrey; width:8cm">
                                    <label for="moderateProject"></label>
                                    <select id="moderateProject" name="moderateProject" class="form-control" style="background-color:lightgrey; font-size:14px" disabled>
                                        @foreach ($moderateProjectLists as $moderateProjectList)
                                            <option value="{{ $moderateProjectList }}">{{ $moderateProjectList }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <th id="supervise3">Student name</th>
                                <td id="supervise4">
                                    <select id="superviseStudent" name="superviseStudent" class="form-control" style="font-size:14px"></select>
                                </td>
                                <th id="moderate3" style="background-color:lightgrey;">Student name</th>
                                <td id="moderate4" style="background-color:lightgrey;">
                                    <select id="moderateStudent" name="moderateStudent" class="form-control" style="background-color:lightgrey; font-size:14px;" disabled></select>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <br/>
            <div class="col-md-12" style="text-align:center">
                <button class="btn btn-primary" type="submit">Confirm</button>
            </div>

        </form>
    </div>
</div>

@endsection
