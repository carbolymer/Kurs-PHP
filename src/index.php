<?php
include 'init.php';
try
{
	init();
	$aThreads = Thread::getAll();
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
<?php if(!is_object(User::$oCurrentUser)):?>
<form action="login.php" method="post">
 Login:<input type="text" name="login" value=""  required="required"/> 
 Hasło:<input type="password" name="password" value=""  required="required"/>
 <input type="submit" value="Zaloguj się" />  | <a href="register.php">rejestracja</a>
</form>
<?php else: ?>
Witaj <b><?php echo User::$oCurrentUser->sName; ?></b> ! | <a href="login.php?logout">Wyloguj się</a>
<?php endif; ?>
<br /><br />
<a href="add.php">Stwórz nowy wątek</a> <br />
<?php if(count($aThreads) > 0):?>
Lista wątków na forum:
<table border="1">
<tr>
 <th>Lp</th>
 <th>Temat</th>
 <th>Autor</th>
 <th>Data</th>
</tr>
<?php foreach($aThreads as $i => $oThread): ?>
<tr>
 <td><?php echo $i+1 ?></td>
 <td><a href="thread.php?id=<?php echo $oThread->iId ?>"><?php echo $oThread->sTitle ?></a><?php 
 if(is_object(User::$oCurrentUser))
	 if(User::$oCurrentUser->bIsAdmin)
	 	echo ' <a href="delete.php?tid='.$oThread->iId.'">[ usuń ]</a>'; 
?></td>
 <td><?php echo $oThread->sAuthorName ?></td>
 <td><?php echo $oThread->sDate ?></td>
</tr>
<?php endforeach?>
</table>
<?php else: ?>
Brak wątków na forum.
<?php endif;?>
</body>
</html>
