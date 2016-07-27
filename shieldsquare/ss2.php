<?php

include "ss2_config.php";

class shieldsquareRequest
{
	public $_zpsbd0 = false;
	public $_zpsbd1 = "";
	public $_zpsbd2 = "";
	public $_zpsbd3 = "";
	public $_zpsbd4 = "";
	public $_zpsbd5 = "";
	public $_zpsbd6 = "";
	public $_zpsbd7 = "";
	public $_zpsbd8 = "";
	public $_zpsbd9 = "";
	public $_zpsbda = "";
	public $__uzma  = "";
	public $__uzmb  = 0;
	public $__uzmc  = "";
	public $__uzmd  = 0;
}

class shieldsquareCurlResponseCode
{
	public $error_string = "";
	public $responsecode = 0;
}

class shieldsquareResponse
{
	public $pid    = "";
	public $url    = "";
	public $reason = "";
	public $responsecode;
}

class shieldsquareCodes
{
	public $ALLOW     = 0;
	public $MONITOR   = 1;
	public $CAPTCHA   = 2;
	public $BLOCK     = 3;
	public $FFD       = 4;
	public $ALLOW_EXP = -1;
}

function loadIp($host,$filepath)
{
	$ip = gethostbyname($host);
	$wfile = fopen($filepath, "w");
	fwrite($wfile, $ip);
	fclose($wfile);
	return $ip;
}

