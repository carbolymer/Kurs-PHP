<?php
function init()
{
	include 'config.php';
	include 'dao/User.php';
	include 'dao/Post.php';
	include 'dao/Thread.php';
	try
	{
		session_start();
		//session_destroy();
		$oDb = new PDO($sConnectionString, $sUser, $sPassword);
		User::$oConnection = &$oDb;
		Thread::$oConnection = &$oDb;
		Post::$oConnection = &$oDb;
		
		$oUser = null;
		if(isset($_SESSION['uid']))
		{
			User::$oCurrentUser = User::findById($_SESSION['uid']);
			if(!is_object(User::$oCurrentUser))
			{
				session_destroy();
				echo "NISZCZEM KURWA SESJEM";
			}
		}
			
	}
	catch(Exception $e)
	{
		die('Wystąpił błąd połączenia z bazą danych!<br/>'.$e->getMessage());
	}
}
?>
