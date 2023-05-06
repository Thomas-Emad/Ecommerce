<header class="p-3 mb-3 border-bottom bg-dark" data-bs-theme="dark">
  <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 ">
      <li><a href="dashboard.php" class="nav-link px-2 link-light">Dashboard</a></li>
      <?php
      if (isset($permission)) {
        if ($permission == 2) {
          echo "
              <li><a href='customers.php' class='nav-link px-2 link-secondary'>Customers</a></li>
            ";
        }
      }
      ?>
      <li><a href="orders.php" class="nav-link px-2 link-secondary">Orders</a></li>
      <li><a href="products.php?site=products" class="nav-link px-2 link-secondary">Products</a></li>
    </ul>

    <?php
    if (isset($_SESSION['username'])) {
      echo "
        <div class='dropdown text-end'>
          <a href='#' class='d-block text-decoration-none dropdown-toggle' id='dropdownUser1' data-bs-toggle='dropdown' aria-expanded='false'>
            <img src='layout/images/img_user/$main_img_profile' onerror='this.onerror=null;this.src=`layout/images/img_user/someone.png`;' alt='mdo' width='32' height='32' class='rounded-circle'>
          </a>
          <ul class='dropdown-menu text-small' aria-labelledby='dropdownUser1'>
            <li><a class='dropdown-item' href='products.php?site=add_product'>New Product...</a></li>
            <li><a class='dropdown-item' href='../profile.php'>Profile</a></li>
            <li><a class='dropdown-item' href='../index.php'>Home Page</a></li>
            <li>
              <hr class='dropdown-divider'>
            </li>
            <li><a class='dropdown-item' href='../layout/templates/logout.php'>Sign out</a></li>
          </ul>
        </div>
        ";
    } else {
      echo "<a href='../index.php' class='btn btn-primary'>Home Page</a>";
    }
    ?>
  </div>
</header>