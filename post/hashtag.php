<?php
/* @File: post/hashtag.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostHashtag extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "GET")
	    	{
				$_user_idUser = $this->getIdUser();
				$_offset = (int) $this->getRequest("offset");
				$_limit = (int) $this->getRequest("limit");
				$_hashtag = str_replace('#', '', $this->getRequest("hashtag"));
				$_hashtag = '%'.$_hashtag.'%';
				
				$query = "SELECT p.idPost,
									p.user_idUser,
									p.content,
									p.createdDate
						
						FROM post p
						
						INNER JOIN comment c
							ON c.post_idPost = p.idPost
						
						INNER JOIN comment_tag t
							ON t.comment_idComment = c.idComment
						
						WHERE t.user_idFriend = (
												SELECT idUser
												
												FROM user
												
												WHERE firstname = :hashtag
													OR lastname = :hashtag
													OR email = :hashtag
												)
							AND (t.user_idUser = :user_idUser 
								OR t.user_idUser in
												(
													SELECT user_idFriend
													
													FROM friendship
													
													WHERE user_idUser = :user_idUser
												))
							
						LIMIT :offset, :limit";
				
				$statement = $this->m_db->prepare($query);
				$statement->bindParam(':user_idUser', $_user_idUser, PDO::PARAM_INT);
				$statement->bindParam(':offset', $_offset, PDO::PARAM_INT);
				$statement->bindParam(':limit', $_limit, PDO::PARAM_INT);
				$statement->bindParam(':hashtag', $_hashtag, PDO::PARAM_STR);
				
				if($statement && $statement->execute())
				{
					$this->addData($statement->fetchAll(PDO::FETCH_ASSOC));
				}
				else
				{
					$this->setCode(10); // CONFLICT: database error
				}
			}
			else
			{
				$this->setCode(8); // METHOD NOT ALLOWED: Only GET
			}
		}
		catch(PDOException $e)
		{
			if(DEBUG) $this->addData(array("msg" => $e->getMessage()));
			var_dump($e);
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

$api = new PostHashtag();
$api->run();
?>
