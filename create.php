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

	$process_form = !empty($_POST['title']);

	$title;
	$phrases = array();
	$people = array();

	if($process_form)
	{
		if(!empty('person0') && !empty('phrase0'))
		{
			$title = $_POST['title'];
			foreach ($_POST as $key => $value) 
			{
				if(strpos($key, 'person') !== FALSE)
				{
					
					$people[] = $value;
				}
				if(strpos($key, 'phrase') !== FALSE)
				{
					$phrases[] = $value;
					
				}
			}
		}
	}

	if($process_form)
	{
		$id;
		$sql = "INSERT INTO game (title, stage, owner) VALUES ('$title', 0, $userid)";

		if($database->query($sql) === TRUE)
		{
			$sql = "SELECT id FROM game where title='$title'";
			$id = $database->query($sql)->fetch_assoc()['id'];

			foreach ($phrases as $phrase) 
			{
				$sql = "INSERT INTO phrases (gameid, phrase) VALUES ($id, '$phrase')";
				if(!$database->query($sql) === TRUE)
				{
					echo $database->error;
					break;
				}
			}

			foreach ($people as $person) 
			{
				$sql = "SELECT id FROM users WHERE username = '$person'";
				$personid = $database->query($sql)->fetch_assoc()['id'];

				$sql = "INSERT into playergame VALUES ($personid, $id)";
				if(!$database->query($sql))
				{
					echo $database->error;
				}else{
					header('location: index.php');
				}
			}
		}else{
			echo $database->error;
		}
	}




?>

<!DOCTYPE html>
<html>
	<head>
		<script type='text/javascript'>
			var n=1;
			

			function addfield(div)
			{
				var container = document.getElementById("phrase");
				var line = document.createElement('div');
				var input = document.createElement('input');
				var id = div + n
				line.id = id;
				input.type = "text";
				input.name = id;
				
				var remove = document.createElement('a');
				remove.href = '#';
				
				
				
				remove.appendChild(document.createTextNode(" Remove"));
				

				line.appendChild(input);
				line.appendChild(remove);
				container.appendChild(line);
				line.appendChild(document.createElement("br"));
			
				
				remove.addEventListener("click", function() { removefield(id) });
				n++;
			}

			function removefield(id)
			{
				field = document.getElementById(id);
				console.log(id);	
				field.parentNode.removeChild(field);
			}

			function newuser(div)
			{
				var users = [<?php foreach($users as $person) echo "'$person',";?>];
				var user = '<?php echo $user; ?>';
				select = document.createElement("select");
				select.name = 'person' + n;

				for (var i = 0; i < users.length; i++) 
				{
					option = document.createElement('option');
					option.value = users[i];
					option.appendChild(document.createTextNode(users[i]));
					if(users[i] == user)
					{
						option.selected = 'selected';
					}
					select.appendChild(option);		
				};

				var line = document.createElement('div');
				var id = line.id =  div + n;
				console.log(id);

				remove = document.createElement('a');
				remove.href = '#';

				remove.addEventListener("click", function() { removefield(id)});
				remove.appendChild(document.createTextNode('remove'));

				line.appendChild(select);
				line.appendChild(remove);
				line.appendChild(document.createElement('br'));

				document.getElementById(div).appendChild(line);
				
				n++;
					
			}

			

		</script>
	</head>
	<body>

		<form action='create.php' method='post'>
			<p>Person / Title</p><input type='text' name='title'>
			<p>Phrases</p>
			<input type='text' name='phrase0'>
			<a href='#' id='addphrase' onclick="addfield('phrase')">Add Phrase</a>
			<div id="phrase"></div>
			<br>
			<p>People</p>
			<select name='person0'>
				<?php foreach($users as $person): ?>
					<option value="<?php echo $person; ?>" <?php if($person == $user) echo "selected='selected'" ?>><?php echo $person ?></option>
				<?php endforeach; ?>
			</select>
			<a href='#' id='addperson' onclick="newuser('user')">Add Person</a>
			<div id='user'></div>
			<br>
			<input type='submit' value='Create'>

		</form>
	</body>
</html>