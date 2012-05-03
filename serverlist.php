<?php
	$gamelist = 	array(
		'name'	=> 'game_name',
		'status'=> 1,
		'serverlist'=> array(			
			'server1_id' => array(	//server 1
				'id'	=> 'server1_id',
				'name'	=> 'server1_name',
				'url'	=> 'server1_url',
				'status'=> 1
			),
			'server2_id' => array(	//server 2
				'id'	=> 'server2_id',
				'name'	=> 'server2_name',
				'status'=> 0
			),
			'server3_id' => array(	//server 3
				'id'	=> 'server3_id',
				'name'	=> 'server3_name',
				'status'=> 1
			)

		)
	);


if(isset($gamelist['serverlist']))
	echo json_encode($gamelist);
else
	echo json_encode(array('error'));
