@extends('layouts.app')
@section('title', 'Main Page')
@section('content')

<div id="content-wrapper">

    <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Overview</li>
        </ol>

        <!-- Icon Cards-->
        <div class="row">
            @auth('staff')
            @if(Auth::guard('staff')->user()->role != 'supervisor')
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-file"></i>
                        </div>
                        <div class="mr-5">Cohort Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="{{url('/cohortmenu')}}">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            @endif
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-warning o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-folder"></i>
                        </div>
                        <div class="mr-5">Project Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/viewproject">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            @endauth
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-secondary o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-list"></i>
                        </div>
                        <div class="mr-5">Project List Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/viewprojectlist">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-danger o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-user"></i>
                        </div>
                        <div class="mr-5">Student Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/MianHome">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-dark o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-pen"></i>
                        </div>
                        <div class="mr-5">Rubric Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/rubric">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-success o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-paper-plane"></i>
                        </div>
                        <div class="mr-5">Form Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/formMain">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
            @auth('staff')
            @if(Auth::guard('staff')->user()->role == 'admin' || Auth::guard('staff')->user()->role == 'admin')
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-light o-hidden h-100 text-dark">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fas fa-fw fa-address-book"></i>
                            </div>
                            <div class="mr-5">Workload Management</div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1 text-dark" href="/viewworkload">
                            <span class="float-left">View Details</span>
                            <span class="float-right">
                                <i class="fas fa-angle-right"></i>
                               </span>
                        </a>
                    </div>
                </div>
            @endif
            @endauth
			@auth('student')
			
				@if(Auth::guard('student')->user()->teamId != null)
			
				 @php
                        $isCompetition = DB::table('team')->select('isCompetition')->where('teamId', auth()->guard('student')->user()->teamId)->get();
                    @endphp
					@if($isCompetition)
                    @if($isCompetition[0]->isCompetition == "1")
								<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-warning o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-trophy"></i>
								</div>
								<div class="mr-5">Competition Submission</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="{{route('projectsubmission', ['isCompetition'=>1])}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
                    @endif
					@endif
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-primary o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-arrow-circle-up"></i>
								</div>
								<div class="mr-5">Project Submission</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="{{route('projectsubmission', ['isCompetition'=>0])}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
			@endif
			@endauth
			@auth('staff')
			@php
				$staff = DB::table('staff')->select('role')->where('staffId', Auth::guard('staff')->user()->staffId)->get();
			@endphp
			@if($staff[0]->role == "fypcommittee" || $staff[0]->role == "admin")
				<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-info o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-cog"></i>
								</div>
								<div class="mr-5">Manage Deliverable</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="{{route('managedeliverable')}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-info o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-cogs"></i>
								</div>
								<div class="mr-5">Manage Deliverable Type</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="{{route('managedeliverabletype')}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
			@endif
				<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-white bg-secondary o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-arrow-circle-down"></i>
								</div>
								<div class="mr-5">Display Submission</div>
							</div>
							<a class="card-footer text-white clearfix small z-1" href="{{route('displaysubmission')}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>
				<div class="col-xl-3 col-sm-6 mb-3">
						<div class="card text-dark bg-light o-hidden h-100">
							<div class="card-body">
								<div class="card-body-icon">
									<i class="fas fa-fw fa-check-square"></i>
								</div>
								<div class="mr-5">Manage Competition Project</div>
							</div>
							<a class="card-footer text-dark clearfix small z-1" href="{{route('selectprojectcompetition')}}">
								<span class="float-left">View Details</span>
								<span class="float-right">
									<i class="fas fa-angle-right"></i>
								</span>
							</a>
						</div>
					</div>	
			@endauth
        </div>

    </div>
    @endsection
