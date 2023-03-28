<?php

include '_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$body = file_get_contents('php://input');
	$json = json_decode($body, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return_error(400, "bad request");
		exit;
	}
	if (!isset($json['username']) or
		!isset($json['password']) or
		!isset($json['location'])) {
		return_error(400, "bad request");
		exit;
	}

	$stm = $pdo->prepare("INSERT INTO users (username, password, location, rank) VALUES (:u, :p, :l, :r)");
	$stm->bindparam(':u', $u, PDO::PARAM_STR);
	$stm->bindparam(':p', $p, PDO::PARAM_STR);
	$stm->bindparam(':l', $l, PDO::PARAM_STR);
	$stm->bindparam(':r', $r, PDO::PARAM_INT);

	$u = $json['username'];
	$p = password_hash($json['password'], PASSWORD_DEFAULT);
	$l = $json['location'];
	$r = 2; // hardcoded for now

	if ($stm->execute()) {
		// $_SESSION['user'] = $pdo->lastInsertId();
		// $_SESSION['time'] = time();
		return_json(201, array("description" => "user created"));
	} else {
		return_error(503, "db error");
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	if (!isset($_SESSION['user'])) {
		return_error(403, "forbidden");
		exit;
	}
	$stm = $pdo->prepare("DELETE FROM users WHERE id = :id");
	$stm->bindParam(":id", $_SESSION['user']);
	if ($stm->execute()) {
		if ($stm->rowCount() == 1) {
			return_json(200, array("description" => "user deleted successfully"));
		} else {
			return_error(410, "user already deleted");
		}
	} else {
		return_error(503, "db error");
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (!isset($_SESSION['user'])) {
		return_error(403, "forbidden");
		exit;
	}
	$stm = $pdo->prepare("SELECT id, username, location FROM users WHERE id = :id");
	$stm->bindParam(":id", $_SESSION['user']);
	if ($stm->execute()) {
		if ($stm->rowCount() == 1) {
			$row = $stm->fetch(PDO::FETCH_ASSOC);
			return_json(200, array("id" => $row['id'],
				"username" => $row['username'], "location" => $row['location']));
		} else {
			unset($_SESSION['user']);
			return_error(410, "user deleted");
		}
	} else {
		return_error(503, "db error");
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	if (!isset($_SESSION['user'])) {
		return_error(403, "forbidden");
		exit;
	}
	$body = file_get_contents('php://input');
	$json = json_decode($body, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return_error(400, "bad request");
		exit;
	}
	$stm = $pdo->prepare("SELECT * FROM users WHERE id = :id");
	$stm->bindParam(':id', $_SESSION['user'], PDO::PARAM_STR);
	if (!$stm->execute()) {
		return_error(503, "db error");
		exit;
	}
	if ($stm->rowCount() != 1) {
		unset($_SESSION['user']);
		return_error(410, "user deleted");
		exit;
	}
	$userRow = $stm->fetch(PDO::FETCH_ASSOC);
	$stm = $pdo->prepare("UPDATE users SET username = :u, location = :l, password = :p WHERE id = :id");
	$stm->bindParam(':u', $u, PDO::PARAM_STR);
	$stm->bindParam(':l', $l, PDO::PARAM_STR);
	$stm->bindParam(':p', $p, PDO::PARAM_STR);
	$stm->bindParam(':id', $userRow['id'], PDO::PARAM_STR);
	$u = @$json['username'] ?: $userRow['username'];
	$l = @$json['location'] ?: $userRow['location'];
	if (isset($json['password'])) {
		$p = password_hash($json['password'], PASSWORD_DEFAULT);
	} else {
		$p = $userRow['password'];
	}
	if (!$stm->execute()) {
		return_error(503, "db error");
		exit;
	}
	return_json(200, array("description" => "user updated"));
	exit;
}

return_error(405, "method not allowed");

?>
