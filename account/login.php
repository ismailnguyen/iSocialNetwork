<?php
/* @File: account/login.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

include("../BusinessLayer.php");

class AccountLogin extends BusinessLayer
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
				$_email = $this->getRequest("email");
			 	$_password = hash('sha256', $this->getRequest("password")); // hash password before compare with database content

        		$params = array(
                        		":email" => $_email,
					            ":password" => $_password
                        		);

				$statement = $this->m_db->prepare("SELECT * FROM user WHERE email = :email AND password = :password");
				if($statement->execute($params))
				{
					if($statement->rowCount() == 1)
					{
						$_result = $statement->fetch();

						$this->addData(array("idUser" => $_result['idUser'],
												"token" => $this->getToken($_result['idUser'])
												));
					}
					else
					{
						$this->addData(array("msg" => 'Wrong email/password'));
						$this->setCode(24); // NOT ACCEPTABLE: Wrong email/password
					}
				}
				else
				{
					$this->setCode(18); // BAD REQUEST
				}
			}
			else
			{
				$this->setCode(23); // METHOD NOT ALLOWED: Only POST
			}
		}
		catch(PDOException $e)
		{
			$this->setCode(36); // INTERNAL SERVER ERROR
		}
		finally
		{
			$this->response();
		}
	}
}

$api = new AccountLogin();
$api->run();
?>
