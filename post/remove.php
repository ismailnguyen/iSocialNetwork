<?php
/* @File: post/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostRemove extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "POST")
	    	{
				$_idPost = $this->getRequest("idPost");
				$_user_idUser = $this->getIdUser();				

        		$params = array(":idPost" => $_idPost,
								":user_idUser" => $_user_idUser);

				$statement = $this->m_db->prepare("SELECT * FROM post
				
													WHERE idPost = :idPost
														AND user_idUser = :user_idUser");
												
				if($statement->execute($params) && $statement->rowCount() == 1)
				{
					$_result = $statement->fetch(PDO::FETCH_ASSOC);
					$_idPost = $_result['idPost'];
					
					$statement = $this->m_db->prepare("DELETE post,
																post_like,
																post_tag,
																comment,
																comment_like,
																comment_tag
														
														FROM post
														
														LEFT JOIN post_like
															ON post.idPost = post_like.post_idPost
														LEFT JOIN post_tag
															ON post.idPost = post_tag.post_idPost
														LEFT JOIN comment
															ON post.idPost = comment.post_idPost
														LEFT JOIN comment_like
															ON comment.idComment = comment_like.comment_idComment
														LEFT JOIN comment_tag
															ON comment.idComment = comment_tag.comment_idComment
														
														WHERE post.idPost = ?");
					
					if(!($statement && $statement->execute(array($_idPost))))
          			{													
						$this->setCode(10); //Error removing post
					}
				}
				else
				{
					$this->setCode(4); //Bad request, post does not exist
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

$api = new PostRemove();
$api->run();
?>
