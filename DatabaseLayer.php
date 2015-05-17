<?php
/* @File: databaseLayer.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */
 
include("Conf.inc.php");

class DatabaseLayer
{
    private static $m_instance = null;
    private $m_pdo = null;

    private function __construct()
    {
        try
        {
            $this->m_pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
            //$this->m_pdo = new PDO('mysql:host=localhost;dbname=myDB', 'root', '');
			$this->m_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            throw new Exception('PDO Error: '.$e->getMessage());
        }
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if(is_null(self::$m_instance))
            self::$m_instance = new self();

        return self::$m_instance;
    }
    
    public function getPDO()
    {
      return $this->m_pdo;
    }
}
?>
