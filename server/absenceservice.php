<?php
/* Service to send daily wrapup of absences 
	Copyright Isaac Mercer 2016
	All Rights Reserved
*/

$servername = "isaacmercer.nz";
$username = "gdcschool-signin";
$password = "uJSZPJRZF8EfG6WX";
$dbname = "gdcschool-signin";
$currentdate = date("l j F Y");
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM `latearrivalstoday` ORDER BY  `latearrivalstoday`.`furtheractionreq` DESC ";

$result = $conn->query($sql);

$record = "<br><br><br>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
		$furtheraction = $row["furtheractionreq"];
		if (!isset($furtheractionheading)) {
			if ($furtheraction != 1) { $record .= "<h3>The following absences did not require further action</h3>"; $furtheractionheading = "done";}
		}
        $record .= "<b>id: </b>" . $row["studentid"]. "<b> Name: </b>" . $row["firstname"]. " " . $row["lastname"]. "<b> Arrived At: </b>" . $row["arrivedat"] . "<b> Justification: </b>" . $row["justification"];
		if ($furtheraction == 1) {
				$record .= " <b>Further action was required " . $row["furtheractiontrigger"] . "</b><br>";
		}
    }
} else {
}
//Create Message Headers
$mail_to = 'isaac@isaacscomputertips.com';
$subject = 'Absence Record for ' . $currentdate;

//Create email
$body_message = 'Here is the list of today\'s absences:<br><br>';
$body_message .= $record."\n";

//Generate Additional Headers
$headers = "From: 14057@gdc.school.nz \r\n";
$headers .= "Reply-To: 14057@gdc.school.nz \r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

if(mail($mail_to, $subject, $body_message, $headers)) {
	echo "email sent ";
}
else 
{		
	echo 	"Email failed ";
}
echo "Script Finished";

?>