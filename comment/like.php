<?php
/* @File: comment/like.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

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
				$_user_idUser = getIdUser();
				$_comment_idComment = getRequest("idComment");
				$_createdDate = date();

        		$params = array(
								":user_idUser" => $_user_idUser,
								":comment_idComment" => $_comment_idComment,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("SELECT * FROM comment_like WHERE user_idUser = :user_idUser AND comment_idComment = :comment_idComment");
				if($statement->execute($params))
				{
				  	//Unlike if like already exist
				  	$result = $statement->fetch();
				  
					$statement = $m_db->prepare("DELETE FROM comment_like WHERE idComment_like = ?");
					if(!($statement && $statement->execute(array($result['idComment_like']))))
          			{
						$this->setCode(27); //Error removing like
					}
				}
				else
				{
					//Like
					$statement = $m_db->prepare("INSERT INTO comment_like (user_idUser, comment_idComment, created_date) VALUES (:user_idUser, :comment_idComment, :createdDate)");
					if($statement && $statement->execute($params))
          			{
            			$_idCommentLike = $m_db->lastInsertId();

            			$this->addData(array("idComment_like" => $_idComment_like,  "createdDate" => $_createdDate));
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
		}
	}
}

$api = new CommentLike();
$api->run();
?>
