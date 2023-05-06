<?php
include_once('init.php');


tran_per();
// If You Are Owner Give All Information About Site.
if ($permission == 2) {
  // Get Sales Number, Profits From DataBase
  $stm = $connect->prepare("SELECT COUNT(id_product) as countSales, if (COUNT(id_product) = 0, '0', SUM(orders.price)) as countPrice FROM product, orders WHERE (id_product = random_product);");
  $stm->execute();
  $sales = $stm->fetch(PDO::FETCH_OBJ);

  // Get num_product From DataBase
  $stm = $connect->prepare("SELECT count(random_product) as num_product FROM `product`");
  $stm->execute();
  $num_product = $stm->fetch(PDO::FETCH_OBJ);

  // Get Comments From DataBase
  $stm = $connect->prepare("SELECT comments.id as id_comment, random_product, com, DATE_FORMAT(time_at, '%Y-%m-%d') as time_at, product.img_bg FROM comments, product WHERE (product.random_product = comments.id_product) ORDER BY `comments`.`time_at` DESC;");
  $stm->execute();
  $comments = $stm->fetchAll(PDO::FETCH_OBJ);

  // Get News 
  $stm = $connect->prepare("SELECT news.username, news.message, news.time_at, users.FullName FROM `news`, `users` WHERE (users.username = news.username) ORDER BY `time_at` DESC;");
  $stm->execute();
  $news = $stm->fetchAll(PDO::FETCH_OBJ);

  // Save Ads
  $stm = $connect->prepare("SELECT * FROM `ads`;");
  $stm->execute();
  $ads = $stm->fetchAll(PDO::FETCH_OBJ);

  if (isset($_POST['save_ads'])) {
    $images = $_FILES['img'];
    $links = $_POST['link'];

    $name_images = $images['name'];
    for ($i = 0; $i < sizeof($links); $i++) {
      @unlink("layout/images/" . $ads[$i]->img);
      move_uploaded_file($images['tmp_name'][$i], "layout/images/" . $name_images[$i]);
      $stm = $connect->prepare("UPDATE `ads` SET `img` = '$name_images[$i]', 
      `link` = '$links[$i]' WHERE `ads`.`id` = $i + 1;");
      $stm->execute();
      header('Refresh:0;');
    }
  }

} else {
  // Get Sales Number, Profits From DataBase
  $stm = $connect->prepare("SELECT COUNT(id_product) as countSales, if (COUNT(id_product) = 0, '0', SUM(orders.price)) as countPrice FROM product, orders WHERE (id_product = random_product) AND (product.username_add = '$username');");
  $stm->execute();
  $sales = $stm->fetch(PDO::FETCH_OBJ);

  // Get num_product From DataBase
  $stm = $connect->prepare("SELECT count(random_product) as num_product FROM `product` WHERE (username_add = '$username');");
  $stm->execute();
  $num_product = $stm->fetch(PDO::FETCH_OBJ);

  // Get Comments From DataBase
  $stm = $connect->prepare("SELECT comments.id as id_comment, random_product, com, DATE_FORMAT(time_at, '%Y-%m-%d') as time_at, product.img_bg FROM comments, product WHERE (product.username_add = '$username') AND (product.random_product = comments.id_product) ORDER BY `comments`.`time_at` DESC;");
  $stm->execute();
  $comments = $stm->fetchAll(PDO::FETCH_OBJ);

  // Get News 
  $stm = $connect->prepare("SELECT news.username, news.message, news.time_at, users.FullName FROM `news`, `users` WHERE (users.username = news.username) AND news.username='$username' ORDER BY `time_at` DESC;");
  $stm->execute();
  $news = $stm->fetchAll(PDO::FETCH_OBJ);
}

// Delete Comments
if (isset($_POST['del_comm'])) {
  $id_comm_del = $_POST['del_comm'];
  $stm = $connect->prepare("DELETE FROM comments WHERE `comments`.`id` = '$id_comm_del'");
  $stm->execute();
  header('Refresh: 0;');
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
  <!-- Start Code Content -->
  <div class="content container">
    <div class="cards">
      <div class="box">
        <span>Sales Number</span>
        <span class="num">
          <?php echo $sales->countSales; ?>
        </span>
      </div>
      <div class="box">
        <span>Profits</span>
        <span class="num">
          <?php echo '$' . $sales->countPrice; ?>
        </span>
      </div>
      <div class="box">
        <span>Number of Products</span>
        <span class="num">
          <?php echo $num_product->num_product; ?>
        </span>
      </div>
    </div>
    <div class="more_sections">
      <div class="comments big_box">
        <h2 class="title_section">- Comments</h2>
        <div class="parent">
          <?php
          foreach ($comments as $comment) {
            echo "
              <form action='' method='POST' class='box'>
                <div class='info'>
                  <div style='display: flex; flex-direction: column; align-items: flex-start;font-size: 0.8rem;'>
                    <img src='layout/images/product_img/$comment->img_bg' class='img'>
                    $comment->time_at
                  </div>
                  <div class='text'>
                    $comment->com
                  </div>
                </div>
                <div class='control'>
                  <a href='../product.php?id=$comment->random_product' target='_blank' class='btn btn-info'>Show</a>
                  <input type='submit' name='del_comm' value='$comment->id_comment' id='del$comment->id_comment' style='display:none;'>
                  <label for='del$comment->id_comment' class='btn btn-danger'>Delete</label>
                </div>
              </form>
              ";
          }
          ?>
        </div>
      </div>
      <div class="news big_box">
        <h2 class="title_section">- news</h2>
        <div class="parent">
          <?php
          foreach ($news as $new) {
            echo "
              <div class='box'>
                <div class='icon'><i class='fa-solid fa-notes-medical'></i></div>
                <div class='info'>
                  <p style='margin:0;'>$new->FullName $new->message</p>
                  <span class='time'>$new->time_at</span>
                </div>
              </div>
            ";
          }
          ?>
        </div>
      </div>
    </div>
    <?php
    if ($permission == 2) {
      echo "
        <form action='' method='POST' class='ads mt-3 mb-2' enctype='multipart/form-data'>
          <h2 class='title_section'>- ADS</h2>
          <div class='input-group mb-3'>
            <span class='input-group-text'>Image</span>
            <input type='file' class='form-control' name='img[]' multiple='multiple' aria-label='Upload' required>
            <span class='input-group-text'>Link</span>
            <input type='text' class='form-control' name='link[]' value='" . $ads[0]->link . "' placeholder='Link..'
              aria-label='Link..' required>
          </div>
          <div class='input-group mb-3'>
            <span class='input-group-text'>Image</span>
            <input type='file' class='form-control' name='img[]' multiple='multiple' aria-label='Upload'>
            <span class='input-group-text'>Link</span>
            <input type='text' class='form-control' name='link[]' value='" . $ads[1]->link . "' placeholder='Link..'
              aria-label='Link..'>
          </div>
          <div class='input-group mb-3'>
            <span class='input-group-text'>Image</span>
            <input type='file' class='form-control' name='img[]' multiple='multiple' aria-label='Upload'>
            <span class='input-group-text'>Link</span>
            <input type='text' class='form-control' name='link[]' value='" . $ads[2]->link . "' placeholder='Link..'
              aria-label='Link..'>
          </div>
          <input type='submit' name='save_ads' value='Save' class='btn btn-success w-100'>
        </form>
      ";
    }
    ?>
  </div>

  <!-- End Code Content -->
  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
</body>

</html>