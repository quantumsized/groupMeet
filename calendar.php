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

    <title>The NOT Nation Builder True Grass Roots</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/navbar-static-top.css" rel="stylesheet">

    <!-- Calendar elements begin -->
    <link href='fullcalendar/fullcalendar.css' rel='stylesheet' />
    <link href='fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='lib/jquery.min.js'></script>
    <script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
    <script src='lib/jquery-ui.custom.min.js'></script>
    <script src='fullcalendar/fullcalendar.min.js'></script>
    <!-- Calendar elements end -->

    <meta name="viewport" content="width=device-width" />
    <!-- Just for debugging purposes. Don't actually copy this line! -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/calendar.js"></script>
    <style>
      #loading {
        position: absolute;
        top: 5px;
        right: 5px;
        }

      #calendar {
        text-align: center;
        font-size: 14px;
        font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
        width: 100%;
        margin: 0 auto;
        }

			#calendar .fc-view {
				overflow: visible;
			}
      div.calendar-trash{
				float: left;
				padding-right: 8px;
				margin-right:5px;
				padding-left:8px;
				padding-top: 3px;
				cursor: pointer;
			}

			.to-trash{
				background-color:#EAEAEA;
				-webkit-border-radius: 5em;
				border-radius: 5em;
			}
      .simple_overlay {

        /* must be initially hidden */
        display:none;
    
        /* place overlay on top of other elements */
        z-index:10000;
    
        /* styling */
        background-color:#fff;
    
        width:300px;
        min-height:150px;
        border: 1px solid #666;
        padding: 10px;
        border-radius: 20px;
    
        /* CSS3 styling for latest browsers */
        -moz-box-shadow:0 0 90px 5px #000;
        -webkit-box-shadow: 0 0 90px #000;
      }
      .simple_overlay .close {
        position: absolute;
        top: -5px;
        right: -5px;
        cursor: pointer;
        border-radius: 14px;
        background: -webkit-gradient(linear, left top, left bottom, from(#ccc), to(#666));
      }
      .simple_overlay label {
        display: inline-block;
        text-align: right;
        width: 60px;
        padding-right: 5px;
      }

    </style>
  </head>

  <body>
    <div class="simple_overlay" id="event_details">
      <svg class="close" width="22" height="22"><g stroke="white" stroke-width="2"><path d="M 6,6 16,16 M 16,6 6,16" /></g></svg>
      <div>
        <label for="event_title">Title: </label>
        <input type="text" id="event_title" />
      </div>
      <div>
        <label>Start: </label>
        <input type="text" id="start_date" size="8" />
        <input type="text" id="start_time" size="6" />
      </div>
      <div>
        <label>End: </label>
        <input type="text" id="end_date" size="8" />
        <input type="text" id="end_time" size="6" />
      </div>
      <div>
        <label>All Day: </label>
        <input type="checkbox" id="allday" checked="true" />
      </div>
      <div>
        <label for="event_link">URL: </label>
        <input type="text" id="event_link" />
      </div>
      <div><input type="button" id="set_event" value="Set" /></div>
    </div>
    <div class="masthead">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2"><img src="images/grass-roots_hd.jpg" class="img-responsive" alt="Responsive image"></div>
        </div>
      </div>
    </div>
    <!-- Static navbar -->
    <?php include("assets/nav.inc");
      echo $nav;
    ?>
    <div class="container">

      <div class="row">
        <div class="col-md-2" style="border:1px solid #aaaaaa;">.col-xs-6 .col-md-4</div>
        <div class="col-md-8">
          <!-- Calendar object here -->
          <div id='calendar'></div>
        </div>
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


    <!-- Bootstrap core JavaScript -->
    <!-- ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- // <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>