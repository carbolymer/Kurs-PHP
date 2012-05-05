<?php
class Thread
{
	static public $oConnection = null;

	public $iId = null;
	public $iAuthorId = null;
	public $sAuthorName = null;
	public $sTitle = null;
	public $sContent = null;
	public $sDate = null;
	public $aPosts = array();

	static public function getAll()
	{
		$aThreads = array();
		$oStatement = self::$oConnection->prepare('
				SELECT *,`threads`.`id` AS `tid`
				FROM `threads`,`users`
				WHERE `threads`.`author_id`=`users`.`id`
				ORDER BY `threads`.`id` DESC');
		$oStatement->execute();
		while($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			$oThread = new self();
			$oThread->iId = $aResult['tid'];
			$oThread->iAuthorId = $aResult['author_id'];
			$oThread->sAuthorName = $aResult['name'];
			$oThread->sTitle = $aResult['title'];
			$oThread->sContent = $aResult['content'];
			$oThread->sDate = $aResult['date'];
			$aThreads[] = $oThread;
		}
		return $aThreads;
	}


	public function __construct($iId = null)
	{
		if($iId != null)
		{
			$this->iId = $iId;
			$oStatement = self::$oConnection->prepare('
					SELECT *,`threads`.`id` AS `tid` 
					FROM `threads`,`users` 
					WHERE `threads`.`author_id`=`users`.`id` 
					AND `threads`.`id` = :id 
					ORDER BY `threads`.`id` ASC');
			$oStatement->bindValue(':id', $iId, PDO::PARAM_INT);
			$oStatement->execute();
			if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
			{
				$this->iAuthorId = $aResult['author_id'];
				$this->sTitle = $aResult['title'];
				$this->sAuthorName = $aResult['name'];
				$this->sContent = $aResult['content'];
				$this->sDate = $aResult['date'];
			}
			else
				$this->iId = null;
		}
	}

	public function getAllPosts()
	{
		if($this->iId == null)
			return array();
		$oStatement = self::$oConnection->prepare('
					SELECT *,`posts`.`id` AS `pid` 
					FROM `posts`,`users`
					WHERE `posts`.`thread_id`=:id
					AND `posts`.`author_id`=`users`.`id`
					ORDER BY `posts`.`id` ASC');
		$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		$oStatement->execute();
		while($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			$oPost = new Post();
			$oPost->iId = $aResult['pid'];
			$oPost->iAuthorId = $aResult['author_id'];
			$oPost->sAuthorName = $aResult['name'];
			$oPost->iThreadId = $aResult['thread_id'];
			$oPost->sContent = $aResult['content'];
			$oPost->sDate = $aResult['date'];
			$this->aPosts[] = $oPost;
		}
		return $this->aPosts;
	}

	public function getLastAnswer()
	{
		$oStatement = self::$oConnection->prepare('SELECT * FROM `posts` WHERE `thread_id` = :id ORDER BY `date` DESC');
		$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		$oStatement->execute();
		if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
		{
			$oPost = new Post();
			$oPost->iId = $aResult['id'];
			$oPost->iThreadId = $aResult['thread_id'];
			$oPost->sContent = $aResult['content'];
			$oPost->sTitle = $aResult['title'];
			$oPost->sDate = $aResult['date'];
			return $oPost;
		}
		return null;
	}

	public function save()
	{
		if($this->iId != null) // update
		{
			$oStatement = self::$oConnection->prepare('
					UPDATE `threads` 
					SET `author_id` = :aid, `title` = :title, `content` = :content 
					WHERE `id` =:id ');
			$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		}
		else // insert
			$oStatement = self::$oConnection->prepare('
					INSERT INTO `threads` (`author_id`, `title`, `content`) 
					VALUES (:aid, :title, :content);');
		$oStatement->bindValue(':aid', $this->iAuthorId, PDO::PARAM_INT);
		$oStatement->bindValue(':title', $this->sTitle, PDO::PARAM_STR);
		$oStatement->bindValue(':content', $this->sContent, PDO::PARAM_STR);

		$oStatement->execute();
		if($this->iId == null)
			$this->iId = self::$oConnection->lastInsertId();
		var_dump($oStatement->errorInfo());
	}

	public function delete()
	{
		$oStatement = self::$oConnection->prepare('DELETE FROM `threads` WHERE `id` = :id');
		$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		$oStatement->execute();
		$oStatement = self::$oConnection->prepare('DELETE FROM `posts` WHERE `thread_id` = :id');
		$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		$oStatement->execute();
	}
};
?>
