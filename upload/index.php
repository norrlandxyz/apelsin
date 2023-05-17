<!DOCTYPE html>
<?PHP
include_once '../config.php';
?>
<html>
	<body>
		<main>
			<?PHP
			session_start();
			if(!isset($_SESSION["user"])) {
				echo "<h1>Needs to be logged in to upload</h1>";
				header("Refresh:2; url=../index.php");
				die();
			}
			?>
			<h1>Upload Torrent</h1>
			<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
				<label for="name">Torrent Name</label>
				<input type="text" id="name" name="name" placeholder="Title of torrent">
				<label for="desc">Torrent Description</label>
				<input type="desc" id="desc" name="desc" placeholder="Tell us about the file!">
				
				<label for="file">File</label>				
				<input type="file" name="torrentFile" id="file" accept=".torrent" placeholder=".torrent file">

				<label for="tag">Pick category</label>
				<select name="tag" id="tag">

					<!-- Future: assign tags from database/config --!>
					<option value="movie">Movie</option>
					<option value="music">Music</option>
					<option value="other">Other</option>
				</select>

				<input formaction="upload.php" type="submit" value="Upload">
			</form>
		</main>
	</body>
</html>
