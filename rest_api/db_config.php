<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");
$getpatameter=json_decode(file_get_contents('php://input',True),true);
	$DB_HOST="localhost";
	$DB_DATABASE="lds_symphony";
	$DB_USERNAME="root";
	$DB_PASSWORD="114920";
	global $CFG;
	$CFG = mysqli_connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
	if(!$CFG){
		$status = 0;
		$message = "Unable to connect to database";
		$response = new stdClass();
		$response->status = $status;
		$response->message = $message;
		$response->apiresult = "";
		echo json_encode($response);
		exit;
	}
	require_once("api_functions.php");
?>