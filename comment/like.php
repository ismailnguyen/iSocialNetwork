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
			if($this->getMethod() == "GET")
	    	{
				$_user_idUser = $this->getIdUser();
				$_comment_idComment = $this->getRequest("idComment");
				$_createdDate = date("Y-m-d H:i:s");

        		$params = array(
								":user_idUser" => $_user_idUser,
								":comment_idComment" => $_comment_idComment,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("SELECT *
				
													FROM comment_like
													
													WHERE user_idUser = :user_idUser
														AND comment_idComment = :comment_idComment");
														
				if($statement->execute($params))
				{
				  	//Unlike if like already exist
				  	$result = $statement->fetch();
				  
					$statement = $this->m_db->prepare("DELETE FROM comment_like
					
														WHERE idComment_like = ?");
					
					if($statement && $statement->execute(array($result['idComment_like'])))
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
					$statement = $this->m_db->prepare("INSERT INTO comment_like
														(
															user_idUser, 
															comment_idComment, 
															created_date
														)
														
														VALUES
														(
															:user_idUser, 
															:comment_idComment, 
															:createdDate
														)");
														
					if($statement && $statement->execute($params))
          			{
            			$_idCommentLike = $this->m_db->lastInsertId();

            			$this->addData(array(
											"idComment_like" => $_idComment_like,
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
			$this->setCode(13); //Server error
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