function getIp($host)
{
	/*Initialize variables*/
	$result = "";						//cache result (IP)
	$cltime = 0;						//cache loaded time (last loaded time)
	$ttl = 3600;						//ttl for IP validity
	$filepath = "/dev/shm/ss_nr_cache";	//path of the cache file

	$shieldsquare_config_data = new shieldsquare_config();
	$ttl = $shieldsquare_config_data->_domain_ttl;
	$filepath = $shieldsquare_config_data->_domain_cache_file . 'ss_nr_cache';

	if($ttl == -1)
	{
		return $host;
	}
	/*file doesnt exist or not accesible*/
	if(!file_exists($filepath))
	{
		$ip = loadIp($host,$filepath);
	}
	/*file exists*/
	else
	{
		$rfile = fopen($filepath, "r");
		$result = fread($rfile,filesize($filepath));
		fclose($rfile);
		$cltime = filemtime($filepath);
		/*file exists with no content*/
		if(!$result || !$cltime)
		{
			$ip = loadIp($host,$filepath);
		}
		else
		{
			$life=time()-$cltime;
			/*file exists with content but the value has expired*/
			if($life>$ttl)
			{
				$ip = loadIp($host,$filepath);
			}
			/*value has not expired*/
			else
			{
				$ip = $result;
			}
		}
	}
	return $ip;
}
function shieldsquare_ValidateRequest(&$shieldsquare_username, &$shieldsquare_calltype, &$shieldsquare_pid)
{
	$shieldsquare_low  = 10000;
	$shieldsquare_high = 99999;
	$shieldsquare_a    = 1;
	$shieldsquare_b    = 3;
	$shieldsquare_c    = 7;
	$shieldsquare_d    = 1;
	$shieldsquare_e    = 5;
	$shieldsquare_f    = 10;
	$shieldsquare_time = time();

	$shieldsquare_request       = new shieldsquareRequest();
	$shieldsquare_RETURNCODES   = new shieldsquareCodes();
	$shieldsquare_response      = new shieldsquareResponse();
	$shieldsquare_config_data   = new shieldsquare_config();
	$shieldsquare_curl_response = new shieldsquareCurlResponseCode;

	$shieldsquare_service_url   = 'http://' . getIp($shieldsquare_config_data->_ss2_domain) . '/getRequestData';

	// Get Curl version
	$curl_info    = curl_version();
	//For older verisons of CURL, the timeout is set to one second. 
	$curl_timeout = 1;
	$is_curl_ms_timeout = false;
	if (version_compare($curl_info['version'], '7.16.2') >= 0)
	{
		if ($shieldsquare_config_data->_timeout_value > 1000)
		{
			echo "ShieldSquare Timeout can't be greater then 1000 Milli seconds";
			exit;
		}
		$curl_timeout       = $shieldsquare_config_data->_timeout_value;
		$is_curl_ms_timeout = true;
	}
	if (strlen($shieldsquare_pid)== 0)
	{
		$shieldsquare_pid = shieldsquare_generate_pid($shieldsquare_config_data->_sid);
	}

	$shieldsquare_ex_time         = $shieldsquare_time + 3600 * 24 * 365 * 10;
	if (isset($_COOKIE["__uzma"])&&isset($_COOKIE["__uzmb"])&&isset($_COOKIE["__uzmc"])&&isset($_COOKIE["__uzmd"]))
	{
		$shieldsquare_lastaccesstime  = isset($_COOKIE["__uzmd"]) ? $_COOKIE["__uzmd"] : 0;
		$shieldsquare_uzmc            = isset($_COOKIE["__uzmc"]) ? $_COOKIE["__uzmc"] : 0;
		$shieldsquare_uzmc            = substr($shieldsquare_uzmc, $shieldsquare_e, strlen($shieldsquare_uzmc) - $shieldsquare_f);
		$shieldsquare_a               = ((int)$shieldsquare_uzmc - $shieldsquare_c) / $shieldsquare_b + $shieldsquare_d;
		if($shieldsquare_a < 1 || ($shieldsquare_a!=floor($shieldsquare_a)) )
			$shieldsquare_a=1;
		$shieldsquare_uzmc            = (string)mt_rand($shieldsquare_low, $shieldsquare_high) . (string)($shieldsquare_c + $shieldsquare_a * $shieldsquare_b) . (string)mt_rand($shieldsquare_low, $shieldsquare_high);
		$shieldsquare_request->__uzma = $_COOKIE["__uzma"];
		$shieldsquare_request->__uzmb = isset($_COOKIE["__uzmb"]) ? $_COOKIE["__uzmb"] : 0;
		$shieldsquare_request->__uzmc = $shieldsquare_uzmc;
		$shieldsquare_request->__uzmd = $shieldsquare_lastaccesstime;
		setcookie("__uzmc", $shieldsquare_uzmc, $shieldsquare_ex_time, '/', "");
		setcookie("__uzmd", $shieldsquare_time, $shieldsquare_ex_time, '/', "");
	}
	else
	{
		$shieldsquare_uzma            = uniqid('', true);
		$shieldsquare_lastaccesstime  = $shieldsquare_time;
		$shieldsquare_uzmc            = (string) mt_rand($shieldsquare_low, $shieldsquare_high) . (string)($shieldsquare_c + $shieldsquare_a * $shieldsquare_b) . (string)mt_rand($shieldsquare_low, $shieldsquare_high);
		$shieldsquare_request->__uzma = $shieldsquare_uzma;
		$shieldsquare_request->__uzmb = $shieldsquare_time;
		$shieldsquare_request->__uzmc = $shieldsquare_uzmc;
		$shieldsquare_request->__uzmd = $shieldsquare_lastaccesstime;
		setcookie("__uzma", $shieldsquare_uzma, $shieldsquare_ex_time, '/', "");
		setcookie("__uzmb", time(), $shieldsquare_ex_time, '/', "");
		setcookie("__uzmc", $shieldsquare_uzmc, $shieldsquare_ex_time, '/', "");
		setcookie("__uzmd", time(), $shieldsquare_ex_time, '/', "");
	}

	$shieldsquare_request->_zpsbd0 = false;
	if (strcmp($shieldsquare_config_data->_mode, "Active") == 0)
		$shieldsquare_request->_zpsbd0 = true;
	$shieldsquare_request->_zpsbd1 = $shieldsquare_config_data->_sid;
	$shieldsquare_request->_zpsbd2 = $shieldsquare_pid;
	$shieldsquare_request->_zpsbd3 = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

	/*building the absolute URL*/
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$req_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://'.$host.$path;
	$shieldsquare_request->_zpsbd4 = $req_url;
	
	$shieldsquare_request->_zpsbd5 = isset($_COOKIE[$shieldsquare_config_data->_sessid]) ? $_COOKIE[$shieldsquare_config_data->_sessid] : '';
	$shieldsquare_request->_zpsbd6 = isset($_SERVER[$shieldsquare_config_data->_ipaddress]) ? $_SERVER[$shieldsquare_config_data->_ipaddress] : '';
	$shieldsquare_request->_zpsbd7 = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	$shieldsquare_request->_zpsbd8 = $shieldsquare_calltype;
	$shieldsquare_request->_zpsbd9 = $shieldsquare_username;
	$shieldsquare_request->_zpsbda = $shieldsquare_time;
	$shieldsquare_json_obj         = json_encode($shieldsquare_request);
	$shieldsquare_response->pid    = $shieldsquare_pid;
	$shieldsquare_response->url    = $shieldsquare_config_data->_js_url;

	if (strcmp($shieldsquare_config_data->_mode, "Active") == 0 && ($shieldsquare_calltype!=4 && $shieldsquare_calltype!=5))
	{
		$shieldsquare_curl_response = shieldsquare_post_sync($shieldsquare_service_url, $shieldsquare_json_obj, $curl_timeout,$is_curl_ms_timeout);
		if ($shieldsquare_curl_response->responsecode === false)
		{
			$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW_EXP;
			$shieldsquare_response->reason       = $shieldsquare_curl_response->error_string;
			$shieldsquare_response->dynamic_JS   = "var __uzdbm_c = 2+2";
		}
		else
		{
			$shieldsquare_response_from_ss     = json_decode($shieldsquare_curl_response->responsecode);
			$shieldsquare_response->dynamic_JS = $shieldsquare_response_from_ss->dynamic_JS;
			switch (intval($shieldsquare_response_from_ss->ssresp))
			{
				case 0:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW;
					break;
				case 1:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->MONITOR;
					break;
				case 2:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->CAPTCHA;
					break;
				case 3:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->BLOCK;
					break;
				case 4:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->FFD;
					break;
				default:
					$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW_EXP;
					$shieldsquare_response->reason       = $shieldsquare_curl_response->error_string;
					break;
			}
		}
	}
	else
	{
	if ($shieldsquare_config_data->_async_http_post === true)
		{
			$error_code = shieldsquare_post_async($shieldsquare_service_url, $shieldsquare_json_obj);
			if (!$error_code)
				$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW_EXP;
			else
				$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW;
				$shieldsquare_response->dynamic_JS = "var __uzdbm_c = 2+2";
		}
		else
		{
			$shieldsquare_curl_response = shieldsquare_post_sync($shieldsquare_service_url, $shieldsquare_json_obj, $curl_timeout, $is_curl_ms_timeout);
			if ($shieldsquare_curl_response->responsecode === false)
			{
				$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW_EXP;
				$shieldsquare_response->reason       = $shieldsquare_curl_response->error_string;
				$shieldsquare_response->dynamic_JS = "var __uzdbm_c = 2+2";
			}
			else
			{
				$shieldsquare_response->responsecode = $shieldsquare_RETURNCODES->ALLOW;
				$shieldsquare_response_from_ss     = json_decode($shieldsquare_curl_response->responsecode);
				$shieldsquare_response->dynamic_JS = $shieldsquare_response_from_ss->dynamic_JS;
			}
		}
	}
	return $shieldsquare_response;
}

