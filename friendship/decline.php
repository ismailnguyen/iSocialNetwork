<?php
/* @File: friendship/decline.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class FriendshipDecline extends BusinessLayer
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
			
				$statement = $this->m_db->prepare("SELECT * FROM friendship WHERE user_idUser = :user_idUser AND user_idFriend = :user_idFriend");
				if($statement->execute($params))
				{  
					$statement = $this->m_db->prepare("DELETE FROM friendship WHERE user_idUser = :user_idUser AND user_idFriend = :user_idFriend");
					if(!($statement && $statement->execute(array($params))))
          			{
						$this->setCode(27); //Error declining friendship
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

$api = new FriendshipDecline();
$api->run();
?>
