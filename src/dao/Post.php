<?php
class Post
{
	static public $oConnection = null;

	public $iId = null;
	public $iAuthorId = null;
	public $sAuthorName = null;
	public $iThreadId = null;
	public $sContent = null;
	public $sDate = null;
	
	public function __construct($iId = null)
	{
		$this->iId = $iId;
		if($iId != null)
		{
			$oStatement = self::$oConnection->prepare('
					SELECT *,`posts`.`id` AS `pid` 
					FROM `posts`,`users`
					WHERE `posts`.`author_id`=`users`.`id`
					AND `posts`.`id` = :id
					ORDER BY `posts`.`id` ASC');
			$oStatement->bindValue(':id', $iId, PDO::PARAM_INT);
			$oStatement->execute();
			if($aResult = $oStatement->fetch(PDO::FETCH_ASSOC))
			{
				$this->iId = $aResult['pid'];
				$this->iAuthorId = $aResult['author_id'];
				$this->sAuthorName = $aResult['name'];
				$this->iThreadId = $aResult['thread_id'];
				$this->sContent = $aResult['content'];
				$this->sDate = $aResult['date'];
			}
			else
				$this->iId = null;
		}
	}
	
	public function save()
	{
		if($this->iId != null) // update
		{
			$oStatement = self::$oConnection->prepare('
					UPDATE `posts` 
					SET `author_id` = :aid, `thread_id` = :tid, `content` = :content 
					WHERE `id` =:id ');
			$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		}
		else // insert
			$oStatement = self::$oConnection->prepare('
					INSERT INTO `posts` (`author_id` ,`thread_id` ,`content`) 
					VALUES (:aid, :tid, :content);');
		$oStatement->bindValue(':aid', $this->iAuthorId, PDO::PARAM_INT);
		$oStatement->bindValue(':tid', $this->iThreadId, PDO::PARAM_INT);
		$oStatement->bindValue(':content', $this->sContent, PDO::PARAM_STR);

		$oStatement->execute();
		if($this->iId == null) 
			$this->iId = self::$oConnection->lastInsertId();
	}

	public function delete()
	{
		$oStatement = self::$oConnection->prepare('DELETE FROM `posts` WHERE `id` = :id');
		$oStatement->bindValue(':id', $this->iId, PDO::PARAM_INT);
		$oStatement->execute();
	}
};
?>
