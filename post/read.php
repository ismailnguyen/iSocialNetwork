<?php
/* @File: post/read.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostRead extends BusinessLayer
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
				$_idPost = $this->getRequest("idPost");
				
				if($_idPost != null)
				{
					$statement = $this->m_db->prepare("SELECT * FROM post WHERE idPost = ?");
					if($statement && $statement->execute(array($_idPost)))
					{
						$this->addData($statement->fetch(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->addData(array("msg" => 'Post does not exist'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong post id
					}
				}
				else
				{
					$statement = $this->m_db->prepare("SELECT *
														FROM post
														WHERE user_idUser = ".$_user_idUser." 
															OR user_idUser in (
																SELECT user_idFriend
																FROM friendship
																WHERE user_idUser = ".$_user_idUser."
															)");
					
					if($statement && $statement->execute(array($_idPost)))
					{
						$this->addData($statement->fetchAll(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->setCode(27); // CONFLICT: database error
					}
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

$api = new PostRead();
$api->run();
?>
