<footer class="row row-cols-sm-1 row-cols-md-5  py-5 m-0 mt-5 border-top  bg-dark" data-bs-theme="dark">

  <div class="col">
    <h5>Pages</h5>
    <ul class="nav flex-column">
      <li class="nav-item mb-2"><a href="index.php" class="nav-link p-0 text-muted">Home</a></li>
      <li class="nav-item mb-2"><a href="profile.php" class="nav-link p-0 text-muted">Profile</a></li>
      <li class="nav-item mb-2"><a href="search.php?top_solds" class="nav-link p-0 text-muted">Top Solds</a></li>
      <li class="nav-item mb-2"><a href="basket.php" class="nav-link p-0 text-muted">My Basket</a></li>
      <li class="nav-item mb-2"><a href="basket.php?last_order" class="nav-link p-0 text-muted">Last Orders</a></li>
      <li class="nav-item mb-2"><a href="admin/dashboard.php" class="nav-link p-0 text-muted">Dashboard</a></li>
    </ul>
  </div>

  <div class="col">
    <h5>Section</h5>
    <ul class="nav flex-column">
      <?php

      foreach ($brands as $br) {
        echo "<li class='nav-item mb-2'><a class='nav-link p-0 text-muted' href='search.php?brand=$br->name'>$br->name</a></li>";
      }
      ?>
    </ul>
  </div>
</footer>