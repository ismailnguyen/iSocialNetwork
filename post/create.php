<?php
/* @File: post/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

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
			if(getMethod() == "POST")
	    	{
				$_User_idUser = getIdUser();
				$_content = getRequest("content");
				$_createdDate = date();

        		$params = array(
								":User_idUser" => $_User_idUser,
								":content" => $_content,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("INSERT INTO Post (User_idUser, content, createdDate) VALUES (:User_idUser, :content, :createdDate)");
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
			unset($m_db);
		}
	}
}

$api = new PostCreate();
$api->run();
?>
