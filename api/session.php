<?php

include '_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$body = file_get_contents('php://input');
	$json = json_decode($body, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return_error(400, "bad request");
		exit;
	}
	if (!isset($json['username']) or !isset($json['password'])) {
		return_error(400, "bad request");
		exit;
	}

	$stm = $pdo->prepare("SELECT id, password FROM users WHERE username = :username");
	$stm->bindParam(':username', $json['username'], PDO::PARAM_STR);
	if (!$stm->execute()) {
		return_error(503, "db error");
		exit;
	}

	$userRow = $stm->fetch(PDO::FETCH_ASSOC);
	
	$successLogin = false;
	if ($stm->rowCount() == 1) {
		$successLogin = password_verify($json['password'], $userRow['password']);
	}
	if ($successLogin) {
		$_SESSION['user'] = $userRow['id'];
		$_SESSION['time'] = time();
		return_json(200, array("description" => "session created"));
		exit;
	}

	return_error(403, "wrong credentials");
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
		return_json(200, array("user_id" => $_SESSION['user'], "creation_time" => $_SESSION['time']));
	} else {
		return_error(403, "forbidden");
	}
	exit;
}

return_error(405, "method not allowed");

?>
