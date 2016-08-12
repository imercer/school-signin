<?php
session_start();

date_default_timezone_set('UTC');
$timestatus = date_create("now", timezone_open('Pacific/Auckland'));
$timeoffset = date_offset_get($timestatus);
if ($timeoffset == 43200) {
	$startTime = mktime(20,40,0);
	$wedstartTime = mktime(21,45,0);
	$endtime = mktime(3,20,00);
} elseif ($timeoffset == 46800) {
	$startTime = mktime(21,40,0);
	$wedstartTime = mktime(22,45,0);
	$endtime = mktime(4,20,00);
}
echo "<script>console.log('$timeoffset')</script>";
//Initialise Variables
$justification = $_GET['justification'];
$studentID = $_SESSION['studentid'];
$currentTime = time();
$servername = "isaacmercer.nz";
$username = "gdcschool-signin";
$password = "uJSZPJRZF8EfG6WX";
$dbname = "gdcschool-signin";
$weekday = date('w');

if ($weekday == 2 ) {
	$startTime = $wedstartTime;	
}
$test = $currentTime - $startTime;
$testtwo = $currentTime - $endtime;
echo "<script>console.log('$startTime')</script>";
echo "<script>console.log('$test $testtwo')</script>";
// Clear previous late log
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	$sql = "DELETE FROM `lastlate` WHERE studentid=$studentID";
	
	if ($conn->query($sql) === TRUE) {
		echo "Deleted the last late record for $studentID";
	} else {
		echo "Unable to delete the last late record for $studentID: " . $conn->error;
	}
	
	$sql = "INSERT INTO `lastlate` (studentid, lastlate)
		VALUES ('$studentID', '$currentTime')";
	
	if ($conn->query($sql) === TRUE) {
		echo "Added lastLate record for $studentID";
	} else {
		echo "Error adding lastLate record for $studentID " . $conn->error;
	}
	$conn->close();


$firstname = $_SESSION['firstname'];
$familyname = $_SESSION['familyname'];
$lastlate = $_SESSION['lastlate'];
$form = $_SESSION['form'];
$time = date('d/m/Y H:i:s');
echo $currentTime - $lastlate;
//Connect to SQL Database
if (($currentTime - $lastlate) < 1209600) {
		$lastlate = date('d/m/Y', $lastlate);
		// Add late arrival to SQL table
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
		VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '1', 'Last late within last 2 weeks: $lastlate')";
		
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$conn->close();
		//Print Slip
		shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nLast Late Arrival: $lastlate \n\n\n' | lpr");
		echo "echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nLast Late Arrival: $lastlate \n\n\n' | lpr";
		// Redirect
		$_SESSION = array();
		echo "<script>window.location.href='../actionrequired.html'</script>";
} else if (($currentTime - $startTime) > 1800 || ($currentTime - $endtime) < 15600) {
		// Add late arrival to SQL table
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
		VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '1', 'More than 30 minutes late, signed in at: $time')";
		
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$conn->close();
		//Print Slip
		shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \n' | lpr");
		echo "echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \n' | lpr";
		// Redirect
		$_SESSION = array();
		echo "<script>window.location.href='../actionrequired.html'</script>";
} else {
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		// Add late arrival to SQL table
		$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
		VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '0', '')";
		
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$conn->close();
		// Print Slip
		shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nsuccessfully signed in, no further action required \n\n\n' | lpr");
		// Redirect
		$_SESSION = array();
		echo "<script>window.location.href='../finished.html'</script>";
}
?>