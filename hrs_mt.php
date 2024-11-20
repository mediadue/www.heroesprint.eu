<?php

$cURL =  isset($_GET['curl']) ? 1 : 0;

// release number
define('MT_RELEASE', '1.2');
define('MT_CURL_OK', ($cURL && in_array('curl', get_loaded_extensions())));
define('MT_TIMEOUT', 10);
define('MT_ERR', !empty($_GET['err']));

if (isset($_GET['mtnotrk'])) {
	die();
}

// report errors if err variable is in query string
if (MT_ERR) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
else {
	error_reporting(0);
}

// the deployed script must always be renamed before using it
if (basename(__FILE__) == 'mytracking.php') {
	die('For your safety: you really should change the name of this file');
}

// is it a test call?
if (!empty($_GET['test'])) {
    die("OK: ".MT_RELEASE);
}

// retrieve query string values
$hop = isset($_GET['hop']) ? $_GET['hop'] : '';

if ($hop) {
	$url = base64url_decode(end(explode("/", $hop)));	
	if (parse_url($url, PHP_URL_SCHEME)) {
		process_content(retrieve_content(calculate_url($hop), $url));
		exit;
	}
}

process_content(retrieve_content(calculate_url($hop)));
exit;


// functions

function base64url_decode($data) {
	return base64_decode(strtr($data, '-_', '+/'));
}

function calculate_url($link) {
    $returnurl = '';
    if ($link == '') {
        $returnurl = 'http://trkapi.com/mytrackingok.gif';
    } else if ((preg_match("/.+/", $link))) {
          $src = array('/m/', '/r/', '/l/');
          $rpl = array('', '/', '/');
          $link = str_replace($src, $rpl, $link);
          $returnurl = 'http://trkapi.com/' . $link;  // 2.0 format
    }
    return $returnurl;
}


function retrieve_content($url, $fallback = "/?mtnotrk=1") {
    $response = '';
	$mt_errstr = '';
	$agent = 'MyTracking/'.MT_RELEASE;
	$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
	$port = '';
	if ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') {
		$port = ":".$_SERVER['SERVER_PORT'];
	}	
	$refr = $protocol.$_SERVER['HTTP_HOST'].$port.$_SERVER['SCRIPT_NAME'];
    if ($url != '') {
        if (MT_CURL_OK) {        
  		    $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_REFERER, $refr);
			curl_setopt($ch, CURLOPT_TIMEOUT, MT_TIMEOUT);
  		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  		    if (!$response = curl_exec($ch)) {
				$mt_errstr = curl_errno($ch).' '.curl_error($ch);
			}
  		    curl_close($ch);
  	    }
  	    else {
			$parts = parse_url($url);
			$scheme = isset($parts['scheme']) ? $parts['scheme'] : 'http';
			$host = isset($parts['host']) ? $parts['host'] : 'trkapi.com';
			$port = isset($parts['port']) ? $parts['port'] : 80;
			$path = isset($parts['path']) ? $parts['path'] : '/';
			$query = isset($parts['query']) ? '?'.$parts['query'] : '';
			if ($fp = fsockopen ($host, $port, $errno, $errstr, MT_TIMEOUT)) {
				fputs ($fp, "GET $path$query HTTP/1.0\r\nHost: $host\r\nUser-Agent: $agent\r\nReferer: $refr\r\n\r\n");
				while (!feof($fp)) {
					$response .= fgets ($fp,128);
				}
				fclose ($fp);
			}
			else {
				$response = false;
				$mt_errstr = $errno.' '.$errstr;
			}
  	    }
    }
	if ($response === false) {
		if (MT_ERR) {
			echo $mt_errstr;
		}
		else {
			header("Location: ".$fallback);
  			exit;
		}
	}
	else {
		return $response;
	}
}


function process_content($pagecontent) {
    if ($pagecontent != '') {
  	    list($headers, $body) = explode("\r\n\r\n", $pagecontent, 2);
  	    $headerlines = explode("\r\n", $headers);
  	    foreach ($headerlines as $header) {
          if (preg_match("/^HTTP|Location:|Vary:|Content-Length:|Content-Type:/i", $header)) {
            header($header);
          }
  	    }
  	    echo $body;
    }
}
