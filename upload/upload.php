<?PHP
//import library
require '../vendor/autoload.php';

use Rhilip\Bencode\TorrentFile;
use Rhilip\Bencode\ParseException;

include_once '../config.php';
$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
?>
<head>
	<title>Uploading...</title>
</head>
<html>
	<body>
	<?PHP
	//Checks for login
	session_start();
	if(!isset($_SESSION["user"])) {
		echo "<h1>You are not logged in, upload unsuccesfull</h1>";
		header("Refresh:2; url=../index.php");
		die();
	}


	//Checks for all input
	if(!isset($_POST["name"])  || !isset($_POST["desc"]) || !isset($_POST["tag"])) {
		die("Missing torrent metadata!");
	}
	if(!isset($_FILES["torrentFile"])) {
		die("Missing .torrent file");
	}

	//initialization
	$torrentName = $_POST["name"];
	$torrentDesc = $_POST["desc"];
	$torrentTag = $_POST["tag"];
	$owner = $_SESSION["user"];

	//Get file info. Info is given from filepath since POST can be spoofed
	$filePath = $_FILES["torrentFile"]["tmp_name"];
	$fileSize = filesize($filePath);
	$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
	$fileType = finfo_file($fileInfo, $filePath);

	if ($fileSize === 0) {
		die("The file is empty.");
	}
	if ($fileSize >  3145728) { // 3 mb (1 byte * 1024 * 1024 * 3)
		die("File is too large");
	}

	//Dont think there are other .torrent formats, but nice to have an array just in case :)
	$allowedTypes = [
		'application/x-bittorrent' => 'torrent'
	];
	//file validation, according to allowedTypes
	if(!in_array($fileType, array_keys($allowedTypes))) {
		die("Disallowed file.");
	}

	//saves file in /content
	$suffix = uniqid();
	$filename = urlencode($torrentName).$suffix;
	$extension = $allowedTypes[$fileType];
	$targetDirectory = __DIR__ . "/content";

	$newFilepath = $targetDirectory . "/" . $filename . "." . $extension;

	//will copy file or die trying
	if (!copy($filePath, $newFilepath)) {
		die("Failed to move file.");
	}
	//Deletes temporary file
	unlink($filePath);

	//loads .torrent-file
	$torrentSize = null;
	$infohash = null;
	try {
		$torrent = TorrentFile::load($newFilepath);
		$torrentSize = $torrent->getSize();
		$infohash = urlencode($torrent->getInfoHashV1ForAnnounce());
	} catch (ParseException $e) {
    		echo "Error loading torrent: ".$e;
		die();
	}


	//Adds torrent to database (name desc owner tags size filepath infohash db)
	torrent2DB($torrentName, $torrentDesc, $owner, $torrentTag, $torrentSize, $filename.".".$extension, $infohash, $db);

	echo "File uploaded sucessfully :)";
	header("Refresh:2; url='../index.php'");
	?>
	</body>
</html>


<?PHP

	function torrent2DB($name, $desc, $owner, $tags, $size, $filepath, $infohash, $db) {
		$stmt = $db->prepare("INSERT into torrents (id, name, `desc`, owner, tags, size, file_path, infohash) VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssiss", $name, $desc, $owner, $tags, $size, $filepath, $infohash);
		$stmt->execute();
		$stmt->close();
		$db->close();
	}

?>
