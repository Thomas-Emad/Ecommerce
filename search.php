<?php
include('init.php');

$title_product = '';

// Get All To Solds.
if (isset($_GET['top_solds'])) {
  $stm = $connect->prepare("SELECT * FROM `product` ORDER BY `product`.`count_pay` DESC");
  $stm->execute();
  $product_filter = $stm->fetchAll(PDO::FETCH_OBJ);
  $title_result = 'Top Solds';
}

// Filter With Only Brand.
if (isset($_GET['brand'])) {
  $title_product = $_GET['brand'];
  $stm = $connect->prepare("SELECT * FROM product WHERE brand = '$title_product';");
  $stm->execute();
  $product_filter = $stm->fetchAll(PDO::FETCH_OBJ);
  $title_result = $title_product;
}

// Search Title
if (isset($_GET['s'])) {
  $title_product = $_GET['s'];
  $stm = $connect->prepare("SELECT * FROM product WHERE name LIKE '%$title_product%';");
  $stm->execute();
  $product_filter = $stm->fetchAll(PDO::FETCH_OBJ);
  $title_result = $title_product;
}

// Ready All CheckBox for print
$values_sql = ['graphics', 'ram', 'processor', 'brand'];
$values = [];
foreach ($values_sql as $sql) {
  $stm = $connect->prepare("SELECT name FROM $sql;");
  $stm->execute();
  $values["$sql"] = $stm->fetchAll(PDO::FETCH_OBJ);
}

// Filter All Product For Inputs
if (isset($_POST['filter'])) {
  if (isset($_POST['brand'])) {
    $brand_filter = implode('\',\'', $_POST['brand']);
    $brand_filter = "AND brand in ('$brand_filter')";
  } else {
    $brand_filter = '';
  }
  if (isset($_POST['processor'])) {
    $processor_filter = implode('\',\'', $_POST['processor']);
    $processor_filter = "AND processor in ('$processor_filter')";
  } else {
    $processor_filter = '';
  }
  if (isset($_POST['graphics'])) {
    $graphics_filter = implode('\',\'', $_POST['graphics']);
    $graphics_filter = "AND graphics in ('$graphics_filter')";
  } else {
    $graphics_filter = '';
  }
  if (isset($_POST['memory'])) {
    $memory_filter = implode('\',\'', $_POST['memory']);
    $memory_filter = "AND ram in ('$memory_filter')";
  } else {
    $memory_filter = '';
  }
  if (isset($_POST['start_price'])) {
    $start_price = filter_var($_POST['start_price'], FILTER_SANITIZE_NUMBER_INT);
    $start_price = "AND price >= '$start_price'";
  } else {
    $start_price = '';
  }
  if (isset($_POST['max_price'])) {
    $max_price = filter_var($_POST['max_price'], FILTER_SANITIZE_NUMBER_INT);
    $max_price = "AND price <= '$max_price'";
  } else {
    $max_price = '';
  }

  $stm = $connect->prepare("SELECT * FROM product WHERE name LIKE '%$title_product%' $start_price $max_price $brand_filter $processor_filter $graphics_filter $memory_filter;");
  $stm->execute();
  $product_filter = $stm->fetchAll(PDO::FETCH_OBJ);
}

