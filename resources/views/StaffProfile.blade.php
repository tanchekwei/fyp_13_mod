@extends('layouts.app')

@section('content')
<script>
    $(document).ready(function()
    {
       $('#title').val("{{$staff['title']}}");
       $('#staffName').val("{{$staff['staffName']}}");
       $('#phoneNo').val("{{$staff['phoneNo']}}");
       $('#designation').val("{{$staff['designation']}}");
       $('#specialization').val("{{$staff['specialization']}}");
       
       $('#submitbtn').click(function(e)
       {
           e.preventDefault();

           $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });
            
            $.ajax({
               url:"{{url('/staffupdateprofile')}}",
               method:"post",
               data:{
                   title:$('#title').val(),
                   staffName:$('#staffName').val(),
                   phoneNo:$('#phoneNo').val(),
                   designation:$('#designation').val(),
                   specialization:$('#specialization').val()
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
            <label class="col-sm-2 col-form-label">Staff Id</label>
            :<div class="col-sm-6">
                {{$staff['staffId']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Staff Name</label>
            :<div class="col-sm-2">
                <select class="custom-select mr-sm-2" id="title" name="title">
                    <option value="Mr.">Mr.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Ts.">Ts.</option>
                    <option value="AP.">AP.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Puan.">Puan.</option>
                    <option value="AP Dr.">AP Dr.</option>
                    <option value="Prof Dr.">Prof Dr.</option>
                </select>
            </div>
            <div class="col-sm-4">
                <input type="text" id="staffName" name="staffName" class="form-control" placeholder="Staff Name" required="required">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Phone No</label>
            :<div class="col-sm-6">
                <input type="text" id="phoneNo" name="phoneNo" class="form-control" placeholder="Phone No" pattern="^(0)(1)([1-9])-*[0-9]{7,8}$">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Email</label>
            :<div class="col-sm-6">
                {{$staff['email']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Full\Part time</label>
            :<div class="col-sm-6">
                {{$staff['full\part']}}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Designation</label>
            :<div class="col-sm-6">
                <input type="text" id="designation" name="designation" class="form-control" placeholder="Designation">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Specialization</label>
            :<div class="col-sm-6">
                <input type="text" id="specialization" name="specialization" class="form-control" placeholder="Specialization">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Department</label>
            :<div class="col-sm-6">
                {{$staff['departmentId']}}
            </div>
        </div>
		<div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Private key</label>
            :<div class="col-sm-6">
			<textarea class="form-control" row="5">{{$staff->priv_key->privateKey}} </textarea> <a href="" data-toggle="modal" data-target="#exampleModal"> Learn how to use</a>
            </div>
        </div>
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