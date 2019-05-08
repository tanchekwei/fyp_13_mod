@extends('layouts.app')

@section('content')
<script>
    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    $(document).ready(function() {
       $("#defaultOpen").trigger('click');
    });
</script>
<div class="container">
        <h2 class="text-center">
            Import File
        </h2>
 
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
        <strong>{!! Session::get('error') !!}</strong>
    </div>
    @endif
 
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
      <div>
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
</div>
@endif
<div class='container'>
    <div class="tab">
      <button class="tablinks" onclick="openCity(event, 'London')" id="defaultOpen">Student</button>
      <button class="tablinks" onclick="openCity(event, 'Paris')">Team</button>
    </div>
    
    <div id="London" class="tabcontent">
        <h3>Import Student Detail</h3>
		<form action="{{ route('importstudent') }}" method="POST" enctype="multipart/form-data">
			{{ csrf_field() }}
			Level:
			<input type='radio' name='level' id='R' value='R' checked="checked"><label for='R'>R-Bachelor</label>
			<input type='radio' name='level' id='M' value='M'><label for='M'>M-Master</label>
			<input type='radio' name='level' id='P' value='P'><label for='P'>P-PhD</label><br>
			Choose your xls/xlsx File : <input type="file" name="file" class="form-control">

			<input type="submit" class="btn btn-primary btn-lg" style="margin-top: 3%">
		</form>
    </div>

    <div id="Paris" class="tabcontent">
        <h3>Import Team Detail</h3>
			<form action="{{ route('importteam') }}" method="POST" enctype="multipart/form-data">
				{{ csrf_field() }}
				Choose your xls/xlsx File : <input type="file" name="file" class="form-control">

				<input type="submit" class="btn btn-primary btn-lg" style="margin-top: 3%">
			</form>
    </div>
</div>
</div>
@endsection