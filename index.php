<?php
$headers = apache_request_headers();
$headers_native = array();
$body = file_get_contents('php://input');

// change this to your server name and port

$scheme = "http";
$server_name = "localhost";
$server_port = 1323;

// don't change anything below this line

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

$url = $scheme."://".$server_name.$request_uri;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_PORT, $server_port);

$headers['Host'] = $server_name;
$headers['Content-Length'] = strlen($body);

foreach($headers as $key=>$value) {
	$headers_native[] = $key.":"." ".$value;
}

if ($method == 'GET') {
	curl_setopt($ch, CURLOPT_POST, false);
} else if ($method == 'POST') {
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
} else if ($method == 'PUT') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
} else if ($method == 'DELETE') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_native);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT , 60);

$result = curl_exec($ch);

curl_close($ch);

$response = explode("\r\n\r\n", $result, 2);
$header_lines = explode("\r\n", $response[0]);

foreach($header_lines as $line) {
	header($line);
}

if(isset($response[1])) {
	echo $response[1];
}
