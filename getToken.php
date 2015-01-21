<?php
mb_http_input("utf-8");
mb_http_output("utf-8");
date_default_timezone_set("PRC");
error_reporting(E_ERROR|E_WARNING);
session_start();

function httpGet($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	
	$res = curl_exec($curl);
	curl_close($curl);
	
	return $res;
}

$appid = "wx48c6d809e977e8fb";
$secret = "96d566201fb67ce017fc01c5f5d5e72f";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$thisurl = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$type = "snsapi_base"; //snsapi_base, snsapi_userinfo

if (!isset($_GET['code'])){
	header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$thisurl&response_type=code&scope=$type&state=STATE1#wechat_redirect");
}else{
	$code = $_GET['code'];
	
	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
	$resOpenID = httpGet($url);
	$_SESSION['access']=$resOpenID;
	$resOpenID = json_decode(resOpenID);
	$access_token = $resOpenID->access_token;
	$openid = $resOpenID->openid;
	$_SESSION['token']=$openid;
	$_SESSION['openid']=$openid;
	
	if($type=="snsapi_userinfo"){
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
		$resUserInfo = httpGet($url);
		$_SESSION['userinfo']=$resUserInfo;
		$resUserInfo = json_decode( $resUserInfo );
		
		$sex = $resUserInfo->sex;
		$province = $resUserInfo->province;
		$city = $resUserInfo->city;
		$country = $resUserInfo->country;
		$headimgurl = $resUserInfo->headimgurl;
	}
	header("Location getpay.php");
}
?>
