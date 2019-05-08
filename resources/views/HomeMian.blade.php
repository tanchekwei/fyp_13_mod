@extends('layouts.app')

@section('content')
    <form method='post' action="{{Route('Home')}}">
    @csrf
    <div class="container">
    <h1 class="h1 text-center">Student Management</h1>
</div>
    
    <div class="container">
    @auth('student')
    <a href="{{action('studentsController@showRegisterTeam')}}" class="btn btn-block btn-outline-secondary">Register Team</a><br/>
    <a href="{{action('ProjectController@showAllTeam')}}" class="btn btn-block btn-outline-secondary">Register Project</a><br/>
    @endauth
    @auth('staff')
    @if(Auth::guard('staff')->user()->role == 'admin' || Auth::guard('staff')->user()->role == 'facultyadmin')
    <a href="{{action('teamsController@showAllTeam')}}" class="btn btn-block btn-outline-secondary">Auto Assign</a><br/>
    <a href="{{action('CohortController@studSpvList')}}" class="btn btn-block btn-outline-secondary">Student Supervisor List</a><br/>
    @endif
    <a href="{{action('teamsController@ApproveProject')}}" class="btn btn-block btn-outline-secondary">Approve Project Registration</a><br/>
    @endauth
    </div>
    </form>
@endsection