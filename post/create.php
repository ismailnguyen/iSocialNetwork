<?php
/* @File: post/create.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostCreate extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "POST")
	    	{
				$_user_idUser = $this->getIdUser();
				$_content = $this->getRequest("content");
				$_createdDate = date("Y-m-d H:i:s");

        		$params = array(
								":user_idUser" => $_user_idUser,
								":content" => $_content,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("INSERT INTO post
													(
														user_idUser,
														content,
														createdDate
													)
													
													VALUES
													(
														:user_idUser, 
														:content, 
														:createdDate
													)");
													
				if($statement && $statement->execute($params))
				{
					$_idPost = $this->m_db->lastInsertId();
					
					$this->setCode(2); // Created

					$this->addData(array(
										"idPost" => $_idPost,
										"createdDate" => $_createdDate
										));
				}
				else
				{
					$this->setCode(10); //Error adding post
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

$api = new PostCreate();
$api->run();
?>
