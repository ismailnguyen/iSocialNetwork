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
				$_offset = (int) $this->getRequest("offset");
				$_limit = (int) $this->getRequest("limit");
								
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
						
						GROUP BY idUser
						
						LIMIT :offset, :limit";
				
				$statement = $this->m_db->prepare($query);
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

$api = new PostMost();
$api->run();
?>
