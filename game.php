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
	$cards;
	$sql = "SELECT users.id, users.username, game.title FROM playergame, users, game WHERE users.id=playergame.playerid AND playergame.gameid=$gameid AND game.id=$gameid";
	$title;
	if($result = $database->query($sql))
	{
		while($row = $result->fetch_assoc())
		{
			$userid = $row['id'];
			$title = $row['title'];
			

			$sql = "SELECT phrases.phrase, cards.called FROM cards, phrases WHERE playerid=$userid AND cards.gameid=$gameid AND phrases.id=cards.phraseid";
			
			
			if($innerResult = $database->query($sql))
			{
				$card = array();
				while($innerRow = $innerResult->fetch_assoc())
				{
					$card[] = array($innerRow['phrase'], $innerRow['called']);

			
				}
				
				
						$score = 0;
				$vert = $hori = array(true, true, true, true);
				$house = true;
				$diagonal = true;
				for($y=0; $y < 4; $y++)
				{
					for($x=0; $x < 4; $x++)
					{
						if(($x + $y * 4) % 5 != 0)
						{
							if($card[($x + $y * 4) - ($x + $y * 4)/5][1] != 1)
							{
								$vert[$y] = false;
								$hori[$x] = false;
								$house = false;
							}

							if((($x + $y * 4) % 3 == 0) && $card[($x + $y * 4) - ($x + $y * 4)/5][1] != 1) $diagonal = false; 
						}
					}
				}
				


				foreach ($vert as $value) 
				{
					if($value) $score += 30;
				}
				foreach ($hori as $value)
				{
					if($value) $score += 30;
				}
				if($diagonal) $score += 45;
				if($house) $score += 100;
				$cards[$row['username']] = array($card, $score);

			

			}
		//print_r($cards);
		}

	}




?>
<!DOCTYPE html>
<html>
	<head>
		<link rel='stylesheet' href='style/sheet.css' type='text/css'>
		<title>Grandma Bingo 2.0 Overview - <?php echo $title ?></title>
	</head>
	<boby>
	<a href='index.php'>Back</a>
	<h2>Overview of: <?php echo $title ?></h2>
		<?php foreach($cards as $username => $card): ?>

			<h3><?php echo $username; ?></h3>

			<table id='overview'>
				<?php for($y = 0; $y < 4; $y++): ?>
					<tr>
						<?php for($x = 0; $x < 4; $x ++): ?>
						
							<?php if(($x + $y * 4) % 5 != 0): ?>
								<td <?php if($card[0][($x + $y * 4) - (($x + $y * 4) / 5)][1]) echo "class='called'"; else echo "class='not_called'"; ?>>
								
								
								<?php echo $card[0][($x + $y * 4) - (($x + $y * 4) / 5)][0]; ?>
								
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

			<h4>Score: <?php echo $card[1] ?></h4>
		<?php endforeach; ?>
	</boby>
</html>