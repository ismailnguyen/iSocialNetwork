<?php
/* @File: friendship/read.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class PostHashtag extends BusinessLayer
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
				
				$query = "SELECT p.idPost,
									p.user_idUser
									p.content,
									p.createdDate
						
						FROM post
						
						INNER JOIN comment c
							ON c.post_idPost = p.idPost
						
						INNER JOIN comment_tag t
							ON t.comment_idTag = c.idComment
						
						WHERE";
							
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

$api = new PostHashtag();
$api->run();
?>
