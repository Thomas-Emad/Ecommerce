<nav class="navbar navbar-expand-lg bg-dark fixed-tp" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01"
      aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse gap-2" id="navbarTogglerDemo01">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="search.php?top_solds">Top Solds</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sections
          </a>
          <ul class="dropdown-menu">
            <?php
            // Ready All Brands for print
            $stm = $connect->prepare("SELECT name FROM brand;");
            $stm->execute();
            $brands = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($brands as $br) {
              echo "<li><a class='dropdown-item' href='search.php?brand=$br->name'>$br->name</a></li>";
            }
            ?>
          </ul>
        </li>
        <?php
        if (!isset($_SESSION['username'])) {
          echo "
            <li class='nav-item'>
              <a class='nav-link' href='login.php'>Login</a>
            </li>
          ";
        }
        ?>
      </ul>
      <form class="d-flex" role="search" action="search.php" method='GET'>
        <input class="form-control me-2" type="search" name="s" placeholder="Search" aria-label="Search">
        <input class="btn btn-outline-success" name="filter" type="submit" value="Search">
      </form>
      <?php
      if (isset($_SESSION['username'])) {
        echo "
          <div class='dropdown text-end'>
            <a href='#' class='d-block text-decoration-none dropdown-toggle' id='dropdownUser1' data-bs-toggle='dropdown' aria-expanded='false'>
              <img src='admin/layout/images/img_user/$main_img_profile' onerror='this.onerror=null;this.src=`admin/layout/images/img_user/someone.png`;' alt='mdo' width='32' height='32' class='rounded-circle'>
            </a>
            <ul class='dropdown-menu text-small' aria-labelledby='dropdownUser1'>
              <li><a class='dropdown-item' href='profile.php'>Profile</a></li>
              <li><a class='dropdown-item' href='basket.php'>My Basket</a></li>
              <li><a class='dropdown-item' href='basket.php?last_order'>Last Ordeing</a></li>
              <li><a class='dropdown-item' href='admin/index.php'>Dashboard</a></li>
              <li>
                <hr class='dropdown-divider'>
              </li>
              <li><a class='dropdown-item' href='layout/templates/logout.php'>Sign out</a></li>
            </ul>
          </div>
        ";
      }
      ?>
    </div>
  </div>
</nav>