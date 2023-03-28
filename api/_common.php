<?php

session_start();

header('Content-Type: application/json');

function return_json($code, $res) {
	http_response_code($code);
	print(json_encode($res));
}

function return_error($code, $msg) {
	$res = array('error' => $msg);
	return_json($code, $res);
}

try {
	$pdo = new PDO("mysql:host=localhost;dbname=barber;", "root", "");
} catch (PDOException $e) {
	echo "Error: cannot connect to DB.";
	echo "<div class=error>" . $e->getMessage() . "</div>";
}

?>
