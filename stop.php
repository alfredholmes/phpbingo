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


	$gameid = $_GET['id'];

	$sql = "SELECT users.id, game.owner FROM playergame, users, game  WHERE game.id=$gameid AND gameid=$gameid AND playerid=users.id AND game.owner=users.id";
	if($result = $database->query($sql))
	{
		$valid_user = false;
		while($row = $result->fetch_assoc())
		{
			if($row['id'] == $row['owner']) $valid_user = true;
			
		}

		if(!$valid_user)
		{
			header('location: index.php');
			die;
		}

	}else{
		header('location: index.php');
		die;
	}

	$sql = "DELETE FROM cards WHERE gameid=$gameid";
	if(!$database->query($sql))
	{
		echo $database->error;
		die;
	}else{
		$sql = "UPDATE game SET stage=0 WHERE id=$gameid";
		if(!$database->query($sql))
		{
			echo $database->error;
		}else{
			header('location: index.php');
		}
	}

?>