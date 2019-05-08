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
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-fw fa-file"></i>
                        </div>
                        <div class="mr-5">Cohort</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="{{url('/showallcohort')}}">
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
                            <i class="fas fa-fw fa-robot"></i>
                        </div>
                        <div class="mr-5">Staff Management</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="/staffpage">
                        <span class="float-left">View Details</span>
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>                                    
        </div>

    </div>
    @endsection