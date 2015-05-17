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
			if($this->getMethod() == "POST")
	    	{
				$_idPost = getRequest("idPost");
				$_user_idUser = getIdUser();				

        		$params = array(
								":idPost" => $_idPost,
								":user_idUser" => $_user_idUser								
								);

				$statement = $m_db->prepare("SELECT * FROM post WHERE idPost = :idPost AND user_idUser = :user_idUser");
				if($statement->execute($params))
				{  
					$statement = $m_db->prepare("DELETE FROM post WHERE idPost = :idPost AND user_idUser = :user_idUser");
					if(!($statement && $statement->execute(array($params))))
          			{
						$this->setCode(27); //Error removing comment
					}
				}
				else
				{
					$this->setCode(18); //Bad request, comment does not exist
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

$api = new PostRemove();
$api->run();
?>