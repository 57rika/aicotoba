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
	echo '<dl>';
	foreach ($omsg as $val) {
		if ($val->sender->screen_name ==$tmes){
				$dtmp=date('Y-m-d', strtotime($val->created_at));
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
				$dtmp=date('Y-m-d', strtotime($val->created_at));
				$ocal[$dtmp]=$ocal[$dtmp]+1;

	    $nodes = $mecab->parseToNode($val->text);
	    foreach ($nodes as $n){
		$tmp=$n->getFeature();
		if (preg_match('/名詞/',$tmp)||preg_match('/感動詞/',$tmp)||
		    preg_match('/動詞/',$tmp)||preg_match('/形容詞/',$tmp)){

/*
    echo $n->getSurface() . "<br />";
echo $n->getFeature() . "<br />";
*/

		    $iwords[$n->getSurface()]=$iwords[$n->getSurface()]+1;
		}
	    }
	}
    }

	// print_r($ical);
// print_r($ocal);
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
   echo 'avgin:' . $avin . " avgout:" . $avout .  " maxindate:" . $maxind . " maxoutdate:" . $maxoutd . "<br>";

	echo '<h1>From Partner</h1>';
	foreach ($nwords as $key =>$value){
	    echo $key . ': ' . $value . '<br>';
	}

	echo '<h1>To Partner</h1>';
    foreach ($iwords as $key =>$value){
	echo $key . ': ' . $value . '<br>';
    }
}
else{
    echo '<form method="POST" action="./index.php">';
    /* ユーザリストから対象選択 */
    if (isset($omsg) && empty($omsf->errors)) {
	echo '<dl>';
	foreach ($omsg as $val) { 
	    $slist[$val->sender->screen_name][0]=$val->sender->name ;
	    $slist[$val->sender->screen_name][1]=$val->sender->profile_image_url_https ;
	}
	foreach ($slist as  $key => $value){
	    echo $value[1] . ',' . $value[0] . '<input type="radio" name="tmes" value="' . $key . '"><br>';
	}
	echo '<input type="submit"></form>';
    } else {
    }
}

//print_r( $nwords);
//print_r( $sents);
// print_r($ical);
// print_r($ocal);
 ?>

