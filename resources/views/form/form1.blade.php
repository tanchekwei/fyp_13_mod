@extends('layouts.app')
@section('title', 'Form 1 Page')
@section('module', 'Form Page')
@section('content')

@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <strong>{{ Session::get('success') }}</strong>
    </div>
@endif

<script>
    $(document).ready(function() {

        //Verify hpNo
        $('#hpNo').bind("keyup change blur", function() {

            if(!$(this).val()) {
                $('#hpNoVerified').empty();
                $('#save').attr("disabled", false);
            } else if(isHpNo($(this).val())) {
                $('#hpNoVerified').show().html('<img src="{{ url('public/images/tick_4_5.png') }}" width="20px" height="20px"/>');
                $('#hpNo').removeClass('verificationError');
                $('#hpNo').addClass('verificationSuccess');
                $('#save').attr("disabled", false);
            } else {
                $('#hpNoVerified').show().html('<img src="{{ url('public/images/cross_10_11.png') }}" width="22px" height="22px"/>');
                $('#hpNo').removeClass('verificationSuccess');
                $('#hpNo').addClass('verificationError');
                $('#save').attr("disabled", true);
            }
        })

        //Verify hpTerm
        $('#hpTerm').bind("keyup change blur", function() {

            if(!$(this).val()) {
                $('#hpTermVerified').empty();
                $('#save').attr("disabled", false);
            } else if(isHpTerm($(this).val())) {
                $('#hpTermVerified').show().html('<img src="{{ url('public/images/tick_4_5.png') }}" width="20px" height="20px"/>');
                $('#hpTerm').removeClass('verificationError');
                $('#hpTerm').addClass('verificationSuccess');
                $('#save').attr("disabled", false);
            } else {
                $('#hpTermVerified').show().html('<img src="{{ url('public/images/cross_10_11.png') }}" width="20px" height="20px"/>');
                $('#hpTerm').removeClass('verificationSuccess');
                $('#hpTerm').addClass('verificationError');
                $('#save').attr("disabled", true);
            }
        })

        //Verify hpPermanent
        $('#hpPermanent').bind("keyup change blur", function() {

           if(!$(this).val()) {
                $('#hpPermanentVerified').empty();
                $('#save').attr("disabled", false);
            } else if(isHpTerm($(this).val())) {
                $('#hpPermanentVerified').show().html('<img src="{{ url('public/images/tick_4_5.png') }}" width="20px" height="20px"/>');
                $('#hpPermanent').removeClass('verificationError');
                $('#hpPermanent').addClass('verificationSuccess');
                $('#save').attr("disabled", false);
            } else {
                $('#hpPermanentVerified').show().html('<img src="{{ url('public/images/cross_10_11.png') }}" width="20px" height="20px"/>');
                $('#hpPermanent').removeClass('verificationSuccess');
                $('#hpPermanent').addClass('verificationError');
                $('#save').attr("disabled", true);
            }
        })

        //Verify hpPermanent
        $('#emailPersonal').bind("keyup change blur", function() {

            if(!$(this).val()) {
                $('#emailPersonalVerified').empty();
                $('#save').attr("disabled", false);
            } else if(isEmail($(this).val())) {
                $('#emailPersonalVerified').show().html('<img src="{{ url('public/images/tick_4_5.png') }}" width="20px" height="20px"/>');
                $('#emailPersonal').removeClass('verificationError');
                $('#emailPersonal').addClass('verificationSuccess');
                $('#save').attr("disabled", false);
            } else {
                $('#emailPersonalVerified').show().html('<img src="{{ url('public/images/cross_10_11.png') }}" width="20px" height="20px"/>');
                $('#emailPersonal').removeClass('verificationSuccess');
                $('#emailPersonal').addClass('verificationError');
                $('#save').attr("disabled", true);
            }
        })

    })

    //HpNo test function
    function isHpNo(hpNo) {
        var regex = /^(\+?6?01)[0-46-9]-*[0-9]{7,}$/;
        return regex.test(hpNo);
    }

    //House Phone No test function
    function isHpTerm(hpNo) {
        var regex = /^(\+?6?08?)[2-9]-*[0-9]{6,}$/;
        return regex.test(hpNo);
    }

    //Email test function
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
</script>

