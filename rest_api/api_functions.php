<?php
	/*check user is migrated or not */
	function checkuser_available($username_email){
		global $CFG;
		$qry_ismigrated = "select * from users where office_email like '".$username_email."' or username like '".$username_email."'";
		$rs_ismigrated = mysqli_query($CFG, $qry_ismigrated);
		if($rs_ismigrated->num_rows > 0){
			$row_ismigrated = mysqli_fetch_assoc($rs_ismigrated);
			return $row_ismigrated;
		} else {
			return false;
		}
	}
	function checkuser_mail($email){
		global $CFG;
		$qry_isuser = "select * from users where email like '".$email."'";
		$rs_isuser = mysqli_query($CFG, $qry_isuser);
		if($rs_isuser->num_rows > 0){
			$row_isuser = mysqli_fetch_assoc($rs_isuser);
			return $row_isuser;
		} else {
			return false;
		}
	}
	function add_sallary($userid, $oldsallary, $newsallary, $increment = 0){
		global $CFG,$USER;
		$loginid = $USER['id'];
		$currenttime = time();
		$qry_addsalary = "insert into salary(employeeid,lastsalary,increment,currentsalary,createdby,createddate) values($userid, $oldsallary, $increment, $newsallary,$loginid,$currenttime)";
		$rs_addsalary = mysqli_query($CFG, $qry_addsalary);
		return $rs_addsalary;
	}
	function loginuser($username_email,$wspassword){
		global $CFG;
		$status = 0;
		$message = "Invalid credentials";
		if($userdata = checkuser_available($username_email)){
			if($userdata['deleted'] == 1){
				$message = "User is deleted from server";
			} else if ($userdata['active'] == 1) {
				$salt = $userdata['salt'];
				$enc_password = md5( $wspassword);
				$qry_login = "select * from users where password like'$enc_password' and (office_email like '".$username_email."' or username like '".$username_email."')";
				$rs_login = mysqli_query($CFG, $qry_login);
				if($rs_login->num_rows > 0){
					$row_login = mysqli_fetch_assoc($rs_login);
					$userid = $row_login['id'];
					if($sessiontoken = generate_loginsession($userid)){
						$status = 1;
						$message = "login successfull";
						$token = $sessiontoken;
					} else {
						$message = "unable to generate user session, Please try again.";
					}
				} else {
					$message = "Invalid credentials";
				}
			} else {
				$message = "User is not active";
			}
		} else {
			$message = "User not found";
		}
		$response = new stdClass();
		$response->status = $status;
		$response->message = $message;
		$response->token = $token;
		return $response;
	}	
	/*get or generate login session */
	function generate_loginsession($userid){
		global $CFG;
		$qry_loginsession = "select * from user_session where userid = ".$userid;
		$rs_loginsession = mysqli_query($CFG, $qry_loginsession);
		if($rs_loginsession->num_rows > 0){
			$row_loginsession = mysqli_fetch_assoc($rs_loginsession);
			$sessiontoken = $row_loginsession['login_token'];
			return $sessiontoken;
		} else {
			$sessiontoken = create_logintoken($userid);
			$currenttime = time();
			$qry_sessiontoken = "insert into user_session(login_token,userid,is_login,createdby,createddate) values('".$sessiontoken."',".$userid.",1,$userid,$currenttime)";
			$rs_ismigrated = mysqli_query($CFG, $qry_sessiontoken);
			if($rs_ismigrated){
				return $sessiontoken;
			} else {
				return false;
			}
		}
	}
	/*get or create login session */
	function create_logintoken($userid){
		$token = time().random_password(6).$userid;
		$token = md5($token);
		return $token;
	}
	/*custom Password encryption method*/
	function password_crypt( $string, $salt, $action = 'e') {
	    $secret_key = $salt;
	    $secret_iv = $salt;
	 
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	 
	    if( $action == 'e' ) {
	     $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    }
	    else if( $action == 'd' ){
	     $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	    }
	 
	    return $output;
	}
	/*generate random Password of size*/
	function random_password($size) {
	    $final_data = '';
	    $keys_alpha = range('A', 'Z');
	    $keys_numb = range(0, 9);
	    $keys_symb = array("#",".","-",",","!","+","_");

	    for ($i = 0; $i < $size; $i++) {
	        $case = rand(0,2);
	        switch ($case) {
	            case 0:
	                $final_data .= $keys_alpha[array_rand($keys_alpha)];
	                break;
	            case 1:
	                $final_data .= $keys_numb[array_rand($keys_numb)];
	                break;
	            case 2:
	                $final_data .= $keys_symb[array_rand($keys_symb)];
	                break;
	            default:
	                $final_data .= $keys_alpha[array_rand($keys_alpha)];
	                break;
	        }
	    }
	    return $final_data;
	}
        
    function getusersession($sessiontoken){
		global $CFG;
		$qry_getsession = "select * from user_session where login_token like '".$sessiontoken."'";
		$rs_getsession = mysqli_query($CFG, $qry_getsession);
		if($rs_getsession->num_rows > 0){
			$row_getsession = mysqli_fetch_assoc($rs_getsession);
			return $row_getsession;
		} else {
			return false;
		}
	} 
    function getuserdetails($userid){
		global $CFG;
		$qry_getuser = "select * from users where id = ".$userid;
		$rs_getuser = mysqli_query($CFG, $qry_getuser);
		if($rs_getuser->num_rows > 0){
			$row_getuser = mysqli_fetch_assoc($rs_getuser);
			$userdetails = new stdClass();
			$userdetails->id=$row_getuser['id'];
	        $userdetails->username=$row_getuser['username'];
	        $userdetails->empname=$row_getuser['emp_name'];
	        $userdetails->email=$row_getuser['email'];
	        $userdetails->office_email=$row_getuser['office_email'];
        	return $userdetails;
		} else {
        	return false;
		}
    }
    function setusersession($userid){
		global $CFG, $USER;
		$qry_getuser = "select * from users where id = ".$userid;
		$rs_getuser = mysqli_query($CFG, $qry_getuser);
		if($rs_getuser->num_rows > 0){
			$row_getuser = mysqli_fetch_assoc($rs_getuser);
			$USER = $row_getuser;
		} else {
			$USER = false;
		}
    }
    function user_byusername_email($wsusername_email){
		global $CFG;
		$qry_getuser = "select * from users where username like '$wsusername_email' or office_email like '$wsusername_email'";
		$rs_getuser = mysqli_query($CFG, $qry_getuser);
		if($rs_getuser->num_rows > 0){
			$row_getuser = mysqli_fetch_assoc($rs_getuser);
        	return $row_getuser['id'];
		} else {
        	return false;
		}
    }
