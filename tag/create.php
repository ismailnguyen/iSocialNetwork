<?php
/* @File: tag/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class TagCreate extends BusinessLayer
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
				$_user_idFriend = $this->getRequest("idFriend");
				$_post_idPost = $this->getRequest("idPost");
				$_createdDate = date("Y-m-d H:i:s");

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend,
								":post_idPost" = > $_post_idPost,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("SELECT * FROM post
				
													WHERE idPost = :post_idPost
														AND user_idUser = :user_idUser");
														
				if($statement->execute($params))
				{
					$statement = $this->m_db->prepare("INSERT INTO tag
														(
															user_idUser,
															user_idFriend,
															post_idPost,
															createdDate
														)
														
														VALUES
														(
															:user_idUser,
															:user_idFriend,
															:post_idPost,
															:createdDate
														)");
														
					if($statement && $statement->execute($params))
					{
						$_idTag = $this->m_db->lastInsertId();
						
						$this->setCode(2); // Created

						$this->addData(array(
											"idTag" => $_idTag,
											"createdDate" => $_createdDate
											));
					}
					else
					{
						$this->setCode(10); //Error adding tag
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
			$this->setCode(13); //Server error
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new TagCreate();
$api->run();
?>
