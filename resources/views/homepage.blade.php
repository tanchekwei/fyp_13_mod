</<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="{{asset('css/app.css')}}" />
    <script src="main.js"></script>
</head>
<body>
    <p>wakaka</p>

    <form method="get" action="{{url('/login')}}">
    <button type="submit" class="btn btn-warning">test</button>
    
    </form>

    <a href="{{url('showallcohort')}}">Maintain Cohort</a>
    
</body>
</html>