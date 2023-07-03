<?php
session_start();
//ログインセッション処理
if (isset($_SESSION['user_id']) && isset($_SESSION['nickname']) && isset($_SESSION['initial_savings']) && isset($_SESSION['username'])) :
  $user_id = $_SESSION['user_id'];
  $username = $_SESSION['username'];
  $nickname = $_SESSION['nickname'];
  $initial_savings = $_SESSION['initial_savings'];
else :
  header('Location: login.php');
  exit();
endif;