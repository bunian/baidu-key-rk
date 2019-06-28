<?php
if($_GET['word'] and $_GET['site'])
{
	header('Content-type: application/json');
	echo wordkey($_GET['word'],$_GET['site']);
}

function curl($key,$pn=0)
{
		$pn= "";
		if($pn){
			$pn= "&pn=".(($pn*50)-50);
		}
		$url = "https://www.baidu.com/s?ie=utf8&wd=". urlencode($key) ."&rn=50".$pn;
		set_time_limit(0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.81 Safari/537.36 SE 2.X MetaSr 1.0");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS,20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		preg_match('/<div id=\"content_left\">(.*?)<div style=\"clear:both;height:0;\"><\/div>/is', $result, $matches);
		return $matches[1];
}
function wordkey($word,$site)
{
	$bd=curl($word).curl($word,'2');

	 preg_match_all('/<(.*?) id=\"([0-9]{1,4})\"(.*?)>(.*?)<div class="f13">(.*?)<\/a>/is', $bd , $matches);
	 
	 $lid=$matches[2];
	 $id=array();
	 foreach($matches[5] as $key => $value){

		  if(strpos($value,$site) !== false){ 
				$id[]=$lid[$key];
		  }
	  
	 }
	return json_encode([
	 'word'=>$word,
	 'site'=>$site,
	 'rank'=>$id,
	 ]);
}
