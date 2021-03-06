<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../../../favicon.ico">
	-->
    <title>{{Html::title()}}</title>
    <!-- Bootstrap core CSS -->
    <link href="{{public_url('res/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{public_url('res/fa/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{public_url('css/dashboard.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="@public_url('res/datetimepicker/css/bootstrap-datetimepicker.min.css')" />
    
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="{{url('profile')}}">Profile</a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{url('home')}}">Home </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{url('dashboard')}}">Dashboard</a>
                </li>
                
            </ul>
            @if(_session('userid'))
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{url('logout')}}">Đăng xuất</a>
                </li>
            </ul>
            @end;
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">