<?php
include_once("connect.php");
session_start();

if (isset($_SESSION['username'])) {
  header('Location: dashboard.php');
  exit();
}

if (isset($_POST['send'])) {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
  $hash_pass = sha1($pass);

  // Check From Data
  $stm = $connect->prepare('SELECT username FROM `users` WHERE email = ? AND password = ?');
  $stm->execute([$email, $hash_pass]);
  $row = $stm->fetch();
  if ($stm->rowCount() > 0) {
    $_SESSION['username'] = $row['username'];
    header('Location: dashboard.php');
  }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="layout/css/all.min.css">
  <link rel="stylesheet" href="layout/css/bootstrap.min.css">
  <link rel="stylesheet" href="layout/css/style.css">
  <title>Dashboard</title>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>

  <div class="box_login">
    <form action="" method="POST">
      <h2 class="text-center mb-4">Login To Dashboard</h2>
      <div class="form-floating mb-2">
        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com">
        <label for="floatingInput">Email address</label>
      </div>
      <div class="form-floating mb-2">
        <input type="password" name="pass" class="form-control" id="floatingPassword" placeholder="Password">
        <label for="floatingPassword">Password</label>
      </div>
      <input type="submit" name="send" class="btn btn-primary w-100" value="Login">
    </form>
  </div>

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>