<?php
/* @File: comment/edit.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class CommentEdit extends BusinessLayer
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
				$_idComment = $this->getRequest("idComment");
				$_user_idUser = $this->getIdUser();
				$_content = $this->getRequest("content");

        		$params = array(
								":idComment" => $_idComment,
								":user_idUser" => $_user_idUser,
								":content" => $_content
								);

				$statement = $this->m_db->prepare("UPDATE comment
													
													SET content = :content 
													
													WHERE idComment = :idComment
														AND user_idUser = :user_idUser");
														
				if($statement && $statement->execute($params))
				{
					$this->addData(array(
										"idComment" => $_idComment,
										"content" => $_content
										));
				}
				else
				{
					$this->setCode(10); //Error editing comment
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

$api = new CommentEdit();
$api->run();
?>
