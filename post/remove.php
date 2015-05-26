<?php
/* @File: post/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostRemove extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "DELETE")
	    	{
				$_idPost = $this->getRequest("idPost");
				$_user_idUser = $this->getIdUser();				

        		$params = array(":idPost" => $_idPost,
								":user_idUser" => $_user_idUser);

				$statement = $this->m_db->prepare("SELECT * FROM post
				
													WHERE idPost = :idPost
														AND user_idUser = :user_idUser");
														
				if($statement->execute($params) && $statement->rowCount() == 1)
				{  
					$statement = $this->m_db->prepare("DELETE FROM post
					
														WHERE idPost = :idPost
															AND user_idUser = :user_idUser");
					
					if($statement && $statement->execute(array($params)))
          			{
						$statement = $this->m_db->prepare("DELETE FROM comment
					
														WHERE post_idPost = :idPost");
															
						if(!($statement && $statement->execute(array($params))))
						{
							$this->setCode(10); //Error removing comments of the post
						}
					}
					else
					{
						$this->setCode(10); //Error removing post
					}
				}
				else
				{
					$this->setCode(4); //Bad request, post does not exist
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

$api = new PostRemove();
$api->run();
?>
