<?php
/* @File: tag/remove.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class TagRemove extends BusinessLayer
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
				$_idTag = $this->getRequest("idTag");
				$_user_idUser = $this->getIdUser();				

        		$params = array(
								":idTag" => $_idTag,
								":user_idUser" => $_user_idUser
								);

				$statement = $this->m_db->prepare("SELECT * FROM tag
				
													WHERE idTag = :idTag
														AND user_idUser = :user_idUser");
														
				if($statement->execute($params) && $statement->rowCount() == 1)
				{  
					$statement = $this->m_db->prepare("DELETE FROM tag
					
														WHERE idTag = :idTag
															AND user_idUser = :user_idUser");
					
					if(!($statement && $statement->execute(array($params))))
          			{
						$this->setCode(10); //Error removing tag
					}
				}
				else
				{
					$this->setCode(4); //Bad request, tag does not exist
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

$api = new TagRemove();
$api->run();
?>
