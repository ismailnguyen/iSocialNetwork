<?php
/* @File: notification/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class NotificationRemove extends BusinessLayer
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
				$_idNotification = $this->getRequest("idNotification");
				$_user_idUser = $this->getIdUser();				

        		$params = array(
								":idNotification" => $_idNotification,
								":user_idUser" => $_user_idUser
								);

				$statement = $this->m_db->prepare("SELECT * FROM notification
				
													WHERE idNotification = :idNotification
														AND user_idUser = :user_idUser");
														
				if($statement->execute($params))
				{  
					$statement = $this->m_db->prepare("DELETE FROM notification
					
														WHERE idNotification = :idNotification
															AND user_idUser = :user_idUser");
					
					if(!($statement && $statement->execute(array($params))))
          			{
						$this->setCode(10); //Error removing notification
					}
				}
				else
				{
					$this->setCode(4); //Bad request, notification does not exist
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

$api = new NotificationRemove();
$api->run();
?>
