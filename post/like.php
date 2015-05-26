<?php
/* @File: post/like.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostLike extends BusinessLayer
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
				$_post_idPost = $this->getRequest("idPost");
				$_createdDate = date("Y-m-d H:i:s");

				$params = array(
								":user_idUser" => $_user_idUser,
								":post_idPost" => $_post_idPost,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("SELECT * FROM post_like
													
													WHERE user_idUser = :user_idUser
														AND post_idPost = :post_idPost");
														
				if($statement->execute($params) && $statement->rowCount() == 1)
				{
					//Unlike if like already exist
					$result = $statement->fetch();
				  
					$statement = $this->m_db->prepare("DELETE FROM post_like
					
														WHERE idPost_like = ?");
														
											
														
					if($statement && $statement->execute(array($result['idPost_like'])))
					{
						$this->setCode(3); // Accepted
					}
					else
					{
						$this->setCode(10); //Error removing like
					}
				}
				else
				{
					//Like
					$statement = $this->m_db->prepare("INSERT INTO post_like
														(
															user_idUser,
															post_idPost,
															createdDate
														)
														
														VALUES
														(
															:user_idUser, 
															:post_idPost, 
															:createdDate
														)");
														
					if($statement && $statement->execute($params))
					{
						$_idPostLike = $this->m_db->lastInsertId();
						
						$this->setCode(2); // Created

						$this->addData(array(
											"idPost_like" => $_idPostLike,
											"createdDate" => $_createdDate
											));
					}
					else
					{
						$this->setCode(10); //Error adding like
					}
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

$api = new PostLike();
$api->run();
?>
