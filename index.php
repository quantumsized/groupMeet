<?php include "./assets/feeder.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Better than Nation Builder">
    <meta name="author" content="Tim Mann">
    <!--<link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->

    <title>Static Top Navbar Example for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/navbar-static-top.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="masthead">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2"><img src="images/grass-roots_hd.jpg" class="img-responsive" alt="Responsive image"></div>
        </div>
      </div>
    </div>
    <?php include("./assets/nav.inc");
      echo $nav;
    ?>
    <div class="container">

      <div class="row">
        <div class="col-md-2" style="border:1px solid #aaaaaa;">.col-xs-6 .col-md-4</div>
        <div class="col-md-8" style="border:1px solid #aaaaaa;">.col-xs-6 .col-md-4</div>
        <div class="col-md-2" style="border:1px solid #aaaaaa;"><?php echo $channel; ?></div>
      </div>

      <!-- Main component for a primary marketing message or call to action -->
      <!--<div class="jumbotron">
        <h1>Navbar example</h1>
        <p>This example is a quick exercise to illustrate how the default, static and fixed to top navbar work. It includes the responsive CSS and HTML, so it also adapts to your viewport and device.</p>
        <p>To see the difference between static and fixed top navbars, just scroll.</p>
        <p>
          <a class="btn btn-lg btn-primary" href="#" role="button">View navbar docs &raquo;</a>
        </p>
      </div>-->

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>