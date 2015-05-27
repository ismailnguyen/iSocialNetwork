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
			if($this->getMethod() == "GET")
	    	{
				$_user_idUser = $this->getIdUser();
				$_user_idFriend = $this->getRequest("idFriend");
				$_createdDate = date("Y-m-d H:i:s");
				$_state = 0; // 0: Invitation sent | 1: Invitation accepted

        		$params = array(
								":user_idUser" => $_user_idUser,
								":user_idFriend" => $_user_idFriend,
								":createdDate" => $_createdDate,
								":state" => $_state
								);
			
				$statement = $this->m_db->prepare("SELECT * FROM friendship
				
													WHERE ((user_idUser = :user_idUser AND user_idFriend = :user_idFriend)
														OR (user_idUser = :user_idFriend AND user_idFriend = :user_idUser))");
														
				if($statement->execute($params)
					&& $statement->rowCount() == 0
					&& $_user_idUser != $_user_idFriend)
				{
					$statement = $this->m_db->prepare("INSERT INTO friendship
														(
															user_idUser, 
															user_idFriend, 
															createdDate, 
															state
														) 
														
														VALUES
														(
														:user_idUser, 
														:user_idFriend, 
														:createdDate,
														:state
														)");
														
					if($statement && $statement->execute($params))
					{
						$this->setCode(2); // Created
						
						$this->addData(array(
											"state" => $_state,
											"createdDate" => $_createdDate
											));
					}
					else
					{
						$this->setCode(10); //Error inviting
					}
				}
				else
				{
					$this->setCode(4); // Friendship already exist
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

$api = new FriendshipInvite();
$api->run();
?>
