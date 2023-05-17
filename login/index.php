<!DOCTYPE html>
<?PHP
include_once '../config.php';
?>
<html>
	<body>
		<main>
			<form method="post">
				<label for="user">Username</label>
				<input type="text" id="user" name="user" placeholder="yourname">
				<label for="passwd">Password</label>
				<input type="password" id="passwd" name="passwd" placeholder="insert password">
				<input formaction="login.php" type="submit" value="login">
				<input formaction="register.php" type="submit" value="register">
			</form>
		</main>
	</body>
</html>
