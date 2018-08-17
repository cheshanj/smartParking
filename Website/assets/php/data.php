<?php
include ('connection.php');
$sql_insert = "INSERT INTO `arduino_ultrasonic` (`sensorId`,`slotStatus`) VALUES ('".$_GET["sensorId"]."','".$_GET["slotStatus"]."')";

if(mysqli_query($con,$sql_insert))
{
echo "Rocord added to the database!";
mysqli_close($con);
}
else
{
echo "error is ".mysqli_error($con );
}
?>