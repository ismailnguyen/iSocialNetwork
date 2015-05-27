<?php
/* @File: friendship/search.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class FriendshipSearch extends BusinessLayer
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
				$_offset = (int) $this->getRequest("offset");
				$_limit = (int) $this->getRequest("limit");
				$_keyword = '%'.$this->getRequest("keyword").'%';
				
				$query = "SELECT u.idUser,
									u.firstname,
									u.lastname,
									u.email,
									u.gender,
									u.birthdate,
									u.createdDate,
						
						CASE f.state
							WHEN 1 THEN 'True'
							ELSE 'False'
						END as isFriend
						
						FROM user u
						
						JOIN friendship f
							ON (f.user_idUser = u.idUser
								OR f.user_idFriend = u.idUser)
						
						WHERE (u.firstname LIKE :key_firstname
							OR u.lastname LIKE :key_lastname
							OR u.email LIKE :key_email)
							
						AND u.idUser = :user_idUser
						
						GROUP BY u.idUser
						
						LIMIT :offset, :limit";
				
				$statement = $this->m_db->prepare($query);
				$statement->bindParam(':user_idUser', $_user_idUser, PDO::PARAM_INT);
				$statement->bindParam(':key_firstname', $_keyword, PDO::PARAM_STR);
				$statement->bindParam(':key_lastname', $_keyword, PDO::PARAM_STR);
				$statement->bindParam(':key_email', $_keyword, PDO::PARAM_STR);
				$statement->bindParam(':offset', $_offset, PDO::PARAM_INT);
				$statement->bindParam(':limit', $_limit, PDO::PARAM_INT);
							
				if($statement && $statement->execute())
				{
					$this->addData($statement->fetchAll(PDO::FETCH_ASSOC));
				}
				else
				{
					$this->setCode(10); // CONFLICT: database error
				}
			}
			else
			{
				$this->setCode(8); // METHOD NOT ALLOWED: Only GET
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

$api = new FriendshipSearch();
$api->run();
?>
