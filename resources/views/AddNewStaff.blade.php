@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h1 text-center">Add New Staff</h1>
</div>
@if ( Session::has('success') )
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
    <span class="sr-only">Close</span>
</button>
<strong>{{ Session::get('success') }}</strong>
</div>
@endif

@if ( Session::has('error') )
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
        <span class="sr-only">Close</span>
    </button>
    <strong>{{ Session::get('error') }}</strong>
</div>
@endif
<div class="container">
    <div class="modal-body">
        <form id="myform" action="{{route('addnewstaff')}}" method="post">
            @csrf
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Staff Id</label>
            :<div class="col-sm-6">
                <input type="text" id="staffId" name="staffId" class="form-control" placeholder="Staff Id" required="required">
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
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required="required">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Full\Part time</label>
            :<div class="col-sm-6">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id='full\part' name="time" value="full" checked="checked">
                  <label class="form-check-label" for="inlineRadio1">Full time</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id='full\part' name="time" value="part">
                  <label class="form-check-label" for="inlineRadio2">Part time</label>
                </div>
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
                <input type="text" id="specialization" name="specilaization" class="form-control" placeholder="Specialization">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <label class="col-sm-2 col-form-label">Department</label>
            :<div class="col-sm-6">
                <select class="custom-select mr-sm-2" id="selectdepartment" name="department">
                    @foreach($departmentresult as $dr)
                    <option value="{{$dr['departmentId']}}">{{$dr['departmentId']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row ">
            <div class="col-sm-2"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <input id='submitbtn' type="submit" class='btn btn-primary' value='submit'>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection