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
	$phrases = array();
	$sql = "SELECT id FROM phrases WHERE gameid=$gameid";
	if($result = $database->query($sql))
	{
		while($row = $result->fetch_assoc())
		{
			$phrases[] = $row['id'];
		}
	}

	

	$sql = "SELECT playerid FROM playergame WHERE gameid=$gameid";

	if($result = $database->query($sql))
	{
		while($row = $result->fetch_assoc())
		{
			$player = $row['playerid'];
			shuffle($phrases);
			
			for($i = 0; $i < 16; $i++)
			{
				if($i % 5 != 0)
				{
					$phrase = $phrases[$i];
					$sql = "INSERT INTO cards (gameid, playerid, phraseid, position) VALUES ($gameid, $player, $phrase, $i)";
					if(!$database->query($sql))
					{
						echo $database->error;
					}else{
						$sql = "UPDATE game SET stage=1 WHERE id=$gameid";
						if(!$database->query($sql))
						{
							echo $database->error;
						}else{
							header('location: index.php');
						}
					}
				}
			}
		}
	}

?>

