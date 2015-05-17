<?php
/* @File: friendship/invite.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

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
				$_user_idUser = getIdUser();
				$_user_idFriend = getRequest("user_idFriend");
				$_createdDate = date();
				$_state = 0; // 0: Invitation sent | 1: Invitation accepted

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend,
								":createdDate" => $_createdDate,
								":state" => $_state
								);

				$statement = $m_db->prepare("INSERT INTO friendship (user_idUser, user_idFriend, createdDate, state) VALUES (:user_idUser, :user_idFriend, :createdDate, :state)");
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
		}
	}
}

$api = new FriendshipInvite();
$api->run();
?>
