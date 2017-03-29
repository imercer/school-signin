<?php
session_start();
/* 	api/process.php
	Updates the database and prints late slip after sign-in 
	Registers the late arrival time against the student's name then adds a record to the database with the justification and full late arrival and student details. Additionally this script calculates whether further action should be required (more than 30 minutes late or late twice in 2 weeks). Finishes by executing a print job with the late slip details.
	
	
	Copyright Isaac Mercer 2016
	All Rights Reserved
*/

// Sets default time zone and calculates times in relation to NZST/NZDT
	date_default_timezone_set('UTC');
	$timestatus = date_create("now", timezone_open('Pacific/Auckland'));
	$timeoffset = date_offset_get($timestatus);
	if ($timeoffset == 43200) { 			// If NZ daylight saving is false
		$startTime = mktime(20,45,0);	 		// Set school start time to 20:45UTC 8:45NZST
		$wedstartTime = mktime(21,50,0);	 	// Set wednesday start time to 21:50UTC 9:50NZST
		$endtime = mktime(3,20,00); 			// Set end of day to 3:20UTC 15:20NZST
	} elseif ($timeoffset == 46800) {		// If NZ daylight saving is true
		$startTime = mktime(19,45,0);			// Set school start time to 19:45UTC 8:45NZDT
		$wedstartTime = mktime(20,50,0);		// Set wednesday start time to 20:50UTC 9:50NZDT
		$endtime = mktime(2,20,00);				// Set end of day to 2:20UTC 15:20NZDT
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

	if ($weekday == 2 ) {			// If morning is Tuesday UTC / Wednesday NZST/NZDT
		$startTime = $wedstartTime;		// Set start time to Wednesday Start Time
	}

//Check that submission hasn't come from barcode scanner
	if(1 === preg_match('~[0-9]~', $justification)){   //If variable justification contains numbers
		exit('<script>window.location.href="../justification.php?error=numbers"</script>'); //Get user to try again
	}
//Check that the submission is a valid string
	if (strlen($justification) < 5){ 				  //If variable justification is less than 5 chars
		exit('<script>window.location.href="../justification.php?error=length"</script>'); //Get user to try again
	} 
	
// Clear student's previous last late
	// Begin Database connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
	
	// Establish Database Request to delete last late row where studentid matches the current student's ID number
		$sql = "DELETE FROM `lastlate` WHERE studentid=$studentID";
	
	if ($conn->query($sql) === TRUE) {	// Was result sucsessful
		echo "Deleted the last late record for $studentID";
	} else {
		echo "Unable to delete the last late record for $studentID: " . $conn->error;
	}
	
	// Establish Database Request to insert a new last late row with the current time and student id 
		$sql = "INSERT INTO `lastlate` (studentid, lastlate)
			VALUES ('$studentID', '$currentTime')";
	
	if ($conn->query($sql) === TRUE) {
		echo "Added lastLate record for $studentID";
	} else {
		echo "Error adding lastLate record for $studentID " . $conn->error;
	}
	
	$conn->close(); // Close connection

//Initialise Variables from Session
	$firstname = $_SESSION['firstname'];
	$familyname = $_SESSION['familyname'];
	$lastlate = $_SESSION['lastlate'];
	$form = $_SESSION['form'];
	
$time = date_format($timestatus, 'D d/m/Y - g:i:sa'); // Create human readable date
echo $currentTime - $lastlate; // Calculate seconds since student's last late arrival

// Record late arrival details in database and print late slip
	if (($currentTime - $lastlate) < 1209600) { 	//Have there been more than 2 weeks since last late arrival
			$lastlate = date('d/m/Y', $lastlate);	

			//Begin database connection
				$conn = new mysqli($servername, $username, $password, $dbname);
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 
				
			// Establish Database request to insert the student's id number, name, arrival time, justification and further action details into today's late arrivals database
				$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
				VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '1', 'Last late within last 2 weeks: $lastlate')";
				
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
			$conn->close(); // Close connection
			
			// Print late arrival slip
				//shell_exec("lpr -o fit-to-page /home/pi/lateslip.png && echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nLast Late Arrival: $lastlate \n\n\n' | lpr");
				shell_exec("echo 'Name: \n$firstname $familyname \nForm Class: $form \nTime Signed In: \n$time \nJustification: \n$justification \nLast Late Arrival: $lastlate\nAuthorised By: \n_____________ \nDO NOT ACCEPT UNLESS DP SIGNATURE IS PRESENT\n\n\n' | lpr");
			// Redirect
				$_SESSION = array(); // Empty session values
				echo "<script>window.location.href='../actionrequired.html'</script>";
				
	} else if (($currentTime - $startTime) > 1800 || ($currentTime - $endtime) < 0) { // Has the student signed in between 9:15/10:20 and 3:30
			//Begin database connection
				$conn = new mysqli($servername, $username, $password, $dbname);
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 
			
			// Establish Database request to insert the student's id number, name, arrival time, justification and further action details into today's late arrivals database
				$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
				VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '1', 'More than 30 minutes late, signed in at: $time')";
				
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
			$conn->close(); // Close conenction
			
			// Print late arrival slip
				//shell_exec("lpr -o fit-to-page /home/pi/lateslip.png && echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \n' | lpr");
				shell_exec("echo 'Name: \n$firstname $familyname \nForm Class: $form \nTime Signed In: \n$time \nJustification: \n$justification \nAuthorised By: \n_____________ \nDO NOT ACCEPT UNLESS DP SIGNATURE IS PRESENT\n\n\n' | lpr");
			// Redirect
				$_SESSION = array(); // Empty session values
				echo "<script>window.location.href='../actionrequired.html'</script>";
				
	} else {	// Normal sign in, no further action required
			/// Begin database connection
				$conn = new mysqli($servername, $username, $password, $dbname);
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 
				
			// Establish Database request to insert the student's id number, name, arrival time, justification into today's late arrivals database
				$sql = "INSERT INTO `latearrivalstoday` (studentid, firstname, lastname, arrivedat, justification, furtheractionreq, furtheractiontrigger)
				VALUES ('$studentID', '$firstname', '$familyname' , '$time', '$justification', '0', '')";
				
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
			$conn->close(); // Close connection
			
			// Print late arrival slip
				//shell_exec("lpr -o fit-to-page /home/pi/lateslip.png && echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nsuccessfully signed in, no further action required \n\n\n' | lpr");
				shell_exec("echo 'Name: \n$firstname $familyname \nForm Class: $form \nTime Signed In: \n$time \nJustification: \n$justification \nsuccessfully signed in, no further action required \n\n\n' | lpr");
			// Redirect
				$_SESSION = array(); // Empty session values
				echo "<script>window.location.href='../finished.html'</script>";
	}
?>
