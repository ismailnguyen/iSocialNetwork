<?php
/* @File: comment/like.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

class CommentLike extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if(getMethod() == "POST")
	    	{
				$_User_idUser = getIdUser();
				$_Comment_idComment = getRequest("Comment_idComment");
				$_createdDate = date();

        		$params = array(
								":User_idUser" => $_User_idUser,
								":Comment_idComment" => $_Comment_idComment,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("SELECT * FROM CommentLike WHERE User_idUser = :User_idUser AND Comment_idComment = :Comment_idComment");
				if($statement->execute($params))
				{
				  	//Unlike if like already exist
				  	$result = $statement->fetch();
				  
					$statement = $m_db->prepare("DELETE FROM CommentLike WHERE idCommentLike = ?");
					if(!($statement && $statement->execute(array($result['idCommentLike']))))
          			{
						$this->setCode(27); //Error removing like
					}
				}
				else
				{
					//Like
					$statement = $m_db->prepare("INSERT INTO CommentLike (User_idUser, Comment_idComment, created_date) VALUES (:User_idUser, :Comment_idComment, :createdDate)");
					if($statement && $statement->execute($params))
          			{
            			$_idCommentLike = $m_db->lastInsertId();

            			$this->addData(array("idCommentLike" => $_idCommentLike,  "createdDate" => $_createdDate));
          			}
					else
					{
						$this->setCode(27); //Error adding like
					}
				}
			}
			else
			{
				$this->setCode(23); //Request method not accepted
			}
		}
		catch(PDOException $e)
		{
			$this->setCode(36); //Server error
		}
		finally
		{
			$this->response();
			unset($m_db);
		}
	}
}

$api = new CommentLike();
$api->run();
?>
