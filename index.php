<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta charset="utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="style.css">
<meta name=”keywords” content=”Aicotoba,診断,カップル,ラブラブ度”>
<meta name=”keywords” content="何気ないメールのやり取りの中からAicotoba【愛言葉】を見つけるアプリ">
<title>Aicotoba</title>
</head>
<body>
<script type="text/javascript">
(function (){
var snow =["images/heart_smaill_gold.png","images/heart_small_pink.png"];
var Zx="-1";
var num=25;
var imgw=34;
var imgh=34;
var spd=70;
var maxOp=0.9;
var miniOp=0.4;

var sx=[], sy=[], sp=[], opa=[];
var len=snow.length, i=0;
var w=window.innerWidth+imgw, h=window.innerHeight+imgh;
for (i=0; i<num; i++){
sx[i]=Math.floor(Math.random()*w);
sy[i]=Math.floor(Math.random()*h);
sp[i]=Math.floor(Math.random()*6)+2;
opa[i]=Math.random()*(maxOp-miniOp)+miniOp;
document.write("<img src="+snow[i%len]+" id='sn"+i+"'>");
}
function moveSnow(){
for (i=0; i<num; i++){
sy[i] += sp[i];
if (sy[i] > h) sy[i] = -imgh;
var ob=document.getElementById("sn"+i).style;
ob.top=-imgh+sy[i]+"px"; ob.left=-imgw+sx[i]+"px";
ob.position="fixed"; ob.zIndex=Zx; ob.opacity=opa[i];
}
setTimeout(moveSnow,spd);
}
moveSnow();
}());</script>

	<header class="intro_header">
		<div class="intro_header_wrapper">
			<h1><img src="images/leaf01.png" class="leaf01">Aicotoba<<img src="images/leaf02.png" class="leaf02"></h1><p>何気ないメールのやり取りの中から"愛言葉"を見つけるアプリ</p>
		</div>
	</header>
	<main class="intro_main">
		<div class="eye_catch">
			<div class="wrapper_intro">
				<p><img src="images/intro_img.png" class="intro_img"></p>
				<div class="intro">
				<p>SNSの普及によって、<br>私たちのコミニュケーションは希薄化したと言われています。<br>
				しかし、人と人との繋がりは、本当に寂しいものになってしまったのでしょうか。<br>
				愛のある温かい言葉を交わしているのではないのでしょうか。<br>
				普段の何気ないメールのやり取りの中から、"Aicotoba"を探してみませんか？</p>
				</div>
				<a href="" class="btn_top">今すぐAicotobaを探してみる</a>
			</div>
	</main>
	<footer>
		<div class="wrapper">
			<small><p>Copyright © Aicokotoba All Rights Reserved.</p></small>
		</div>
	</footer>
</body>
</html>

<?php

session_start();

require_once 'common.php';
require_once 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

//セッションに入れておいたさっきの配列
$access_token = $_SESSION['access_token'];

//OAuthトークンとシークレットも使って TwitterOAuth をインスタンス化
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

//ユーザー情報をGET
$user = $connection->get("account/verify_credentials");
//(ここらへんは、Twitter の API ドキュメントをうまく使ってください)

//$message = $connection->OAuthRequest("https://api.twitter.com/1/direct_messages.xml","GET",array('count' => '2','page' => '1'));

//GETしたユーザー情報をvar_dump
var_dump( $user );

 echo "あなたの名前：".$user->name;
 ?>