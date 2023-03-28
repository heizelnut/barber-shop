<?php

include '_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$body = file_get_contents('php://input');
	$json = json_decode($body, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return_error(400, "bad request");
		exit;
	}
	if (!isset($json['user']) or !isset($json['password'])) {
		return_error(400, "bad request");
		exit;
	}

	if ($json['user'] == 'barack' and $json['password'] == 'piccina91') {
		$_SESSION['user'] = $json['user'];
		$_SESSION['time'] = time();
		return_json(200, array("description" => "session created"));
	} else {
		return_error(403, "forbidden");
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	if (isset($_SESSION['user'])) {
		unset($_SESSION['user']);
		return_json(200, array("description" => "logged out"));
	} else {
		return_json(201, array("description" => "already logged out"));
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_SESSION['user'])) {
		return_json(200, array("user" => $_SESSION['user'], "creation" => $_SESSION['time']));
	} else {
		return_error(403, "forbidden");
	}
	exit;
}

return_error(405, "method not allowed");

?>
