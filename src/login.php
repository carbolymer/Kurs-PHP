<?php
include 'init.php';
try
{
	init();
	if(isset($_GET['logout']))
	{
		session_destroy();
		header('Location: index.php');
	}

	if(isset($_POST))
		if($oUser = User::logIn($_POST['login'],$_POST['password']))
		{
			$_SESSION['uid'] = $oUser->iId;
			header('Location: index.php');
		}			
}

catch(Exception $e)
{
	die('Wystapil blad!<br />'.$e->getMessage());
}
?><!DOCTYPE HTML>
<html>
<head>
 <title>Forum</title>
 <meta charset="UTF-8" />
</head>
<body>
<a href="/">&lt; &lt; Powrót</a> <br />
Niepoprawny login lub hasło!
</body>
</html>
