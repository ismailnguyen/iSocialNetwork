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

        		$params = array(
								":user_idUser" => $_user_idUser,
								":post_idPost" => $_post_idPost,
								":content" => $_content,
								":createdDate" => $_createdDate
								);
			
				$statement = $this->m_db->prepare("SELECT * FROM post WHERE idPost = ?");
				if($statement->execute(array($_post_idPost)))
				{
					if($statement->rowCount() == 1)
					{
						$statement = $this->m_db->prepare("INSERT INTO comment (user_idUser, post_idPost, content, createdDate) VALUES (:user_idUser, :post_idPost, :content, :createdDate)");
						if($statement && $statement->execute($params))
						{
							$_idComment = $this->m_db->lastInsertId();

							$this->addData(array("idComment" => $_idComment,  "createdDate" => $_createdDate));
						}
						else
						{
							$this->setCode(27); // CONFLICT: database error
						}
					}
					else
					{
						$this->addData(array("msg" => 'Post does not exist'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong post id
					}
				}
				else
				{
					$this->setCode(18); // BAD REQUEST
				}
			}
			else
			{
				$this->setCode(23); // METHOD NOT ALLOWED: Only POST
			}
		}
		catch(PDOException $e)
		{
			if(DEBUG) $this->addData(array("msg" => $e->getMessage()));
			$this->setCode(36); // INTERNAL SERVER ERROR
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
