<?php
include("admin/init.php");

// Time Expected Arrive => +2D.
$date = date_create();
date_modify($date, "+2Days");
$time_expected_arrive = date_format($date, "Y/m/d");

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
} else {
  header("Location: 404.php");
  exit();
}

// Get All Information About All Orders.
if (isset($_GET['last_order'])) {
  $site_order = 'last_order';
  $stm = $connect->prepare("SELECT users.FullName, users.username, users.img_profile, users.location, order_number, sum(orders.price) all_price, count(id_product) as count_products, DATE(DATE_SUB(time_expected, INTERVAL 1 DAY)) As time_to_me, DATE(time_expected) as time_expected, orders.status FROM `orders`, `product`, `users` WHERE orders.username='$username' AND (random_product = id_product) AND (orders.username = users.username) AND (orders.status > 1) GROUP BY order_number ORDER BY `orders`.`status` ASC;");
} else {
  $site_order = 'new_order';
  $stm = $connect->prepare("SELECT product.random_product, orders.id, product.name, orders.price, orders.count, users.FullName, time_ordering FROM `orders`
  INNER JOIN product, users WHERE (product.random_product = orders.id_product) AND (product.username_add = users.username) AND (orders.username = '$username') AND (orders.status = '0')
    ORDER BY `orders`.`time_ordering` DESC;");
}
$stm->execute();
$orders = $stm->fetchAll(PDO::FETCH_OBJ);

// If User Click Buy.
if (isset($_POST['buy_now'])) {
  $stm = $connect->prepare("SELECT LENGTH(cart) as length_cart FROM `users` WHERE username = '$username';");
  $stm->execute();
  $length_cart = $stm->fetch(PDO::FETCH_OBJ)->length_cart;
  if ($length_cart > 6) {
    $or_num = rand(10, 10000);
    foreach ($orders as $or) {
      $stm = $connect->prepare("UPDATE `orders` SET `order_number` = '$or_num', `status` = '1', `time_ordering` = current_timestamp(), `time_expected` = '$time_expected_arrive' WHERE `id_product` = '$or->random_product' AND (username = '$username');
    UPDATE `product` SET `count_pay` = count_pay + 1, `count_allow` = count_allow - 1 WHERE `product`.`random_product` = '$or->random_product';");
      $stm->execute();
    }
    header("Refresh: 0;");
  } else {
    $errors[] = 'You need to fill in the payment information first.';
  }
}

// Delete One Product
if (isset($_POST['delete_product'])) {
  $delete_product = $_POST['delete_product'];
  $stm = $connect->prepare("DELETE FROM `orders` WHERE `orders`.`id` = $delete_product");
  $stm->execute();
  header("Refresh: 0;");
}

// Delete All Product
if (isset($_POST['delete_all_product'])) {
  $stm = $connect->prepare("DELETE FROM `orders` WHERE `orders`.`status` = '0' AND (username = '$username')");
  $stm->execute();
  header("Refresh: 0;");
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
  <link rel="stylesheet" href="layout/css/basket.css">
  <title>Ordering</title>
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
  <!-- Start content -->
  <div class="content container mt-2 mb-2">
    <?php
    if ($site_order == 'last_order') {
      echo '<h2 class="text-center">Last Order</h2>';
    } else {
      echo '<h2 class="text-center">Order Basket</h2>';
    }
    ?>
    <div class="parent">
      <?php
      if ($site_order == 'new_order') {
        $price_all_product = 1;
        foreach ($orders as $ord) {
          $price_all_product += $ord->price * $ord->count;
          echo "
          <div class='box'>
            <div class='text'>
              <span><b>Title: </b>" . $ord->name . "</span>
              <span title='price For One'><b>price For One: </b>" . $ord->price . "$</span>
            </div>";
          echo "
            <form action='' method='POST' class='info'>
              <input type='submit' value='$ord->id' name='delete_product' id='$ord->id' style='display:none;'>
              <label for='$ord->id' class='btn btn-danger w-100 mb-1'>Delete</label>
            </form>
          </div>
          ";
        }
      } elseif ($site_order == 'last_order') {
        foreach ($orders as $ord) {
          echo "
          <div class='box'>
            <div class='text'>
              <span><b>Title: </b>" . $ord->count_products . " Products</span>
              <span title='price For One'><b>price For One: </b>" . $ord->all_price . "$</span>
            </div>
            <div class='info'>
            <a href='#$ord->order_number' class='btn btn-info' data-bs-toggle='modal'>Show</a>";
          if ($ord->status == 1 || $ord->status == 2) {
            echo "<span class='btn btn-info'>In Way</span>";
          } elseif ($ord->status == 4) {
            echo "<span class='btn btn-success'>Arrived</span>";
          } elseif ($ord->status == 3) {
            echo "<span class='btn btn-danger'>Rejected</span>";
          }
          echo "
            </div>
            <div class='modal fade modal-lg' id='$ord->order_number' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Information About Order..</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                  </div>
                  <div class='modal-body'>
                    <div class='parent'>";
          // Get All Info About One Product In The Order.
          $stm = $connect->prepare("SELECT product.name, orders.price, users.FullName, orders.count as count, product.img_bg FROM `orders`, `product`, `users` WHERE order_number='$ord->order_number' AND (random_product = id_product)  AND (users.username = product.username_add);");
          $stm->execute();
          $pro_order = $stm->fetchAll(PDO::FETCH_OBJ);
          foreach ($pro_order as $pro_o) {
            echo "
                        <div class='product'>
                          <div class='text'>
                            <img src='admin/layout/images/product_img/$pro_o->img_bg' onerror='this.onerror=null;this.src=`admin/layout/images/bad_img.jpg;' class='img'>
                            <div>
                              <p class='m-0 w-75'>$pro_o->name</p>
                              <b style='font-size:0.9rem'>Seller: </b><a href='profile.php?id=$ord->username'>$pro_o->FullName</a>
                            </div>
                          </div>
                          <div class='info'>
                            <b class='price'>$pro_o->price$</b>
                            <small><b>Count: </b>$pro_o->count</small>
                            </div>
                          </div>";
          }
          echo "
                          <hr class='m-0'>
                          <div class='product'>
                            <div class='text'>
                              <img src='admin/layout/images/img_user/$ord->img_profile' onerror='this.onerror=null;this.src=`admin/layout/images/img_user/someone.png`;' class='img'>
                              <div>
                                <b class='m-0'>$ord->FullName</b>
                                <p class='m-0'>Location: $ord->location</p>
                              </div>
                            </div>
                            <div class='info'>
                              <a href='profile.php?id=$ord->username' target='_blank' class='btn btn-secondary text-light'><i class='fa-solid fa-user me-1'></i>Profile</a>
                            </div>  
                          </div>
                        </div>
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                  </div>
                </div>
              </div>
            </div>
          </div>";
        }
      }
      ?>
    </div>
    <?php
    if (!empty($orders) && $site_order == 'new_order') {
      echo "
          <form class='buy' action='' method='POST'>
            <a href='#buy_now' name='send' class='btn btn-success mb-3' data-bs-toggle='modal'>Buy Now</a>
            <input type='submit' class='btn btn-danger' name='delete_all_product' value='Delete All'>
            <!-- Modal => Order Now -->
            <div class='modal fade' id='buy_now' tabindex='-1' aria-labelledby='buy_now_lable' aria-hidden='true'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='buy_now_lable'>Are You Sure?..</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                  </div>
                  <div class='modal-body'>
                    <p class='m-0'><b>Price All Your Product:</b> $price_all_product$</p>
                    <p class='m-0'><b>Expected Arrival Time:</b> $time_expected_arrive</p>
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                    <input type='submit' class='btn btn-success' name='buy_now' value='Buy Now'>
                  </div>
                </div>
              </div>
            </div>
          </form>
        ";
    } elseif (empty($orders)) {
      echo "<h4 class='text-center' style='height: 50vh; display: flex; align-items: center; justify-content: center; opacity: 0.7;'>You Don't Have Any Thing in Your Basket.</h4>";
    }
    ?>
  </div>
  <!-- End content -->

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>