<?php
	include ('sql/validate_login.php');

	$valid_user = false;
	$username = "";
	$password = "";

	$stages = array(0 => 'Picking phrases', 1 => 'Playing');

	if(!empty($_POST['username']) && !empty($_POST['password']))
	{

		$username = $_POST['username'];
		$password = $_POST['password'];


		
		if(validate($username, $password, $database))
		{
			$valid_user = true;
			if(!isset($_COOKIE['username']))
			{
				
				setcookie('username', $username, time() + (86400 * 14), '/');
				setcookie('password', $password, time() + (86400 * 14), '/');
			}
		}
	}else if(isset($_COOKIE['username']))
	{
		$username = $_COOKIE['username'];
		$password = $_COOKIE['password'];

		if(validate($username, $password, $database))
		{
			$valid_user = true;
		}
	}
	$userid;
	if(!empty($username)) $userid = $database->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id']; 

	$games = array();
	$sql = "select game.title, game.stage, game.owner, playergame.gameid FROM game, users, playergame WHERE users.username='$username' AND playergame.playerid=users.id AND playergame.gameid = game.id";
	if($result = $database->query($sql))
	{
		while ($row = $result->fetch_assoc()) 
		{
			$games[$row['title']] = array($row['stage'], $row['owner'], $row['gameid']);
		}
		print_r($result->fetch_assoc());
	}else{
		echo $database->error;
	}


?>


<!DOCTYPE html>
<html>

	<head>
		<title>Grandma Bingo 2.0</title>
		<link rel="stylesheet" type="text/css" href="style/sheet.css">
	</head>

	<body>
		<?php if(!$valid_user): ?>
			<div>
				<h3>Log in</h3>
				<form method='post' action='index.php'>
					<p>Username:</p><input type='text' name='username'><br>
					<p>Password:</p><input type='password' name='password'>
					<input type='submit'>
				</form>
			</div>
		<?php else:?>
			<div>
				<a href="logout.php">Logout</a>
					<h1>Grandma Bingo! v2.0</h1>
					<h3>
						<?php echo $username; ?>'s bingo games
					</h3>
					<table id='home'>
						<tr>
							<th>Person</th>
							<th>Stage</th>
							<th>Action</th>
						</tr>
						<?php foreach ($games as $title => $state): ?>

							<tr>
								<td class='middle'><?php echo $title ?></td><td class='middle'> <?php echo $stages[$state[0]]; ?></td>
									<td class='middle'>
									<?php if($state[0] == 0): ?>
										<a href='<?php echo "edit.php?id=$state[2]"; ?>'>Enter Phrases</a>

										<?php if($state[1]==$userid): ?>
											<a href='start.php?id=<?php echo $state[2]; ?>'>Start Game</a>
										<?php endif; ?>
								
									<?php else: ?>
										<a href='play.php?id=<?php echo $state[2]; ?>'>Play</a>
										<?php if($state[1] == $userid): ?>
											<a href='stop.php?id=<?php echo $state[2]; ?>'>Stop Game</a>
										<?php endif; ?>
										<a href='game.php?id=<?php echo $state[2]; ?>'>Overview</a>
									<?php endif; ?>
									</td>
									<?php if($state[1] == $userid): ?>
										
									<td><a href="delete.php?id='<?php echo $state[2]; ?>'">Delete</a></td>

									<?php endif; ?>
							</tr>


						<?php endforeach; ?>
					</table>

					<a href='create.php'>Create Game</a>
					<a href='rules.php'>Rules</a>
			</div>
		<?php endif; ?> 
	</body>

</html>