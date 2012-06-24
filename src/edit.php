<?php
include 'init.php';
try
{
	init();
	$sError = '';
	$bIsAccessDenied = true;
	$iThreadId = null;
	$iPostId = null;
	if(is_object(User::$oCurrentUser))
	{
		if(isset($_GET['tid']))
		{
			$oThread = new Thread($_GET['tid']);
			if($iThreadId = $oThread->iId)
				if($oThread->iAuthorId == User::$oCurrentUser->iId || User::$oCurrentUser->bIsAdmin)
					$bIsAccessDenied = false;
		}
		if(isset($_GET['pid']))
		{
			$oPost = new Post($_GET['pid']);
			if($iPostId = $oPost->iId)
				if($oPost->iAuthorId == User::$oCurrentUser->iId || User::$oCurrentUser->bIsAdmin)
				{
					$bIsAccessDenied = false;
					$iThreadId = $oPost->iThreadId;
				}
		}
		
		if(!empty($_POST) && !$bIsAccessDenied)
		{
			if(isset($_GET['pid']))
			{
				if(strlen(trim($_POST['content'])) < 3 || strlen(trim($_POST['content'])) > 10000)
					$sError .= 'Twoja odpowiedź musi mieć conajmniej 3 znaki. <br />';
				if(strlen($sError) == 0)
				{
					$oResponse = new Post($iPostId);
					$oResponse->sContent = str_replace("\n",'<br />',trim(htmlspecialchars($_POST['content'])));
					var_dump($_GET);
					var_dump("shit");
					$oResponse->save();
				}
			}
			if(isset($_GET['tid']))
			{
				if(strlen(trim($_POST['title'])) < 3 || strlen(trim($_POST['title'])) > 200 )
					$sError .= 'Temat wątku musi mieć od 3 do 200 znaków<br />';
				if(strlen(trim($_POST['content'])) < 3 || strlen(trim($_POST['content'])) > 10000)
					$sError .= 'Treść wątku musi mieć conajmniej 3 znaki. <br />';
				if(strlen($sError) == 0)
				{
					$oThread = new Thread($iThreadId);
					$oThread->sTitle = htmlspecialchars(trim($_POST['title']));
					$oThread->sContent = htmlspecialchars(trim($_POST['content']));
					var_dump("thit");
					$oThread->save();
					
				}
			}
			if(strlen($sError) == 0)
				header('Location: thread.php?id='.$iThreadId);
		}
	}
}
catch(Exception $e)
{
	die('Wystapil blad!<br />'.$e->getMessage());
}
?><!DOCTYPE HTML>
<html>
<head>
 <title>Forum - edycja</title>
 <meta charset="UTF-8" />
</head>
<body>
<a href="thread.php?id=<?php echo $iThreadId; ?>">&lt; &lt; Powrót</a> <br />
<?php if($bIsAccessDenied):?>
Nie masz dostępu do tej strony!
<?php else: ?>

<?php if(!empty($_GET['tid'])):?>
Edycja tematu:
<form action="edit.php?tid=<?php echo $iThreadId; ?>" method="post">
Temat: <br /><input type="text" name="title" value="<?php if(empty($_POST)) echo $oThread->sTitle; else echo $_POST['title']; ?>" required="required"/> <br />
Treść: <br /><textarea name="content" rows="10" cols="50" required="required"><?php if(empty($_POST)) echo $oThread->sContent; else echo $_POST['content']; ?></textarea>
<br /><b><?php echo $sError ?></b>
<input type="submit" value="Zaktualizuj" />
</form>
<?php else: ?>
Edycja postu:
<form action="edit.php?pid=<?php echo $iPostId; ?>" method="post">
Treść: <br /><textarea name="content" rows="10" cols="50" required="required"><?php echo $oPost->sContent; ?></textarea>
<br /><b><?php echo $sError ?></b>
<input type="submit" value="Zaktualizuj" />
</form>
<?php endif;?>


<?php endif; ?>
</body>
</html>