@extends('layouts.app')
@section('content')

    
    <script src="http://code.jquery.com/jquery-3.3.1.min.js"
               integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
               crossorigin="anonymous"></script>
   <script>
       jQuery(document).ready(function(){	
        jQuery('#buttonId').click(function(e){
           e.preventDefault();
           $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          jQuery.ajax({
              url: "{{ url('/TeamTable') }}"+"/"+$('#studId').val(),
              method: 'post',
              data: {
                 id: $('#studId').val()
              },
              success: function(result){
			var currow = $(this).closest('tr');
            var makeup = "";
            var count = 0;
            
            
                $('#showTable tbody tr td:nth-child(1)').each(function()
                    {
                        var check = $(this).text().trim();
                        if(check === result.studentId )
                        {
                            count += 1;
                        }
                    }); 
					
                if(count === 0)
                {
                    makeup += "<tr><td>"+result.studentId+"</td><td>"+result.studentName+"</td><td><button id='btnRemove' class='btn btn-primary'>Remove</button></td></tr>";
                    $('#showTable tbody').append(makeup);
                    currow.remove();
                }
                else
                {
                    var errmsg = "this person is already in the list";
                    alert(errmsg);
                }
              },
                      

              error: function(result){
                console.log('fail');
              }});
            });
            
            $('#showTable tbody').on('click','#btnRemove',function()
                {
                    var currow = $(this).closest('tr');
                    currow.remove();
                });
                
//Submit Button
                $('#btnSubmit').click(function(e)
                {
                    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
                
                var vals1 = [];
                $("#showTable tbody tr td:nth-child(1)").each(function()
                        {
                            var data = $(this).text();
                            vals1.push(data);
                            
                            
                        });
                
                $.ajax({
                    url: "{{ url('/registerTeamTable') }}"+"/"+$('#passId').html(),
              method: 'post',
              data: {
                 arr: vals1
              },
              success: function(result){
                $('#message').text(result);
                console.log(vals1);
                console.log(result);
              },
                      

              error: function(result){
                console.log(result);
                console.log($('#passId').html());
              }});
            });
                    
                });
</script>
<body>
<body>
    <p id="test"></p>
@if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
@endif
<div class="container">
<h3 align="center">Register Team</h3><br />
        <?php
            $LatestteamID = $team;
            $_SESSION["LatestteamID"] = $LatestteamID;
        ?>
<table class="table table-striped" border="2">
    <tr>
        <td><a>team ID: </a></td><td><a value="$LatestteamID" id="passId">{{$LatestteamID}}</a></td>
        </tr>
		@if( Auth::user()->teamId == null)
        <tr><td><a><u>Please insert Student ID: </u></a></td>
		@else
		<tr><td><a><u>Team Member: </u></a></td>
		@endif
        <td>
		@if( Auth::user()->teamId == null)
		<input type="text" id="studId"><input type="button" id="buttonId" name="Search" value="Search">
		@endif
		</td></tr>		
        </table>
        <table id="showTable" class="table table-striped" max="4">
            <thead>
                <tr><td>Student ID: </td><td>Student Name: </td><td>Action: </td></tr>
            </thead>
        <tbody>
            
                <?php
                /*
                @foreach($member as $members)
                <tr><td>{{$members->studentId}}</td><td>{{$members->studentName}}</td><td></td>
                    @endforeach
                 */
                ?>
				@if($student !=null)
					@foreach($student as $sr)
						<tr><td>{{$sr->studentId}}</td><td>{{$sr->studentName}}</td><td></td>
					@endforeach
				@else
					<tr><td>{{Auth::user()->studentId}}</td><td>{{Auth::user()->studentName}}</td><td></td>
				@endif
        </tbody>
        </table>
@if( Auth::user()->teamId == null)
        <input type="button" id="btnSubmit" name="Add" value="Add"><br/>
        @else
        <p>You had registered as team</p>
        @endif
        <p id='message'></p>
        </div>
@endsection
<?php
/*
<body>
    <p id="test"></p>
@if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
@endif
<div class="container">
<h3 align="center">Register team</h3><br />
        
            $LatestteamID = $student["cohortId"]."_";
            $LatestteamID = ++$team["teamId"];
            $_SESSION["LatestteamID"] = $LatestteamID;
        
            
<table class="table table-striped" border="2">
    <tr>
        <td><a>team ID: </a></td><td><a value="$LatestteamID" id="passId">{{$LatestteamID}}</a></td>
        </tr>
        <tr><td><a><u>Please insert Student ID: </u></a></td>
        <td><input type="text" id="studId"><input type="button" id="buttonId" name="Search" value="Search"></td></tr>
        </table>
        <table id="showTable" class="table table-striped" max="4">
            <thead><tr><td>Student Id: </td><td>Student Name: </td><td>Action: </td></tr></thead>
        <tbody>
            @foreach ($student as $students)
            <tr><td>{{$students["studentId"]}}</td><td>{{$students["studentName"]}}</td>
                @endforeach
        </tbody>
        </table>
        <input type="button" id="btnSubmit" name="Add" value="Add"><br/>
        
        </div>
*/
?>