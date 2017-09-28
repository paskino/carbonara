<?php

// @apache_setenv('no-gzip', 1);
// @ini_set('max_execution_time', 0);
// @ini_set('zlib.output_compression', 0);
// @ini_set('implicit_flush', 1);
@ini_set('display_errors','On');

error_reporting(E_ALL);

include '.conn';

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
		}elseif($action == "breakdown"){
			
			$items = array("soffritto" , "pepe", "peperoncino" ,
								"padella" , 
								"chiara" , 
								"persone_uova" , 
								"kindaformaggio" , 
								"formaggio" );
			
			for ($i = 0 ; $i<count($items); $i++){
			$breakdown_msg = array();
			$el = $items[$i];
			$stmt = $link->prepare("SELECT ".$el.", COUNT(*) FROM
			feedback GROUP BY ".$el);
			//$stmt = $link->prepare("SELECT * FROM feedback");
			if ($stmt != false) {
				$result = $stmt->execute();
					
				if ($result) {
					// this line must be used before bind result
					// https://bugs.php.net/bug.php?id=51386
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt,
					$chiara, $count);
					while (mysqli_stmt_fetch($stmt)) {
						$chiara = $chiara == NULL ? "non_sa" : $chiara;
						array_push($breakdown_msg, 
							array(
								$chiara => $count
							)
						);
					}
				
				}
				$stmt->close();
			}
			array_push($msg, array($el => $breakdown_msg));
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
