<?php
/* @File: post/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostCreate extends BusinessLayer
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
				$_user_idUser = getIdUser();
				$_content = getRequest("content");
				$_createdDate = date();

        		$params = array(
								":user_idUser" => $_user_idUser,
								":content" => $_content,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("INSERT INTO post (user_idUser, content, createdDate) VALUES (:user_idUser, :content, :createdDate)");
				if($statement && $statement->execute($params))
				{
				  $_idPost = $m_db->lastInsertId();

				  $this->addData(array("idPost" => $_idPost,  "createdDate" => $_createdDate));
				}
				else
				{
					$this->setCode(27); //Error adding post
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
		}
	}
}

$api = new PostCreate();
$api->run();
?>