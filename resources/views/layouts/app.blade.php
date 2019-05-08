<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="_token" content="{{csrf_token()}}" />

        <title>@yield('title')</title>
        <!-- Bootstrap core CSS-->
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Custom fonts for this template-->
        <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

        <!-- Page level plugin CSS-->
        <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">

        <script src="http://code.jquery.com/jquery-3.3.1.min.js"
                integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
                crossorigin="anonymous">
        </script>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
        <script>
                    $(window).load(function () {
                        // Animate loader off screen
                        $(".se-pre-con").fadeOut("slow");
                        ;
                    });
        </script>
		
		<!-- lau added-->
		<link rel="stylesheet" href="{{asset('css/lau.css')}}">
		<!---->
		
        <link rel="stylesheet" href="{{asset('css/table.css')}}">


        <style>

            /* The Close Button */
            .close {
                color: #aaaaaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
                cursor: pointer;
            }
            /* Style the tab */
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }

            /* Style the buttons inside the tab */
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 17px;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
                background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
                background-color: #ccc;
            }

            /* Style the tab content */
            .tabcontent {
                display: none;
                padding: 6px 12px;
                -webkit-animation: fadeEffect 1s;
                animation: fadeEffect 1s;
                border: 1px solid #ccc;
                border-top: none;
            }

            /* Fade in tabs */
            @-webkit-keyframes fadeEffect {
                from {opacity: 0;}
                to {opacity: 1;}
            }

            @keyframes fadeEffect {
                from {opacity: 0;}
                to {opacity: 1;}
            }

            .no-js #loader { display: none;  }
            .js #loader { display: block; position: absolute; left: 100px; top: 0; }
            .se-pre-con {
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url(images/loader-64x/Preloader_2.gif) center no-repeat #fff;
            }
        </style>

    </head>

    <body id="page-top">

        <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

            <a class="navbar-brand mr-1" href="/index">FYP Management System</a>

            <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
                <i class="fas fa-bars"></i>
            </button>

            <div class="input-group" style="width:150px;margin-left:65%">
                @auth('staff')
                <input disabled="disabled" value="{{ Auth::guard('staff')->user()->staffName }}" type="text" class="form-control" style="text-align: center">
                @else
                @auth('student')
                <input disabled="disabled" value="{{ Auth::guard('student')->user()->studentName }}" type="text" class="form-control" style="text-align: center">
                @endauth
                @endauth
            </div>

            <!-- Navbar -->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="/index" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        @auth('staff')
                        <a class="dropdown-item" href="{{url('/staffprofile')}}">Profile</a>
                        @endauth
                        @auth('student')
                        <a class="dropdown-item" href="{{url('/studentprofile')}}">Profile</a>
                        @endauth
                        <div class="dropdown-divider"></div>
                        {{-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a> --}}

                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>

        </nav>

		
		
		
        <div id="wrapper">

            <ul class="sidebar navbar-nav">
                <?php
                if ($__env->yieldContent('title') === "Main Page") {
                    ?>
                    <li class="nav-item active">
                        <?php
                    } else {
                        ?>
                    <li class="nav-item link">
                        <?php
                    }
                    ?>
                    @auth('staff')
                    @if(Auth::guard('staff')->user()->role == 'admin' || Auth::guard('staff')->user()->role == 'facultyadmin')
                    <a class="nav-link" href="/Staffindex">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @endif
                    @if(Auth::guard('staff')->user()->role == 'fypcommittee' || Auth::guard('staff')->user()->role == 'supervisor')
                    <a class="nav-link" href="/showallcohort">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @endif
                    @endauth
                    @auth('student')
                    <a class="nav-link" href="/index">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @endauth
                </li>
                @auth('staff')
                <li class="nav-item link">
                    <a class="nav-link" href="{{url('/cohortmenu')}}">
                        <i class="fas fa-fw fa-file"></i>
                        <span>Cohort</span>
                    </a>
                </li>                
                <?php
                if ($__env->yieldContent('module') === "Project Page") {
                    ?>
                    <li class="nav-item active">
                        <?php
                    } else {
                        ?>
                    <li class="nav-item link">
                        <?php
                    }
                    ?>
                    <a class="nav-link" href="/viewproject">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>Project</span>
                    </a>
                </li>
                @endauth
                <?php
                if ($__env->yieldContent('module') === "ProjectList Page") {
                    ?>
                    <li class="nav-item dropdown active">
                        <?php
                    } else {
                        ?>
                    <li class="nav-item dropdown link">
                        <?php
                    }
                    ?>
					<a class="nav-link" href="/viewprojectlist">
                        <i class="fas fa-fw fa-list"></i>
                        <span>ProjectList</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/MianHome">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Student</span></a>
                </li>
                @auth('staff')
                <li class="nav-item">
                    <a class="nav-link" href="/staffpage">
                        <i class="fas fa-fw fa-robot"></i>
                        <span>Staff</span></a>
                </li>
                @endauth
				
				<li class="nav-item link">
                    <a class="nav-link" href="/rubric">
                        <i class="fas fa-fw fa-pen"></i>
                        <span>Rubric</span>
                    </a>
                </li>  

				<li class="nav-item link">
                    <a class="nav-link" href="/formMain">
                        <i class="fas fa-fw fa-paper-plane"></i>
                        <span>Form</span>
                    </a>
                </li>  
                
                @auth('staff')
                @if(Auth::guard('staff')->user()->role == 'admin' || Auth::guard('staff')->user()->role == 'facultyadmin' || Auth::guard('staff')->user()->role == 'fypcommittee')
                <?php
                if ($__env->yieldContent('module') === "Workload Page") {
                    ?>
                    <li class="nav-item dropdown active">
                        <?php
                    } else {
                        ?>
                    <li class="nav-item dropdown link">
                        <?php
                    }
                    ?>
                    <a class="nav-link dropdown-toggle" href="/viewworkload" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fas fa-fw fa-address-book"></i>
                        <span>Workload</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                        <h6 class="dropdown-header">Workload:</h6>
                        <a class="dropdown-item" href="/viewworkload">Workload Main</a>
                        <a class="dropdown-item" href="/updateformula">Update Formula</a>
                    </div>
                </li>
                @endif
                @endauth
				
				@auth('student')
			
				@if(Auth::guard('student')->user()->teamId != null)
			
				 @php
                        $isCompetition = DB::table('team')->select('isCompetition')->where('teamId', auth()->guard('student')->user()->teamId)->get();
                    @endphp
					@if($isCompetition)
                    @if($isCompetition[0]->isCompetition == "1")
							<li class="nav-item link">
							<a class="nav-link" href="{{route('projectsubmission', ['isCompetition'=>1])}}">
							<i class="fas fa-fw fa-trophy"></i>
							<span>Competition Submission</span>
							</a>
							</li>  
							
                    @endif
					@endif
					<li class="nav-item link">
							<a class="nav-link" href="{{route('projectsubmission', ['isCompetition'=>0])}}">
							<i class="fas fa-fw fa-arrow-circle-up"></i>
							<span>Project Submission</span>
							</a>
							</li>  
			@endif
			@endauth
			@auth('staff')
			@php
				$staff = DB::table('staff')->select('role')->where('staffId', Auth::guard('staff')->user()->staffId)->get();
			@endphp
			@if($staff[0]->role == "fypcommittee" || $staff[0]->role == "admin")
				<li class="nav-item link">
							<a class="nav-link" href="{{route('managedeliverable')}}">
							<i class="fas fa-fw fa-cog"></i>
							<span>Deliverable</span>
							</a>
							</li>
				<li class="nav-item link">
							<a class="nav-link" href="{{route('managedeliverabletype')}}">
							<i class="fas fa-fw fa-cogs"></i>
							<span>Deliverable Type</span>
							</a>
							</li>
			@endif
				<li class="nav-item link">
							<a class="nav-link" href="{{route('displaysubmission')}}">
							<i class="fas fa-fw fa-arrow-circle-down"></i>
							<span>Display Submission</span>
							</a>
							</li>
				<li class="nav-item link">
							<a class="nav-link" href="{{route('selectprojectcompetition')}}">
							<i class="fas fa-fw fa-check-square"></i>
							<span>Competition Project</span>
							</a>
							</li>
			@endauth 
            </ul>

            <div id="content-wrapper">

                <div class="container">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

                <!-- Sticky Footer -->
                <footer class="sticky-footer" style="height:30px">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright © TARUC 2018</span>
                        </div>
                    </div>
                </footer>

            </div>
            <!-- /.content-wrapper -->

        </div>
        <!-- /#wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="/">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

        <!-- Page level plugin JavaScript-->
        <script src="{{ asset('vendor/chart.js/Chart.min.js')}}"></script>
        <script src="{{ asset('vendor/datatables/jquery.dataTables.js')}}"></script>
        <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.js')}}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{ asset('js/sb-admin.min.js')}}"></script>

        <!-- Demo scripts for this page-->
        <script src="{{ asset('js/demo/datatables-demo.js')}}"></script>
        <script src="{{ asset('js/demo/chart-area-demo.js')}}"></script>
    </body>
</html>
