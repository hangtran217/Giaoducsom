<?php
session_start();
// Change this to your connection info.

include("connection.php");

// $DATABASE_HOST = 'localhost';
// $DATABASE_USER = 'root';
// $DATABASE_PASS = '';
// $DATABASE_NAME = 'db';
// // Try and connect using the info above.
// $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// if ( mysqli_connect_errno() ) {
// 	// If there is an error with the connection, stop the script and display the error.
// 	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
// }

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $conn->prepare('SELECT id_admin, password FROM tbl_admin WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password);
	$stmt->fetch();
	// Account exists, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if ($_POST['password'] === $password) {
		// Verification success! User has logged-in!
		// Khởi tạo sessions, ta biết được user đã đăng nhập, nó hoạt động như cookies nhưng lưu dữ liệu trên server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		header('Location:'.SITEURL.'/admin/index.php');
	} else {
		// Incorrect password
		echo '<script>alert("Incorrect username and/or password!")</script>';
		print 'setTimeout(function(){alert(\'whatever you want\');}, 100)';
	}
} else {
	// Incorrect username
	echo '<script>window.location="index.php"</script>';
	echo '<script>alert("Incorrect username and/or password!")</script>';
}

	$stmt->close();
}
?>