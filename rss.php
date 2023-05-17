<?php
// DATABASE
include_once("config.php");
$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

//headers
header('Content-Type: application/rss+xml; charset=utf-8');
?>

<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Apelsin Torrent Tracker</title>
	<link><?PHP echo $trackerURL; ?> </link>
        <description>Apelsin Torrent Tracker RSS Feed</description>
        <?php
        // Fetch torrents
        $torrents = $db->query("SELECT * FROM torrents");

        while ($torrent = $torrents->fetch_assoc()) {
            $category = $torrent['tags'];
            $name = $torrent['name'];
            $link = $trackerURL."/upload/content/" . $torrent['file_path'];
            $size = ($torrent['size'] < 1073741824) ? round($torrent['size'] / 1048576, 2) . " MB" : round($torrent['size'] / 1073741824, 2) . " GB";
            $uploadDate = $torrent['upload_date'];
            ?>

            <item>
                <category><?= $category ?></category>
                <title><?= $name ?></title>
                <link><?= $link ?></link>
                <description>
                    Size: <?= $size ?><br>
                    Uploaded: <?= $uploadDate ?><br>
                </description>
            </item>
        <?php
        }
        ?>
    </channel>
</rss>

<?php
$db->close();
?>

