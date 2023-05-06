<?php
$username = '';
include_once("init.php");

// Get All Information About User
if (!isset($_GET['user'])) {
  $user_profile = $username;
} else {
  $user_profile = $_GET['user'];
}

$stm = $connect->prepare("SELECT email, FullName, active, admin, status, img_profile, location, create_at, cart FROM `users` WHERE username = '$user_profile';");
$stm->execute();
$info_user = $stm->fetch(PDO::FETCH_OBJ);
$name_img = $info_user->img_profile;

// If Have And Problem Transformation To 404 Error Page.
if (empty($info_user)) {
  header("Location: 404.php");
  exit();
}

if ($username == $user_profile && strlen($info_user->cart)) {
  $cart_info = explode(',', $info_user->cart);
  $name_cart = $cart_info[0];
  $num_cart = $cart_info[1];
  $cvv_cart = $cart_info[2];
} else {
  $name_cart = '';
  $num_cart = '';
  $cvv_cart = '';
}

// When User Save His New Information
if (isset($_POST['save_change'])) {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
  $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
  $types_img = ['png', 'jpg', 'jpeg'];
  $errors = [];
  $mess = [];

  // info Cart
  $name_cart = $_POST['name_cart'];
  $num_cart = $_POST['num_cart'];
  $cvv_cart = $_POST['cvv_cart'];
  $cart_info = "$name_cart,$num_cart,$cvv_cart";

  // Check From Input Length Letters.
  if (strlen($name) < 3) {
    $errors[] = "- It Need Some Bigger Than.";
  }
  if (strlen($email) < 15) {
    $errors[] = "- We Need Real Email.";
  }
  if (strlen($location) < 7) {
    $errors[] = "- Are This Is Real Location?!.";
  }

  // IF You Don't Have Any Error Save The Image.
  if (empty($errors) && $_FILES['img_profile']['error'] != 4) {
    $ty_img = explode('/', $_FILES['img_profile']['type'])[1];
    if (in_array($ty_img, $types_img)) {
      $name_img = $username . '.' . $ty_img;
      move_uploaded_file($_FILES['img_profile']['tmp_name'], "admin/layout/images/img_user/$name_img");
      @unlink("admin/layout/images/img_user/$info_user->img_profile");
    } else {
      $errors[] = "- Can't Upload Your Img Profile, Because His Type Not Support.";
    }
  }

  if (empty($errors)) {
    $stm = $connect->prepare("UPDATE `users` SET
    `email` = '$email',
    `FullName` = '$name',
    `img_profile` = '$name_img',
    `location` = '$location',
    `cart` = '$cart_info'
      WHERE `users`.`username` = '$username';");
    $stm->execute();
    header("Refresh:0;");
  }
}

// When Change Your Password
if (isset($_POST['save_password'])) {
  // First Check From Last Password Then Send New Password Hash.
  $last_pass_input = sha1($_POST['last_password_input']);
  $stm = $connect->prepare("SELECT true as status_password FROM `users` 
    WHERE (username = '$username') AND (password = '$last_pass_input');");
  $stm->execute();
  $status_password = $stm->fetch(PDO::FETCH_OBJ);

  if (!empty($status_password)) {
    $new_pass_input = sha1($_POST['new_password_input']);
    $stm = $connect->prepare("UPDATE `users` 
      SET `password` = '$new_pass_input' WHERE `users`.`username` = '$username';");
    $stm->execute();
    header("Refresh:0;");
  } else {
    $errors[] = "- Error In Last Password, Can't Change The Password.";
  }
}

// If You Want To Be A Seller
if (isset($_POST['sell'])) {
  $stm = $connect->prepare("UPDATE `users` SET
    `admin` = '3' WHERE `users`.`username` = '$username';");
  $stm->execute();
  header("Refresh:0;");
}

// Delete Account 
if (isset($_POST['del_acc'])) {
  @unlink("admin/layout/images/img_user/$info_user->img_profile");
  $stm = $connect->prepare("DELETE FROM users WHERE `users`.`username` = '$username'");
  $stm->execute();

  unset($_SESSION['username']);
  header("Refresh:0");
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
  <link rel="stylesheet" href="admin/layout/css/profile.css">
  <link rel="stylesheet" href="admin/layout/css/products.css">
  <title>Profile</title>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>
  <?php
  // Print Any errors In All Pages, And Messages.
  if (!empty($errors)) {
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
  ?>

  <div class="content_profile container">
    <h2 class="text-center">Helle,
      <?php echo $info_user->FullName; ?>
    </h2>
    <div class="parent">
      <form action="" method="POST" enctype='multipart/form-data'>
        <div class="img_profile_group">
          <?php if ($username == $user_profile)
            echo "<input type='file' name='img_profile' id='img_profile'>"; ?>
          <label for="img_profile"><img src="admin/layout/images/img_user/<?php echo $name_img; ?>"
              onerror='this.onerror=null;this.src=`admin/layout/images/img_user/someone.png`;'
              for="img_profile"></label>
        </div>
        <div class="row gy-3 mb-3">
          <div class="col-md-6">
            <?php
            if ($info_user->admin == 0 || $info_user->admin == 3) {
              echo "<p class='alert alert-info'>Your Are User</p>";
            } elseif ($info_user->admin == 1) {
              echo "<p class='alert alert-info'>Your Are Admin</p>";
            } elseif ($info_user->admin == 2) {
              echo "<p class='alert alert-info'>Your Are Owner</p>";
            }
            ?>
          </div>
          <div class="col-md-6">
            <?php
            if ($info_user->active == 0) {
              echo "<p class='alert alert-danger'>Your Email Is Not Active</p>";
            } elseif ($info_user->active == 1) {
              echo "<p class='alert alert-success'>Your Email Is Active</p>";
            }
            ?>
          </div>
        </div>

        <div class="form-floating mb-3">
          <input type="email" class="form-control" <?php if ($username == $user_profile)
            echo "name='email'";
          else
            echo "disabled"; ?> id="input_email" placeholder="Email" value="<?php echo $info_user->email; ?>">
          <label for="input_email">Email address</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" <?php if ($username == $user_profile)
            echo "name='name'";
          else
            echo "disabled"; ?> id="name_user" placeholder="Your Name" value="<?php echo $info_user->FullName; ?>">
          <label for="name_user">Your Name</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" <?php if ($username == $user_profile)
            echo "name='location'";
          else
            echo "disabled"; ?> name="location" id="location_input" placeholder="Your Location"
            value="<?php echo $info_user->location; ?>">
          <label for="location_input">Your Location</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="floatingInput" placeholder="Time Create Your Account.."
            value="<?php echo $info_user->create_at; ?>" disabled>
          <label for="floatingInput">Time Create Your Account..</label>
        </div>
        <?php
        if ($user_profile == $username) {
          echo "
            <div class='cart'>
              <h4>- Payment Method</h4>
              <div class='row gy-3 mb-3'>
                <div class='col-md-6'>
                  <div class='form-floating'>
                    <input type='text' class='form-control' name='name_cart' value='$name_cart' id='cc-name' placeholder='Full name as displayed on card'>
                    <label for='cc-name'>Name on card</label>
                    <div class='invalid-feedback'>
                      Name on card is required
                    </div>
                  </div>
                </div>
                <div class='col-md-6'>
                  <div class='form-floating'>
                    <input type='text' class='form-control' name='num_cart' value='$num_cart' id='cc-number' placeholder='Credit card number is required'>
                    <label for='cc-number'>Credit card number</label>
                    <div class='invalid-feedback'>
                      Credit card number is required
                    </div>
                  </div>
                </div>
                <div class='col-md-3'>
                  <div class='form-floating'>
                    <input type='text' class='form-control' name='cvv_cart' value='$cvv_cart' id='cc-cvv' placeholder='CVV'>
                    <label for='cc-cvv'>CVV</label>
                    <div class='invalid-feedback'>
                      Security code required
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <input type='submit' name='save_change' value='Save Your Change' class='btn btn-success mb-2 w-100'>

            <a href='#password_modal' class='btn btn-primary w-100  mb-3' data-bs-toggle='modal'>Change Your Password..</a>
            <!-- Modal -->
            <div class='modal fade' id='password_modal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Change Your Password...</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                  </div>
                  <div class='modal-body'>
                    <div class='form-floating mb-3'>
                      <input type='password' class='form-control' name='last_password_input' id='last_password_input' placeholder='Last Password'>
                      <label for='last_password_input'>Last Password</label>
                    </div>
                    <div class='form-floating mb-3'>
                      <input type='password' class='form-control' name='new_password_input' id='new_password_input' placeholder='New Password'>
                      <label for='new_password_input'>New Password</label>
                    </div>
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                    <input type='submit' name='save_password' class='btn btn-success' value='Save changes'>
                  </div>
                </div>
              </div>
            </div>

            <a href='#delete_account' class='btn btn-danger w-100  mb-3' data-bs-toggle='modal'>Delete Your Account</a>
            <!-- Modal -->
            <div class='modal fade' id='delete_account' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Delete Your Account!!</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                  </div>
                  <div class='modal-body'>
                    <p>- Will Delete All Product And Delete All Your Profits.</p>
                    <p>- Now, Are You Sure??</p>
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-success' data-bs-dismiss='modal'>Close</button>
                    <input type='submit' name='del_acc' class='btn btn-secondary' value='Delete'>
                  </div>
                </div>
              </div>
            </div>
          ";
        }

        if ($info_user->admin == 0 && $user_profile == $username) {
          echo "<input type='submit' name='sell' value='Want To Be Sell' class='btn btn-warning mb-2 w-100'>";
        } elseif ($info_user->admin == 3 && $user_profile == $username) {
          echo "<p class='alert alert-info'>Wait For Owner Accept You For Be Seller.</p>";
        }
        ?>
      </form>
    </div>
  </div>
  <section class="content container">
    <?php
    $stm = $connect->prepare("SELECT random_product, img_bg, name, price FROM `product` WHERE username_add = '$user_profile'");
    $stm->execute();
    $all_product = $stm->fetchAll(PDO::FETCH_OBJ);

    echo "
    <div class='title_section'>
      <h2>Your Products</h2>
    </div>
    <div class='parent'>";
    foreach ($all_product as $pro) {
      echo "
        <div class='main_box'>
          <img src='admin/layout/images/product_img/$pro->img_bg' onerror='this.onerror=null;this.src=`layout/images/bad_img.jpg`;' alt='img product'>
          <div class='info'>
            <div class='text'>
              <span class='title'>$pro->name</span>
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
    if (empty($all_product)) {
      echo "<p style='text-align: center; color: #999; margin: 0;'>Don't Have Any Product</p>";
    }
    ?>
  </section>

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/fontawesome.min.js"></script>
</body>

</html>