<?php

 	function connect($username, $password, $database)
 	{

 		return new mysqli('localhost', $username, $password, $database);
 	}

 	$database = connect('root', 'raspberry', 'bingo');

 	


?>