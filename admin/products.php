<?php
include_once('init.php');

tran_per();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
} else {
  $user_id = $_SESSION['username'];
}

if (strtolower($_GET['site']) == 'products') {
  if ($permission >= 2) {
    $stm = $connect->prepare("SELECT random_product, img_bg, name, price, count_allow, count_pay FROM `product`");
  } else {
    $stm = $connect->prepare("SELECT random_product, img_bg, name, price, count_allow, count_pay FROM `product` WHERE username_add = '$user_id'");
  }
  $stm->execute();
  $all_product = $stm->fetchAll(PDO::FETCH_OBJ);
} elseif (strtolower($_GET['site']) == 'add_product') {
  if (isset($_POST['send'])) {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $random_product = rand(1, 1000000);
    $brand = $_POST['brand'];
    $memory = $_POST['memory'];
    $processor = filter_var($_POST['processor'], FILTER_SANITIZE_STRING);
    $graphics = filter_var($_POST['graphics'], FILTER_SANITIZE_STRING);
    $storage = filter_var($_POST['storage'], FILTER_SANITIZE_STRING);
    $color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
    $des = filter_var($_POST['des'], FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $count_allow = $_POST['count_allow'];
    $type_allow_images = ['png', 'jpg', 'jpeg'];
    $images = '';

    if (strlen($title) < 5 || strlen($color) < 0 || strlen($des) < 10) {
      $errors[] = 'You Need Fill All Inputs';
    }
    if ($price <= 0 || $count_allow <= 0) {
      $errors[] = 'The Price Or Count Allow, Greater Than 0';
    }

    // Check From Upload Img, And Upload It.
    if (empty($errors)) {
      if ($_FILES['img_bg']['error'] == 4) {
        $errors[] = 'You Need Upload Background For Your Product';
      } else {
        $random_name_img = $random_product . rand(10, 1000);
        $type_bg = explode('/', $_FILES['img_bg']['type'])[1];
        if (in_array($type_bg, $type_allow_images)) {
          $new_name_img = $random_name_img . '.' . $type_bg;
          move_uploaded_file($_FILES['img_bg']['tmp_name'], 'layout/images/product_img/' . $new_name_img);
          $img_bg = $new_name_img;
        } else {
          $errors[] = '- Should This Image (' . $_FILES['img_bg']['name'] . ') His Type Only Be -> Png, Jpg';
        }
      }
    }

    // Check From Upload Img, And Upload It.
    if (empty($errors)) {
      if ($_FILES['images']['error'][0] == 4) {
        $errors[] = 'You Need Upload Images For Your Product';
      } else {
        for ($i = 0; $i < sizeof($_FILES['images']['name']); $i++) {
          $random_name_img = $random_product . rand(10, 1000);
          $type_bg = explode('/', $_FILES['images']['type'][$i])[1];
          if (in_array($type_bg, $type_allow_images)) {
            $new_name_img = $random_name_img . '.' . $type_bg;
            move_uploaded_file($_FILES['images']['tmp_name'][$i], 'layout/images/product_img/' . $new_name_img);
            $images .= $new_name_img . ',';
          } else {
            $errors[] = '- Should Any This Img (' . $_FILES['images']['name'][$i] . ') His Type Only Be -> Png, Jpg';
          }
        }
      }
    }

    if (empty($errors)) {
      $stm = $connect->prepare("INSERT INTO `product` (`id`, `name`, `random_product`, `username_add`, `brand`, `img_bg`, `imgs_product`, `ram`, `processor`, `graphics`, `storage`, `color`, `des`, `price`, `time_add`, `status`, `count_allow`, `count_pay`) 
      VALUES (NULL, '$title', '$random_product', '$user_id', '$brand', '$img_bg', '$images', '$memory', '$processor', '$graphics', '$storage', '$color', '$des', '$price', current_timestamp(), '0', '$count_allow', '0')");
      $stm->execute();

      // Add New Message In NEWS.
      add_message($user_id, 'Add New Product', true);
    }
  }
} elseif (strtolower($_GET['site']) == 'edit_product') {
  // Get All Information About This Product For Edit.
  $id_product = strtolower($_GET['id']);
  $stm = $connect->prepare("SELECT product.name, brand AS brand, des, ram, processor, graphics, storage, color, price, img_bg, imgs_product, count_allow FROM `product` WHERE random_product = '$id_product'");
  $stm->execute();
  $content = $stm->fetch(PDO::FETCH_OBJ);

  // If Have And Problem Transformation To 404 Error Page.
  if (empty($content)) {
    header("Location: ../404.php");
    exit();
  }

  // All Variables
  $title = $content->name;
  $brand = $content->brand;
  $memory = $content->ram;
  $processor = $content->processor;
  $graphics = $content->graphics;
  $storage = $content->storage;
  $color = $content->color;
  $des = $content->des;
  $price = $content->price;
  $count_allow = $content->count_allow;
  $img_bg = $content->img_bg;
  $images = $content->imgs_product;

  if (isset($_POST['send'])) {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $brand = $_POST['brand'];
    $memory = $_POST['memory'];
    $processor = filter_var($_POST['processor'], FILTER_SANITIZE_STRING);
    $graphics = filter_var($_POST['graphics'], FILTER_SANITIZE_STRING);
    $storage = filter_var($_POST['storage'], FILTER_SANITIZE_STRING);
    $color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
    $des = filter_var($_POST['des'], FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $count_allow = $_POST['count_allow'];
    $type_allow_images = ['png', 'jpg', 'jpeg'];

    if (strlen($title) < 5 || strlen($color) < 0 || strlen($des) < 10) {
      $errors[] = 'You Need Fill All Inputs';
    }
    if ($price <= 0 || $count_allow <= 0) {
      $errors[] = 'The Price Or Count Allow, Greater Than 0';
    }

    // Check From Upload Background img, And Upload It.
    if (empty($errors)) {
      if ($_FILES['img_bg']['error'] != 4) {
        // First Delete Last Images.
        @unlink("layout/images/product_img/$img_bg");
        $img_bg = '';

        $random_name_img = $id_product . rand(10, 1000);
        $type_bg = explode('/', $_FILES['img_bg']['type'])[1];
        if (in_array($type_bg, $type_allow_images)) {
          $new_name_img = $random_name_img . '.' . $type_bg;
          move_uploaded_file($_FILES['img_bg']['tmp_name'], 'layout/images/product_img/' . $new_name_img);
          $img_bg = $new_name_img;
        } else {
          $errors[] = '- Should This Image (' . $_FILES['img_bg']['name'] . ') His Type Only Be -> Png, Jpg';
        }
      }
    }

    // Check From Upload small images, And Upload It.
    if (empty($errors)) {
      if ($_FILES['images']['error'][0] != 4) {
        // First Delete Last Images.
        $images_array = explode(',', $images);
        foreach ($images_array as $img) {
          @unlink("layout/images/product_img/$img");
        }

        $images = '';
        for ($i = 0; $i < sizeof($_FILES['images']['name']); $i++) {
          $random_name_img = $id_product . rand(10, 1000);
          $type_bg = explode('/', $_FILES['images']['type'][$i])[1];
          if (in_array($type_bg, $type_allow_images)) {
            $new_name_img = $random_name_img . '.' . $type_bg;
            move_uploaded_file($_FILES['images']['tmp_name'][$i], 'layout/images/product_img/' . $new_name_img);
            $images .= $new_name_img . ',';
          } else {
            $errors[] = '- Should Any This Img (' . $_FILES['images']['name'][$i] . ') His Type Only Be -> Png, Jpg';
          }
        }
      }
    }

    // If You Don't Have Any Error Go To Upload.
    if (empty($errors)) {
      $stm = $connect->prepare("UPDATE `product` SET
        `name` = '$title',
        `brand` = '$brand',
        `img_bg` = '$img_bg',
        `imgs_product` = '$images',
        `processor` = '$processor',
        `graphics` = '$graphics',
        `storage` = '$storage',
        `color` = '$color',
        `des` = '$des',
        `price` = '$price',
        `ram` = '$memory',
        `count_allow` = '$count_allow' WHERE `product`.`random_product` = $id_product");
      $stm->execute();

      // Add New Message In NEWS.
      add_message($user_id, 'Edit in Product', true);
    }
  }
  // Delete Product
  if (isset($_POST['del'])) {
    $images_array = explode(',', $images);
    foreach ($images_array as $img) {
      @unlink("layout/images/product_img/$img");
    }
    @unlink("layout/images/product_img/$img_bg");

    $stm = $connect->prepare("DELETE FROM `product` WHERE `random_product` = $id_product");
    $stm->execute();

    // Add New Message In NEWS.
    add_message($user_id, 'Delete Product', true);
  }
} else {
  header("Location: ../404.php");
  exit();
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
  <link rel="stylesheet" href="layout/css/products.css">
  <title>Dashboard</title>
  <style>

  </style>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>

  <section class="content container">
    <?php
    // Print Any Errors In All Pages
    if (isset($errors)) {
      echo "
      <div class='alert alert-warning alert-dismissible fade show alert_mess' role='alert'>";
      foreach ($errors as $err) {
        echo $err . '<br>';
      }
      echo "
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>
      ";
    }

    if (strtolower($_GET['site']) == 'products') {
      echo "
        <div class='title_section'>
          <h2>Your Products</h2>
          <a href='products.php?site=add_product' class='btn btn-secondary'>Add New Product</a>
        </div>
        <div class='parent'>";
      foreach ($all_product as $pro) {
        $full_count = $pro->count_allow + $pro->count_pay;
        echo "
              <div class='main_box'>
                <img src='layout/images/product_img/$pro->img_bg' onerror='this.onerror=null;this.src=`layout/images/bad_img.jpg`;' alt='img product'>
                <div class='info'>
                  <div class='text'>
                    <span class='title'>$pro->name</span>
                    <span class='salary'>$$pro->price</span>
                  </div>
                  <div class='text'>
                    <span class='title'>Full Quantity</span>
                    <span class='salary'>$full_count</span>
                  </div>
                  <div class='text'>
                    <span class='title'>Available</span>
                    <span class='salary'>$pro->count_allow</span>
                  </div>
                  <div class='text'>
                    <span class='title'>Sold Quantity</span>
                    <span class='salary'>$pro->count_pay</span>
                  </div>
                  <div class='text'>
                    <a href='products.php?site=edit_product&id=$pro->random_product' class='btn btn-outline-secondary'>Edit</a>
                    <a href='../product.php?id=$pro->random_product' class='btn btn-outline-info' target='_blank'>Visit Product</a>
                  </div>
                </div>
              </div>
            ";
      }
      echo "
        </div>
      ";
    } elseif (strtolower($_GET['site']) == 'add_product') {
      // Ready All CheckBox for print
      $values_sql = ['graphics', 'ram', 'processor', 'brand'];
      $values = [];
      foreach ($values_sql as $sql) {
        $stm = $connect->prepare("SELECT name FROM $sql;");
        $stm->execute();
        $values["$sql"] = $stm->fetchAll(PDO::FETCH_OBJ);
      }

      echo "
        <div class='title_section'>
          <h2>Add New Product..</h2>
          </div>
          <form action='' method='POST' class='add_proudct' enctype='multipart/form-data'>
          <div class='img'>
            <label for='img_pro'>Choose Background<i class='fa-solid fa-plus'></i></label>
            <input type='file' name='img_bg' id='img_pro'>
          </div>
          <div class='parent'>
            <div class='box'>
              <div class='form-floating'>
                <input type='text' class='form-control' name='title' id='title' value='";
      if (isset($title))
        echo $title;
      echo "' placeholder='Title' autocomplete='off'>
                <label for='title'>Title</label>
              </div>
              <div class='form-floating position-relative'>
                <input type='number' class='form-control' name='price' value='";
      if (isset($price))
        echo $price;
      echo "' id='price' placeholder='Price' autocomplete='off'>
                <label for='price'>Price</label>
                <span class='position-absolute' style='z-index: 100;top:50%;right:30px;transform:translateY(-50%);color:#444'>$</span>
              </div>
            </div>
            <div class='form-floating'>
              <textarea class='form-control' placeholder='info' name='des' id='dess' value=' style='height: 100px' autocomplete='off'>";
      if (isset($des))
        echo $des;
      echo "</textarea>
            <label for='dess'>Description</label>
            </div>
            <div>
              <label for='images'>- Choose All Images</label>
              <input class='form-control' type='file' name='images[]' id='images' multiple='multiple'>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <select class='form-select' id='floatingSelect' name='brand' aria-label='Floating label select example'>";
      foreach ($values['brand'] as $value) {
        echo "<option value='$value->name'>$value->name</option>";
      }
      echo "</select>
              <label for='floatingSelect'>Brand</label>
              </div>
              <div class='form-floating'>
                <select class='form-select' id='select_memory' name='memory' aria-label='Floating label select example'>";
      foreach ($values['ram'] as $value) {
        echo "<option value='$value->name'>$value->name</option>";
      }
      echo "</select>
                <label for='select_memory'>Ram</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <select class='form-select' id='select_processor' name='processor' aria-label='Floating label select example'>";
      foreach ($values['processor'] as $value) {
        echo "<option value='$value->name'>$value->name</option>";
      }
      echo "</select>
                <label for='select_processor'>Processor</label>
              </div>
              <div class='form-floating'>
                <select class='form-select' id='select_graphics' name='graphics' aria-label='Floating label select example'>";
      foreach ($values['graphics'] as $value) {
        echo "<option value='$value->name'>$value->name</option>";
      }
      echo "</select>
                <label for='select_graphics'>Graphics</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <input type='text' class='form-control' name='storage' value='";
      if (isset($storage))
        echo $storage;
      echo "' id='storage' placeholder='Storage' autocomplete='off'>
                <label for='storage'>Storage</label>
              </div>
              <div class='form-floating'>
                <input type='text' class='form-control' name='color' id='color' placeholder='Color' autocomplete='off'>
                <label for='color'>Color</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <input type='number' class='form-control' name='count_allow' value='";
      if (isset($count_allow))
        echo $count_allow;
      echo "' id='count_allow' placeholder='Count' autocomplete='off'>
                <label for='count_allow'>Count</label>
              </div>
            </div>
            <input type='submit' name='send' value='Add...' class='btn btn-primary w-100'>
          </div>
        </form>
      ";
    } elseif (strtolower($_GET['site']) == 'edit_product') {
      // Ready All CheckBox for print
      $values_sql = ['graphics', 'ram', 'processor', 'brand'];
      $values = [];
      foreach ($values_sql as $sql) {
        $stm = $connect->prepare("SELECT name FROM $sql;");
        $stm->execute();
        $values["$sql"] = $stm->fetchAll(PDO::FETCH_OBJ);
      }

      echo "
        <div class='title_section'>
          <h2>Edit Last Product..</h2>
          </div>
          <form action='' method='POST' class='add_proudct' enctype='multipart/form-data'>
          <div class='img'>
            <label for='img_pro'>Choose Background<i class='fa-solid fa-plus'></i></label>
            <input type='file' name='img_bg' id='img_pro'>
          </div>
          <div class='parent'>
            <div class='box'>
              <div class='form-floating'>
                <input type='text' class='form-control' name='title' id='title' value='";
      if (isset($title))
        echo $title;
      echo "' placeholder='Title' autocomplete='off'>
                <label for='title'>Title</label>
              </div>
              <div class='form-floating position-relative'>
                <input type='number' class='form-control' name='price' value='";
      if (isset($price))
        echo $price;
      echo "' id='price' placeholder='Price' autocomplete='off'>
                <label for='price'>Price</label>
                <span class='position-absolute' style='z-index: 100;top:50%;right:30px;transform:translateY(-50%);color:#444'>$</span>
              </div>
            </div>
            <div class='form-floating'>
              <textarea class='form-control' placeholder='info' name='des' id='dess' value=' style='height: 100px' autocomplete='off'>";
      if (isset($des))
        echo $des;
      echo "</textarea>
            <label for='dess'>Description</label>
            </div>
            <div>
              <label for='images'>- Choose All Images</label>
              <input class='form-control' type='file' name='images[]' id='images' multiple='multiple'>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <select class='form-select' id='floatingSelect' name='brand' aria-label='Floating label select example'>";
      foreach ($values['brand'] as $value) {
        if ($brand == $value->name) {
          echo "<option value='$value->name' selected>$value->name</option>";
        } else {
          echo "<option value='$value->name'>$value->name</option>";
        }
      }
      echo "    </select>
                <label for='floatingSelect'>Brand</label>
              </div>
              <div class='form-floating'>
                <select class='form-select' id='select_memory' name='memory' aria-label='Floating label select example'>";
      foreach ($values['ram'] as $value) {
        if ($memory == $value->name) {
          echo "<option value='$value->name' selected>$value->name</option>";
        } else {
          echo "<option value='$value->name'>$value->name</option>";
        }
      }
      echo " </select>
                <label for='select_memory'>Ram</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <select class='form-select' id='select_processor' name='processor' aria-label='Floating label select example'>";
      foreach ($values['processor'] as $value) {
        if ($processor == $value->name) {
          echo "<option value='$value->name' selected>$value->name</option>";
        } else {
          echo "<option value='$value->name'>$value->name</option>";
        }
      }
      echo "    </select>
                <label for='select_memory'>Processor</label>
              </div>
              <div class='form-floating'>
                <select class='form-select' id='select_graphics' name='graphics' aria-label='Floating label select example'>";
      foreach ($values['graphics'] as $value) {
        if ($graphics == $value->name) {
          echo "<option value='$value->name' selected>$value->name</option>";
        } else {
          echo "<option value='$value->name'>$value->name</option>";
        }
      }
      echo "    </select>
                <label for='select_memory'>Graphics</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <input type='text' class='form-control' name='storage' value='";
      if (isset($storage))
        echo $storage;
      echo "' id='storage' placeholder='Storage' autocomplete='off'>
                <label for='storage'>Storage</label>
              </div>
              <div class='form-floating'>
                <input type='text' class='form-control' name='color' id='color' value='";
      if (isset($color))
        echo $color;
      echo "'placeholder='Color' autocomplete='off'>
                <label for='color'>Color</label>
              </div>
            </div>
            <div class='box'>
              <div class='form-floating'>
                <input type='number' class='form-control' name='count_allow' value='";
      if (isset($count_allow))
        echo $count_allow;
      echo "' id='count_allow' placeholder='Count' autocomplete='off'>
                <label for='count_allow'>Count</label>
              </div>
            </div>
            <input type='submit' name='send' value='Save Changle...' class='btn btn-primary w-100'>
            <input type='submit' name='del' value='Delete Product...' class='btn btn-danger w-100'>
          </div>
        </form>
      ";
    }
    ?>
  </section>

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>