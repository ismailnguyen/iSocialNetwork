<?php
/* @File: post/tag/read.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../../BusinessLayer.php");

class PostTagRead extends BusinessLayer
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
				$_idTag = $this->getRequest("idTag");
				$_idPost = $this->getRequest("idPost");
				
				if($_idTag != null)
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM post_tag
														
														WHERE idTag = ?");
														
					if($statement && $statement->execute(array($_idTag)))
					{
						$this->addData($statement->fetch(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->addData(array("msg" => 'Tag does not exist'));
						$this->setCode(9); // NOT ACCEPTABLE: Wrong tag id
					}
				}
				elseif($_idPost != null)
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM post_tag
														
														WHERE post_idPost = ?");
														
					if($statement && $statement->execute(array($_idPost)))
					{
						$this->addData($statement->fetch(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->addData(array("msg" => 'Post does not exist'));
						$this->setCode(9); // NOT ACCEPTABLE: Wrong post id
					}
				}
				else
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM post_tag
														
														WHERE user_idFriend = :user_idUser 
													");
					
					if($statement && $statement->execute(array(":user_idUser" => $_user_idUser)))
					{
						$this->addData($statement->fetchAll(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->setCode(10); // CONFLICT: database error
					}
				}
			}
			else
			{
				$this->setCode(8); // METHOD NOT ALLOWED: Only GET
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

$api = new PostTagRead();
$api->run();
?>
