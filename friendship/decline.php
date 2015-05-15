<?php
/* @File: friendship/decline.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

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
			if(getMethod() == "POST")
	    	{
				$_User_idUser = getIdUser();
				$_User_idFriend = getRequest("User_idFriend");
				$_createdDate = date();

        		$params = array(
								":User_idUser" => $_User_idUser,
								":User_idFriend" => $_User_idFriend
								);
			
				$statement = $m_db->prepare("SELECT * FROM Friendship WHERE User_idUser = :User_idUser AND User_idFriend = :User_idFriend");
				if($statement->execute($params))
				{  
					$statement = $m_db->prepare("DELETE FROM Friendship WHERE User_idUser = :User_idUser AND User_idFriend = :User_idFriend");
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
			unset($m_db);
		}
	}
}

$api = new FriendshipDecline();
$api->run();
?>
