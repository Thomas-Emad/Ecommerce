<?php
include('init.php');

if (isset($_SESSION['username'])) {
  header('Location: index.php');
  exit();
}

if (isset($_POST['send'])) {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
  $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
  $hash_pass = sha1($pass);

  if (strlen($email) < 5) {
    $errors[] = 'You Need Write Email.';
  }
  if (strlen($name) <= 3) {
    $errors[] = 'Name Should Greater Than or Equal to 3 Letters.';
  }
  if (strlen($pass) < 3) {
    $errors[] = 'Password Should Greater Than 3 Letters.';
  }

  // Check From Data
  $stm = $connect->prepare('SELECT username FROM `users` WHERE email = ?');
  $stm->execute([$email]);
  $row = $stm->fetch();
  if ($stm->rowCount() > 0) {
    $errors[] = 'We Have That\' Email.';
  }
  if (!isset($errors)) {
    $username = rand(1000, 1000000);
    $stm = $connect->prepare('INSERT INTO `users` (`id`, `username`, `password`, `email`, `img_profile`, `FullName`, `location`, `create_at`, `active`, `admin`, `status`, `cart`)
      VALUES (NULL, ?, ?, ?, "", ?, "", current_timestamp(), "1", "0", "0", "")');
    $stm->execute([$username, $hash_pass, $email, $name]);
    $row = $stm->fetch();
    $_SESSION['username'] = $username;
    header('Location: index.php');
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
  <title>Register</title>
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
      <h2 class="text-center mb-4">Register</h2>
      <div class="form-floating mb-2">
        <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com" value='<?php if (isset($email)):
          echo $email;
        endif; ?>'>
        <label for="floatingInput">Email address</label>
      </div>
      <div class="form-floating mb-2">
        <input type="text" class="form-control" name="name" id="floatingInput" placeholder="Full Name" value='<?php if (isset($name)):
          echo $name;
        endif; ?>'>
        <label for="floatingInput">Full Name</label>
      </div>
      <div class="form-floating mb-2">
        <input type="password" class="form-control" name="pass" id="floatingPassword" placeholder="Password">
        <label for="floatingPassword">Password</label>
      </div>
      <input type="submit" name="send" class="btn btn-primary w-100 mb-2" value="Register">
      <a href="login.php" class='btn btn-secondary w-100'>Login</a>
    </form>
  </div>


  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>