<?php
require_once("db_config.php");
$wsfunction = $getpatameter['wsfunction'];
$wsusername = $getpatameter['wsusername'];
$wspassword = $getpatameter['wspassword'];
$status = 0;
$api_result="";
$message = "";
if($wsfunction == "login" && $wspassword != "" && $wsusername != ""){
  $login_response = loginuser($wsusername,$wspassword);
  $status = $login_response->status;
  $message = $login_response->message;
  $api_result->token=$login_response->token;
} else {
	$message = "Please enter credentials.";
}
$response = new stdClass();
$response->status = intval($status);
$response->message = ucfirst($message);
$response->api_result = $api_result;
echo json_encode($response);
?>