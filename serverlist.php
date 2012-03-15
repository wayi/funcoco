<?php

$gamelist = array(
	//game 1
	'game1_id'	=> array(
		'name'	=> 'server1_name',
		'status'=> 1,
		'serverlist'=> array(			
			array(	//server 1
				'id'	=> 'server1_id',
				'name'	=> 'server1_name',
				'url'	=> 'server1_url',
				'status'=> 1
			),
			array(
				//server 2
				'id'	=> 'server2_id',
				'name'	=> 'server2_name',
				'status'=> 0
			),
			array(
				//server 3
				'id'	=> 'server3_id',
				'name'	=> 'server3_name',
				'status'=> 1
			)

		)
	)
);


$game_id = 'game1_id';

if(isset($gamelist[$game_id]) && isset($gamelist[$game_id]['serverlist']))
	echo json_encode($gamelist[$game_id]);
else
	echo json_encode(array('error'));
