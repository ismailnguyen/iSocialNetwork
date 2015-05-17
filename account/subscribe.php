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
				$_firstname = getRequest("firstname");
				$_lastname = getRequest("firstname");
				$_email = getRequest("email");
			 	$_password = hash(getRequest("password")); //do not forget to hash password before saving !
				$_gender = getRequest("gender");
				$_birthdate = getRequest("birthdate");
				$_createdDate = date();

				$params = array(
								":firstname" => $_firstname,
								":lastname" => $_lastname,
								":email" => $_email,
								":password" => $_password,
								":gender" => $_gender,
								":birtdhdate" => $_birthdate,
								":createdDate" => $_createdDate
								);

				$statement = $m_db->prepare("SELECT * FROM user WHERE email = ?");
				if($statement->execute(array($_email)) && count($statement->fetch()) == 0)
				{
					$statement = $m_db->prepare("INSERT INTO user (firstname, lastname, email, password, gender, birthdate, createdDate) VALUES (:firstname, :lastname, :email, :password, :gender, :birthdate, :createdDate)");
					if($statement && $statement->execute($params))
					{
						$_id = $m_db->lastInsertId();

						$this->addData(array("idUser" => $_id, "token" => getToken($_id), "createdDate" => $_createdDate));
					}
					else
					{
						$this->setCode(27); //Error adding user
					}
				}
				else
				{
					$this->setCode(24); //Error user already exist
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

$api = new AccountSubscribe();
$api->run();
?>
