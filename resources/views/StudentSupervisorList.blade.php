@extends('layouts.app')
@section('content')

<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function () {
        jQuery('#Search').on('click', function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
			jQuery.ajax({
                    url: "{{ url('/studentSupervisorTable') }}" + "/" + $('#ddlList1').val(),
                    method: 'post',
                    data: {

                    },
                    success: function (result) {
                        $('#showTable tr').remove();
                        var test = "";
                        test += "<thead><tr><td>Title</td><td>Satff Name</td><td>ProjectCode</td><td>Student Name</td></tr></thead><tbody>"
                        for (var i = 0; i < result.length; i++)
                        {
                            test += "<tr><td>" + result[i].title + "</td><td>" + result[i].staffName + "</td><td>" + result[i].projectCode + "</td><td>" + result[i].studentName + "</td></tr>"
                        }
                        test += "</tbody>"
                        $('#showTable').append(test);

                        console.log(result);
                    },

                    error: function (result) {
                        console.log('fail');
                        console.log(result);
                    }});
			/*
//supervisor table
            if ($('#ddlList').val() == "Supervisor")
            {
                jQuery.ajax({
                    url: "{{ url('/studentSupervisorTable') }}" + "/" + $('#ddlList1').val(),
                    method: 'post',
                    data: {

                    },
                    success: function (result) {
                        $('#showTable tr').remove();
                        var test = "";
                        test += "<thead><tr><td>Title</td><td>Satff Name</td><td>Phone No</td><td>Email</td><td>Status</td><td>Role</td><td>Specialization</td></tr></thead><tbody>"
                        for (var i = 0; i < result.length; i++)
                        {
                            test += "<tr><td>" + result[i].title + "</td><td>" + result[i].staffName + "</td><td>" + result[i].phoneNo + "</td><td>" + result[i].email + "</td><td>" + result[i].status + "</td><td>" + result[i].role + "</td><td>" + result[i].specialization + "</td></tr>"
                        }
                        test += "</tbody>"
                        $('#showTable').append(test);

                        console.log(result);
                    },

                    error: function (result) {
                        console.log('fail');
                        console.log(result);
                    }});
            }
//student table
            else if ($('#ddlList').val() == "Student")
            {
                jQuery.ajax({
                    url: "{{ url('/studentSupervisorTable1') }}" + "/" + $('#ddlList1').val(),
                    method: 'post',
                    data: {

                    },
                    success: function (result) {
                        $('#showTable tr').remove();
                        var test = "";
                        test += "<thead><tr><td>Student ID</td><td>Student Name</td><td>Phone No</td><td>TARC Email</td><td>Status</td><td>Tutorial Group</td><td>Team ID</td></tr></thead>"
                        for (var i = 0; i < result.length; i++)
                        {
                            test += "<tbody><tr><td>" + result[i].studentId + "</td><td>" + result[i].studentName + "</td><td>" + result[i].phoneNo + "</td><td>" + result[i].TARCemail + "</td><td>" + result[i].status + "</td><td>" + result[i].tutorialGroup + "</td><td>" + result[i].teamId + "</td></tr>"
                        }
                        test += "</tbody>"
                        $('#showTable').append(test);
                        console.log(result);
                    },

                    error: function (result) {
                        console.log('fail');
                        console.log(result);
                    }});
            } else
            {
                var test = "";
                test += "<tr><td>Please select a role</td></tr>"
            }
			*/
            $('#showTable tbody').append(test);
        });
    });
</script>
<div class="container">
    <h3 align="center">Student Supervisor List</h3><br />
    <table class="table table-striped" border="2">
        <tr>
            <td>
                <select id="ddlList1">
                    @foreach ($cohort as $cohorts)
                    <option value="{{$cohorts->cohortId}}">{{$cohorts->cohortId}}</option>
                    @endforeach
                </select>
            </td>   
            <td>
                <input type="button" id="Search" value="Search">
            </td>
        </tr>
    </table>
    <table id="showTable" class="table table-striped">
    </table>
</div>
@endsection