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
				$_offset = $this->getRequest("offset");
				$_limit = $this->getRequest("limit");
				$_keyword = $this->getRequest("keyword");
				
				$params = array(
								":user_idUser" => $_user_idUser,
								":offset" => $_offset,
								":limit" => $_limit,
								":keyword" => '%'.$_keyword.'%'
								);
				
				$query = "SELECT u.idUser,
									u.firstname,
									u.lastname,
									u.email,
									u.gender,
									u.birthdate,
									u.createdDate,
									f.user_idUser,
									f.user_idFriend,
									f.state
									
						CASE WHEN f.user_idUser IS NOT NULL THEN
							f.user_idUser ELSE
							f.user_idFriend END AS idFriend
						
						FROM user u
						
						INNER JOIN friendship f
							ON u.idUser = idFriend
						
						WHERE u.firstname LIKE :keyword
							OR u.lastname LIKE :keyboard
							OR u.email LIKE :keyboard					
						
						GROUP BY idUser";
							
				if($_limit != null)
					$query .= " LIMIT ".($_offset != null) ? ":offset, " : "".":limit;";
				
				$statement = $this->m_db->prepare($query);
				
				if($statement && $statement->execute($params))
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
