@extends('layouts.app')

@section('content')
<?php
    $cid=Session::get('cohortId');
?>
<div class="container">
    <h1 class="h1 text-center">Cohort: {{Session::get('cohortId')}}</h1>
</div>
<div class="container">    
    <a href="{{route('addsupervisor',['id'=>$cid,'staffId'=>Auth::user()->staffId])}}" class="btn btn-block btn-outline-secondary">Add Supervisor to Cohort</a><br>
    <a href="{{route('staffpairing',['id'=>$cid])}}" class="btn btn-block btn-outline-secondary">Add Supervisor Pairing</a><br>
    <form action="rubricIndex" method="POST">
        @csrf
        <input name="templateVersion" type="submit" value="Template Version" class="btn btn-block btn-outline-secondary"/>
    </form><br>
	@if(Auth::user()->role == 'admin' || Auth::user()->role == 'facultyadmin')
    <a href="{{url('studentmaintenance')}}" class="btn btn-block btn-outline-secondary">Import Student/Team</a><br>
    @endif
</div>
@endsection