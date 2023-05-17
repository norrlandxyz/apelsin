<?php
// User plaintext
header('Content-Type: text/plain');

//import bencoding library
require 'vendor/autoload.php';

use Rhilip\Bencode\Bencode;
use Rhilip\Bencode\ParseException;

//initialize database connection
include_once("config.php");
$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

//Bittorrent variables, 
//if $_GET['event'] is set, use that value, otherwise use ''
$event = isset($_GET['event']) ? $_GET['event'] : '';
$info_hash = isset($_GET['info_hash']) ? urlencode($_GET['info_hash']) : '';
$peer_id = isset($_GET['peer_id']) ? $_GET['peer_id'] : '';
$port = isset($_GET['port']) ? $_GET['port'] : 0;
$uploaded = isset($_GET['uploaded']) ? $_GET['uploaded'] : 0;
$downloaded = isset($_GET['downloaded']) ? $_GET['downloaded'] : 0;
$left = isset($_GET['left']) ? $_GET['left'] : 0;
$i2p_b32_addr = $_SERVER["HTTP_X_I2P_DESTB32"];


//DEBUGGING FAKEINFO
//$info_hash = "test";
//$port = "8080";

//add peer to database
if (!empty($i2p_b32_addr) && !empty($port) && !empty($info_hash)) {
	$knownPeer = false;
	//search for peer in database, is it known or not?
	//$peerList = $db->query("SELECT * FROM peers WHERE infohash='".$info_hash."'");
	//query
	$stmt = $db->prepare("SELECT * FROM peers WHERE infohash = :infohash");
	//binds variable
	$stmt->bindParam(':infohash', $info_hash);
	//executes and exits
	$stmt->execute();
	$stmt->close();
	//sets peerlist
	$peerList = $statement->fetchAll();
	if ($peerList !== false) {
		while($peer = $peerList->fetch_assoc()) {
			if ($peer['b32_addr'] == $i2p_b32_addr) {
				$knownPeer = true;
				break;
			}
		}
	}
	if ($knownPeer == false) {
		//peer is unknown, add to database

		//prepare statement && bind
		$stmt = $db->prepare("INSERT INTO peers (id, infohash, b32_addr, port) VALUES (null, ?, ?, ?)");
		$stmt->bind_param("sss", $info_hash, $i2p_b32_addr, $port);

		//execution and closure
		$stmt->execute();
		$stmt->close();
	} else {
		//TODO: use prepared statements for security
	
    		// Refresh the last_update column to current timestamp
   		//$db->query("UPDATE peers SET last_update = CURRENT_TIMESTAMP WHERE infohash = '".$info_hash."' AND b32_addr = '".$i2p_b32_addr."'");
		$stmt = $db->prepare("UPDATE peers SET last_update = CURRENT_TIMESTAMP WHERE infohash = :infohash AND b32_addr = :b32_addr");
		$stmt->bindParam(':infohash', $info_hash); 
		$stmt->bindParam(':b32_addr', $i2p_b32_addr); 
		$stmt->execute();
		$stmt->close();

    		//set offline peers that have been gone for 2 minutes to neither be leeching nor seeding
    		//$db->query("UPDATE peers SET seeding = 0, leeching = 0 WHERE TIMESTAMPDIFF(MINUTE, last_update, CURRENT_TIMESTAMP) > 2");
		$stmt = $db->prepare("UPDATE peers SET seeding = 0, leeching = 0 WHERE TIMESTAMPDIFF(MINUTE, last_update, CURRENT_TIMESTAMP) > :minutes");
		$minutes = 2;
		$stmt->bindParam(':minutes', $minutes);
		$stmt->execute();
		$stmt->close();
	}
}
 
// Handle events
if (!empty($event)) {
	switch ($event) {
		case 'started':
            		//Update peer status based on $left
            		if ($left > 0) {
                		//peer is leeching
                		$stmt = $db->prepare("UPDATE peers SET leeching = 1, seeding = 0 WHERE infohash = :infohash AND b32_addr = :b32_addr");
            		} else {
                		//peer is seeding
                		$stmt = $db->prepare("UPDATE peers SET seeding = 1, leeching = 0 WHERE infohash = :infohash AND b32_addr = :b32_addr");
            		}
            		$stmt->bindParam(':infohash', $info_hash);
            		$stmt->bindParam(':b32_addr', $i2p_b32_addr);
            		$stmt->execute();
            		break;
        	case 'stopped':
        	case 'paused':
            		// Update peer status to no longer seeding/leeching
            		$stmt = $db->prepare("UPDATE peers SET seeding = 0, leeching = 0 WHERE infohash = :infohash AND b32_addr = :b32_addr");
            		$stmt->bindParam(':infohash', $info_hash);
            		$stmt->bindParam(':b32_addr', $i2p_b32_addr);
            		$stmt->execute();
            		break;
        	case 'completed':
            		// Update peer status to indicate completion
            		$stmt = $db->prepare("UPDATE peers SET complete = 1 WHERE infohash = :infohash AND b32_addr = :b32_addr");
            		$stmt->bindParam(':infohash', $info_hash);
            		$stmt->bindParam(':b32_addr', $i2p_b32_addr);
            		$stmt->execute();
            		break;
        	default:
            		// Do nothing for other events
            		break;
	}
}

// Tracker response

//Get all peer addresses. Includes current user's address, not sure it should be ?
//"DISTINCT" means there are no duplicates
//$peerList = $db->query("SELECT DISTINCT b32_addr, port FROM peers WHERE infohash='".$info_hash."'");
$stmt = $db->prepare("SELECT DISTINCT b32_addr, port FROM peers WHERE infohash = :infohash");
$stmt->bindParam(':infohash', $info_hash);
$stmt->execute();
$peerList = $stmt->fetchAll();
$stmt->close();

$peers = array();
if ($peerList !== false) {
	while($db_peer = $peerList->fetch_assoc()) {
		//invtval makes sure port is of type int
		$peers[] = array('ip' => $db_peer['b32_addr'], 'port' => intval($db_peer['port']));
	}
}

//if a torrent hash is set, response will be sent accordinly
if (!empty($info_hash)) {

	// Get peer data from torrent
	$leechQuery = $db->prepare("SELECT count(*) FROM peers WHERE infohash = :infohash AND leeching = 1");
	$leechQuery->bindParam(':infohash', $info_hash);
	$leechQuery->execute();
	$leechQueryResponse = $leechQuery->fetch();
	$leechers = intval($leechQueryResponse[0]);

	$seedQuery = $db->prepare("SELECT count(*) FROM peers WHERE infohash = :infohash AND seeding = 1");
	$seedQuery->bindParam(':infohash', $info_hash);
	$seedQuery->execute();
	$seedQueryResponse = $seedQuery->fetch();
	$seeders = intval($seedQueryResponse[0]);

	//create response
	$response = array(
    		'interval' => 60,
    		'complete' => $seeders,
    		'incomplete' => $leechers,
		'peers' => $peers,
		'min interval' => 120
	);

	//Response to clients
	echo Bencode::encode($response);
}
//disconnect from database
$db->close();
?>
