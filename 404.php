<?php include_once('init.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="layout/css/all.min.css">
  <link rel="stylesheet" href="layout/css/bootstrap.min.css">
  <link rel="stylesheet" href="layout/css/navbar.css">
  <title>404 Page</title>
  <style>
    .box {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .box img {
      width: 30%;
    }

    .box p {
      font-size: 2rem;
      font-weight: bold
    }
  </style>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>

  <div class="box mt-4">
    <img src="layout/images/404-error.png" alt="404-error">
    <p>Not Fount Page</p>
  </div>

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>