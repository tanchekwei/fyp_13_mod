@extends('layouts.app')

@section('content')
<script>
    $(document).ready(function()
    {
        $('#phoneNo').val("{{$student['phoneNo']}}");
       $('#submitbtn').click(function(e)
       {
           e.preventDefault();

           $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });
            
            $.ajax({
               url:"{{url('/studentupdateprofile')}}",
               method:"post",
               data:{
                   phoneNo:$('#phoneNo').val()
               },
               success:function(result)
               {
                   alert(result);
                   location.reload();
               },
               error:function(result)
               {
                   console.log(result);
               }
            });
       });
       
    });
</script>
<div class="container">
    <h1 class="h1 text-center">Profile</h1>
</div>
<div class="container">
    <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Student ID</label>
            :<div class="col-sm-6">
                {{$student['studentId']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Name</label>
            :<div class="col-sm-6">
                {{$student['studentName']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Phone No</label>
            :<div class="col-sm-6">
                <input type="text" id="phoneNo" name="phoneNo" class="form-control" placeholder="Phone No">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">TARC Email</label>
            :<div class="col-sm-6">
                {{$student['TARCemail']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Tutorial Group</label>
            :<div class="col-sm-6">
                {{$student['tutorialGroup']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Project Individual Title</label>
            :<div class="col-sm-6">
                {{$student['individualTitle']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Cohort</label>
            :<div class="col-sm-6">
                {{$student['cohortId']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Programme</label>
            :<div class="col-sm-6">
                {{$student['programmeId']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Team</label>
            :<div class="col-sm-6">
                {{$student['teamId']}}
            </div>
        </div>
		<!--lau edit --> 
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Private key</label>
            :<div class="col-sm-6">
			<textarea class="form-control" row="5">{{$student->priv_key->privateKey}} </textarea> <a href="" data-toggle="modal" data-target="#exampleModal"> Learn how to use</a>
            </div>
        </div>
		<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Instructions to use private key..</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
        Step 1: Have <a href="https://git-scm.com/downloads" target="_blank"> Git </a> installed. </br>
		Step 2: Navigate to folder located in C:\Users\&lt;PC-NAME&gt;\.ssh (create folder if not exists).</br>
		Step 3: Create new file with name "id_rsa".</br>
		Step 4: Paste private key in newly created file.</br>
		Step 5: Restart any application that uses git.</br></br>
		
		Now you will be able to push your work to your repositories.
	  
      </div>
    </div>
  </div>
</div>
		
		<!---->
		<div class="form-group row ">
            <div class="col-sm-2"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <input id='submitbtn' type="button" class='btn btn-primary' value='Save Change'>
            </div>
        </div>
</div>
@endsection