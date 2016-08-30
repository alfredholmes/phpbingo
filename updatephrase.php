<?php 

	include ('sql/validate_login.php');

	$valid_user = false;
	$username = "";
	$password = "";
 	if(isset($_COOKIE['username']))
	{
		$username = $_COOKIE['username'];
		$password = $_COOKIE['password'];

		if(validate($username, $password, $database))
		{
			$valid_user = true;
		}
	}


	if(!$valid_user)
	{
		header('location: index.php');
		die;
	}

	$phrases = array();
	$id;
	if(!isset($_POST['id']))
	{
		header('location: index.php');
		die;
	}else{
		$id = $_POST['id']; 
	}

	$sql = "SELECT stage FROM game WHERE id=$id";
	
	if($database->query($sql)->fetch_assoc()['stage'] != 0)
	{
		header('location: index.php');
		die;
	}

	

	foreach ($_POST as $key => $value) 
	{
		if(strpos($key, 'phrase') !== FALSE)
		{
			if(!empty($value)) $phrases[] = mysqli_real_escape_string($database, $value);
			
		}
	}

	$sql = "DELETE FROM phrases WHERE gameid=$id";

	if(!$database->query($sql))
	{
		echo $database->error;
		die;
	}

	foreach ($phrases as $phrase) 
	{
		$sql = "INSERT INTO phrases (gameid, phrase) VALUES ($id, '$phrase')";
		if(!$database->query($sql))
		{
			echo $database->error;
			die;
		}
	}
	header("location: edit.php?id=$id");
	die;


?>