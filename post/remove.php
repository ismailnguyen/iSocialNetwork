<?php
/* @File: post/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

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
			if(getMethod() == "POST")
	    	{
				$_idPost = getRequest("idPost");
				$_User_idUser = getIdUser();				

        		$params = array(
								":idPost" => $_idPost,
								":User_idUser" => $_User_idUser								
								);

				$statement = $m_db->prepare("SELECT * FROM Post WHERE idPost = :idPost AND User_idUser = :User_idUser");
				if($statement->execute($params))
				{  
					$statement = $m_db->prepare("DELETE FROM Post WHERE idPost = :idPost AND User_idUser = :User_idUser");
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
			unset($m_db);
		}
	}
}

$api = new PostRemove();
$api->run();
?>
