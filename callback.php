<?php
// Copyright 2004-Present Facebook. All Rights Reserved.

/**
 * You should reference http://developers.facebook.com/docs/credits/ as you
 * familiarize yourself with callback.php. In particular, read all the steps
 * under "Credits Tutorial" and "Credits Callback".
 *
 * Your application needs the following inputs and outputs
 *
 * @param int order_id
 * @param string status
 * @param string method
 * @param array order_details (JSON-encoded)
 *
 * @return array A JSON-encoded array with order_id, next_state (optional: error code, comments)
 */

// Enter your app information below
$app_secret = 'd83947a3c54d613077535867fab720ff';

// Prepare the return data array
$data = array('content' => array());

// Parse the signed_request to verify it's from Facebook
$request = parse_signed_request($_REQUEST['signed_request'], $app_secret);

if ($request == null) {
	// Handle an unauthenticated request here
	die(make_error_report('unauthenticated'));	
}

// Grab the payload
$payload = $request['credits'];

// Retrieve all params passed in
$func = $_REQUEST['method'];

if ($func == 'payments_completed') {
	$payload = json_decode(stripcslashes($payload),true);
	// Grab the order status
	$status = $payload['status'];
	// Write your apps logic here for validating and recording a
	// purchase here.
	// 
	// Generally you will want to move states from `placed` -> `settled`
	// here, then grant the purchasing user's in-game item to them.
	if ($status == 'placed') {
		$next_state = 'settled';
		$data['content']['status'] = $next_state;
	}

	// Compose returning data array_change_key_case
	$orderid = $payload['orderid'];
	$data['content']['orderid'] = $orderid;

} else if ($func == 'payments_get_gamecash') {
	//some payment method can't save in wgs, so need to save all into game cash
	$credits = json_decode(stripcslashes($payload),true);
	$credit = (int)$credits['credits'];
		//pay with money
		$cash_info = array(
			'rate'		=> 2,
			'gamecash'	=> $credit * 2, 
			'unit'		=> 'money',
			'unit_image'	=> 'http://10.0.2.106/kevyu/api/currency/gold.gif',
		);
	if(!isset($cash_info)){
		die(make_error_report(sprintf ('get ratio failed. content:%s',$payload )));
	}
	$data['content'] = $cash_info;
} 

// Required by api_fetch_response()
$data['method'] = $func;

// Send data back
echo json_encode($data);

// You can find the following functions and more details
// on http://developers.facebook.com/docs/authentication/canvas.
function parse_signed_request($signed_request, $app_secret) {
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	//
	// Decode the data
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);

	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		error_log('Unknown algorithm. Expected HMAC-SHA256');
		return null;
	}

	// Check signature
	$expected_sig = hash_hmac('sha256', $payload, $app_secret, $raw = true);
	if ($sig !== $expected_sig) {
		error_log('Bad Signed JSON signature!');
		return null;
	}
	return $data;
}

function base64_url_decode($input) {
	//return base64_decode(strtr($input, '-_', '+/'));
	return base64_decode($input);
}

function make_error_report($message, $code = 500){
	return json_encode(array(
		'error' => array(
			'code' 	=> $code,
			'msg' 	=> $message
		)
	));
}