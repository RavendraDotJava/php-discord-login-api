<?php
include("config.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['code'])){
    echo 'no code';
    exit();
}

$discord_code = $_GET['code'];


$payload = [
    'code'=>$discord_code,
    'client_id'=>'1189442130171678761',
    'client_secret'=>'BW3sRSubv64mVWI41E9f3RolSJTQOVIE',
    'grant_type'=>'authorization_code',
    'redirect_uri'=>SITE_URL.'process-oauth.php',
    'scope'=>'identify+email+guilds',
];

//print_r($payload);

$payload_string = http_build_query($payload);
$discord_token_url = "https://discordapp.com/api/oauth2/token";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);

if(!$result){
    echo curl_error($ch);
}

$result = json_decode($result,true);
$access_token = $result['access_token'];

$discord_users_url = "https://discordapp.com/api/users/@me";
$header = array("Authorization: Bearer $access_token", "Content-Type: application/x-www-form-urlencoded");

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_URL, $discord_users_url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);

$result = json_decode($result, true);


session_start();

$_SESSION['logged_in'] = true;
$_SESSION['userData'] = [
    'name'=>$result['username'],
    'discord_id'=>$result['id'],
    'avatar'=>$result['avatar'],
    'email'=>$result['email'],
];
$checkmail=$db->getRow("select * from registers where emailid='{$result['email']}' ");
//echo json_encode($db->getLastQuery());exit;
             if($checkmail['emailid']!=''){	
			 $userid=$checkmail['id'];
			 $_SESSION['userid']=$userid;
			header("location: my-dashboard.php");exit;
			}else{
				 
//print_r($result);
$aryData=array(	
					'token'     	    	=> 	token('registers'), 
					'fullname'     	   		=>	$result['global_name'],
					'username'     	   		=>	$result['username'],
					'proimg'     	   		=>	$result['avatar'],
					'emailid'     	 		=>	$result['email'],
					'mobileno'  			=>	0,	
					'password'    	  		=>	md5(rand(11111,99999)),
					'status'     	    	=> 	1, 
					'create_at'     	  	=> 	date("Y-m-d H:i:s"), 
											);  
					$flgIn1 = $db->insertAry("registers",$aryData);	
					$userid=$flgIn1;
                    //echo json_encode($db->getLastQuery());exit;
					
					$aryData1=array(	
					'userid'     	   		=>	$flgIn1,
					'regtype'     	   		=>	2,
					'discorduniqueid'     	=>	$result['id'],
											);  
					$flgIn2 = $db->insertAry("registers_detail",$aryData1);	
					$_SESSION['userid']=$userid;
                    //echo json_encode($db->getLastQuery());exit;
					header("location: my-dashboard.php");
                    exit();
					}
