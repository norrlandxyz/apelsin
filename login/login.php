<?PHP
include_once '../config.php';
?>
<head>
	<title>Login</title>
</head>
<html>
	<body>
<?PHP
	if(isset($_POST['user']) && isset($_POST['passwd'])) {
		$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
		$username = $_POST['user'];
		$passwd = $_POST['passwd'];
		$stmt = $conn->prepare("SELECT passwd FROM users WHERE username = ? LIMIT 1");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$db_hash_raw = $stmt->get_result();

		//error handling
		//print_r("Error: ".$stmt->error);

		if ($db_hash_raw->num_rows != 1) {
			echo "<h2>Wrong Login! (row error)</h1>";
			header("Refresh:5; url=index.php");
		}
		else {
			while ($row = $db_hash_raw->fetch_assoc()) {
				$db_hash = $row['passwd'];
			}
			if (password_verify($passwd, $db_hash)) {
				session_start();
				$_SESSION['user'] = $username;
				echo "<h2>Login successful!</h1>";
				//Gets user permissions
				$stmt = $conn->prepare("SELECT perm FROM users WHERE username = ? LIMIT 1");
				$stmt->bind_param("s", $username);
				$stmt->execute();
				$db_perm_raw = $stmt->get_result();
				//reads rows
				while ($row = $db_perm_raw->fetch_assoc()) {
					//sets perm in session
					$db_perm = $row['perm'];
				}
				$_SESSION['perm'] = $db_perm;
				header("Refresh:2; url=../user");
			}
			else {
				echo "<h2>Wrong Login! (hash error)</h1>";
				header("Refresh:2; url=index.php");
			}
		}
		$stmt->close();
		$conn->close();
		session_write_close();
	} else {
		echo "<h2>Wrong Login!</h1>";
		header("Refresh:2; url=index.php");
	}
?>
		<main>
	
		</main>
	</body>
</html>






