<?PHP
include_once("../config.php");
session_start();
$username = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!isset($username)) {
	echo "not logged in! redirecting in ...";
	header("Refresh:0.5; url=../login");
	die();
}

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
?>

<!DOCTYPE html>
<html>
        <head>
                <title>User</title>
                <link rel="stylesheet" href="../style/index.css">
        </head>
        <body>
                <header>
                        <img class='logo' src="../orenji-logo.png" alt="logo">
                        <div class=title>
                                <h1>Torrent Tracker</h1>
                                <p>User Interface</p>
                                <?PHP
                                if(isset($_SESSION["user"])) {
                                        echo "<p><i> Welcome ".$_SESSION ["user"]."! <a href='../login/logout.php'>[Logout]</a></i></p>";
                                } ?>
                        </div>
                </header>
                <nav>
                        <ul>
                                <li><a href="../">Browse</a></li>
                                <li><a href="../rss.php">RSS</a></li>
                                <li><a href="../user">User</a></li>
                                <li><a href="../login">Login</a></li>
                                <li><a href="../upload">Upload</a></li>
                        </ul>
                </nav>
		</main>
                        <form action="#" class="search">
                                <input type="text" placeholder="Search...">
                                <input id="submit" type="submit" value="Search">
                        </form>
                        </div>
                        <table>
                                <thead>
                                        <tr>
                                                <th>Category</th>
                                                <th>Name</th>
                                                <th>Link</th>
                                                <th>Size</th>
                                                <th>Uploaded</th>
                                                <th>Seed</th>
                                                <th>Leech</th>
                                                <th>Complete</th>
                                        </tr>
                                </thead>
                                <tbody>
                <?PHP
                        //echo torrents
			$stmt = $db->prepare("SELECT * FROM torrents WHERE owner = ?");
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$torrents = $stmt->get_result(); 	
                        while($torrent = $torrents->fetch_assoc()) {
                                echo "</tr>";
                                echo    "<td>".$torrent['tags']."</td>".
                                        "<td>".$torrent['name']."</td>".
                                        "<td><a class='downloadLink' href=upload/content/".$torrent['file_path'].">📁</a> <a class='downloadLink' href='#'>🧲</a></p>";
                                        //checks if file is larger than 1GB, if not MB unit is used
                                        if ($torrent['size'] < 1073741824) {
                                                echo "<td>".round($torrent['size']/1048576, 2)." MB</td>";
                                        } else {
                                                echo "<td>".round($torrent['size']/1073741824, 2)." GB</td>";
                                        }
                                        echo "<td>".$torrent['upload_date']."</td>";
                                $seedersQuery = $db->query("SELECT count(*) FROM peers WHERE infohash='".$torrent['infohash']."' AND seeding=1");       
                                $seederQueryResponse = mysqli_fetch_array($seedersQuery);
                                echo "<td>".$seederQueryResponse[0]."↑</td>";

                                $leeachersQuery = $db->query("SELECT count(*) FROM peers WHERE infohash='".$torrent['infohash']."' AND leeching");
                                $leeachersQueryResponse = mysqli_fetch_array($leeachersQuery);
                                echo "<td>".$leeachersQueryResponse[0]."↓</td>";

                                $completesQuery = $db->query("SELECT count(*) FROM peers WHERE infohash='".$torrent['infohash']."' AND complete");
                                $completesQueryResponse = mysqli_fetch_array($completesQuery);
                                echo "<td>".$completesQueryResponse[0]."✓</td>";
                                echo "</tr>";

                                //Prints out all peers from a torrent
                                /* $peerAddr = $db->query("SELECT * FROM peers WHERE infohash='".$torrent['infohash']."'");
                                while($peer = $peerAddr->fetch_assoc()) {
                                        echo "<p> <b>".$peer["id"].":</b> ".$peer["b32_addr"]."</p>";
                                } */
                        }
                ?>              </tbody>
                        </table>

		<main>
	        <footer>
                        <p><i>Contact with thedonald@mail.i2p<i></p>
                        <p><i>Copyright © Donald .J Trump</i></p>
                </footer>
	</body>
</html>
	
