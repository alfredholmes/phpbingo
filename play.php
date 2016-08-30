<?php 
	include ('sql/validate_login.php');


	if(isset($_COOKIE['username']) && isset($_COOKIE['password']))
	{
		if(!(validate($_COOKIE['username'], $_COOKIE['password'], $database)))
		{
			header('location: index.php');
			die;
		}else{

		}

	}else{
		header('location: index.php');
		die;
	}
	$user = $_COOKIE['username'];
	$userid;
	$sql = "SELECT id, username FROM users";
	$users;
	if($result = $database->query($sql))
	{
		while($row = $result->fetch_assoc())
		{
			$users[] = $row['username'];
			if($row['username']==$user)
			{
				$userid = $row['id'];
			}
		}
	}
	
	$gameid = $_GET['id'];
	$sql = "SELECT phrases.phrase, cards.called, game.title FROM cards, phrases, game WHERE playerid=$userid AND cards.gameid=$gameid AND phrases.id=cards.phraseid AND game.id=$gameid";
	$title;
	$card = array();
	if($result = $database->query($sql))
	{

		while($row = $result->fetch_assoc())
		{
			
			$card[] = array($row['phrase'], $row['called']);
			$title = $row['title'];
		}
	}


	if(isset($_GET['click']))
	{
		$position = $_GET['click'];
		$sql = "SELECT called from cards WHERE position=$position AND  playerid=$userid AND gameid=$gameid";
		
		$called;

		if($database->query($sql)->fetch_assoc()['called'] == 1)
		{
			$called = 0;
		}else{
			$called = 1;
		}

		$sql = "UPDATE cards SET called=$called WHERE position=$position AND  playerid=$userid AND gameid=$gameid";
		if(!$database->query($sql))
		{
			echo $database->error;
		}
		header("location: play.php?id=$gameid");
		die;
	}


	
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel='stylesheet' href='style/sheet.css' type='text/css'>
		<title>Grandma Bingo 2.0 Playing</title>
	</head>
	<body>
	<a href='index.php'>Back</a>
	<h1>Playing Bingo: <?php echo $title ?></h1>
		<table id='full'>
			<?php for($y = 0; $y < 4; $y++): ?>
				<tr>
					<?php for($x = 0; $x < 4; $x++): ?>
						
						<?php if(($x + $y * 4) % 5 != 0): ?>
							<td <?php if($card[($x + $y * 4) - (($x + $y * 4) / 5)][1]) echo "class='called'"; else echo "class='not_called'"; ?>>
							<a href='play.php?id=<?php echo $gameid; ?>&click=<?php echo $x + $y * 4; ?>'>
							
							<?php echo $card[($x + $y * 4) - (($x + $y * 4) / 5)][0]; ?>
							<?php if(!$card[($x + $y * 4) - (($x + $y * 4) / 5)][1]): ?></a> <?php endif; ?>
							</td>						
						<?php else: ?>
							<td class="negative">
								<?php if($x + $y * 4 == 0)
								{
									echo 'B'; 
								}else if($x + $y * 4 == 5)
								{
									echo 'I';
								}else if($x + $y * 4 == 10)
								{
									echo 'N';
								}else{
									echo 'GO';
								}
								?>

							</td>
						<?php endif; ?>

					<?php endfor; ?>
				</tr>
			<?php endfor; ?>
		</table>
	</body>
</html>