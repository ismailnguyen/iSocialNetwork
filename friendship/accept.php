<?php
/* @File: friendship/accept.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class FriendshipAccept extends BusinessLayer
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
				$_user_idUser = getIdUser();
				$_user_idFriend = getRequest("idFriend");
				$_createdDate = date(); // date of acceptation
				$_state = 1; // 0: Invitation sent | 1: Invitation accepted
				$_oldState = 0;

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend,
								":createdDate" => $_createdDate,
								":state" => $_state,
								":oldState" => $_oldState
								);
				
				$statement = $m_db->prepare("SELECT * FROM friendship WHERE user_idUser = :user_idUser AND user_idFriend = :user_idFriend AND state = :oldState");
				if($statement->execute($params))
				{  
					$statement = $m_db->prepare("UPDATE friendship SET state = :state, createdDate = :createdDate WHERE user_idUser = :user_idUser AND user_idFriend = :user_idFriend AND state = :oldState");
					if($statement && $statement->execute($params))
					{
						$this->addData(array("createdDate" => $_createdDate));
					}
					else
					{
						$this->setCode(27); //Error accepting friendship
					}
				}
				else
				{
					$this->setCode(18); //Bad request, friendship does not exist
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

$api = new FriendshipAccept();
$api->run();
?>
