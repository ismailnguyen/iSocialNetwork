<?php
/* @File: comment/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class CommentCreate extends BusinessLayer
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
				$_user_idUser = $this->getIdUser();
				$_post_idPost = $this->getRequest("idPost");
				$_content = $this->getRequest("content");
				$_createdDate = date("Y-m-d H:i:s");
				
				$_hashtag = array();
				
				preg_match_all('/(#\w+)/', $_content, $_matches);
				foreach ($_matches[0] as $_ht)
					$_hashtag[] = '%'.$_ht.'%';

        		$params = array(
								":user_idUser" => $_user_idUser,
								":post_idPost" => $_post_idPost,
								":content" => $_content,
								":createdDate" => $_createdDate
								);
			
				$statement = $this->m_db->prepare("SELECT *
						
													FROM post
													
													WHERE idPost = ?");
													
				if($statement->execute(array($_post_idPost)))
				{
					if($statement->rowCount() == 1)
					{
						$statement = $this->m_db->prepare("INSERT INTO comment
															(
																user_idUser, 
																post_idPost, 
																content, 
																createdDate
															)
															
															VALUES
															(
																:user_idUser, 
																:post_idPost, 
																:content, 
																:createdDate
															)");
															
						if($statement && $statement->execute($params))
						{
							$_idComment = $this->m_db->lastInsertId();
							
							$this->setCode(2); // Created

							$this->addData(array(
												"idComment" => $_idComment,
												"createdDate" => $_createdDate
												));
							
							foreach($_hashtag as $_h)
							{
								$statement = $this->m_db->prepare("SELECT TOP 1 idUser
								
																	FROM user
																	
																	WHERE firstname LIKE :hashtag
																		OR lastname LIKE :hashtag
																		OR email LIKE :hashtag");
																	
								if($statement && $statement->execute(array(":hashtag" => $_h)))
								{
									$_result = $statement->fetch(PDO::FETCH_ASSOC);
									
									$statement = $this->m_db->prepare("INSERT INTO comment_tag
																		(
																			user_idUser,
																			user_idFriend,
																			comment_idComment,
																			createdDate
																		)
																		
																		VALUES
																		(
																			:user_idUser,
																			:user_idFriend,
																			:comment_idComment,
																			:createdDate
																		)");
																
									if(!($statement && $statement->execute(array(
																				":user_idUser" => _user_idUser,
																				":user_idFriend" => $_result['idUser'],
																				":comment_idComment" => $_idComment,
																				":createdDate" => $_createdDate
																				))))
									{
										$this->setCode(10); // CONFLICT: database error
									}
								}
							}
						}
						else
						{
							$this->setCode(10); // CONFLICT: database error
						}
					}
					else
					{
						$this->addData(array("msg" => 'Post does not exist'));
						$this->setCode(9); // NOT ACCEPTABLE: Wrong post id
					}
				}
				else
				{
					$this->setCode(4); // BAD REQUEST
				}
			}
			else
			{
				$this->setCode(8); // METHOD NOT ALLOWED: Only POST
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

$api = new CommentCreate();
$api->run();
?>
