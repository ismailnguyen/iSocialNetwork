<?php
/* @File: friendship/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class FriendshipRemove extends BusinessLayer
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
				$_user_idFriend = $this->getRequest("idFriend");
				$_createdDate = date("Y-m-d H:i:s");

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend
								);
			
				$statement = $this->m_db->prepare("SELECT * FROM friendship
				
													WHERE ((user_idUser = :user_idUser AND user_idFriend = :user_idFriend)
														OR (user_idUser = :user_idFriend AND user_idFriend = :user_idUser))
														AND state = 1");
														
				if($statement->execute($params) && $statement->rowCount() == 1)
				{
					$_result = $statement->fetch(PDO::FETCH_ASSOC);
					
					$statement = $this->m_db->prepare("DELETE FROM friendship
					
														WHERE idFriendship = ?");
															
					if(!($statement && $statement->execute(array($_result['idFriendship']))))
          			{
						$this->setCode(10); //Error removing friendship
					}
				}
				else
				{
					$this->setCode(4); //Bad request, friendship does not exist
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

$api = new FriendshipRemove();
$api->run();
?>
