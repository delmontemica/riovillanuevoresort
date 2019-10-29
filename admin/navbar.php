<div class="wrapper">
  <div class="sidebar" data-color="green" data-image="">
    <div class="sidebar-wrapper">
      <div class="logo">
        <a href="<?php echo $main_url; ?>" onclick="return confirm('Are you sure you want to go back to the home page?')">
          <img src="<?php echo $base_url_admin; ?>assets/img/logo.png" width="210px" class="center-block"/>
        </a>
      </div>
      <ul class="nav" ng-controller="AdminNavbarController">
        <li ng-repeat="item in list" class="{{ isActive(item.url) }}">
          <a href="{{ item.url }}">
            <i class="{{ item.icon }}"></i>
            <p>{{ item.name }}</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="main-panel">
    <nav class="navbar navbar-default navbar-fixed">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" aria-controls="navigation-index">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Rio Villa Nuevo Mineral Water Resort Admin</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a style="cursor:pointer" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-globe"></i>
                <b class="caret hidden-lg hidden-md"></b>
                <p class="hidden-lg hidden-md">
                  Notifications
                  <b class="caret"></b>
<?php
$result = $db->query('SELECT * FROM notification WHERE unread=1 LIMIT 20');
?>
                  <div class="notification-icon"><?php echo $result->num_rows; ?></div>
                </p>
              </a>
              <ul class="dropdown-menu notification-menu">
                <div style="border-bottom: 1px solid #ccc; padding:10px">
                  Notification
                </div>
                <div class="notification-body">
<?php
$result = $db->query('SELECT * FROM notification ORDER BY ID DESC LIMIT 20');
while ($row = $result->fetch_assoc()):
?>
                  <li<?php echo $row['unread'] ? ' class="unread"' : ''; ?>><?php echo str_replace("onclick=''", "onclick='readNotification({$row['ID']})'", $row['message']); ?><span class="timestamp"><?php echo dateFormat($row['timestamp'], 'M d, Y h:i:s A'); ?></span></li>
<?php endwhile;?>
                </div>
                <div style="padding:10px;text-align:center;border-top: 1px solid #ccc">
                  <a href="#/notification">See All</a>
                </div>
              </ul>
            </li>
            <li>
              <a style="cursor:pointer" class="btnUpdate">
                <i class="fa fa-download"></i>
                <p class="hidden-lg hidden-md">Update</p>
              </a>
            </li>
            <li class="dropdown">
              <a style="cursor:pointer" class="dropdown-toggle" data-toggle="dropdown">
                <p><?php echo getAdminInfo()['name'] ?> <b class="caret"></b></p>
              </a>
              <ul class="dropdown-menu">
                <li><a style="cursor:pointer" data-toggle="modal" data-target="#editProfileModal">Edit Profile</a></li>
                <li><a style="cursor:pointer" data-toggle="modal" data-target="#changePasswordModal">Change Password</a></li>
              </ul>
            </li>
            <li>
              <a style="cursor:pointer" onclick="logout()"><p>Log out</p></a>
            </li>
            <li class="separator hidden-lg"></li>
          </ul>
        </div>
      </div>
    </nav>
