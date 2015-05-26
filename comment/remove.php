<?php
/* @File: comment/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class CommentRemove extends BusinessLayer
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
				$_idComment = $this->getRequest("idComment");
				$_User_idUser = $this->getIdUser();				

        		$params = array(
								":idComment" => $_idComment,
								":user_idUser" => $_user_idUser
								);

				$statement = $this->m_db->prepare("SELECT *
													
													FROM comment
													
													WHERE idComment = :idComment
														AND user_idUser = :user_idUser");
				
				if($statement->execute($params) && $statement->rowCount() == 1)
				{  
					$statement = $this->m_db->prepare("DELETE comment,
																comment_like,
																comment_tag
														
														FROM comment
														
														INNER JOIN comment_like
															ON comment.idComment = comment_like.comment_idComment
														INNER JOIN comment_tag
															ON comment.idComment = comment_tag.comment_idComment
														
														WHERE
															AND comment.idComment = :idComment
															AND comment.user_idUser = :user_idUser
														");
					
					if(!($statement && $statement->execute(array($params))))
          			{
						$this->setCode(10); // CONFLICT: Error removing comment
					}
				}
				else
				{
					$this->setCode(4); //Bad request, comment does not exist
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

$api = new CommentRemove();
$api->run();
?>
