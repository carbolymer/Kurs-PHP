<?php
include 'init.php';
try
{
	init();
	if(is_object(User::$oCurrentUser))
	{
		if(isset($_GET['tid']))
		{
			$oThread = new Thread($_GET['tid']);
			if($iThreadId = $oThread->iId)
				if(User::$oCurrentUser->bIsAdmin)
					$oThread->delete();
		}
		if(isset($_GET['pid']))
		{
			$oPost = new Post($_GET['pid']);
			if($iPostId = $oPost->iId)
				if($oPost->iAuthorId == User::$oCurrentUser->iId || User::$oCurrentUser->bIsAdmin)
					$oPost->delete();
		}
	}
	header('Location: index.php');
}

catch(Exception $e)
{
	die('Wystąpił błąd!<br />'.$e->getMessage());
}
?>
