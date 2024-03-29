<?php
/*
 * @return array A JSON-encoded array (optional: error code, comments)
 */

// Enter your app information below
$app_secret = 'YOUR_APP_SECRET';

// Prepare the return data array
$data = array('content' => array());

// Parse the signed_request to verify it's from f8d
$request = parse_signed_request($_REQUEST['signed_request'], $app_secret);

if ($request == null) {
	// Handle an unauthenticated request here
	die(make_error_report('unauthenticated'));	
}

// Grab the payload
$payload = $request['credits'];

// Retrieve all params passed in
$func = $_REQUEST['method'];

if ($func == 'payments_gamecash_completed') {
	$payload = json_decode(stripcslashes($payload),true);
	// Grab the order status
	$status = $payload['status'];
	// Write your apps logic here for validating and recording a
	// purchase here.
	$success = true;
	if($success){
		$data['content']['note'] = "we have save {$payload['makeup']} gamecash here";
		
		// Generally you will want to move states from `placed` -> `settled`
		// here, then grant the purchasing user's in-game item to them.
		if ($status == 'placed') {
			$next_state = 'settled';
			$data['content']['status'] = $next_state;
		}

		// Compose returning data array_change_key_case
		$orderid = $payload['orderid'];
		$data['content']['orderid'] = $orderid;
	}else{
		//if 
		die(make_error_report('payment failed',501));	
	}


} else if ($func == 'validate_account'){
	$account = json_decode($payload,true);
	$server_id = $account['server_id'];
	$account = $account['user']['uid'];
	if( account_exists($server_id, $account) )
		$data['content']['account_exists']  = 1;	//account exists
	else
		$data['content']['account_exists']  = 0;	//account does not exist
} 

// Required by api_fetch_response()
$data['method'] = $func;

// Send data back
echo json_encode($data);

function account_exists($server_id, $account){
	return true;
}

// You can find the following functions and more details
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
