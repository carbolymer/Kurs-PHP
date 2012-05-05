<?php
include 'init.php';
try
{
	init();
	$sError = '';
	if(is_object(User::$oCurrentUser))
	{
		if(!empty($_POST))
		{
			if(strlen(trim($_POST['title'])) < 3 || strlen(trim($_POST['title'])) > 200 )
				$sError .= 'Temat wątku musi mieć od 3 do 200 znaków<br />';
			if(strlen(trim($_POST['content'])) < 3 || strlen(trim($_POST['content'])) > 10000)
				$sError .= 'Treść wątku musi mieć conajmniej 3 znaki. <br />';
			if(strlen($sError) == 0)
			{
				$oThread = new Thread;
				$oThread->sTitle = htmlspecialchars(trim($_POST['title']));
				$oThread->sContent = htmlspecialchars(trim($_POST['content']));
				$oThread->iAuthorId = User::$oCurrentUser->iId;
				$oThread->save();
				header('Location: thread.php?id='.$oThread->iId);
			}
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
 <title>Forum - dodawanie nowego wątku</title>
 <meta charset="UTF-8" />
</head>
<body>
<a href="/">&lt; &lt; Powrót</a> <br />
<?php if(is_object(User::$oCurrentUser)):?>
<form action="add.php" method="post">
Temat: <br /><input type="text" name="title" value="" required="required"/> <br />
Treść: <br /><textarea name="content" rows="10" cols="50" required="required"></textarea>
<br /><b><?php echo $sError ?></b>
<input type="submit" value="Stwórz nowy wątek" />
</form>
<?php else: ?>
Musisz się zalogować aby mieć dostęp do tej strony!
<?php endif; ?>
</body>
</html>
