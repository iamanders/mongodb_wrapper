<?php
	include("db_mongo.php");
	
	//Connect
	$m = new db_mongo();
	$m->connect();
	$m->set_db('my_database');
	
	//Insert some stuff
	$m->insert('users', array('name' => 'Bart Simpson', 'username' => 'bart'));
	$m->insert('users', array('name' => 'Lisa Simpson', 'username' => 'lisa'));
	$m->insert('users', array('name' => 'Maggie Simpson', 'username' => 'maggie'));
	
	//Query
	$m->query('users', array()); //Get all
	$m->query_one('users', array('username' => 'lisa')); //Get lisa by username
	
?>