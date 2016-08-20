<?php
session_start();
/* 	api/getstudentbyid.php
	Get the student's details by using their ID number 
	Gets the row from student-names database which matches the ID number requested and then gets the timestamp of the student's last late arrival. These details are then saved to session for future use.
	
	
	Copyright Isaac Mercer 2016
	All Rights Reserved
*/

// Initialise Variables
	$idnumber = $_GET['idnumber'];
	$servername = "isaacmercer.nz";
	$username = "gdcschool-signin";
	$password = "uJSZPJRZF8EfG6WX";
	$dbname = "gdcschool-signin";
	
//Check that the submission is a valid ID number
	if (strlen($idnumber) != 5){ 		//If variable justification does not equal 5 chars
		exit('<script>window.location.href="../index.html?error=length"</script>'); //Get user to try again
	} 
// Begin Database Connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

// Establish Database Request to obtain student details by ID number
	$sql = "SELECT * FROM `student-names` WHERE studentid=$idnumber";
	$result = $conn->query($sql);

// Process Result
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			// Set database data as persistent variables
				$_SESSION['studentid'] = $row["studentid"];
				$_SESSION['firstname'] = $row["firstname"];
				$_SESSION['familyname'] = $row["familyname"];
				$_SESSION['form'] = $row["formclass"];
		}
	} else {
		//Send user back to home if no data is found
			echo "<script>window.location.href='../index.html?error=notfound'</script>";
	}

// Establish Database Request to obtain student's last late timestamp by ID number
	$sql = "SELECT * FROM `lastlate` WHERE studentid=$idnumber";
	$result = $conn->query($sql);

// Process Result
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			// Set database result as persistent variable
				$_SESSION['lastlate'] = $row["lastlate"];
			//Redirect to next page
				echo "<script>window.location.href='../justification.php'</script>";
		}
	} else {
			// Set 0 as the lastlate timestamp if no database record found
				$_SESSION['lastlate'] = 0;
			// Redirect to next page
				echo "<script>window.location.href='../justification.php'</script>";
	}
// Close database connection
	$conn->close();	

?>