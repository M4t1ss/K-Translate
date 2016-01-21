<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="K-Translate - Machine Translation Combination">
	<meta name="author" content="Matīss Rikters">
	<title>K-Translate - Machine Translation Combination</title>
	<link href="css/style.css" rel="stylesheet">
	<link href="css/chunkStyle.css" rel="stylesheet">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
</head>
<body role="document">
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="#">K-Translate</a>
	</div>
	<div id="navbar" class="navbar-collapse collapse">
	  <ul class="nav navbar-nav">
		<li><a href="?id=input">Input translations to combine</a></li>
		<li><a href="?id=api">Translate with online systems</a></li>
	  </ul>
	</div><!--/.nav-collapse -->
  </div>
</nav>
<br/>
<div class="container theme-showcase" role="main">
	<div class="page-header">
		<h1>Machine Translation Combination</h1>
	</div>
	<?php
    if (isset($_GET['id']) && in_array($_GET['id'], array('input','api','inputresult','apiresult')))
      include $_GET['id'].".php";
    else
      include "input.php";
  ?>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php
