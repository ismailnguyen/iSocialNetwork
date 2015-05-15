<?php
/* @File: comment/edit.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

class CommentEdit extends BusinessLayer
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
				$_idComment = getRequest("idComment");
				$_User_idUser = $this->getIdUser();
				$_content = getRequest("content");

        		$params = array(
								":idComment" => $_idComment,
								":User_idUser" => $_User_idUser,
								":content" => $_content
								);

				$statement = $m_db->prepare("UPDATE Comment SET content = :content WHERE idComment = :idComment AND User_idUser = :User_idUser");
				if($statement && $statement->execute($params))
				{
					$this->addData(array("idComment" => $_idComment,  "content" => $_content));
				}
				else
				{
					$this->setCode(27); //Error editing comment
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

$api = new CommentEdit();
$api->run();
?>