{{-- @php
    $cohortYear = substr($studentDetail['cohortId'], 0, 4);
    $cohortSession = $cohortYear + 1;
    $academicSession = $cohortYear . "/" . $cohortSession;
@endphp --}}

<div class="container">
    <div class="col-md-12" style="padding-top:50px">
        <h2 style="text-align:center;">Form 1 - Student Details</h2><br />

        <div class="form1">
            <form action="storeForm1" method="POST">
                @csrf

                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row">Student Name</th>
                                    <td colspan="5"> {{ $studentDetail['studentName'] }} </td>
                                </tr>

                                <tr>
                                    <th scope="row">Registration No.</th>
                                    <td colspan="5"> {{ $studentDetail['studentId'] }} </td>
                                </tr>

                                <tr>
                                    <th scope="row">Academic Year</th>
                                    <td colspan="2"> {{ $academicSession }} </td>
                                    <th scope="row">Cohort</th>
                                    <td colspan="2"> {{ $studentDetail['cohortId'] }} </td>
                                </tr>

                                <tr>
                                    <th scope="row">Programme</th>
                                    <td colspan="5"> {{ $programmeDetail['programmeId'] }} </td>
                                </tr>

                                <tr>
                                    @if (empty($form1Detail))
                                        <th scope="row">House Phone Contact (Term)</th>
                                        <td style="border-right-color:white">
                                            <input type="text" name="hpTerm" id="hpTerm" class="form-control"><br/>
                                        </td>
                                        <td id="hpTermVerified"></td>
                                        <th scope="row">House Phone Contact (Permanent)</th>
                                        <td style="border-right-color:white">
                                            <input type="text" name="hpPermanent" id="hpPermanent" class="form-control">
                                        </td>
                                        <td id="hpPermanentVerified"></td>
                                    @else
                                        <th scope="row">House Phone Contact (Term)</th>
                                        <td style="border-right-color:white">
                                            <input type="text" name="hpTerm" id="hpTerm" class="form-control" value="{{ $form1Detail['termContact'] }}">
                                        </td>
                                        <td id="hpTermVerified"></td>
                                        <th scope="row">House Phone Contact (Permanent)</th>
                                        <td style="border-right-color:white">
                                            <input type="text" name="hpPermanent" id="hpPermanent" class="form-control" value="{{ $form1Detail['permanentContact'] }}">
                                        </td>
                                        <td id="hpPermanentVerified"></td>
                                    @endif
                                </tr>

                                <tr>
                                    <th scope="row">Handphone No.</th>
                                    <td colspan="4" style="border-right-color:white">
                                        <input type="text" name="hpNo" id="hpNo" class="form-control" @if ($studentDetail['phoneNo'] !== NULL) value="{{ $studentDetail['phoneNo'] }}" @endif/>
                                    </td>
                                    <td id="hpNoVerified"></td>
                                </tr>

                                <tr>
                                    <th scope="row">Email (TAR UC)</th>
                                    <td colspan="5"> {{ $studentDetail['TARCemail'] }} </td>
                                </tr>

                                <tr>
                                    <th scope="row">Email (Personal)</th>
                                    @if (empty($form1Detail))
                                        <td colspan="4" style="border-right-color:white">
                                            <input type="text" name="emailPersonal" id="emailPersonal" class="form-control">
                                        </td>
                                        <td id="emailPersonalVerified"></td>
                                    @else
                                        <td colspan="4" style="border-right-color:white">
                                            <input type="text" name="emailPersonal" id="emailPersonal" class="form-control" value="{{ $form1Detail['emailPersonal'] }}"/>
                                        </td>
                                        <td id="emailPersonalVerified"></td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br/>

                <div class="col-md-12" style="text-align:center">
                    <button class="btn btn-primary" id="save" type="submit">Save</button>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection
