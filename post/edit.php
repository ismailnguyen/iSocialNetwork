<?php
/* @File: post/edit.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostEdit extends BusinessLayer
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
				$_idPost = $this->getRequest("idPost");
				$_user_idUser = $this->getIdUser();
				$_content = $this->getRequest("content");

        		$params = array(
								":idPost" => $_idPost,
								":user_idUser" => $_user_idUser,
								":content" => $_content
								);

				$statement = $this->m_db->prepare("UPDATE post 
				
													SET content = :content 
													
													WHERE idPost = :idPost 
														AND user_idUser = :user_idUser");
														
				if($statement && $statement->execute($params))
				{
					$this->addData(array(
										"idPost" => $_idPost,
										"content" => $_content
										));
				}
				else
				{
					$this->setCode(10); //Error editing post
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
			$this->setCode(13); //Server error
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new PostEdit();
$api->run();
?>
