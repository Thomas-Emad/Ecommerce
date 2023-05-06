<?php
include('init.php');

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
}

// Get ID Product
$name_product = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

// All Information About Product.
$stm = $connect->prepare("SELECT product.name AS title, price, users.FullName, users.username as sold, brand AS brand, img_bg, imgs_product, processor, graphics, ram, storage, color, des, count_pay FROM `product` 
                            INNER JOIN users WHERE (random_product = '$name_product') AND (username_add = username);");
$stm->execute();
$v_p = $stm->fetchObject();

// If Have And Problem Transformation To 404 Error Page.
if (empty($v_p)) {
  header("Location: 404.php");
  exit();
}

// If User click To "Add To Card" => After Add product Refresh Page, "order_now" => After Add Go To Basket Page.
if (isset($_POST['add_to_basket']) || isset($_POST['order_now'])) {
  $time_now = date_format(date_create(), "Y-m-d");
  $stm = $connect->prepare("INSERT INTO `orders` (`id`, `id_product`, `order_number`, `price`, `count`, `username`, `status`, `time_ordering`, `time_expected`, `time_arrived`)
    VALUES (NULL, ?, '0', ?, '1', ?, '0', ?, NULL, NULL);");
  $stm->execute([$name_product, $v_p->price, $username, $time_now]);

  if (isset($_POST['order_now'])) {
    header("Location: basket.php");
  } else {
    header("Refresh:0;");
  }
}

// Get All Comments For This Product.
if (isset($permission) && $permission == 2) {
  $stm = $connect->prepare("SELECT comments.id, users.FullName, users.img_profile, comments.com, comments.time_at, comments.status FROM `comments` INNER JOIN users ON (id_user = users.username) AND (id_product = '$name_product');");
} else {
  $stm = $connect->prepare("SELECT comments.id, users.FullName, users.img_profile, comments.com, comments.time_at FROM `comments` INNER JOIN users ON (id_user = users.username) AND  (comments.status = 1) AND (id_product = '$name_product');");
}
$stm->execute();
$comment = $stm->fetchAll(PDO::FETCH_OBJ);

// Add New Comment 
if (isset($_POST['add_comment'])) {
  $comment_text = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
  $stm = $connect->prepare("INSERT INTO `comments` (`id`, `id_user`, `id_product`, `com`, `time_at`, `status`) 
    VALUES (NULL, '$username', '$name_product', '$comment_text', current_timestamp(), '1')");
  $stm->execute();

  $stm = $connect->prepare("INSERT INTO `news` (`id`, `username`, `message`, `time_at`) VALUES (NULL, '$v_p->sold', 'Someone Add Comment In product', current_timestamp())");
  $stm->execute();

  header("Refresh:0;");
}

// Delete Comment
if (isset($_POST['del_comm'])) {
  $id_comment = $_POST['del_comm'];
  $stm = $connect->prepare("DELETE FROM comments WHERE `comments`.`id` = $id_comment");
  $stm->execute();
  header("Refresh:0;");
}

// Hidden Comment
if (isset($_POST['hidden'])) {
  $id_comment = $_POST['hidden'];
  $stm = $connect->prepare("UPDATE `comments` SET `status` = '0' WHERE `comments`.`id` = $id_comment;");
  $stm->execute();
  header("Refresh:0;");
}

// show Comment
if (isset($_POST['show'])) {
  $id_comment = $_POST['show'];
  $stm = $connect->prepare("UPDATE `comments` SET `status` = '1' WHERE `comments`.`id` = $id_comment;");
  $stm->execute();
  header("Refresh:0;");
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
  <link rel="stylesheet" href="layout/css/product.css">
  <title>Product</title>
</head>

<body>
  <!-- Start navbar -->
  <?php include('layout/templates/navbar.php'); ?>
  <!-- End navbar -->

  <!-- Start prodcut -->
  <div class="prodcut container mt-3">
    <h2 class="title_section">
      <?php echo $v_p->title; ?>
    </h2>
    <div id="silder_images" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <?php
        $image_silder = explode(',', $v_p->imgs_product);
        for ($i = 0; $i < sizeof($image_silder); $i++) {
          if ($image_silder[$i] != '') {
            if ($i == 0) {
              echo "
                  <button type='button' data-bs-target='#silder_images' class='active' aria-current='true'></button>
                ";
            } else {
              echo "
                <button type='button' data-bs-target='#silder_images' data-bs-slide-to='$i'></button>
              ";
            }
          }
        }
        ?>
      </div>
      <div class="carousel-inner b-r-5">
        <?php
        for ($i = 0; $i < sizeof($image_silder); $i++) {
          if ($image_silder[$i] != '') {
            if ($i == 0) {
              echo "
              <div class='carousel-item active'>
                <img src='admin/layout/images/product_img/$image_silder[$i]' class='d-block img-silder' alt='...'>
              </div>
            ";
            } else {
              echo "
            <div class='carousel-item'>
              <img src='admin/layout/images/product_img/$image_silder[$i]' class='d-block img-silder' alt='...'>
            </div>
            ";
            }
          }
        }
        ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#silder_images" data-bs-slide="prev"
        style='background-color: #ddd; border-radius: 10px;'>
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#silder_images" data-bs-slide="next"
        style='background-color: #ddd; border-radius: 10px;'>
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    <div class="parent">
      <div class="text box">
        <h3>- Infomation</h3>
        <ol>
          <li><b>Brand: </b>
            <?php echo $v_p->brand; ?>
          </li>
          <li><b>Processor: </b>
            <?php echo $v_p->processor; ?>
          </li>
          <li><b>Graphics: </b>
            <?php echo $v_p->graphics; ?>
          </li>
          <li><b>Ram: </b>
            <?php echo $v_p->ram; ?>
          </li>
          <li><b>Storage: </b>
            <?php echo $v_p->storage; ?>
          </li>
          <li><b>Color: </b>
            <?php echo $v_p->color; ?>
          </li>
        </ol>
        <div class="des">
          <h3>- Description</h3>
          <p class="p-1" style="font-size: 0.9rem;">
            <?php echo $v_p->des; ?>
          </p>
        </div>
      </div>
      <div class="info box">
        <form action="" method="POST" class="order">
          <p class="price text-center fs-2 m-0 mb-1"><b><sup>$</sup>
              <?php echo $v_p->price; ?>
            </b>
          </p>
          <div class="modal fade" id="order_error_login" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Error Order</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  - If You Want Buy Product, First You Need <a href="login.php" class='btn btn-primary'>Login</a>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (isset($username)) {
            echo "
            <input type='submit' class='btn btn-success w-100 mb-2' name='add_to_basket' value='Add To Card'>
            <input type='submit' class='btn btn-warning w-100 mb-1' name='order_now' value='Order Now'>          
          ";
          } else {
            echo "
            <a data-bs-toggle='modal' data-bs-target='#order_error_login' class='btn btn-success w-100 mb-2'>Add To Card</a>
            <a data-bs-toggle='modal' data-bs-target='#order_error_login' class='btn btn-warning w-100 mb-1'>Order Now</a>
          ";
          }
          ?>
          <div style="font-size: 0.9rem; display:flex; justify-content: space-between;">
            <a href="profile.php?user=<?php echo $v_p->sold; ?>" class="m-2" target="_blank"><b>Vendors: </b>
              <?php echo $v_p->FullName; ?>
            </a>
            <span class="m-2"><b>Count Buy: </b>
              <?php echo $v_p->count_pay; ?>
            </span>
          </div>
        </form>
      </div>
    </div>
    <div class="comments mt-2 position-relative <?php if (!isset($_SESSION['username']))
      echo "locked"; ?>">
      <h4>- Comments</h4>
      <form action="" method="POST" class=""
        style="font-size: 0.9rem; display:flex; justify-content: space-between; align-items: center; gap: 5px;">
        <div class="form-floating w-100">
          <input type="text" name="comment" class="form-control" id="comment" placeholder="name@example.com">
          <label for="comment">New Comment</label>
        </div>
        <input type="submit" <?php if (isset($_SESSION['username']))
          echo "name='add_comment'"; ?>
          class="btn btn-secondary w-25 rounded-pill" value="Send">
      </form>
    </div>
    <form action="" method="POST" class="last_com mt-2">
      <?php
      foreach ($comment as $com) {
        echo "
        <div class='box'>
          <div class='info'>
            <img src='admin/layout/images/img_profile/" . $com->img_profile . "'  onerror='this.onerror=null;this.src=`admin/layout/images/img_user/someone.png`;' class='img_profile' alt='img profile'>
            <div class='text'>
              <span class='user_name'>- " . $com->FullName . "</span>
              <p>" . $com->com . "</p>
            </div>
          </div>
          <div class='change'>";
        if (isset($permission) && $permission == 2) {
          if ($com->status == 1) {
            echo "
              <input type='submit' name='del_comm' id='del$com->id' value='$com->id' class='d-none'>
              <label for='del$com->id' class='btn btn-danger mb-1'>Delete</label>
  
              <input type='submit' name='hidden' id='hid$com->id' value='$com->id' class='d-none'>
              <label for='hid$com->id' class='btn btn-warning'>Hidden</label>
              ";
          } elseif ($com->status == 0) {
            echo "
              <input type='submit' name='del_comm' id='del$com->id' value='$com->id' class='d-none'>
              <label for='del$com->id' class='btn btn-danger mb-1'>Delete</label>
  
              <input type='submit' name='show' id='show$com->id' value='$com->id' class='d-none'>
              <label for='show$com->id' class='btn btn-success'>Show</label>
              ";
          }
        }
        echo "</div>
        </div>
        ";
      }

      ?>
    </form>
  </div>
  <!-- End prodcut -->

  <!-- Start Footer  -->
  <?php include('layout/templates/footer.php'); ?>

  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>