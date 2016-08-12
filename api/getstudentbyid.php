<?php
session_start();
$idnumber = $_GET['idnumber'];
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

$sql = "SELECT * FROM `student-names` WHERE studentid=$idnumber";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["studentid"]. " - Name: " . $row["firstname"]. " " . $row["familyname"]. "<br>";
		$_SESSION['studentid'] = $row["studentid"];
		$_SESSION['firstname'] = $row["firstname"];
		$_SESSION['familyname'] = $row["familyname"];
		$_SESSION['form'] = $row["formclass"];
    }
} else {
    echo "<script>window.location.href='../index.html'</script>";
}

$sql = "SELECT * FROM `lastlate` WHERE studentid=$idnumber";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
		$_SESSION['lastlate'] = $row["lastlate"];
	    echo "<script>window.location.href='../justification.php'</script>";
    }
} else {
		$_SESSION['lastlate'] = 0;
	    echo "<script>window.location.href='../justification.php'</script>";
}
$conn->close();

?>