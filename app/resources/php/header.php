<?php try{ ?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../../../favicon.ico">
	-->
    <title><?php echoif(Html::title()); ?></title>
    <!-- Bootstrap core CSS -->
    <link href="<?php public_url('res/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php public_url('res/fa/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php public_url('css/style.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php public_url('css/shop-style.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php public_url('css/live-search.css'); ?>" rel="stylesheet" type="text/css">

</head>

<body>
    <div class="cube-wrapper">
        <header>
            <?php $this->template("header-top"); 
            $this->template("header-middle"); 
            $this->template("header-menu",null,true); ?>
        </header>
        <!-- end header --><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>