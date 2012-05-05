<?php
include 'init.php';
try
{
	init();
	$sError = '';
	$bRegistrationComplete = false;
	if(!is_object(User::$oCurrentUser))
	{
		if(!empty($_POST))
		{
			if(!preg_match("#^([a-z0-9_-]{3,30})$#si",$_POST['login']))
				$sError .= 'Nieprawidłowy login - może składać się tylko z liter, cyfr oraz znaków "_" oraz \'-\'. Login musi mieć od 3 do 30 znaków.<br/>';
			if(!preg_match("#^([a-z0-9_-\s]{3,30})$#si",trim($_POST['username'])))
				$sError .= 'Nieprawidłowa nazwa użytkownika - może składać się tylko z liter, cyfr, spacji oraz znaków "_" oraz \'-\'. Nazwa użytkownika musi mieć od 3 do 30 znaków.<br/>';
			if(!preg_match("#^([a-z0-9_-]{5,30})$#si",trim($_POST['password'])))
				$sError .= 'Nieprawidłowe hasło - może składać się tylko z liter, cyfr oraz znaków "_" oraz \'-\'. Hasło musi mieć od 5 do 30 znaków.<br/>';
			if($_POST['password'] != $_POST['password2'])
				$sError .= 'Podano różne hasła. <br/>';
			if(strlen($sError) == 0)
				if(!User::checkUserName(trim($_POST['username']), trim($_POST['login'])))
					$sError .= 'Podana nazwa użytkownika lub login już istnieje. <br />';
			if(strlen($sError) == 0)
			{
				$bRegistrationComplete = true;
				$oUser = new User;
				$oUser->sName = trim($_POST['username']);
				$oUser->sPassword = sha1(trim($_POST['password']));
				$oUser->sLogin = trim($_POST['login']);
				$oUser->save();
				if($oUser->iId != null)
					$_SESSION['uid'] = $oUser->iId;
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
 <title>Forum - rejestracja</title>
 <meta charset="UTF-8" />
</head>
<body>
<a href="/">&lt; &lt; Powrót</a> <br />
<?php 
if(!$bRegistrationComplete): 
if(!isset($_SESSION['uid'])):
?>
Rejestracja na forum. Aby móc pisać posty i zakładać nowe tematy konieczna jest rejestracja.  <br /> <br />
<form action="register.php" method="post">
 Twój login: <br /><input type="text" name="login" value=""  required="required" /> <br />
 Wyświetlana nazwa użytkownika: <br /><input type="text" name="username" value=""  required="required" /> <br />
 Hasło:  <br /><input type="password" name="password" value=""  required="required" /> <br />
 Powtórz hasło:  <br /><input type="password" name="password2"  required="required" /> <br />
<b><?php echo $sError ?></b>
 <input type="submit" value="Zarejestruj się" />
</form>
<?php else: ?>
Aby móc zarejestrować nowe konto wpierw się wyloguj.
<?php endif;?>
<?php else: ?>
Rejestracja zakończona. Twoje konto zostało automatycznie zalogowane na forum.
<?php endif;?>
</body>
</html>
