<!DOCTYPE HTML>
<!--
	Industrious by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>

<head>
    <title>ARTIK Cloud based Smart Parking System</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="refresh" content="2" >
    <link rel="stylesheet" href="assets/css/main.css" />
   
    <style>
        rainbowParking {
            display: block;
            background-color: #dddddd;
            padding: 30px;
            font-size: 70px;
            line-height: 60%;
            margin-left: 40px;
            margin-right: 40px;
            border-style: groove;
        }

        indigoParking {
            display: block;
            background-color: #dddddd;
            padding: 30px;
            font-size: 70px;
            line-height: 60%;
            margin-left: 40px;
            margin-right: 40px;
            border-style: groove;
        }

        div.container {
            margin: 15px;
        }

        div.left,
        div.right {
            float: left;
            padding: 10px;
        }

        div.left {
            background-color: white;
            width: 900px;
            height: 500px;
        }

        div.right {
            background-color: #dddddd;
            width: 360px;
            height: 400px;
            margin-top: 20px;
            margin-bottom: 100px;
        }

        div.right-output {
            background-color: #dddddd;
            width: 360px;
            height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            margin-bottom: 100px;
        }

        .myBox {
  position:absolute;
  left:471px;
  top:94px;
  width:204px;
  height:183px;
  filter:alpha(opacity=90);
  opacity:0.90;
  z-index:7;
}

.redBox {
  background: red;
}

.greenBox {
  background: green;
}


.square-box {
    position: relative;
    width: 20%;
    overflow: hidden;
    background: blue;
	
	&:before {
		content: "";
		display: block;
		padding-top: 100%;
	}
}

.square-content {
    position:  absolute;
    top: 0; left: 0; bottom: 0; right: 0;
    color: white;
	
	div {
	   display: table;
	   width: 100%;
	   height: 100%;
	}
	
	span {
		display: table-cell;
		text-align: center;
		vertical-align: middle;
		color: white
	}
}


    </style>
</head>

<body class="is-preload" >

    <!-- Header -->
    <header id="header">
        <a class="logo" href="index.html">SmartPark</a>
        <nav>
            <a href="#menu">Menu</a>
        </nav>
    </header>

    <!-- Nav -->
    <nav id="menu">
		<ul class="links">
			<li>
				<a href="index.html">Home</a>
			</li>
			<li>
				<a href="websocket.html">Parking Lot Status</a>
			</li>
			<li>
				<a href="slotstatus.php">Parking Space Availability</a>
			</li>
		</ul>
	</nav>

    <!-- Heading -->
    <div id="heading">
        <h1>ARTIK Cloud based Smart Parking System</h1>
    </div>

    <!-- Main -->
    <section id="main" class="wrapper">

        <h2 style="color:green;text-align:center;font-size: 50px">Main Parking Status</h2>

        <div class="container">
           

<?php
$user = 'root';
$password = '';
$db = 'smart_parking';
$host = 'localhost';
$port = 3306;

$link = mysqli_init();
$conn = mysqli_real_connect(
   $link,
   $host,
   $user,
   $password,
   $db,
   $port
);

// $server = "localhost:3306";
// $database = "smart_parking";
// $username = "root";
// $password = "";

// $conn = new mysqli($server, $username, $password);
// $db_handle = $conn->select_db($database);


//List the Columns for the Report 
echo "<table border='1'> 
<tr> 
<th>Sensor ID</th> 
<th>Slot Status</th> 
</tr>"; 

$mysqli = new mysqli("localhost:3306", "$user", "$password", "$db");

//$result = $mysqli->query("SELECT sensorId,slotStatus,time FROM arduino_ultrasonic au1 WHERE timestamp = (SELECT MAX(timestamp) FROM arduino_ultrasonic au2 WHERE au1.sensorId = au2.sensorId)");

$result = $mysqli->query("SELECT sensorId,slotStatus,time FROM arduino_ultrasonic au1 WHERE time = (SELECT MAX(time) FROM arduino_ultrasonic au2 WHERE au1.sensorId = au2.sensorId)");

//$result = $conn->mysqli_query($result);

// while($row = mysqli_fetch_array($result)) 

while($row = $result->fetch_assoc()) 
  { 
  echo "<tr>"; 
  echo "<td>" . $row['sensorId'] . "</td>"; 
  //echo "<td>" . $row['slotStatus'] . "</td>"; 
  if($row['sensorId']=='Ultra_sensor_01' && $row['slotStatus']=='Occupied') // [val1] can be 'approved'
         echo "<td style='background-color: #FF0000;'>".$row['slotStatus']."</td>"; 
  else if($row['sensorId']=='Ultra_sensor_01' && $row['slotStatus']=='Free')// [val2]can be 'rejected'
         echo "<td style='background-color: #008000;'>".$row['slotStatus']."</td>"; 
  else if($row['sensorId']=='Ultra_sensor_02' && $row['slotStatus']=='Occupied')// [val2]can be 'rejected'
         echo "<td style='background-color: #FF0000;'>".$row['slotStatus']."</td>"; 
  else if($row['sensorId']=='Ultra_sensor_02' && $row['slotStatus']=='Free')// [val2]can be 'rejected'
         echo "<td style='background-color: #008000;'>".$row['slotStatus']."</td>"; 
  else if($row['sensorId']=='Ultra_sensor_03' && $row['slotStatus']=='Occupied')// [val2]can be 'rejected'
         echo "<td style='background-color: #FF0000;'>".$row['slotStatus']."</td>"; 
  else if($row['sensorId']=='Ultra_sensor_03' && $row['slotStatus']=='Free')// [val2]can be 'rejected'
         echo "<td style='background-color: #008000;'>".$row['slotStatus']."</td>";  
  echo "</tr>"; 
  } 
echo "</table>";  
$mysqli->close();
?>

            </div>


    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="inner">
            <div class="content">
                <section>
                    <h3>SHU Final Year Project</h3>
                    <p>This project is based on IoT concept.I used Raspberry Pi, Arduino and Samsung Artik cloud service to create this project.
                        This a simple demonstrantion of upcoming smart parking.
                    </p>
                </section>
                <section>
                    <h4>Links</h4>
                    <ul class="alt">
                        <li>
                            <a href="https://raspberrypi.org">Raspberry Pi</a>
                        </li>
                        <li>
                            <a href="https://arduino.cc">Arduino</a>
                        </li>
                        <li>
                            <a href="https://www.artik.io/">Samsung Artik</a>
                        </li>
                        
                    </ul>
                </section>
                <section>
                    <h4>Social Media Links</h4>
                    <ul class="plain">
                        <li>
                            <a href="www.twitter.com">
                                <i class="icon fa-twitter">&nbsp;</i>Twitter</a>
                        </li>
                        <li>
                            <a href="www.facebook.com">
                                <i class="icon fa-facebook">&nbsp;</i>Facebook</a>
                        </li>
                        <li>
                            <a href="www.instagram.com">
                                <i class="icon fa-instagram">&nbsp;</i>Instagram</a>
                        </li>
                        <li>
                            <a href="www.github.com">
                                <i class="icon fa-github">&nbsp;</i>Github</a>
                        </li>
                    </ul>
                </section>
            </div>
            <div class="copyright">
                &copy; CheshanJ. Photos:
                <a href="https://unsplash.co">Unsplash</a>, Video:
                <a href="https://coverr.co">Coverr</a>.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>