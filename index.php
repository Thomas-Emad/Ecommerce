<?php
include('init.php');

// Get All Product
$stm = $connect->prepare("SELECT name AS title, random_product, img_bg, price, time_add FROM `product` ORDER BY `product`.`time_add` DESC LIMIT 10");
$stm->execute();
$product = $stm->fetchAll(PDO::FETCH_OBJ);

// Get Ads From DB
$stm = $connect->prepare("SELECT * FROM `ads`");
$stm->execute();
$ads = $stm->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="layout/css/bootstrap.min.css">
  <link rel="stylesheet" href="layout/css/navbar.css">
  <link rel="stylesheet" href="layout/css/style.css">
  <title>Home</title>
</head>

<body>
  <!-- Start navbar -->
  <?php include('layout/templates/navbar.php'); ?>
  <!-- End navbar -->

  <!-- Start To Silder -->
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
        aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
        aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
        aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner b-r-5">
      <?php
      $num = 0;
      foreach ($ads as $a) {
        if ($num == 1) {
          echo "
          <div class='carousel-item active'>
            <a href='$a->link'><img src='admin/layout/images/$a->img' class='d-block w-100' alt='Show'></a>
          </div>
          ";
        } else {
          echo "
          <div class='carousel-item'> 
            <a href='$a->link'><img src='admin/layout/images/$a->img' class='d-block w-100' alt='Show'></a>
          </div>
          ";
        }
        $num++;
      }

      ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
      data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
      data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- End To Silder -->
  <!-- Start prodcut -->
  <div class="prodcut container mt-3">
    <h2 class="title_section">New prodcut</h2>
    <div class="parent">
      <?php
      foreach ($product as $pro) {
        echo "
          <a href='product.php?id=" . $pro->random_product . "' class='box'>
            <img src='admin/layout/images/product_img/" . $pro->img_bg . "' alt='img product'>
            <div class='text'>
              <span class='title'>" . $pro->title . "</span>
              <span class='salary'>$" . $pro->price . "</span>
            </div>
          </a>
          ";
      }
      ?>
    </div>
  </div>
  <!-- End prodcut -->

  <!-- Start Footer  -->
  <?php include('layout/templates/footer.php'); ?>

  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>