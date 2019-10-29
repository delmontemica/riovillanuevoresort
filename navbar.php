<div class="navbar-wrapper">
  <div class="navbar navbar-inverse navbar-fixed-top">
<?php if (isLogged() && !isVerified()): ?>
  <div class="verifyEmail">Please verify your email first. <a href="ajax/resendEmail.php" class="verifyEmailText">Resend</a> email?</div>
<?php endif;?>
    <div class="navbar-content">
      <div class="navbar-header">
        <a class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <img class="navbar-brand" src="image/logo.png">
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav" ng-controller="HomeNavbarController">
          <li ng-repeat="item in list" class="{{ isActive(item.url) }}">
            <a href="{{ item.url }}" ng-click="hideCollapse()">{{ item.name }}</a>
          </li>
<!-- <?php if (isset($_SESSION['account'])): ?>
          <li>
            <a href="" class="btn-apply" data-toggle="dropdown"><?php echo 'Hi, ' . getUserInfo()['name']; ?></a>
              <ul class="dropdown-menu">
                <li><a href="" data-toggle="modal" data-target="#editAccModal">Edit Profile</a></li>
                <li><a href="changepassword.php">Change Password</a></li>
                <li><a href="" data-toggle="modal" data-target="#resHistoryModal">Reservation History</a></li>
                <li><a href="" data-toggle="modal" data-target="#resListModal">Reservation List</a></li>
                <li><a style="cursor:pointer" onclick="logout()">Logout</a></li>
              </ul>
          </li>
<?php else: ?>
          <li><a href="" class="btn-apply" data-toggle="modal" data-target="#loginModal">Log-in</a></li>
<?php endif;?> -->
        </ul>
      </div>
    </div>
  </div>
</div>
