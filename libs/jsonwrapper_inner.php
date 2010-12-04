<?php

require_once 'JSON/JSON.php';

function json_encode($arg)
{
	global $services_json;
	if (!isset($services_json)) {
		$services_json = new Services_JSON();
	}
	return $services_json->encode($arg);
}

function json_decode($arg, $assoc = false, $depth = 512)
{
	global $services_json;
	if (!isset($services_json)) {
		$services_json = new Services_JSON();
	}
	
	$json = $services_json->decode($arg);
	if( $assoc && is_object($json) ){
		return object_to_array_recursive($json);
	}
	
	return $json;
}

if( !function_exists('object_to_array_recursive') ){
	function object_to_array_recursive($data){
		if (is_object($data)) $data = get_object_vars($data);
		return is_array($data) ? array_map(__FUNCTION__, $data) : $data;
	}
}
?>
