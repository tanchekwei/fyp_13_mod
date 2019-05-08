@extends('layouts.app')
@section('content')

<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>

<script>
    jQuery(document).ready(function () {
        jQuery('#btnProject').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            /*
             jQuery.ajax({
             url: "{{ url('/registerProject') }}" + "/" + $('#projCode').val(),
             method: 'post',
             data: {
             id: $('#projCode').val()
             },
             success: function (result) {
             $('#showTable2 tr').remove();
             var test = "";
             if (result.status == "assigned")
             {
             test += "<thead>Please insert correct project code</thead>";
             } else
             {
             test += "<thead><tr><td>Project code: </td><td>supervsor ID: </td><td>status: </td><td>Action 1: </td><td>Action 2: </td>\n\
             </tr></thead><tbody><tr><td>" + result.projectCode + "</td><td>" + result.supervisorId + "</td><td>" + result.status + "</td>\n\
             <td><input type='button' id='btnRegister' class='btn btn-primary' value='Register'></td>\n\
             <td><button id='btnRemove' class='btn btn-primary'>Remove</button></td></tr></tbody>";
             }
             $('#showTable2').append(test);
             
             console.log(result);
             },
             
             error: function (result) {
             console.log(result);
             }});
             */

            jQuery.ajax({
                url: "{{ url('/registerProject') }}",
                method: 'post',
                data: {
                    id: $('#projCode').val()
                },
                success: function (result) {
                    $('#showTable2 tr').remove();
                    var test = "";
                    test += "<thead><tr><td>Project Code: </td><td>Supervisor: </td><td>Status: </td><td>Action 1: </td></tr></thead>";
					
                    for (var i = 0; i < result.length; i++)
                    {
                        test += "<tbody><tr><td>" + result[i].projectCode + "</td><td>" + result[i].staffName + "<input type='hidden' id='hiddenid' value="+result[i].staffId+"></td><td>" + result[i].status + "</td><td><input type='button' id='btnRegister' class='btn btn-primary' value='Register'></td></tr></tbody>";
                    }
					
                    $('#showTable2').append(test);

                    console.log(result);
                },

                error: function (result) {
                    console.log(result);
                }});


            $(document).on('click', '#btnRemove', function ()
            {
                console.log("a");
                var currow = $(this).closest('tr');
                currow.remove();
            });


//Register Button
            $(document).on('click', '#btnRegister', function ()
            {
                var currow = $(this).closest('tr');
                var result = currow.find('td:eq(0)').text();
                var result1 = currow.find('td:eq(1) input[id=hiddenid]').val();
                console.log(result1);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('/registerProjectTable') }}",
                    method: 'post',
                    data: {
                        projectCode: result,
                        supervisorId: result1
                    },
                    success: function (result) {
                        var word = "Project is approved";
                        $('#message').text(word);
                        console.log("success");
                        location.reload();
                    },
                    error: function (result) {
                        var word = "Register project failed";
                        $('#message').text(word);
                        console.log("fail");
                        console.log(result);
                    }});
            });
        });

    });
</script>
@if (\Session::has('success'))
<div class="alert alert-success">
    <p>{{ \Session::get('success') }}</p>
</div><br />
@endif
<div class="container">
    <h3 align="center">Register Project</h3><br />
    <table class="table table-striped" border="2">
        <tr>
            <td style="width:20%"><label><u>Team Details</u></label><br/></td><td><p>Your current supervisor is : </p><a id="check">{{$supervisor}}</a></td>
        </tr>
        <?php for($i = 0; $i<count($student);$i++){?>
        <tr>
            <td><a>Team member <?php echo $i+1?>:</a></td><td><a value="{{$student[$i]->studentName}}">{{$student[$i]->studentName}}</a></td>
        </tr>
        <?php }?>
        <tr>
            <td style="width:20%"><label><u>Project Details</u></label></td><td></td>
        </tr>
        <tr>
		@if($title == null)
            <td><a>Please click search button: </a></td>
            <td><input type="button" id="btnProject" name="Search" value="Search"></td>
		@else
			<td><a>Project Title:</a></td>
            <td>{{$title->title}}</td>
		@endif
        </tr>
    </table>
    <table id="showTable2" class="table table-striped" max="4">
    </table>
    <a id="message"></a>

</div>
@endsection