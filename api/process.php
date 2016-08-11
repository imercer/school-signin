<?php
session_start();
$justification = $_GET['justification'];
$studentID = $_SESSION['studentid'];
$currentTime = time();
$servername = "localhost";
$username = "school-signin";
$password = "uJSZPJRZF8EfG6WX";
$dbname = "gdc-signin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "UPDATE `student-names` SET lastLate='$currentTime' WHERE studentid=$studentID";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();

$firstname = $_SESSION['firstname'];
$familyname = $_SESSION['familyname'];
$lastlate = $_SESSION['lastlate'];
$form = $_SESSION['form'];
$time = date('d/m/Y H:i:s');
echo $currentTime - $lastlate;
if (($currentTime - $lastlate) < 1209600) {
	$lastlate = date('d/m/Y', $lastlate);
	// Create connection
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
	shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nLast Late Arrival: $lastlate \n\n\n' | lpr");
	echo "echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nLast Late Arrival: $lastlate \n\n\n' | lpr";
	session_unset();
	/*echo "<script>window.location.href='../actionrequired.html'</script>";*/
} else {
	// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		
		$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
		VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '0', '')";
		
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$conn->close();
	shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nsuccessfully signed in, no further action required \n\n\n' | lpr");
	session_unset();
	echo "<script>window.location.href='../finished.html'</script>";
}
?>