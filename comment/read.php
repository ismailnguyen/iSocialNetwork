<?php
/* @File: comment/read.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class CommentRead extends BusinessLayer
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
				$_post_idPost = $this->getRequest("idPost");
			
				$statement = $this->m_db->prepare("SELECT * FROM post WHERE idPost = ?");
				if($statement->execute(array($_post_idPost)))
				{
					if($statement->rowCount() == 1)
					{
						$statement = $this->m_db->prepare("SELECT * FROM comment WHERE post_idPost = ?");
						if($statement && $statement->execute(array($_post_idPost)))
						{
							$this->addData($statement->fetchAll(PDO::FETCH_ASSOC));
						}
						else
						{
							$this->setCode(27); // CONFLICT: database error
						}
					}
					else
					{
						$this->addData(array("msg" => 'Post does not exist'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong post id
					}
				}
				else
				{
					$this->setCode(18); // BAD REQUEST
				}
			}
			else
			{
				$this->setCode(23); // METHOD NOT ALLOWED: Only GET
			}
		}
		catch(PDOException $e)
		{
			if(DEBUG) $this->addData(array("msg" => $e->getMessage()));
			$this->setCode(36); // INTERNAL SERVER ERROR
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new CommentRead();
$api->run();
?>
