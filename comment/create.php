<?php
/* @File: comment/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

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
			if(getMethod() == "POST")
	    	{
				$_User_idUser = getIdUser();
				$_Post_idPost = getRequest("Post_idPost");
				$_content = getRequest("content");
				$_createdDate = date();

        		$params = array(
								":User_idUser" => $_User_idUser,
								":Post_idPost" => $_Post_idPost,
								":content" => $_content,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("INSERT INTO Comment (User_idUser, Post_idPost, content, createdDate) VALUES (:User_idUser, :Post_idPost, :content, :createdDate)");
				if($statement && $statement->execute($params))
				{
				  $_idComment = $m_db->lastInsertId();

				  $this->addData(array("idComment" => $_idComment,  "createdDate" => $_createdDate));
				}
				else
				{
					$this->setCode(27); //Error adding comment
				}
			}
			else
			{
				$this->setCode(23); //Request method not accepted
			}
		}
		catch(PDOException $e)
		{
			$this->setCode(36); //Server error
		}
		finally
		{
			$this->response();
			unset($m_db);
		}
	}
}

$api = new CommentCreate();
$api->run();
?>
