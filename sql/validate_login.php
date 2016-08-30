<?php
	include('/var/www/html/sql/connect_to_database.php');


	function validate($username, $password, $database)
	{
		$username = $username;
		$databasepassword = $database->query("SELECT * FROM users WHERE username = '$username'")->fetch_assoc()['password'];
		
		if($databasepassword == $password)
		{
			return true;
		}

		return false;
	}


?>