<?php
/* @File: post/most.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostMost extends BusinessLayer
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
				
				$params = array(
								":user_idUser" => $_user_idUser,
								":offset" => $_offset,
								":limit" => $_limit
								);
				
				SELECT * FROM User WHERE id_user = (SELECT id_user FROM Loan GROUP BY id_user ORDER BY COUNT(id_user) DESC LIMIT 1)
				
				$query = "SELECT idUser,
									firstname,
									lastname,
									email,
									gender,
									birthdate,
									createdDate
						
						FROM user
							
						WHERE idUser in (
										SELECT user_idUser
								
										FROM post
										
										GROUP BY user_idUser
										
										ORDER BY COUNT(user_idUser) DESC
										)
						
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

$api = new PostMost();
$api->run();
?>
