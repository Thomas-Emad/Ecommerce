<?php
include_once('init.php');

// If User Don't Have Enght permission Tans Page To index.php
tran_per();
if ($permission != 2) {
  header("Location: index.php");
}

// Get All Information About All Users.
$stm = $connect->prepare("SELECT username, email, img_profile, FullName, location, create_at, active, admin, status, cart FROM `users` ORDER BY admin DESC;");
$stm->execute();
$usersAll = $stm->fetchAll(PDO::FETCH_OBJ);

// If Owner Change Any Thing Upload It
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['active_email'])) {
    $user_edit = $_POST['active_email'];
    $stm = $connect->prepare("UPDATE `users` SET `active` = '1' WHERE `users`.`username` = $user_edit;");
  } elseif (isset($_POST['sell'])) {
    $user_edit = $_POST['sell'];
    $stm = $connect->prepare("UPDATE `users` SET `admin` = '1' WHERE `users`.`username` = $user_edit;");
    // Add New Message In NEWS.
    add_message($user_edit, 'Make Someone A Sell', false);

  } elseif (isset($_POST['black_list'])) {
    $user_edit = $_POST['black_list'];

    // Add New Message In NEWS.
    add_message($user_edit, 'Add Someon To BlackList', false);
  } elseif (isset($_POST['del'])) {
    $user_edit = $_POST['del'];
    $stm = $connect->prepare("DELETE FROM users WHERE `users`.`username` = $user_edit;");
    for ($i = 0; $i < sizeof($usersAll); $i++) {
      if ($usersAll[$i]->username == $user_edit) {
        $img_profile_del = $usersAll[$i]->img_profile;
        @unlink("admin/layout/images/img_user/$img_profile_del");
      }
    }
  }
  $stm->execute();
  header("Refresh:0;");
}

// Change Permission For User
if (isset($_POST['per_user'])) {
  $user_edit = $_POST['per_user'];
  $per_user = $_POST['permission'];
  $stm = $connect->prepare("UPDATE `users` SET `admin` = '$per_user' WHERE `users`.`username` = $user_edit;");
  $stm->execute();

  // Add New Message In NEWS.
  add_message($user_edit, 'Changed Permission Somone', true);
}

