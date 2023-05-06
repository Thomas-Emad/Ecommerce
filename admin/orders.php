<?php
include_once('init.php');

// Get All Orders
tran_per();
if ($permission > 1) {
  $stm = $connect->prepare("SELECT users.FullName, users.username, users.img_profile, users.location, order_number, sum(orders.price) all_price, count(id_product) as count_products, DATE(DATE_SUB(time_expected, INTERVAL 1 DAY)) As time_to_me, DATE(time_expected) as time_expected, orders.status FROM `orders`, `product`, `users` WHERE  (random_product = id_product) AND (orders.username = users.username) GROUP BY order_number ORDER BY `orders`.`status` ASC;");
} else {
  $stm = $connect->prepare("SELECT users.FullName, users.username, users.img_profile, users.location, order_number, sum(orders.price) all_price, count(id_product) as count_products, DATE(DATE_SUB(time_expected, INTERVAL 1 DAY)) As time_to_me, DATE(time_expected) as time_expected, orders.status FROM `orders`, `product`, `users` WHERE product.username_add='$username' AND (random_product = id_product) AND (orders.username = users.username) GROUP BY order_number ORDER BY `orders`.`status` ASC;");
}
$stm->execute();
$orders = $stm->fetchAll(PDO::FETCH_OBJ);

