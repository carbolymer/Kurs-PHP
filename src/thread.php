<?php
include 'init.php';
try
{
	init();
	$oThread = null;
	$aPosts = array();
	$sError = '';
	if(isset($_GET['id']))
	{
		$oThread = new Thread($_GET['id']);
		if($oThread->iId == null)
			$oThread = null;
		else
		{
			if(!empty($_POST) && is_object(User::$oCurrentUser))
			{
				if(strlen(trim($_POST['content'])) < 3 || strlen(trim($_POST['content'])) > 10000)
					$sError .= 'Twoja odpowiedź musi mieć conajmniej 3 znaki. <br />';
				if(strlen($sError) == 0)
				{
					$oResponse = new Post;
					$oResponse->iAuthorId = User::$oCurrentUser->iId;
					$oResponse->iThreadId = $oThread->iId;
					$oResponse->sContent = str_replace("\n",'<br />',trim(htmlspecialchars($_POST['content'])));
					$oResponse->save();
				}
			}
			$aPosts = $oThread->getAllPosts();
		}
	}
}

catch(Exception $e)
{
	die('Wystąpił błąd!<br />'.$e->getMessage());
}

?><!DOCTYPE HTML>
<html>
<head>
 <title>Forum</title>
 <meta charset="UTF-8" />
</head>
<body>
<a href="/">&lt; &lt; Powrót</a> <br />
<?php if(is_object($oThread)): ?>
<h1><?php echo $oThread->sTitle; ?></h1>
<dl>
<dt>[ <?php echo $oThread->sDate ?> ] <b><?php echo $oThread->sAuthorName ?></b> napisał: <?php
if(is_object(User::$oCurrentUser))
{
	if(User::$oCurrentUser->iId == $oThread->iAuthorId || User::$oCurrentUser->bIsAdmin)
		echo ' <a href="edit.php?tid='.$oThread->iId.'">[ edytuj ]</a>';
	if(User::$oCurrentUser->bIsAdmin)
		echo ' <a href="delete.php?tid='.$oThread->iId.'">[ usuń ]</a>';
}
?></dt>
<dd><?php echo $oThread->sContent ?></dd>
<?php  foreach($aPosts as $oPost): ?>
<dt>[ <?php echo $oPost->sDate ?> ] <b><?php echo $oPost->sAuthorName ?></b> napisał:<?php
if(is_object(User::$oCurrentUser))
{
	if(User::$oCurrentUser->iId == $oPost->iAuthorId || User::$oCurrentUser->bIsAdmin)
		echo ' <a href="edit.php?pid='.$oPost->iId.'">[ edytuj ]</a> <a href="delete.php?pid='.$oPost->iId.'">[ usuń ]</a>';
}
?></dt>
<dd><?php echo $oPost->sContent ?></dd>
</dl>
<?php endforeach;?>
<br />
<?php if(is_object(User::$oCurrentUser)):?>
<form action="thread.php?id=<?php echo $oThread->iId?>" method="post">
Odpowiedź: <br /><textarea name="content" rows="10" cols="50" required="required"><?php if(!empty($_POST['content']) && strlen($sError) > 0) echo htmlspecialchars($_POST['content']);?></textarea>
<br /><b><?php echo $sError ?></b>
<input type="submit" value="Stwórz nowy wątek" />
</form>
<?php endif;?>
<?php else: ?>
Nie ma takiego wątku.
<?php endif; ?>
</body>
</html>
