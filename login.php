<?php
include('init.php');

if (isset($_SESSION['username'])) {
  header('Location: index.php');
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
    header('Location: index.php');
    echo 'Good';
  } else {
    $errors[] = 'Error logging in.';
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
  <link rel="stylesheet" href="layout/css/navbar.css">
  <link rel="stylesheet" href="layout/css/style.css">
  <title>Login</title>
</head>

<body>

  <!-- Start navbar -->
  <?php include('layout/templates/navbar.php'); ?>
  <!-- End navbar -->

  <?php
  if (isset($errors)) {
    echo "
    <div class='alert alert-warning d-flex align-items-center' role='alert' style='position: absolute; bottom: 10px; right: 10px; width: 80%; z-index: 10000;'>
      <i class='fa-solid fa-triangle-exclamation me-2'></i>
      <div>";
    foreach ($errors as $error) {
      echo '- ' . $error . '<br>';
    }
    echo " </div>
    </div>  
    ";
  }
  ?>

  <div class="box_login">
    <form action="" method="POST">
      <h2 class="text-center mb-4">Login</h2>
      <div class="form-floating mb-2">
        <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com" value='<?php if (isset($email)):
          echo $email;
        endif; ?>'>
        <label for="floatingInput">Email address</label>
      </div>
      <div class="form-floating mb-2">
        <input type="password" class="form-control" name="pass" id="floatingPassword" placeholder="Password">
        <label for="floatingPassword">Password</label>
      </div>
      <input type="submit" name="send" class="btn btn-primary w-100 mb-2" value="Login">
      <a href="register.php" class="btn btn-success w-100">Register</a>
      <a href="admin/index.php" class='text-dark'>Sign in as Seller</a>
    </form>
  </div>


  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>