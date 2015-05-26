<?php
/* @File: utils.php
 *
 *              --- API iSocialNetwork ---
 *
 * @Author: Fabien GAMELIN, Ismaïl NGUYEN, Bruno VACQUEREL
 *
 *               ESGI - 3A AL - 2014/2015
 */

session_start();
 
define("DEBUG", true); //false


define("DB_HOST", "localhost"); // database host
define("DB_USER", "root"); // database username
define("DB_PASSWORD", ""); // database password
define("DB_NAME", "iSocialNetwork"); // database name

define("TOKEN_KEY", "3Tr4hz23HErte"); // token key "grain de sable"
define("NO_TOKEN", false); // do not use token


if(DEBUG)
{
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
?>
