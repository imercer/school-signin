<?php
session_start();
$justification = $_GET['justification'];
/*
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
}
$conn->close();
*/
$studentID = $_SESSION['studentid'];
$firstname = $_SESSION['firstname'];
$familyname = $_SESSION['familyname'];
$form = $_SESSION['form'];
$time = date('d/m/Y H:i:s');
shell_exec("echo 'Name: $firstname $familyname \nForm Class: $form \nTime Signed In: $time \nJustification: $justification \nsuccessfully signed in, no further action required \n\n\n' | lpr");
session_unset();
echo "<script>window.location.href='../finished.html'</script>";
?>