// Change Status Any Product For [Owner]
if ($permission == 2) {
  if (isset($_POST['status'])) {
    $status = 0;
    $order_number = $_POST['num_order'];
    if ($_POST['status'] == 'Accept') {
      $status = 1;
    } elseif ($_POST['status'] == 'Rejected') {
      $status = 3;
    } elseif ($_POST['status'] == 'Wait') {
      $status = 2;
    } elseif ($_POST['status'] == 'Success') {
      $status = 4;
    }
    $stm = $connect->prepare("UPDATE `orders` SET `status` = '$status' WHERE `orders`.`order_number` = '$order_number';");
    $stm->execute();
    header("Refresh: 0");
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
  <link rel="stylesheet" href="layout/css/orders.css">
  <title>All Orders</title>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>

  <div class="content container" style="height:71vh">
    <div class='title_section'>
      <h2>All Orders</h2>
    </div>
    <div class="parent">
      <?php
      foreach ($orders as $or) {
        if ($permission == 1 && $or->status != 0) {
          echo "
            <div class='box'>
              <div class='text'>
                <div class='img'></div>
                  <div>
                      <b class='m-0'>$or->count_products Product</b>
                      <p class='m-0'>Arrival timer for you: $or->time_to_me</p>
                      <p class='m-0'>Client Arrival Time: $or->time_expected</p>
                  </div>
                </div>
                <div class='info'>
                  <b class='price'>$or->all_price$</b>";
          if ($or->status == 1) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-info'><i class='fa-solid fa-clock me-1'></i>in Way</a>";
          } elseif ($or->status == 2) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-secondary'><i class='fa-solid fa-calendar-days me-1'></i>Wait</a>";
          } elseif ($or->status == 3) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-danger'><i class='fa-solid fa-triangle-exclamation me-1'></i>Bad</a>";
          } elseif ($or->status == 4) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-success'><i class='fa-solid fa-check me-1'></i>Success</a>";
          }
          echo "<!-- Modal -->
                <div class='modal fade modal-lg' id='$or->order_number' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                          <h1 class='modal-title fs-5' id='exampleModalLabel'>Information About Order..</h1>
                          <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                        <div class='parent'>";

          // Get All Info About One Product In The Order.
          $stm = $connect->prepare("SELECT product.name, orders.price, users.FullName, orders.count as count, product.img_bg FROM `orders`, `product`, `users` WHERE order_number='$or->order_number' AND (random_product = id_product)  AND (users.username = product.username_add);");
          $stm->execute();
          $pro_order = $stm->fetchAll(PDO::FETCH_OBJ);
          foreach ($pro_order as $pro_o) {
            echo "
            <div class='box'>
              <div class='text'>
                <img src='layout/images/product_img/$pro_o->img_bg' class='img'></img>
                <div>
                  <p class='m-0 w-75'>$pro_o->name</p>
                  <b style='font-size:0.9rem'>Seller: </b><a href='../profile.php?id=$or->username'>$pro_o->FullName</a>
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
                            <div class='box'>
                            <div class='text'>
                                <img src='layout/images/img_user/$or->img_profile' class='img'></img>
                                <div>
                                <b class='m-0'>$or->FullName</b>
                                <p class='m-0'>Location: $or->location</p>
                                </div>
                            </div>
                            <div class='info'>
                                <a href='../profile.php?id=$or->username' class='btn btn-secondary text-light'><i class='fa-solid fa-user me-1'></i>Profile</a>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class='modal-footer'>
                          <button type='button' class='btn btn-secondary text-light' data-bs-dismiss='modal'>Close</button>
                        </div>
                    </div>
                    </div>
                </div>
              </div>
            </div>
            ";
        } elseif ($permission > 1) {
          echo "
            <div class='box'>
              <div class='text'>
                <div class='img'></div>
                  <div>
                      <b class='m-0'>$or->count_products Product</b>
                      <p class='m-0'>Arrival timer for you: $or->time_to_me</p>
                      <p class='m-0'>Client Arrival Time: $or->time_expected</p>
                  </div>
                </div>
                <div class='info'>
                  <b class='price'>$or->all_price$</b>";
          if ($or->status == 0) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-success'><i class='fa-solid fa-check me-1'></i>Accept</a>";
          } elseif ($or->status == 1) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-info'><i class='fa-solid fa-clock me-1'></i>in Way</a>";
          } elseif ($or->status == 2) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-secondary'><i class='fa-solid fa-calendar-days me-1'></i>Wait</a>";
          } elseif ($or->status == 3) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-danger'><i class='fa-solid fa-triangle-exclamation me-1'></i>Bad</a>";
          } elseif ($or->status == 4) {
            echo "<a href='#$or->order_number' data-bs-toggle='modal' class='btn btn-success'><i class='fa-solid fa-check me-1'></i>Success</a>";
          }
          echo "<!-- Modal -->
                <div class='modal fade modal-lg' id='$or->order_number' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                          <h1 class='modal-title fs-5' id='exampleModalLabel'>Information About Order..</h1>
                          <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                        <div class='parent'>";
          // Get All Info About One Product In The Order.
          $stm = $connect->prepare("SELECT product.name, orders.price, users.FullName, orders.count as count, product.img_bg FROM `orders`, `product`, `users` WHERE order_number='$or->order_number' AND (random_product = id_product) AND (product.username_add = users.username);");
          $stm->execute();
          $pro_order = $stm->fetchAll(PDO::FETCH_OBJ);
          foreach ($pro_order as $pro_o) {
            echo "
            <div class='box'>
              <div class='text'>
                <img src='layout/images/product_img/$pro_o->img_bg' class='img'></img>
                <div>
                  <p class='m-0 w-75'>$pro_o->name</p>
                  <b style='font-size:0.9rem'>Seller: </b><a href='../profile.php?id=$or->username'>$pro_o->FullName</a>
                </div>
              </div>
              <div class='info'>
                <b class='price'>$pro_o->price$</b>
                <small><b>Count: </b>$pro_o->count</small>
              </div>
            </div>";
          }
          if ($permission == 2) {
            echo "
            <form method='POST' action=''>
              <input type='text' name='num_order' value='$or->order_number' hidden>
              <input type='submit' name='status' class='btn btn-success' value='Accept'>
              <input type='submit' name='status' class='btn btn-danger' value='Rejected'>
              <input type='submit' name='status' class='btn btn-secondary' value='Wait'>
              <input type='submit' name='status' class='btn btn-success' value='Success'>
            </form>
            ";
          }
          echo " 
                            <hr class='m-0'>
                            <div class='box'>
                              <div class='text'>
                                <img src='layout/images/img_user/$or->img_profile' class='img'></img>
                                <div>
                                <b class='m-0'>$or->FullName</b>
                                <p class='m-0'>Location: $or->location</p>
                              </div>
                            </div>
                            <div class='info'>
                                <a href='../profile?id=$or->username' class='btn btn-secondary text-light'><i class='fa-solid fa-user me-1'></i>Profile</a>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class='modal-footer'>
                          <button type='button' class='btn btn-secondary text-light' data-bs-dismiss='modal'>Close</button>
                        </div>
                    </div>
                    </div>
                </div>
              </div>
            </div>
            ";
        }
      }

      ?>
    </div>
  </div>

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
</body>

</html>