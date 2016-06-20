<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="shortcut icon" href="images/favicon.ico">
<meta name=”keywords” content=”Aicotoba,診断,カップル,ラブラブ度”>
<meta name=”keywords” content="何気ないメールのやり取りの中からAicotoba&quot;愛言葉&quot;を見つけるアプリ">
<meta name="viewport" content="width=device-width,user-scalable=yes">
<title>friend list</title>
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
	}());
</script>

	<header>
		<div class="header_wrapper">
			<h1>Aicotoba</h1><span>何気ないメールのやり取りの中から"愛言葉"を見つけるアプリ</span>
		</div>
	</header>
	<?php

session_start();

require_once 'common.php';
require_once 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$options = array('-d', '/usr/local/lib/mecab/dic/mecab-ipadic-neologd');
$mecab = new MeCab_Tagger($options);


//セッションに入れておいたさっきの配列
    $access_token = $_SESSION['access_token'];

/*  OAuthトークンとシークレットも使って TwitterOAuth をインスタンス化  */
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

if(isset($_POST['tmes'])){
    $tmes= $_POST['tmes'];
}
//ユーザー情報をGET
//$user = $connection->get("account/verify_credentials");


$messages = $connection->OAuthRequest("https://api.twitter.com/1.1/direct_messages.json","GET",array('count'=>'200'));
//$array = json_decode( $messages , true ) ;
$omsg = json_decode( $messages ) ;
if (isset($tmes)){
  /* ターゲットが決まってる時の処理、診断 */
	if (isset($omsg) && empty($omsf->errors)) {
	foreach ($omsg as $val) {
		$twuname=$val->recipient->name;

		if ($val->sender->screen_name ==$tmes){
				$twfname=$val->sender->name;
				$dtmp=date('Y年m月d日', strtotime($val->created_at));
				$ical[$dtmp]=$ical[$dtmp]+1;
/* mecab */
		$nodes = $mecab->parseToNode($val->text);
		foreach ($nodes as $n){
		    $tmp=$n->getFeature();
		    if (preg_match('/名詞/',$tmp)||preg_match('/感動詞/',$tmp)||
			preg_match('/動詞/',$tmp)||preg_match('/形容詞/',$tmp)){
			$nwords[$n->getSurface()]=$nwords[$n->getSurface()]+1;
		    }
		}
	    }
	}
    } else {
	echo 'つぶやきはありません。';
    }

    $sents = $connection->OAuthRequest("https://api.twitter.com/1.1/direct_messages/sent.json","GET",array('count'=>'200'));
    $imsg = json_decode( $sents ) ;
    $array = json_decode( $sents, true ) ;
    foreach ($imsg as $val) {
	if ($val->recipient->screen_name ==$tmes){
				$dtmp=date('Y年m月d日', strtotime($val->created_at));
				$ocal[$dtmp]=$ocal[$dtmp]+1;

	    $nodes = $mecab->parseToNode($val->text);
	    foreach ($nodes as $n){
		$tmp=$n->getFeature();
		if (preg_match('/名詞/',$tmp)||preg_match('/感動詞/',$tmp)||
		    preg_match('/動詞/',$tmp)||preg_match('/形容詞/',$tmp)){

		    $iwords[$n->getSurface()]=$iwords[$n->getSurface()]+1;
		}
	    }
	}
    }

	$maxin=0; $maxout=0; $i=0;
   foreach ($ical as $key=>$value){
   		$iall=$iall+$value;
		   $i=$i+1;
		if ($value>=$maxin){$maxin=$value;$maxind=$key;}
   }
   $avin=$iall/$i;
	$i=0;
   foreach ($ocal as $key=>$value){
   		$oall=$oall+$value;
		   $i=$i+1;
		if ($value>=$maxout){$maxout=$value;$maxoutd=$key;}
   }
   $avout=$oall/$i;
   $lovedo= $ocal[$maxoutd]/$ical[$maxoutd] *100;

	echo '<main>';
		echo '<div class="eye_catch">';
			echo '<div class="wrapper">';
				echo '<p class="result_title"><span class="aicotoba">' . $twuname . '</span>さんの診断結果</p>';
				echo '<div class="heart">';
					echo '<p>' . $twfname . 'とのAicotoba度は</p>';
					echo '<p><span>' . (int)$lovedo . '%</span><span>です。</span></p>';
				echo '</div>';
				echo '<div class="details">';
					echo '<p><span>一日平均</span><span id="oneday_send">' . (int)$avout . '通</span><span>のメールを送りました。</span></p>';
					echo '<p><span>一日平均</span><span id="oneday_receive">' . (int)$avin . '通</span><span>のメールが届きました。</span></p>';
					echo '<p><span>最もメールを送った日は</span><span id="most_send">' . $maxoutd . '</span><span>です。</span></p>';
					echo '<p><span>最もメールを受け取った日は</span><span id="most_receive">' . $maxind . '</span><span>です。</span></p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="my_aicotoba">';
			echo '<div class="wrapper">';
				echo '<h2><img src="images/leaf01.png">あなたの<span class="aicotoba">Aicotoba</span><img src="images/leaf02.png"></h2>';
				echo '<div class="word">';
				foreach ($iwords as $key =>$value){

					$size=$value*14;
					echo '<p style="position: absolute; top: ' . rand(60,440) . 'px; left: ' . rand(60,1000) . 'px; font-size: ' . $size . 'px; font-family: Angelina,Hannari;">' . $key . '</p>';
				}
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="lovers_aicotoba">';
			echo '<div class="wrapper">';
				echo '<h2><img src="images/leaf01.png"><span>' .$twfname .'</span>の<span class="aicotoba">Aicotoba</span><img src="images/leaf02.png"></h2>';
				echo '<div class="word">';
					foreach ($nwords as $key =>$value){
	   			$size=$value*14;
	   				echo '<p style="position: absolute; top: ' . rand(60,440) . 'px; left: ' . rand(60,1000) . 'px; font-size: ' . $size . 'px; font-family: Angelina,Hannari;">' . $key . '</p>';
					}


				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="message">';
			echo '<div class="wrapper">';
				echo '<p>いかがでしたか？<br>"Aicotoba"はたくさん見つかりましたでしょうか？<br>たくさん見つけた方も、そうでなかった方も、<br>思いやりや優しさを込めてコトバを贈っていきましょう。</p>';
			echo '</div>';
		echo '</div>';
		echo '<div class="share">';
			echo '<div class="wrapper">';
				echo '<h2>ふたりの<span class="aicotoba">Aicotoba</span>を…</h2>';
				echo '<div class="twitter"><a href=""><img src="images/twitter.svg" width="30" height="30"><span>ツイートする</span></a></div>';
				echo '<div class="facebook"><a href=""><img src="images/facebook.svg" width="30" height="30"><span>シェアする</span></a></div>';
			echo '</div>';
		echo '</div>';
	echo '</main>';

}
else{
	echo '<main>';
		echo '<div class="friend_list">';
			echo '<div class="wrapper">';
				echo '<p>Aicotobaを探したいお相手をお選びください。</p>';
				echo '<div class="list">';
					echo '<table>';

					    echo '<form method="POST" action="./result.php">';
					    /* ユーザリストから対象選択 */
					    if (isset($omsg) && empty($omsf->errors)) {
						foreach ($omsg as $val) {
						    $slist[$val->sender->screen_name][0]=$val->sender->name ;
						    $slist[$val->sender->screen_name][1]=$val->sender->profile_image_url_https ;
						}
						foreach ($slist as  $key => $value){

							echo '<tr>';
						    echo '<td><img src="'. $value[1] . '"></td>';
						 	echo '<td>' . $value[0] . '</td>';
					    	echo '<td><input type="radio" name="tmes" value="' . $key . '"></td>';
						  	echo '</tr>';

						}


				echo '</table>';
				echo '</div>';
				echo '<input type="submit" value ="Start" class="btn_start"></form>';
			echo '</div>';
		echo '</div>';
	echo '</main>';
    } else {
    }
}
?>

	<footer>
		<div class="wrapper">
			<small><p>Copyright © Aicokotoba All Rights Reserved.</p></small>
		</div>
	</footer>
</body>
</html>