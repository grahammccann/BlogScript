<?php

error_reporting(E_ALL);
include('includes/inc-constants.php');
include('includes/inc-DB.php');
$driver = DRIVER;
$host   = HOST;
$user   = USER;
$pass   = PASS;
$data   = DATA;

// INITIAL LOAD ON ALL PAGES
$cookie = isset($_COOKIE['anon_session']) ? $_COOKIE['anon_session'] : NULL;

if (!$cookie)
{
  $cookie = md5(date('Y-m-d H:i:s'));
  setcookie('anon_session', $cookie, NULL, '/');
}
global $ANON_SESSION_ID;
$ANON_SESSION_ID = $cookie;

