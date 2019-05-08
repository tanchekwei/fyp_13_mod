@extends('layouts.app')
@section('content')

<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function () {
        jQuery('#btnShow').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/PendingProject') }}"+"/"+"{{Session::get('staffId')}}",
                method: 'post',
                data: {
                },
                success: function (result) {
                    $('#showTable tr').remove();
                    var test = "";

                    test += "<thead><tr><td>Team ID: </td><td>Status: </td><td>Project Code: </td><td>Supervisor: </td><td>Action 1: </td></tr></thead>";
                    for (var i = 0; i < result.length; i++)
                    {
                        if (result[i].status == "pending")
                        {
                            test += "<tbody><tr><td>" + result[i].teamId + "</td><td>" + result[i].status + "</td><td>" + result[i].projectCode + "</td><td>" + result[i].supervisor + "</td><td><button id='btnApprove' class='btn btn-success'>Approve</button></td></tr></tbody>";
                        } else if (result[i].status == "assigned")
                        {
                            test += "<tbody><tr><td>" + result[i].teamId + "</td><td>" + result[i].status + "</td><td>" + result[i].projectCode + "</td><td>" + result[i].supervisor + "</td><td><button id='btnReject' class='btn btn-danger'>Reject</button></td></tr></tbody>";
                        } else
                        {
                            test += "<tbody><tr><td>Invalid project</td></tr></tbody>";
                        }
                    }

                    $('#showTable').append(test);
                    console.log(result);
                },

                error: function (result) {
                    console.log('fail');
                    console.log(result);
                }});
        });

//approve Button(currently removed)

        $(document).on('click', '#btnApprove', function ()
        {
            var currow = $(this).closest('tr');
            var result1 = currow.find('td:eq(0)').text();
            var result3 = currow.find('td:eq(2)').text();
            var result4 = currow.find('td:eq(3)').text();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('/AcceptProject') }}",
                method: 'post',
                data: {
                    teamId: result1,
                    status: "assigned",
                    projectCode: result3,
                    supervisor: result4
                },
                success: function (result) {
                    location.reload();
                },

                error: function (result) {
                    console.log('fail');
                    console.log(result);
                    console.log(result1);
                }});
        });
		
//Reject Button
        $(document).on('click', '#btnReject', function ()
        {
            var currow = $(this).closest('tr');
            var result1 = currow.find('td:eq(0)').text();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('/RejectProject') }}",
                method: 'post',
                data: {
                    teamId: result1,
                    status: "unassigned",
                    projectCode: null,
                    supervisor: null
                },
                success: function (result) {
                    location.reload();
                },

                error: function (result) {
                    console.log('fail');
                    console.log(result);
                }});
        });

    });
</script>
<div class="container">
    <h3 align="center">Approve Project Registration</h3><br />
    <table class="table table-striped" border="2">
        <tr><td>
                <input type="button" id="btnShow" value="Show pending team">
            </td></tr>
    </table>
    <table id="showTable" class="table table-striped">
    </table>
    <p id='message'></p>
</div>
@endsection