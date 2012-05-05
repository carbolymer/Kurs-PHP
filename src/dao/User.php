<?php
class User
{
	public static $oConnection = null;
	public static $oCurrentUser = null;

	public $iId = null;
	public $sName = null;
	public $sLogin = null;
	public $sPassword = null;
	public $bIsAdmin = false;
	
	public static function logIn($sLogin,$sPassword)
	{
		$oStatement = self::$oConnection->prepare('
				SELECT * FROM `users` 
				WHERE `login` = :login 
				AND `password` = :password');
		$oStatement->bindValue(':login', $sLogin, PDO::PARAM_STR);
		$oStatement->bindValue(':password', sha1($sPassword), PDO::PARAM_STR);
		$oStatement->execute();
		if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			$oNewUser = new User();
			$oNewUser->iId = $aResult['id'];
			$oNewUser->sName = $aResult['name'];
			$oNewUser->sLogin = $aResult['login'];
			$oNewUser->sPassword = $aResult['password'];
			$oNewUser->bIsAdmin = (bool) $aResult['is_admin'];
			return $oNewUser;
		}
		return false;
	}
	
	public static function checkUserName($sName, $sLogin)
	{
		$oStatement = self::$oConnection->prepare('
				SELECT * FROM `users` 
				WHERE `name` = :name 
				OR `login` = :login');
		$oStatement->bindValue(':name', $sName, PDO::PARAM_STR);
		$oStatement->bindValue(':login', $sLogin, PDO::PARAM_STR);
		$oStatement->execute();
		if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			return false;
		}
		return true;
	}
	
	public static function findById($iId)
	{
		$oStatement = self::$oConnection->prepare('SELECT * FROM `users` WHERE `id` = :id');
		$oStatement->bindValue(':id', $iId, PDO::PARAM_INT);
		$oStatement->execute();
		if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			$oUser = new User();
			$oUser->iId = $aResult['id'];
			$oUser->sName = $aResult['name'];
			$oUser->sLogin = $aResult['login'];
			$oUser->sPassword = $aResult['password'];
			$oUser->bIsAdmin = (bool) $aResult['is_admin'];
			return $oUser;
		}
		return false;
	}
	
	public function save()
	{
		if($this->iId != null) // update
		{
			$oStatement = self::$oConnection->prepare('
					UPDATE `users` 
					SET `name` = :name, `login` = :login, `password` = :password, `is_admin` = :is_admin 
					WHERE `id` =:id ');
			$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		}
		else // insert
			$oStatement = self::$oConnection->prepare('
					INSERT INTO `users` (`name` ,`login` ,`password`, `is_admin`) 
					VALUES (:name, :login, :password, :is_admin);');
		$oStatement->bindValue(':name', $this->sName, PDO::PARAM_INT);
		$oStatement->bindValue(':login', $this->sLogin, PDO::PARAM_INT);
		$oStatement->bindValue(':password', $this->sPassword, PDO::PARAM_STR);
		$oStatement->bindValue(':is_admin', $this->bIsAdmin, PDO::PARAM_BOOL);

		$oStatement->execute();
		if($this->iId == null) 
			$this->iId = self::$oConnection->lastInsertId();
	}

};
?>
