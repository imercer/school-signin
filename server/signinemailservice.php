<?php
/* 	signinemailservice.php
	Service to send daily wrapup of late arrivals 
	Gets the contents of todayslatearrivals database and formats it and sends list as an email to the nominated address. Runs on server-side every weekday after 3:20pm.
	
	
	Copyright Isaac Mercer 2016
	All Rights Reserved
*/

//CONFIG **EDIT THIS** -----------------------------------------------------------------

$attendanceofficeremail = "isaac@isaacscomputertips.com";

//DO NOT EDIT BELOW THIS LINE ----------------------------------------------------------

//Initialise Variables
	$servername = "isaacmercer.nz";
	$username = "gdcschool-signin";
	$password = "uJSZPJRZF8EfG6WX";
	$dbname = "gdcschool-signin";
	$currentdate = date("l j F Y");

// Begin database connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

// Establish Database Request to obtain all late arrivals today sort by further action requirements
	$sql = "SELECT * FROM `latearrivalstoday` ORDER BY  `latearrivalstoday`.`furtheractionreq` DESC ";
	$result = $conn->query($sql);

// Process Result
	$record = "<br><br><br>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			// Print further action seperator heading before first 'no further action' late arrival
				$furtheraction = $row["furtheractionreq"];
				if (!isset($furtheractionheading)) {
					if ($furtheraction != 1) { $record .= "<h3>The following late arrivals did not require further action</h3>"; $furtheractionheading = "done";}
				}
			// Add Id number, student name, arrival time and justification to email body
				$record .= "<b>id: </b>" . $row["studentid"]. "<b> Name: </b>" . $row["firstname"]. " " . $row["lastname"]. "<b> Arrived At: </b>" . $row["arrivedat"] . "<b> Justification: </b>" . $row["justification"];
			// If further action was required then also add those details and the reason for the trigger
				if ($furtheraction == 1) {
						$record .= " <b>Further action was required " . $row["furtheractiontrigger"] . "</b><br>";
				}
		}
	} else {
		$record .= "<i><h3>There were not late arrivals today</h3></i>";	// Add no late arrivals to the email if there were no late arrivals on that paticular day
	}
// Generate Email
	//Create Message Headers
	$mail_to = $attendanceofficeremail; 				// Send email to attendance officer
	$subject = 'Sign in record for ' . $currentdate;	// Set subject
	
	//Create body
	$body_message = 'Here is the list of today\'s late arrivals:<br><br>';
	$body_message .= $record."\n";						// Add database values
	
	//Generate Additional Headers
	$headers = "From: 14057@gdc.school.nz \r\n";		// Add from address
	$headers .= "Reply-To: 14057@gdc.school.nz \r\n";	// Add reply-to address
	$headers .= "MIME-Version: 1.0\r\n";				// Enable email formating
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

// Send email
	if(mail($mail_to, $subject, $body_message, $headers)) {
		echo "email sent ";
	}
	else 
	{		
		echo 	"Email failed ";
	}
echo "Script Finished";

?>