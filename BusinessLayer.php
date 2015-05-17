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

	protected $m_db = null; //PDO Instance

    public function __construct($_useToken=true)
    {
		try
		{
			$this->m_db = DatabaseLayer::getInstance()->getPDO();

			$this->setRequest();

			$this->m_output = ($this->getRequest("output") != null && $this->getRequest("output") == "xml") ? "xml" : "json";

			if($_useToken && !checkToken($this->getRequest("token")))
			{
				$this->setCode(30); // Invalid token !
				$this->response();
			}
			else
			{
				$this->m_idUser = $this->getRequest("idUser");
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

    public function checkToken(String $token)
    {
        if($this->getToken($this->getRequest("idUser")) == token)
            return true;

        return false;
    }

	public function addData($_data)
	{
		//array_push($this->m_data, $data);
		
		foreach($_data as $_key => $_value)
		{
			$this->$m_data[$_key] = $_value;
		}
	}

	public function getMethod()
	{
		return $this->m_request["method"];
	}

	public function getRequest($key)
	{
		try
		{
			return (array_key_exists($key, $this->m_request)) ? htmlentities($this->m_request[$key]) : null;
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
			$this->m_request["method"] = $_SERVER['REQUEST_METHOD'];
			//var_dump($this->m_request); // trick to show whole request content
			
			switch($this->m_request["method"])
			{
				case "POST":
					$_method = $_POST; //&$_POST
					break;
				case "GET":
					$_method = $_GET; //&$_GET
					break;
				case "PUT":
					$_method = $_PUT; //&$_PUT
					break;
				case "DELETE":
					$_method = $_DELETE; //&$_DELETE
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

	public function setCode($code)
	{
		$this->m_code = $code;
	}

    private function getError($type) //type: code, status
    {
        $status = Array(
                  		    0 => array('code' => 100, 'status' => 'Continue'),
                  		    1 => array('code' => 101, 'status' => 'Switching Protocols'),
                  		    3 => array('code' => 200, 'status' => 'OK'),
                  		    4 => array('code' => 201, 'status' => 'Created'),
                  		    5 => array('code' => 202, 'status' => 'Accepted'),
                  		    6 => array('code' => 203, 'status' => 'Non-Authoritative Information'),
                  		    7 => array('code' => 204, 'status' => 'No Content'),
                  		    8 => array('code' => 205, 'status' => 'Reset Content'),
                  		    9 => array('code' => 206, 'status' => 'Partial Content'),
                  		    10 => array('code' => 300, 'status' => 'Multiple Choices'),
                  		    11 => array('code' => 301, 'status' => 'Moved Permanently'),
                  		    12 => array('code' => 302, 'status' => 'Found'),
                  		    13 => array('code' => 303, 'status' => 'See Other'),
                  		    14 => array('code' => 304, 'status' => 'Not Modified'),
                  		    15 => array('code' => 305, 'status' => 'Use Proxy'),
                  		    16 => array('code' => 306, 'status' => '(Unused)'),
                  		    17 => array('code' => 307, 'status' => 'Temporary Redirect'),
                  		    18 => array('code' => 400, 'status' => 'Bad Request'),
                  		    19 => array('code' => 401, 'status' => 'Unauthorized'),
                  		    20 => array('code' => 402, 'status' => 'Payment Required'),
                  		    21 => array('code' => 403, 'status' => 'Forbidden'),
                  		    22 => array('code' => 404, 'status' => 'Not Found'),
                  		    23 => array('code' => 405, 'status' => 'Method Not Allowed'),
                  		    24 => array('code' => 406, 'status' => 'Not Acceptable'),
                  		    25 => array('code' => 407, 'status' => 'Proxy Authentication Required'),
                  		    26 => array('code' => 408, 'status' => 'Request Timeout'),
                  		    27 => array('code' => 409, 'status' => 'Conflict'),
                  		    28 => array('code' => 410, 'status' => 'Gone'),
                  		    29 => array('code' => 411, 'status' => 'Length Required'),
                  		    30 => array('code' => 412, 'status' => 'Precondition Failed'),
                  		    31 => array('code' => 413, 'status' => 'Request Entity Too Large'),
                  		    32 => array('code' => 414, 'status' => 'Request-URI Too Long'),
                  		    33 => array('code' => 415, 'status' => 'Unsupported Media Type'),
                  		    34 => array('code' => 416, 'status' => 'Requested Range Not Satisfiable'),
                  		    35 => array('code' => 417, 'status' => 'Expectation Failed'),
                  		    36 => array('code' => 500, 'status' => 'Internal Server Error'),
                  		    37 => array('code' => 501, 'status' => 'Not Implemented'),
                  		    38 => array('code' => 502, 'status' => 'Bad Gateway'),
                  		    39 => array('code' => 503, 'status' => 'Service Unavailable'),
                  		    40 => array('code' => 504, 'status' => 'Gateway Timeout'),
                  		    41 => array('code' => 505, 'status' => 'HTTP Version Not Supported')
                  		);

        return (isset($status[$this->m_code])) ? $status[$this->m_code][$type] : "";
    }

    public function response()
    {
		if($this->m_output == "xml")
  		{
  		  header("HTTP/1.1 ".$this->getError('code')." ".$this->getError('status'));
  			header('Content-type: text/xml');
  			
			echo '<?xml version="1.0"?>';
			
			echo '<xml>';
			
  			echo '<code>';
  			echo $this->m_code;
  			echo '</code>';
  			
  			echo '<result>';
			
			if($this->m_data != null)
  			{
				foreach($this->m_data as $index => $post)
				{
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
				}
			}
			else
			{
				echo 'null';
			}
  			
  			echo '</result>';
			
			echo '</xml>';
  		}
  		else
  		{
        	header("HTTP/1.1 ".$this->getError('code')." ".$this->getError('status'));
        	header("Content-Type: application/json");
        
        	echo json_encode(array("code" => $this->m_code, "result" => ($this->m_data != null) ? $this->m_data : null));
  		}
		
		unset($this->m_db);
		die(); // End all operation
    }
}
?>
