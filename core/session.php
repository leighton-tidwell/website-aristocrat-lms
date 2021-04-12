<?php
ini_set('session.cookie_httponly','1'); // mitigate xss
ini_set('session.use_only_cookies','1'); // prevent session fixation
ini_set('session.entropy_file','/dev/urandom/'); // better entropy source
ini_set('session.entropy_length','512'); // better entropy source
ini_set('cookie_lifetime','0'); // smaller exploitation window
ini_set('session.cookie_secure','1'); // owasp a9 violations
ini_set('session.use_trans_sid','0'); 

$session_name = "sid"; // session name
$secure = false; // https or no
$httponly = true; // no javascript
$lifeTime = 3600;
$domain = $_SERVER['SERVER_NAME'];
$authenticator = hash_hmac('sha256', "" . $_SERVER['HTTP_USER_AGENT'] . "|" . $_SERVER['REMOTE_ADDR'] . "");
$key = hash_hmac('sha256',"" . $_SERVER['REMOTE_ADDR'] . "|" . $_SERVER['HTTP_USER_AGENT'] . "");

$cookieParams = session_get_cookie_params(); // get current cookie params
session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
session_name($session_name); // set name to one above
session_start();

?>
