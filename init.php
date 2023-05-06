<?php
include_once("admin/connect.php");
session_start();

date_default_timezone_set('Africa/Cairo');

if (isset($_SESSION['username'])) {
  // Check From Your Permissions.
  $username = $_SESSION['username'];
  $stm = $connect->prepare("SELECT admin, img_profile FROM  `users` WHERE username='$username'");
  $stm->execute();
  $info_main_user = $stm->fetch(PDO::FETCH_NUM);
  $permission = $info_main_user[0];
  $main_img_profile = $info_main_user[1];
}