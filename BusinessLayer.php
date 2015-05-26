<?php
/* @File: BusinessLayer.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */
 
include("DatabaseLayer.php");

class BusinessLayer
{
	private $m_code = 0; // Response code
	private $m_data = array(); // Response datas
	private $m_request = array(); // Request datas
	private $m_output; // Output format: xml, json
	private $m_idUser; // Current user id

	public $m_db = null; //PDO Instance

    public function __construct($_useToken=true)
    {
		try
		{
			$this->m_db = DatabaseLayer::getInstance()->getPDO();

			$this->setRequest();

			$this->m_output = ($this->getRequest("output") != null && $this->getRequest("output") == "xml") ? "xml" : "json";
			
			if($_useToken) // If token is needed we check it
			{
				if($this->getRequest("token") != null) // If token is well provided
				{
					if($this->checkToken($this->getRequest("token"))) // If provided token is valid
					{
						$this->m_idUser = $this->getRequest("idUser");
					}
					else
					{
						$this->addData(array("msg" => "Invalid token"));
						$this->setCode(19); // UNAUTHORIZED: Invalid token
						$this->response();
					}
				}
				else
				{
					$this->setCode(18); // Bad request, Need token !
					$this->response();
				}
			}
		}
		catch(Exception $e)
		{
			$this->addData(array("error" => $e->getMessage()));
			$this->setCode(39); // Service unavailable
			$this->response();
		}
    }
	
	public function getIdUser()
	{
		return $this->m_idUser;
	}

    public function getToken($_id)
    {
        return hash('sha256', $_id.TOKEN_KEY);
    }

    public function checkToken($_token)
    {
        if($this->getToken($this->getRequest("idUser")) == $_token)
            return true;

        return false;
    }

	public function addData($_data)
	{
		//array_push($this->m_data, $data);
		
		foreach($_data as $_key => $_value)
		{
			$this->m_data[$_key] = $_value;
		}
	}

	public function getMethod()
	{
		return $this->m_request["method"];
	}

	public function getRequest($_key)
	{
		try
		{
			return (array_key_exists($_key, $this->m_request)) ? htmlentities($this->m_request[$_key]) : null;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
			//$this->addData(array("error" => $e->getMessage()));
        	//$this->setCode(18); // Bad Request
        	//$this->response();
		}
	}

	//get each POST, GET, PUT, DELETE and put it in $m_request like 'request_key => request_value'
	private function setRequest()
	{
		try
		{
			if(isset($_SERVER['PHP_AUTH_USER'])) $this->m_request["idUser"] = $_SERVER['PHP_AUTH_USER'];
			if(isset($_SERVER['PHP_AUTH_PW'])) $this->m_request["token"] =  $_SERVER['PHP_AUTH_PW'];
			
			$this->m_request["method"] = $_SERVER['REQUEST_METHOD'];
			
			if($this->m_request["method"] == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
			{
				if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
				{
					$this->m_request["method"] = 'DELETE';
				}
				elseif($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
					$this->m_request["method"] = 'PUT';
				}
				else
				{
					throw new Exception("Unexpected Header");
				}
			}
		
			switch($this->m_request["method"])
			{
				case "DELETE":
				case "POST":
					$_method = $this->cleanInputs($_POST);
					break;
				case "PUT":
				case "GET":
					$_method = $this->cleanInputs($_GET);
					break;
				default:
					$this->setCode(23); // METHOD NOT ALLOWED
					break;
			}

			foreach($_method as $_key => $_value)
			{
				$this->m_request[$_key] = $_value;
			}
		}
		catch(Exception $e)
		{
			$this->addData(array("error" => $e->getMessage()));
        	$this->setCode(18); // Bad Request
        	$this->response();
		}
	}
	
	private function cleanInputs($_data) {
        $clean_input = Array();
		
        if (is_array($_data))
		{
            foreach ($_data as $k => $v)
			{
                $clean_input[$k] = $this->cleanInputs($v);
            }
        }
		else
		{
            $clean_input = trim(strip_tags($_data));
        }
		
        return $clean_input;
    }


	public function setCode($code)
	{
		$this->m_code = $code;
	}

    private function getError($type) //type: code, status
    {
        $status = array(		
                  		    1 => array(
										'code' => 200,
										'status' => 'OK'
										),

                  		    2 => array(
										'code' => 201,
										'status' => 'Created'
										),

                  		    3 => array(
										'code' => 202,
										'status' => 'Accepted'
										),

                  		    4 => array(
										'code' => 400,
										'status' => 'Bad Request'
										),

                  		    5 => array(
										'code' => 401,
										'status' => 'Unauthorized'
										),

                  		    6 => array(
										'code' => 403,
										'status' => 'Forbidden'
										),

                  		    7 => array(
										'code' => 404,
										'status' => 'Not Found'
										),

                  		    8 => array(
										'code' => 405,
										'status' => 'Method Not Allowed'
										),

                  		    9 => array(
										'code' => 406,
										'status' => 'Not Acceptable'
										),

                  		    10 => array(
										'code' => 409,
										'status' => 'Conflict'
										),

                  		    11 => array(
										'code' => 412,
										'status' => 'Precondition Failed'
										),
							
                  		    12 => array(
										'code' => 417,
										'status' => 'Expectation Failed'
										),
							
                  		    13 => array(
										'code' => 500,
										'status' => 'Internal Server Error'	
										),
							
                  		    14 => array(
										'code' => 501,
										'status' => 'Not Implemented'
										),
							
                  		    15 => array(
										'code' => 502,
										'status' => 'Bad Gateway'
										),
							
                  		    16 => array(
										'code' => 503,
										'status' => 'Service Unavailable'
										)
                  		);

        return isset($status[$this->m_code]) ? $status[$this->m_code][$type] : $status[14][$type];
    }

    public function response()
    {
		if($this->m_output == "xml")
  		{
			header("HTTP/1.1 ".$this->getError('code')." ".$this->getError('status'));
  			header('Content-type: text/xml');
  			
			echo '<?xml version="1.0"?>';
			
			echo '<response>';
			
  			echo '<code>';
  			echo $this->m_code;
  			echo '</code>';
  			
  			echo '<result>';
			
			if($this->m_data != null)
  			{
				foreach($this->m_data as $index => $post)
				{
					echo '<'.$index.'>';
					
					if(is_array($post))
					{
						foreach($post as $key => $value)
						{
							echo '<'.$key.'>';
							
							if(is_array($value))
							{
								foreach($value as $tag => $val)
								{
									echo '<'.$tag.'>'.htmlentities($val).'</'.$tag.'>';
								}
							}
							else
							{
								echo htmlentities($value);
							}
							
							echo '</'.$key.'>';
						}
					}
					else
					{
						echo htmlentities($post);
					}
					
					echo '</'.$index.'>';
				}
			}
			else
			{
				echo 'null';
			}
  			
  			echo '</result>';
			
			echo '</response>';
  		}
  		else
  		{
        	header("HTTP/1.1 ".$this->getError('code')." ".$this->getError('status'));
        	header("Content-Type: application/json");
        
        	echo json_encode(array(
									"code" => $this->m_code,
									"result" => $this->m_data != null ? $this->m_data : null
								));
  		}
		
		unset($this->m_db);
		die(); // End all operation
    }
}
?>
