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

class TagRead extends BusinessLayer
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
				$_idNotification = $this->getRequest("idNotification");
				
				if($_idNotification != null)
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM notification
														
														WHERE idNotification = ?");
														
					if($statement && $statement->execute(array($_idNotification)))
					{
						$this->addData($statement->fetch(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->addData(array("msg" => 'Notification does not exist'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong notification id
					}
				}
				else
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM post
														
														WHERE user_idUser = ".$_user_idUser." 
															OR user_idUser in
																			(
																				SELECT user_idFriend
																				FROM friendship
																				WHERE user_idUser = ".$_user_idUser."
																			)");
					
					if($statement && $statement->execute(array($_idNotification)))
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
				$this->setCode(23); // METHOD NOT ALLOWED: Only GET
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

$api = new TagRead();
$api->run();
?>