// If Have And Problem Transformation To 404 Error Page.
if (empty($_GET) || strlen($title_result) == 0) {
  header("Location: 404.php");
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
  <link rel="stylesheet" href="layout/css/navbar.css">
  <link rel="stylesheet" href="layout/css/search.css">
  <title>Search</title>
</head>

<body>
  <!-- Start navbar -->
  <?php include('layout/templates/navbar.php'); ?>
  <!-- End navbar -->

  <div class="content container">
    <details class="mb-3">
      <summary>
        <div class="title_section">
          <div class="fs-3">
            <b class="m-0">Result:</b>
            <?php echo "$title_result"; ?>
          </div>
          <i class="fa-solid fa-arrow-down-wide-short btn btn-dark"></i>
        </div>
      </summary>
      <form action="" method="POST" class="big_box">
        <div class="parent">
          <div class="box">
            <p class="m-0" style="font-size: 1.1rem;"><b>Brand:</b></p>
            <?php
            foreach ($values['brand'] as $value) {
              if (isset($_POST['brand']) && in_array($value->name, $_POST['brand'])) {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='brand[]' value='$value->name' id='$value->name' checked>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              } else {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='brand[]' value='$value->name' id='$value->name'>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              }
            }
            ?>
          </div>
          <div class="box">
            <p class="m-0" style="font-size: 1.1rem;"><b>Processor:</b></p>
            <?php
            foreach ($values['processor'] as $value) {
              if (isset($_POST['processor']) && in_array($value->name, $_POST['processor'])) {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='processor[]' value='$value->name' id='$value->name' checked>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              } else {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='processor[]' value='$value->name' id='$value->name'>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              }
            }
            ?>
          </div>
          <div class="box">
            <p class="m-0" style="font-size: 1.1rem;"><b>Graphics:</b></p>
            <?php
            foreach ($values['graphics'] as $value) {
              if (isset($_POST['graphics']) && in_array($value->name, $_POST['graphics'])) {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='graphics[]' value='$value->name' id='$value->name' checked>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              } else {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='graphics[]' value='$value->name' id='$value->name'>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              }
            }
            ?>
          </div>
          <div class="box">
            <p class="m-0" style="font-size: 1.1rem;"><b>Memory:</b></p>
            <?php
            foreach ($values['ram'] as $value) {
              if (isset($_POST['memory']) && in_array($value->name, $_POST['memory'])) {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='memory[]' value='$value->name' id='$value->name' checked>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              } else {
                echo "
                <div class='form-check'>
                  <input class='form-check-input' type='checkbox' name='memory[]' value='$value->name' id='$value->name'>
                  <label class='form-check-label' for='$value->name'>
                    $value->name
                  </label>
                </div>
                ";
              }
            }
            ?>
          </div>
        </div>
        <div class="box  mt-2 w-100">
          <p class="m-0" style="font-size: 1.1rem;"><b>Price:</b></p>
          <div class="price">
            <div>
              <label for="start_price" class="form-label m-0">Min Price</label>
              <input type="range"
                value="<?php (isset($_POST['start_price'])) ? print($_POST['start_price']) : print('1'); ?>"
                id="start_price" name="start_price" min="1" max="100000"
                oninput="this.nextElementSibling.value = this.value">
              <output>
                <?php (isset($_POST['start_price'])) ? print($_POST['start_price']) : print('1'); ?>
              </output>
            </div>
            <div>
              <label for="max_price" class="form-label m-0">Max Price</label>
              <input type="range"
                value="<?php (isset($_POST['max_price'])) ? print($_POST['max_price']) : print('100000'); ?>"
                id="max_price" name="max_price" min="1" max="100000"
                oninput="this.nextElementSibling.value = this.value">
              <output>
                <?php (isset($_POST['max_price'])) ? print($_POST['max_price']) : print('100000'); ?>
              </output>
            </div>
          </div>
        </div>
        <div class="box">
          <input type="submit" name="filter" class="btn btn-primary mt-2 w-100" value="Filter">
        </div>
      </form>
    </details>
    <div class="product">
      <div class='parent'>
        <?php
        foreach ($product_filter as $pro) {
          echo "
            <div class='main_box'>
              <img src='admin/layout/images/product_img/$pro->img_bg' onerror='this.onerror=null;this.src=`layout/images/bad_img.jpg`;' alt='img product'>
              <div class='info'>
                <div class='text'>
                  <div>
                    <p class='title m-0'>$pro->name</p>
                    <b class='title m-0'>Brand: </b>$pro->brand
                  </div>
                  <span class='salary'>$$pro->price</span>
                </div>
                <div class='text'>
                  <a href='product.php?id=$pro->random_product' class='btn btn-outline-info w-100' target='_blank'>Visit Product</a>
                </div>
              </div>
            </div>
          ";
        }
        echo "</div>";
        if (empty($product_filter)) {
          echo "<p style='text-align: center; color: #999; margin: 0;'>Don't Have Any Product</p>";
        }
        ?>
      </div>
    </div>


    <!-- Start Footer  -->
    <?php include('layout/templates/footer.php'); ?>
    <script src="layout/js/all.min.js"></script>
    <script src="layout/js/bootstrap.bundle.min.js"></script>
    <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>