<?php
/* @File: account/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class AccountRemove extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "DELETE")
	    	{
				$_user_idUser = $this->getIdUser();				
 
				$statement = $this->m_db->prepare("DELETE post,
															post_like,
															post_tag,
															comment,
															comment_like,
															comment_tag
													
													FROM post
													
													INNER JOIN post_like
														ON post.idPost = post_like.post_idPost
													INNER_JOIN post_tag
														ON post.idPost = post_tag.post_idPost
													INNER JOIN comment
														ON post.idPost = comment.post_idPost
													INNER JOIN comment_like
														ON comment.idComment = comment_like.comment_idComment
													INNER JOIN comment_tag
														ON comment.idComment = comment_tag.comment_idComment
													
													WHERE post.user_idUser = :idUser;
													
													
													DELETE comment,
															comment_like,
															comment_tag
														
													FROM comment
													
													INNER JOIN comment_like
														ON comment.idComment = comment_like.comment_idComment
													INNER JOIN comment_tag
														ON comment.idComment = comment_tag.comment_idComment
													
													WHERE comment.user_idUser = :idUser;
													
													DELETE FROM post_like
													WHERE user_idUser = :idUser;
													
													DELETE FROM post_tag
													WHERE user_idUser = :idUser
														OR user_idFriend = :idUser;
													
													DELETE FROM comment_like
													WHERE user_idUser = :idUser;
													
													DELETE FROM comment_tag
													WHERE user_idUser = :idUser
														OR user_idFriend = :idUser;
														
													DELETE FROM notification
													WHERE user_idUser = :idUser;
													
													DELETE FROM friendship
													WHERE user_idUser = :idUser
														OR user_idFriend = :idUser;
													
													DELETE FROM user
													WHERE idUser = :idUser;
													");
													
				if(!($statement && $statement->execute(array(":idUser" => $_user_idUser))))
				{													
					$this->setCode(10); //Error removing user
				}
			}
			else
			{
				$this->setCode(8); //Request method not accepted
			}
		}
		catch(PDOException $e)
		{
			if(DEBUG) $this->addData(array("msg" => $e->getMessage()));
			$this->setCode(13); // INTERNAL SERVER ERROR
		}
		catch(Exception $e)
		{
			if(DEBUG) $this->addData(array("msg" => $e->getMessage()));
			$this->setCode(4); // Bad request
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new AccountRemove();
$api->run();
?>
