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
			if(getMethod() == "POST")
	    	{
				$_email = getRequest("email");
			 	$_password = hash(getRequest("password")); //do not forget to hash password before saving !

        		$params = array(
                        		":email" => $_email,
					            ":password" => $_password
                        		);

				$statement = $m_db->prepare("SELECT * FROM User WHERE email = :email AND password = :password");
				if($statement->execute($params))
				{
					$result = $statement->fetch();
				  
          			$this->addData(array("idUser" => $result['id'], "token" => getToken($result['id'])));
				}
				else
				{
					$this->setCode(24); //Wrong email/password
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

$api = new AccountLogin();
$api->run();
?>
