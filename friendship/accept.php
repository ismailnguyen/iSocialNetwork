<?php
/* @File: friendship/accept.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

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
				$_User_idUser = getIdUser();
				$_User_idFriend = getRequest("User_idFriend");
				$_createdDate = date(); // date of acceptation
				$_state = 1; // 0: Invitation sent | 1: Invitation accepted
				$_oldState = 0;

        		$params = array(
								":User_idUser" => $_User_idUser,
								":User_idFriend" => $_User_idFriend,
								":createdDate" => $_createdDate,
								":state" => $_state,
								":oldState" => $_oldState
								);
				
				$statement = $m_db->prepare("SELECT * FROM Friendship WHERE User_idUser = :User_idUser AND User_idFriend = :User_idFriend AND state = :oldState");
				if($statement->execute($params))
				{  
					$statement = $m_db->prepare("UPDATE Friendship SET state = :state, createdDate = :createdDate WHERE User_idUser = :User_idUser AND User_idFriend = :User_idFriend AND state = :oldState");
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
			unset($m_db);
		}
	}
}

$api = new FriendshipAccept();
$api->run();
?>
