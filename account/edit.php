<?php
/* @File: account/edit.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class AccountEdit extends BusinessLayer
{
	public function __construct()
	{
		parent::__construct(NO_TOKEN);
	}

	public function run()
	{
		try
		{
			if($this->getMethod() == "POST")
			{
				$_user_idUser = $this->getIdUser();
				$_firstname = $this->getRequest("firstname");
				$_lastname = $this->getRequest("lastname");
				$_email = $this->getRequest("email");
			 	$_password = hash('sha256', $this->getRequest("password")); // hash password for safe database storage
				$_gender = $this->getRequest("gender");
				$_birthdate = $this->getRequest("birthdate");

				$params = array(
								":idUser" => $_user_idUser,
								":firstname" => $_firstname,
								":lastname" => $_lastname,
								":email" => $_email,
								":password" => $_password,
								":gender" => $_gender,
								":birthdate" => $_birthdate,
								);
													
				if($statement->execute(array($params)))
				{

					$statement = $this->m_db->prepare("UPDATE user
							
														SET firstname = :firstname, 
															lastname = :lastname, 
															email = :email, 
															password = :password, 
															gender = :gender, 
															birthdate = :birthdate

														WHERE idUser = :idUser");
						
						$this->addData(array(
												"idUser" => $_id,
												"firstname" => $_firstname,
												"lastname" => $_lastname,
												"email" => $_email,
												"password" => $_password,
												"gender" => $_gender,
												"birthdate" => $_birthdate
												));
				}
				else
				{
					$this->setCode(4); // BAD REQUEST
				}
			}
			else
			{
				$this->setCode(8); // METHOD NOT ALLOWED: Only POST
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

$api = new AccountEdit();
$api->run();
?>
