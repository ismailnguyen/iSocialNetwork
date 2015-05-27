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

				$statement = $this->m_db->prepare("SELECT *
													
													FROM user
													
													WHERE email = :email
														AND password = :password");
														
				if($statement->execute($params))
				{
					if($statement->rowCount() == 1)
					{
						$_result = $statement->fetch(PDO::FETCH_ASSOC);
											
						$this->addData(array(
											"idUser" => $_result['idUser'],
											"token" => $this->getToken($_result['idUser']),
											"firstname" => $_result['firstname'],
											"lastname" => $_result['lastname'],
											"email" => $_result['email'],
											"password" => $_result['password'],
											"gender" => $_result['gender'],
											"birthdate" => $_result['birthdate'],
											"createdDate" => $_result['createdDate']
											));						
											
						$_SESSION['idUser'.$_result['idUser']] = $this->getToken($_result['idUser']);
					}
					else
					{
						$this->addData(array("msg" => 'Wrong email/password'));
						$this->setCode(9); // NOT ACCEPTABLE: Wrong email/password
					}
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

$api = new AccountLogin();
$api->run();
?>