function shieldsquare_post_async($url, $payload)
{
	$cmd = "curl -X POST -H 'Content-Type: application/json' --connect-timeout 1 -m 1";
	$cmd .= " -d '" . urlencode($payload) . "' " . "'" . $url . "'";
	$cmd .= " > /dev/null 2>&1 &";
	exec($cmd, $output, $exit);
	return $exit == 0;
}

function shieldsquare_post_sync($url, $params, $timeout, $is_curl_ms_timeout)
{
	/* Sending the Data to the ShieldSquare Server */
	$shieldsquare_curl_response  = new shieldsquareCurlResponseCode;
	$shieldsquare_curl           = curl_init($url);
	$shieldsquare_curl_post_data = urlencode($params);
	curl_setopt($shieldsquare_curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($shieldsquare_curl, CURLOPT_POST, true);
	curl_setopt($shieldsquare_curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($shieldsquare_curl, CURLOPT_POSTFIELDS, $shieldsquare_curl_post_data);
	curl_setopt($shieldsquare_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($shieldsquare_curl_post_data)));
	if ($is_curl_ms_timeout)
		curl_setopt($shieldsquare_curl, CURLOPT_TIMEOUT_MS, $timeout);
	else
		curl_setopt($shieldsquare_curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($shieldsquare_curl, CURLOPT_NOSIGNAL, 1);
	$shieldsquare_curl_response->responsecode = curl_exec($shieldsquare_curl);
	$shieldsquare_curl_response->error_string = curl_error($shieldsquare_curl);
	curl_close($shieldsquare_curl);
	return $shieldsquare_curl_response;
}

function shieldsquare_generate_pid(&$shieldsquare_sid)
{
	$t = explode(" ", microtime());
	list($p1, $p2, $p3, $p4, $p5) = explode("-", $shieldsquare_sid);
	$sid_min = $num = hexdec($p4);

	return sprintf('%08s-%04x-%04s-%04s-%04x%04x%04x',
			shieldsquare_IP2Hex(),
			$sid_min,
			substr("00000000" . dechex($t[1]), -4),
			substr("0000" . dechex(round($t[0] * 65536)), -4),
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

function shieldsquare_IP2Hex()
{
	$hex = "";
	$shieldsquare_config_data = new shieldsquare_config();
	$ip = $_SERVER[$shieldsquare_config_data->_ipaddress];
	$part = explode('.', $ip);
	for ($i = 0; $i <= count($part)-1; $i++)
		$hex .= substr("0" . dechex($part[$i]), -2);
	return $hex;
}
?>