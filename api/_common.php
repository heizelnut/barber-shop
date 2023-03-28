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

?>
