<?php
/* @File: account/subscribe.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class AccountSubscribe extends BusinessLayer
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
				$_firstname = $this->getRequest("firstname");
				$_lastname = $this->getRequest("lastname");
				$_email = $this->getRequest("email");
			 	$_password = hash('sha256', $this->getRequest("password")); //do not forget to hash password before saving !
				$_gender = $this->getRequest("gender");
				$_birthdate = $this->getRequest("birthdate");
				$_createdDate = date("Y-m-d H:i:s");

				$params = array(
								":firstname" => $_firstname,
								":lastname" => $_lastname,
								":email" => $_email,
								":password" => $_password,
								":gender" => $_gender,
								":birthdate" => $_birthdate,
								":createdDate" => $_createdDate
								);

				$statement = $this->m_db->prepare("SELECT * FROM user WHERE email = ?");
				if($statement->execute(array($_email)))
				{
					if(count($statement->fetch()) != 0)
					{
						$statement = $this->m_db->prepare("INSERT INTO user (firstname, lastname, email, password, gender, birthdate, createdDate) VALUES (:firstname, :lastname, :email, :password, :gender, :birthdate, :createdDate)");
						if($statement && $statement->execute($params))
						{
							$_id = $this->m_db->lastInsertId();

							$this->addData(array("idUser" => $_id,
													"token" => $this->getToken($_id),
													"createdDate" => $_createdDate));
						}
						else
						{
							$this->setCode(27); //Error adding user
						}
					}
					else
					{
						$this->addData(array("error" => "User already exist"));
						$this->setCode(24); //Error user already exist
					}
				}
				else
				{
					$this->addData(array("PDO error" => $statement->errorCode()));
					$this->addData(array("error" => "Bad request"));
					$this->setCode(24); // Bad request
				}
			}
			else
			{
				$this->setCode(23); //Request method not accepted
			}
		}
		catch(PDOException $e)
		{
			$this->addData(array("PDO error" => $statement->errorCode()));
			$this->setCode(36); //Server error
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new AccountSubscribe();
$api->run();
?>
