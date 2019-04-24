<?php
/**
 * Version details
 * 
 * @package    local_lingk
 * @copyright  (C) 2018 Lingk Inc (http://www.lingk.io)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once 'db_config.php';
$title = $_POST['contenttitle'];
$desc = $_POST['contentdescription'];
$email = $_POST['contentemail'];
$data = $_POST['contentdata'];
    if(!empty($title) && !empty($desc) && !empty($email) && !empty($data) ){
        if($content = $objleave->addcontent($title, $desc, $data, $email )){
            $message="Email already added";
        } else {
            $status = 0;
            $message = "Failed to add content";
        }
    } else {
        $status=3;
        $message = 'Something missing...';
    }
/*Return Value*/
$final_response->status = strval($status);
$final_response->message = $message;
$final_response->api_result = $api_result;
echo json_encode($final_response);
    /*Return Value*/
 
?>