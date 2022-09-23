<?php
session_start();
$member = isset($_SESSION['member']);
if (empty($_SESSION['member']) || !isset($_SESSION['member'])) {
    header("Location: login.php"); 
    exit;
  } else {
    $member = $_SESSION['member'];
}

