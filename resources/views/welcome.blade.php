<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>FYP Management System</title>

        <!-- Bootstrap core CSS -->
        <link href="/public/vendor/bootstrapwel/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="/public/vendor/fontawesome-freewel/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic" rel='stylesheet' type='text/css'>

        <!-- Plugin CSS -->
        <link href="/public/vendor/magnific-popupwel/magnific-popup.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="/public/css/creative.min.css" rel="stylesheet">
		
		
		<!-- Bootstrap core CSS -->
        <link href="vendor/bootstrapwel/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="vendor/fontawesome-freewel/css/all.min.css" rel="stylesheet" type="text/css">


        <!-- Plugin CSS -->
        <link href="vendor/magnific-popupwel/magnific-popup.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/creative.min.css" rel="stylesheet">
		
		

    </head>

    <body id="page-top">

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">TARUC</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
				@if (Route::has('login'))
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
					@auth('student')
                        <a href="{{ url('/index') }}">Home</a>
                    @else
                        @auth('staff')
                        <?php
                            if(Auth::guard('staff')->user()->role== 'admin' || Auth::guard('staff')->user()->role=='facultyadmin')
                            {
                                ?>
								<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#about">About</a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ url('/Staffindex') }}">Home</a>
                        </li>
                                <?php
                            }
                            else
                            {
                                ?>
								<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#about">About</a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ url('/showallcohort') }}">Home</a>
                        </li>
                                <?php
                            }
                        ?>
                        @else
							<li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#about">About</a>
                        </li>
                            <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{url('auth/google')}}">Login</a>
                        </li>
                        @endauth
                    @endauth  
                    </ul>
                </div>
				@endif
            </div>
        </nav>

        <header class="masthead text-center text-white d-flex">
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <h1 class="text-uppercase">
                            <strong>Welcome To <br />FYP Management System</strong>
                        </h1>
                        <hr>
                    </div>
                    <div class="col-lg-8 mx-auto">
                        <a class="btn btn-primary btn-xl js-scroll-trigger" href="#about">About Us</a>
                    </div>
                </div>
            </div>
        </header>

        <section class="bg-primary" id="about" style="background-image: url(/public/img/aboutus.jpeg);background-repeat:no-repeat;background-position:center center;background-size:cover">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mx-auto text-center">
                        <h2 class="section-heading text-black" style="font-weight:bold">About Us</h2>
                        <hr class="light my-4" style="color: black">
                        <p class="text-faded mb-4" style="color: black;font-weight:bold">In this era of globalization, there is immense emphasis on efficiency, accuracy, speed, and quality output among organizations, including institutions of higher learning. The Final Year Project (FYP) Management System was developed to automate the management and coordination processes related to FYP. It consists of the following modules: Cohort, Supervisor and Student Maintenance, Project Management and Registration, Auto-assignment of Supervisors to Students, Supervisor Workload Tracking, Student Assessments, Forms and Template Management, as well as Security.
                            <br /><br />Besides benefiting the faculties and their FYP committees with increased efficiency and effectiveness in the important tasks related to FYP management, this system also plays a crucial role in the acquisition and organization of data that is needed to facilitate the uploading of project artifacts to TAR UC’s I²Hub project repository.
</p>
                        <a class="btn btn-light btn-xl js-scroll-trigger" href="https://docs.google.com/forms/d/e/1FAIpQLSfJZG5doXaHbD8o2Z_mrYoDPNSxLrizCPaXY71bfB8o-gMEyA/viewform" style="color: black;font-weight:bold">Vote For Us!!</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bootstrap core JavaScript -->
        <script src="/public/vendor/jquerywel/jquery.min.js"></script>
        <script src="/public/vendor/bootstrapwel/js/bootstrap.bundle.min.js"></script>

        <!-- Plugin JavaScript -->
        <script src="/public/vendor/jquery-easingwel/jquery.easing.min.js"></script>
        <script src="/public/vendor/scrollrevealwel/scrollreveal.min.js"></script>
        <script src="/public/vendor/magnific-popupwel/jquery.magnific-popup.min.js"></script>

        <!-- Custom scripts for this template -->
        <script src="/public/js/creative.min.js"></script>
		
		<script src="vendor/jquerywel/jquery.min.js"></script>
        <script src="vendor/bootstrapwel/js/bootstrap.bundle.min.js"></script>

        <!-- Plugin JavaScript -->
        <script src="vendor/jquery-easingwel/jquery.easing.min.js"></script>
        <script src="vendor/scrollrevealwel/scrollreveal.min.js"></script>
        <script src="vendor/magnific-popupwel/jquery.magnific-popup.min.js"></script>

        <!-- Custom scripts for this template -->
        <script src="js/creative.min.js"></script>

    </body>

</html>
