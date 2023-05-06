<?php
include_once("connect.php");
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

  function tran_per()
  {
    global $permission;
    if ($permission == 0 || $permission == 3) {
      header("Location: ../index.php");
    }
  }

  // Add New Message In NEWS.
  function add_message($username, $message, $refresh)
  {
    global $connect;
    $stm = $connect->prepare("INSERT INTO `news` (`id`, `username`, `message`, `time_at`) VALUES (NULL, '$username', '$message', current_timestamp())");
    $stm->execute();
    if ($refresh == true) {
      header("Refresh:0;");
    }
  }

} else {
  header("Location: index.php");
}