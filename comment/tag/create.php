<?php
/* @File: comment/tag/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../../BusinessLayer.php");

class CommentTagCreate extends BusinessLayer
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
				$_comment_idComment = $this->getRequest("idComment");
				$_createdDate = date("Y-m-d H:i:s");

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend,
								":comment_idComment" = > $_comment_idComment,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("SELECT * FROM comment
				
													WHERE idComment = :comment_idComment
														AND user_idUser = :user_idUser");
														
				if($statement->execute($params))
				{
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

$api = new CommentTagCreate();
$api->run();
?>
