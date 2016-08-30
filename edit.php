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

	$sql = "SELECT stage FROM game WHERE id=$gameid";
	
	if($database->query($sql)->fetch_assoc()['stage'] != 0)
	{
		header('location: index.php');
		die;
	}


	$sql = "SELECT users.username FROM playergame, users WHERE gameid=$gameid AND playerid=users.id";
	if($result = $database->query($sql))
	{
		$valid_user = false;
		while($row = $result->fetch_assoc())
		{
			if($row['username'] == $username) $valid_user = true;
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

	$sql = "SELECT phrase FROM phrases WHERE gameid = $gameid";
	$phrases = array();
	if($result = $database->query($sql))
	{
		while($row = $result->fetch_assoc())
		{
			$phrases[] = $row['phrase'];
		}
	}


?>

<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript">
		var n=<?php echo sizeof($phrases) ?>;
		function removefield(id)
		{
			field = document.getElementById(id);
			field.parentNode.removeChild(field);
		}

		function addfield(div)
		{
			var input = document.createElement('input');
			var name = 'phrase' + n
			input.type = 'text';
			input.name = name;

			remove = document.createElement('a');
			remove.href = '#';
			remove.appendChild(document.createTextNode('Remove'));

			container = document.createElement(div);
			container.appendChild(input);
			container.appendChild(remove);
			container.id = name;

			container.appendChild(document.createElement('br'));

			document.getElementById(div).appendChild(container);

			remove.addEventListener("click", function() { removefield(name)});
			n++;		}

		</script>
	</head>
	<body>
		<form action='updatephrase.php' method='post'>
			<a href='index.php'>Back</a>
			<a href='#' onclick="addfield('additions')">Add Phrase</a>
			<?php foreach ($phrases as $key => $value): ?>
				<div id='phrase<?php echo $key?>'>
				<input type='text' value='<?php echo htmlspecialchars($value, ENT_QUOTES); ?>' name='phrase<?php echo $key?>'>
				<a href='#' onclick="removefield('phrase<?php echo $key ?>')">Remove</a>
				</div>
			<?php endforeach; ?>
				<div id='additions'>
				
				</div>
			<br>
			<input type='hidden' value='<?php echo $gameid?>' name='id'>
			<input type='submit' value='Save'>
		</form>
	</body>
</html>