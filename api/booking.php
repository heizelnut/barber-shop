<?php

include '_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user'])) {
		return_error(403, "not logged in");
		exit;
	}
    $body = file_get_contents('php://input');
    $json = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return_error(400, "bad request");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
	$json = json_decode($body, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['user'])) {
		return_error(403, "not logged in");
		exit;
	}
    $body = file_get_contents('php://input');
    $json = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return_error(400, "bad request");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user'])) {
		return_error(403, "not logged in");
		exit;
	}
    $body = file_get_contents('php://input');
    $json = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return_error(400, "bad request");
        exit;
    }
}

?>
