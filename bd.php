<?php
error_reporting(0);
header('Access-Control-Allow-Origin:*');
header('Content-type:text/html;charset=utf-8');

if($_GET['word'] and $_GET['site']){
	header('Content-type: application/json'); 
	echo wordkey($_GET['word'],$_GET['site']);
}



function curl($key,$pn=0)
{
		if($pn){
			$pn= "&pn=".(($pn*50)-50);
		}else{
			$pn= "";
			
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

function xzh($site)
{
		
		$url = "https://www.baidu.com/s?ie=utf8&wd=site%3A". $site."&rn=3";
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

		preg_match('/<div class="f13">(.*?)<\/div>/is', $result , $matches);
		$matches=$matches['1']??false;
		if(strpos($matches,'w_100,h_100') !== false){
			preg_match('/w_100,h_100">(.*?)<\/span><\/a>/is', $matches , $matches);
		}else{
			preg_match('/>(.*?)<\/a>/is',$matches, $matches);
		}
		return $matches['1']?? false;
		
}


function wordkey($word,$site)
{
	$bd=curl($word).curl($word,'2');
	$xzh=xzh($site);

	 preg_match_all('/<(.*?) id=\"([0-9]{1,4})\"(.*?)>(.*?)<div class="f13">(.*?)<\/a>/is', $bd , $matches);
	 $lid=$matches[2];
	 $id=array();

	 foreach($matches[5] as $key => $value){

	  if(strpos($value,$xzh) !== false){ 
			$id[]=$lid[$key];
	  }
	  
	 }
	 if(strpos($xzh,$site) !== false){ 
		$xzh="null";
	 }
    if(count($id)==0){
    	$id='前100暂无排名，请继续努力！';
    }
	return json_encode([
	 'word'=>$word,
	 'site'=>$site,
	 'xzh'=>$xzh,
	 'rank'=>$id,
	 ]);
	
	
}