// Delete From Black List.
if (isset($_POST['del_black_list'])) {
  $user_edit = $_POST['del_black_list'];
  $stm = $connect->prepare("UPDATE `users` SET `status` = '0' WHERE `users`.`username` = $user_edit;");
  $stm->execute();

  // Add New Message In NEWS.
  add_message($user_edit, 'Delete Someone From BlackList', true);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="layout/css/all.min.css" />
  <link rel="stylesheet" href="layout/css/bootstrap.min.css" />
  <link rel="stylesheet" href="layout/css/style.css" />
  <title>Customers</title>
</head>

<body>
  <?php include('layout/templates/navbar.php'); ?>

  <!-- Start Content -->
  <div class="customers container">
    <h2 class="title_section">- Customers</h2>
    <div class="parent">
      <?php
      foreach ($usersAll as $users) {
        if ($users->admin == 1) {
          $status_user = 'Sell';
        } elseif ($users->admin == 2) {
          $status_user = 'Owner';
        } elseif ($users->admin == 0 || $users->admin == 3) {
          $status_user = 'User';
        }
        echo "
            <div class='box'>
              <div class='info'>
                <img src='layout/images/img_user/$users->img_profile' onerror='this.onerror=null;this.src=`layout/images/img_user/someone.png`;' class='img'>
                <div class='text'>
                  <span class='name'>$users->FullName</span>
                  <span>$status_user</span>
                </div>
              </div>
              <div>";
        if ($users->status == 1) {
          echo "
                  <span data-bs-toggle='modal' class='btn btn-warning'>Blocked</span>
                ";
        }
        if ($users->admin == 3) {
          echo "
                  <span data-bs-toggle='modal' class='btn btn-info'>Want Sell</span>
                ";
        }
        echo "   <a href='#$users->username' data-bs-toggle='modal' class='btn btn-primary'>Show</a>
              </div>
              <div class='modal fade modal-lg' id='$users->username' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true' >
                <div class='modal-dialog'>
                  <div class='modal-content'>
                    <div class='modal-header'>
                      <h1 class='modal-title fs-5' id='exampleModalLabel'>
                        $users->FullName
                      </h1>
                      <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                      <form action='' method='POST' class='row'>
                        <div class='img col-12' style='overflow: hidden;'>
                          <img src='layout/images/img_user/$users->img_profile' onerror='this.onerror=null;this.src=`layout/images/img_user/someone.png`;' alt='Img Profile' style='width: 60px; height: 60px; background-color: #ddd; border-radius: 100%; display: block; margin: 10px auto; '>
                        </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>
                          <p class='alert alert-info m-0'>He Is $status_user</p>
                        </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>";
        if ($users->active == 0) {
          echo "<p class='alert alert-warning m-0'>His Email Is Not Active</p>";
        } elseif ($users->active == 1) {
          echo "<p class='alert alert-success m-0'>His Email Is Active</p>";
        }
        echo "             </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>
                          <input type='text' class='form-control' id='input_name' disabled placeholder='FullName' value='$users->FullName'>
                          <label for='input_name' style='left: 5px;'>FullName</label>
                        </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>
                          <input type='email' class='form-control' id='input_email' disabled placeholder='Email' value='$users->email'>
                          <label for='input_email' style='left: 5px;'>Email address</label>
                        </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>
                          <input type='text' class='form-control' id='input_location' disabled placeholder='Location' value='$users->location'>
                          <label for='input_location' style='left: 5px;'>Location</label>
                        </div>
                        <div class='form-floating col-sm-12 col-md-6 mb-2'>
                          <input type='text' class='form-control' id='input_create' disabled  placeholder='Time Join' value='$users->create_at'>
                          <label for='input_create' style='left: 5px;'>Time Join</label>
                        </div>
                        <hr>
                        <div class='form-floating mb-2'>
                          <select class='form-select' id='permission' name='permission' aria-label='Floating label select example'>';
                            <option value='2'>Owner</option>
                            <option value='1'>Sell</option>
                            <option value='0'>User</option>
                          </select>
                          <label for='permission'>Permission</label>
                          <div>
                            <label for='per_$users->username' class='btn btn-success w-100 mt-2'>Change Permission</label>
                            <input type='submit' name='per_user' id='per_$users->username' value='$users->username' style='display:none;'>
                          </div>
                        </div>
                        <hr>
                        <div class='control'>
                          <input type='submit' name='active_email' value='$users->username' id='active_email$users->username' style='display:none;'>
                          <label for='active_email$users->username' class='btn btn-success w-100 mb-2'>Active Email</label>";

        if ($users->admin == 0 || $users->admin == 3) {
          echo "
                            <input type='submit' name='sell' value='$users->username' id='sell$users->username' style='display:none;'>
                            <label for='sell$users->username' class='btn btn-info w-100 mb-2'>Make A Sell</label>
                            ";
        }

        if ($users->status == 0) {
          echo "
                            <input type='submit' name='black_list' value='$users->username' id='black_list$users->username' style='display:none;'>
                            <label for='black_list$users->username' class='btn btn-warning w-100 mb-2'>Add To Black List</label>  
                            ";
        } elseif ($users->status == 1) {
          echo "
                            <input type='submit' name='del_black_list' value='$users->username' id='del_black_list$users->username' style='display:none;'>
                            <label for='del_black_list$users->username' class='btn btn-warning w-100 mb-2'>Delete From Black List</label>  
                            ";
        }
        echo "           <input type='submit' name='del' value='$users->username' id='del$users->username' style='display:none;'>
                          <label for='del$users->username' class='btn btn-danger w-100 mb-2'>Delete Account</label>
                        </div>
                      </form>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary text-light' data-bs-dismiss='modal'>
                        Close
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ";
      }

      ?>

    </div>
  </div>
  <!-- End Content -->

  <?php include('layout/templates/footer.php'); ?>
  <script src="layout/js/all.min.js"></script>
  <script src="layout/js/bootstrap.bundle.min.js"></script>
</body>

</html>