//     function check_logindevice($userid,$wsdevicetoken,$wsdevicetype){
// 		global $CFG;
//     	$qry_checklogindevice = "select * from user_login_devices where userid = $userid and devicetoken = '$wsdevicetoken' and devicetype = '$wsdevicetype'";
// 		$rs_checklogindevice = mysqli_query($CFG, $qry_checklogindevice);
// 		if($rs_checklogindevice->num_rows > 0){
// 			$row_checklogindevice = mysqli_fetch_assoc($rs_checklogindevice);
// 			$devicedetails = new stdClass();
// 			$devicedetails->id=$row_checklogindevice['id'];
// 			$devicedetails->userid=$row_checklogindevice['userid'];
// 			$devicedetails->devicetoken=$row_checklogindevice['devicetoken'];
// 			$devicedetails->devicetype=$row_checklogindevice['devicetype'];
// 			$devicedetails->is_login=$row_checklogindevice['is_login'];
// 			return $devicedetails;
// 		} else {
//         	return false;
// 		}
//     }
//     function update_logindevice($userid,$wsdevicetoken,$wsdevicetype){
// 		global $CFG;
// 		$currenttime= time();
//     	$qry_logindevice = "update user_login_devices set is_login=1, modifieddate=".$currenttime." where userid = $userid and devicetoken = '$wsdevicetoken' and devicetype = '$wsdevicetype'";
// 		$rs_logindevice = mysqli_query($CFG, $qry_logindevice);
// 		return $rs_logindevice;
//     }
//     function setnew_logindevice($userid,$wsdevicetoken,$wsdevicetype){
// 		global $CFG;
// 		$currenttime= time();
//   		$qry_setlogindevice = "insert into user_login_devices(userid,devicetoken,devicetype,is_login,createddate) values(".$userid.",'".$wsdevicetoken."','".$wsdevicetype."',1,".$currenttime.")";
// 		$rs_setlogindevice = mysqli_query($CFG, $qry_setlogindevice);
// 		return $rs_setlogindevice;
//     }
//     function reset_logindevice($wsdevicetoken,$wsdevicetype){
// 		global $CFG;
// 		$currenttime= time();
//     	$qry_logindevice = "update user_login_devices set is_login=0, modifieddate=".$currenttime." where devicetoken = '$wsdevicetoken' and devicetype = '$wsdevicetype'";
// 		$rs_logindevice = mysqli_query($CFG, $qry_logindevice);
// 		return $rs_logindevice;
//     }
// function datatoarray($data){
// 	$find = array("[","]","\\");
// 	$replace = array("");
// 	$data=str_replace($find, $replace, $data);
// 	return explode(",",$data);
// }

	function only_validatepassword($username_email,$wspassword){
		global $CFG;
		$status = 0;
		$message = "Invalid credentials";
		if($userdata = checkuser_available($username_email)){
			if($userdata['deleted'] == 1){
				$message = "User is deleted from server";
			} else if ($userdata['active'] == 1) {
				$salt = $userdata['salt'];
				$qry_login = "select * from users where password like'$enc_password' and (office_email like '".$username_email."' or username like '".$username_email."')";
				$rs_login = mysqli_query($CFG, $qry_login);
				if($rs_login->num_rows > 0){
					$status = 1;
					$message = "Valid password";
				} else {
					$message = "Invalid password";
				}
			} else {
				$message = "User is not active";
			}
		} else {
			$message = "User not found";
		}
		$response = new stdClass();
		$response->status = $status;
		$response->message = $message;
		return $response;	
	}
	function is_allowedpi($call_fromip){
		if($access = get_externalaccess()){
			return true;
		} else {
			if($allowedip = get_allowedip()){
				if(in_array($call_fromip, $allowedip)){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	function set_invalidaccess($call_fromip, $datacalled){
		global $CFG, $USER;
		$datacalled = json_encode($datacalled);
		$createddate = time();
		$userid = $USER['id'];
		if(empty($userid)){
			$userid = 0;
		}
		$qry_setdata = "insert into user_accessip(ip,acessdata,creatdeby,createddate) values('$call_fromip','$datacalled',$userid, $createddate)";
		$rs_setdata = mysqli_query($CFG, $qry_setdata);
		return $rs_setdata;
	}
	function create_user($employeename, $gender, $dob, $email, $phone, $phone1, $join_date, $designation, $leaveallowed, $leaveforward){
		global $CFG, $USER;
		$userid = $USER['id'];
		if(empty($phone1)){
			$phone1 = $phone;
		}
		if(empty($leaveallowed)){
			$leaveallowed = 0;
		}
		if(empty($leaveforward)){
			$leaveforward = 0;
		}
		$time_dob = strtotime($dob);
		$time_join_date = strtotime($join_date);
		$createddate = time();
		$password = random_password(16);
		$salt = random_password(16);
		$qry_adduser = "insert into users(emp_name,dob,email,phone,phone1,password,salt,gender,joiningdate,designation,createdby,createddate,leaveallowed,leaveforward) values('$employeename', $time_dob,'$email',$phone,$phone1, '$password', '$salt',$gender, $time_join_date, '$designation',$userid, $createddate, $leaveallowed, $leaveforward)";
		$rs_adduser = mysqli_query($CFG, $qry_adduser);
		return $rs_adduser;
	}
	function get_userlist(){
		global $CFG, $USER;
		$userlist = array();
		$userid = $USER['id'];
		$qry_getuser = "select * from users where deleted = 0";
		$rs_getuser = mysqli_query($CFG, $qry_getuser);
		// print($rs_getuser);
		while ($row_getuser = mysqli_fetch_assoc($rs_getuser)) {
			$userdata = array_to_object($row_getuser);
			if($userdata->active){
				$userdata->active = "active";
			} else {
				$userdata->active = "suspended";
			}
			unset($userdata->password);
			unset($userdata->deleted);
			unset($userdata->salt);
			unset($userdata->allowed_outside);
			unset($userdata->createdby);
			unset($userdata->createddate);
			unset($userdata->modifiedby);
			unset($userdata->modifieddate);
			array_push($userlist, $userdata);
		}
		return $userlist;
	}
	function array_to_object($array_data)
	{
		$returndata = $array_data;
		if(is_array($array_data)){
			$returndata = new stdClass();
			foreach ($array_data as $key => $data) {
				if(is_null($data)){
					$data = "";
				}
				$returndata->$key = $data;
			}
		}
		return $returndata;
	}
	function get_allowedip(){
		global $CFG;
		$allowedip = array();
		$qry_allowedip = "select * from ems_setting where setting like'allowed_ip' and active = 1 and deleted = 0";
		$rs_allowedip = mysqli_query($CFG, $qry_allowedip);
		if($rs_allowedip->num_rows > 0){
			while($row_allowedip = mysqli_fetch_assoc($rs_allowedip)){
				array_push($allowedip, $row_allowedip['value']);
			}
		}
		return $allowedip;
	}
	function get_externalaccess(){
		global $CFG;
		$allowedip = array();
		$qry_access = "select * from ems_setting where setting like'external_access' and active = 1 and deleted = 0";
		$rs_access = mysqli_query($CFG, $qry_access);
		if($rs_access->num_rows > 0){
			$row_access = mysqli_fetch_assoc($rs_access);
			return intval($row_access['value']);
		} else {
			return false;
		}
	}
	function send_forgotpasswordlink($userid){
		return true;
	}
	class classLeave 
	{ 
	    // Constructor 
	    $DB = "";
	    $USER = "";
	    $currenttime = 0;
	    public function __construct(){ 
			global $CFG;
	        $this->DB = $CFG;
	        $this->USER = $USER;
	        $this->currenttime = time();
	    } 
		/*
		* Apply Leave
		*/      
		public function applyleave($empid, $date, $leavetype = 2){
			$leave_date = strtotime($date);

			echo $qry_adduser = "insert into leaves(leavetype,userid,leavedate,createddate,createdby) values($leavetype,$empid,$leave_date,$this->currenttime, $this->currenttime)";
			// $rs_adduser = mysqli_query($this->DB, $qry_adduser);
			return $rs_adduser;
		}

	} 
   
$objleave = new classLeave();
?>