<?php
/* @File: tag/read.php
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
				$_idTag = $this->getRequest("idTag");
				
				if($_idTag != null)
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM tag
														
														WHERE idTag = ?");
														
					if($statement && $statement->execute(array($_idTag)))
					{
						$this->addData($statement->fetch(PDO::FETCH_ASSOC));
					}
					else
					{
						$this->addData(array("msg" => 'Tag does not exist'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong tag id
					}
				}
				else
				{
					$statement = $this->m_db->prepare("SELECT *
					
														FROM tag
														
														WHERE user_idFriend = :user_idUser 
													");
					
					if($statement && $statement->execute(array(":user_idUser" => $_user_idUser)))
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
