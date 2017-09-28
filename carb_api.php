<?php

// @apache_setenv('no-gzip', 1);
// @ini_set('max_execution_time', 0);
// @ini_set('zlib.output_compression', 0);
// @ini_set('implicit_flush', 1);
@ini_set('display_errors','On');

include("config.php");

error_reporting(E_ALL);
$host = $CONFIG["database"]["host"];
$user = $CONFIG["database"]["user"];
$db = $CONFIG["database"]["name"];
$password = $CONFIG["database"]["pass"];

$link = mysqli_init();
$success = mysqli_real_connect(
		$link,
		$host,
		$user,
		$password,
		$db
);

$link->set_charset("utf8");

$msg = array();
// Check connectionSELECT ID FROM wp_users
if (!$link) {
	//$msg = "Connection failed: " . mysqli_connect_error();
	array_push($msg, array(
	"msg" => "Connection failed: " . mysqli_connect_error()
	));
} else {
	if( !array_key_exists( 'action', $_GET )) {
		//$msg = "Please specify requested parameter";
		array_push($msg, array(
		"msg" => "Please specify requested parameter"
				));
	} else {
		
		$action = $_GET['action'];
		
		if ($action == "get_all"){
			
			$stmt = $link->prepare("SELECT ID , date , identita, soffritto, pepe, peperoncino, padella, chiara, persone_uova, kindaformaggio, formaggio, ricetta FROM feedback");
			//$stmt = $link->prepare("SELECT * FROM feedback");
			if ($stmt != false) {
				$result = $stmt->execute();
					
				if ($result) {
					// this line must be used before bind result
					// https://bugs.php.net/bug.php?id=51386
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $ID , $date_time , $identita, $soffritto, $pepe, $peperoncino, $padella, $chiara, $persone_uova, $kindaformaggio, $formaggio, $ricetta);
					while (mysqli_stmt_fetch($stmt)) {
						array_push($msg, 
							array(
								"ID" => $ID,
								"date_time" => $date_time, 
								"identita" => $identita, 
								"soffritto" => $soffritto, 
								"pepe" => $pepe, 
								"peperoncino" => $peperoncino,
								"padella" =>$padella, 
								"chiara" => $chiara, 
								"persone_uova" => $persone_uova, 
								"kindaformaggio" => $kindaformaggio, 
								"formaggio" => $formaggio, 
								"ricetta" => $ricetta
							)
						);
					}
				
				}
				$stmt->close();
			}
		}
				
	}

}

if (sizeof($msg) == 0) {
	array_push($msg, array(
	"msg" => "noresult"
			));
} 
//header("Content-type: application/json; charset=utf-8");
echo (json_encode($msg));
//echo "<p>".json_last_error();
mysqli_close($link);


?>