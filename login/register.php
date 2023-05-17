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
		//todo: add check for if user already exists
		try {

			$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$username = $_POST['user'];
			$passwd = $_POST['passwd'];

			//$userList = $conn->query("SELECT * FROM users WHERE username='".$username."'");
			$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$userList = $stmt->get_result();

			//checks for already existing username
			$alreadyExist = false;
			if ($userList !== false) {
				while($user = $userList->fetch_assoc()) {
					if ($user["username"] == $username) {
						$alreadyExist = true;
						failedReg("Username already exists");
						break;
					}
				}
				//user did not exist
				if ($alreadyExist == false) {
					addUser($username, $passwd, $conn);
				}
				
			}
			//did not find username
			else {
				addUser($username, $passwd, $conn);
			}
			$stmt->close();

		}
		catch (Exception $e) {
			failedReg("Failed to register user:".$e);
		}
	} else {
		failedReg("No input!");
	}
?>
		<main>
	
		</main>
	</body>
</html>


<?PHP

function addUser($username, $passwd, $conn) {
	//checks if username is fine
	if (preg_match('/[^\w]/', $username) || $username !== strip_tags($username)) {
		failedReg("username is not fine 😡");
	}
	else {
		$stmt = $conn->prepare("INSERT into users (id, username, passwd, perm) VALUES (null, ?, ?, 0)");
		$hash = password_hash($passwd, PASSWORD_DEFAULT);
		$stmt->bind_param("ss", $username, $hash);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		session_write_close();
		echo "<h2>User Registered!</h2>";
		header("Refresh:2; url='../login'");
	}
}

function failedReg($error) {
	echo "<h2>".$error."</h1>";
	//incase something went wrong, user creadentials should not be saved.
	session_start();
	session_unset();
	session_destroy();
	header("Refresh:2; url=index.php");
	die("suicide, just in case");
}

?>



