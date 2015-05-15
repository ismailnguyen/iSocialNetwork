<?php
/* @File: post/like.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

require_once 'BusinessLayer.php';

class PostLike extends BusinessLayer
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
				$_User_idUser = getIdUser();
				$_Post_idPost = getRequest("Post_idPost");
				$_createdDate = date();

        $params = array(
                        ":User_idUser" => $_User_idUser,
                        ":Post_idPost" => $_Post_idPost,
                        ":createdDate" => $_createdDate
                        );

				$statement = $m_db->prepare("SELECT * FROM PostLike WHERE User_idUser = :User_idUser AND Post_idPost = :Post_idPost");
				if($statement->execute($params))
				{
				  //Unlike if like already exist
				  $result = $statement->fetch();
				  
					$statement = $m_db->prepare("DELETE FROM PostLike WHERE idPostLike = ?");
					if(!($statement && $statement->execute(array($result['idPostLike']))))
          {
						$this->setCode(27); //Error removing like
					}
				}
				else
				{
					//Like
					$statement = $m_db->prepare("INSERT INTO PostLike (User_idUser, Post_idPost, createdDate) VALUES (:User_idUser, :Post_idPost, :createdDate)");
					if($statement && $statement->execute($params))
          {
            $_idPostLike = $m_db->lastInsertId();

            $this->addData(array("idPostLike" => $_idPostLike,  "createdDate" => $_createdDate));
          }
					else
					{
						$this->setCode(27); //Error adding like
					}
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

$api = new PostLike();
$api->run();
?>
