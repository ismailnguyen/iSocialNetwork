<?php
/* @File: friendship/invite.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

class FriendshipInvite extends BusinessLayer
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
				$_createdDate = date();
				$_state = 0; // 0: Invitation sent | 1: Invitation accepted

        		$params = array(
								":User_idUser" => $_User_idUser,
								":User_idFriend" => $_User_idFriend,
								":createdDate" => $_createdDate,
								":state" => $_state
								);

				$statement = $m_db->prepare("INSERT INTO Friendship (User_idUser, User_idFriend, createdDate, state) VALUES (:User_idUser, :User_idFriend, :createdDate, :state)");
				if($statement && $statement->execute($params))
				{
				  $_idFriendship = $m_db->lastInsertId();

				  $this->addData(array("idFriendship" => $_idFriendship,  "createdDate" => $_createdDate));
				}
				else
				{
					$this->setCode(27); //Error inviting
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

$api = new FriendshipInvite();
$api->run();
?>
