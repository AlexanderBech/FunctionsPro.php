<?php
/* =========================================================
* FunctionsPro.php 1.0
* Author: Alexander Bech / www.alexanderbech.com
* http://github.com/AlexanderBech/FunctionsPro.php
* ========================================================== */

/*
*	Print array surrounded by PRE-tag
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function pre($array){
	$output = '<pre style="background:yellow;">';
	$output .= print_r($array, true);
	$output .= '</pre>';
	return $output;
}

/*
*	Print email with javascript protection against spam robots
*	Usage: printEmail(your@email.com, '<optional: user agent string>', 'Email click here', 'Test email', 'This is the body text');
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function printEmail($email, $agent='', $text='', $subject='', $body=''){
	// Email supplied?
	if(!isset($email) || empty($email)) return '(email is missing)';
	// Email seems valid?
	if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) return '(email is not valid)';
	// Email parts
	$email_parts = explode('@', $email);
	// Body
	$body = addslashes($body);
	if(!empty($agent)){
		$body .= '%0A%0A--%0A';
		$body .= 'My browser is: '.addslashes($agent);
	}
	// Output
	$output = '<script language="javascript">'."\n";
	$output .= '<!--'."\n";
	$output .= 'var part1 = "'.$email_parts[0].'";'."\n"; // user
	$output .= 'var part2 = "y='.addslashes($body).'";'."\n"; // body
	$output .= 'var part3 = "ject='.addslashes($subject).'";'."\n"; // subject
	$output .= 'var part4 = "'.$email_parts[1].'";'."\n"; // domain
	$output .= 'var part5 = '.(!empty($text)?'"'.addslashes($text).'"':'part1+\'@\'+part4').';'."\n"; // text
	$output .= 'document.write(\'<a href="mai\'+\'lto:\'+part1+\'@\'+part4+\'?sub\'+part3+\'&bod\'+part2+\'" target="_blank">\');'."\n";
	$output .= 'document.write(part5+\'</a>\');'."\n";
	$output .= '// -->'."\n";
	$output .= '</script>';
	// Return
	return $output;
}

/*
*	Add HTTP to URL if missing
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function addhttp($url) {
	if(!isset($url) || empty($url)) return false;
	if(substr($url, 0, 1) == '/') return $url;
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

/*
*	Remove empty HTML tags from string
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function removeEmptyHtmlTags($string){
	$cleaned = preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $string);
	return $cleaned;
}

/*
*	Strip HTML from string
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function stripHtml($input){
	return preg_replace('/\s{2,}/', ' ', strip_tags($input));
}

/*
*	Generate random string
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function generateRandStr($length=10){
	$randstr = "";
	for($i=0; strlen($randstr)<$length; $i++){
		$randnum = mt_rand(0,61);
		if($randnum>=36){
			$randstr .= chr($randnum+61);
		}
	}
	return $randstr;
}

/*
*	Embed video (Youtube or Vimeo - others can easily be added)
*	Usage: embedVideo('http://www.youtube.com/watch?v=_7Mi77iqMjA');
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function embedVideo($url="", $width=500, $autoplay=FALSE, $loop=FALSE){

	if(empty($url)) return "Video url is missing";

	// Size
	$ratio = 0.5625;
	$height = $width * $ratio;

	// Random ID
	$iframe_id = 'video'.generateRandStr();

	if(strpos($url, 'youtube') !== FALSE){
		// YOUTUBE
		$parsed_url = parse_url($url, PHP_URL_QUERY);
		parse_str($parsed_url, $parsed_string);
		$video_id = $parsed_string['v'];
		$embed_html = '<iframe id="'.$iframe_id.'" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/*|VIDEOID|*?rel=0" frameborder="0" allowfullscreen></iframe>';
		return str_replace('*|VIDEOID|*', $video_id, $embed_html);

	} else if(strpos($url, 'vimeo') !== FALSE){
		// VIMEO
		$parsed_url = parse_url($url);
		$video_id = str_replace('/', '', $parsed_url['path']);
		$embed_html = '<iframe id="'.$iframe_id.'" src="http://player.vimeo.com/video/*|VIDEOID|*?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff'.($autoplay?'&amp;autoplay=1':'').($loop?'&amp;loop=1':'').'&amp;api=1&amp;player_id='.$iframe_id.'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		return str_replace('*|VIDEOID|*', $video_id, $embed_html);

	}

	// Not supported
	return 'Video url not supported.';
}

/*
*	Get title from URL
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function getTitle($Url){

	// create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $Url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    // $output contains the output string
    $str = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

	//$str = file_get_contents($Url);
    if(strlen($str)>0){
        preg_match("/\<title\>(.*)\<\/title\>/",$str,$title);
        return $title[1];
    } else {
    	return 'Unable to get title from link';
    }
}

/*
*	Get city from IP
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function getLocation($ip){
	// url
	$url = 'http://api.hostip.info/get_json.php?ip='.$ip.'&position=false';
	// create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $url);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    // $output contains the output string
    $str = curl_exec($ch);
    // close curl resource to free up system resources
    curl_close($ch);
    $location = json_decode($str, true);
    if(!empty($location) && is_array($location)){
    	// Check city
    	if(!isset($location['city']) || empty($location['city']) || stristr($location['city'], 'unknown')){
    		unset($location['city']);
    	}
    	// Check lat/lng
    	if(!is_numeric($location['lat']) || !is_numeric($location['lng'])){
    		unset($location['lat']);
    		unset($location['lng']);
    	}

    	return $location;
    }
    return false;
}

/*
*	Get URL friendly string (machine name)
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function slugify($text){
	// Replace Æ
	$text = preg_replace('/æ/', 'ae', $text);
	$text = preg_replace('/Æ/', 'ae', $text);
	// Replace Ø
	$text = preg_replace('/ø/', 'oe', $text);
	$text = preg_replace('/Ø/', 'oe', $text);
	// Replace Å
	$text = preg_replace('/å/', 'aa', $text);
	$text = preg_replace('/Å/', 'aa', $text);
	// Replace É
	$text = preg_replace('/é/', 'e', $text);
	$text = preg_replace('/É/', 'e', $text);
	// replace all non letters or digits with -
	$text = preg_replace('/[^&\w]+/', '-', $text);
	// trim and lowercase
	$text = strtolower(trim(htmlspecialchars($text), '-'));
	return $text;
}

/*
*	Get a files change date and return as no cache
*	Usage: version('js/scripts.js');
*	Output: 'js/scripts.js?1107722733'
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function version($file){
	return $file.'?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/'.$file);
}

/*
*	Get a users age
*	Usage: getAge(<birthday timestamp>, <current time timestamp>);
*
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function getAge($b, $t){
	$age = ($b < 0) ? ( $t + ($b * -1) ) : $t - $b;
	$year = 60 * 60 * 24 * 365;
	$ageYears = $age / $year;
	return floor($ageYears);
}

/*
*	Get the size of a file
*	
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function getFileSize($path){
	$size = filesize($path);
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$power = $size > 0 ? floor(log($size, 1024)) : 0;
	return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

/*
*	Get time ago or until, a specific time
*	
*	Author: Alexander Bech / www.alexanderbech.com
*	http://github.com/AlexanderBech/
*/
function time_since($current_time=NULL, $other_time=NULL){

	if(empty($current_time) || empty($other_time)) return false;
	
	$time_difference = $current_time - $other_time;

	// If time is in the future
	$ago = " ago";
	$now = "Just now";
	if($time_difference < 0){
		$time_difference = abs($time_difference);
		$ago = "";
		$now = "in a few seconds";
	}

	$seconds = $time_difference ; 
	$minutes = round($time_difference / 60 );
	$hours = round($time_difference / 3600 ); 
	$days = round($time_difference / 86400 ); 
	$weeks = round($time_difference / 604800 ); 
	$months = round($time_difference / 2419200 ); 
	$years = round($time_difference / 29030400 );

	// Seconds
	if($seconds <= 60){
		return $now;
		/*return "$seconds seconds ago";*/
	} else if($minutes <=60){
		//Minutes
		if($minutes <= 5){
		/*if($minutes==1){*/
			return $now;
			/*return "1 minute ago";*/
		} else {
			return "$minutes minutes".$ago; 
	   }
	} else if($hours <=24){
		//Hours
		if($hours==1){
			return "1 hour".$ago;
		} else {
			return "$hours hours".$ago;
		}
	} else if($days <= 7){
		//Days
		if($days==1){
			return "1 day".$ago;
		} else {
			return "$days days".$ago;
	   }
	} else if($weeks < 4){
		//Weeks
		if($weeks==1){
			return "1 week".$ago;
		} else {
			return "$weeks weeks".$ago;
		}
	} else if($months < 12){
		//Months
		if($months==1){
			return "1 month".$ago;
		} else {
			return "$months months".$ago;
		}
	} else {
		//Years
		if($years==1){
			return "1 year".$ago;
		} else {
			return "$years years".$ago;
		}
	}
}

?>