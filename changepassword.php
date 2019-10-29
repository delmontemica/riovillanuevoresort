<?php
$includejs = ['js/googleapi.js'];

require_once 'header.php';
require_once 'modal.php';

if (!isLogged()) {
  header('Location: ./');
}
?>
<h1 style="width:100%;background-color:#1abc9c;text-align:center;margin:0;height:70px;line-height:66px">
  <a style="cursor:pointer" onclick="if(confirm('Are you sure you want to go back to the home page?')) location.href='./'"><img src="image/logo.png" alt=""></a>
</h1>
<div style="padding:0 30px;overflow:auto">
  <h1>Change Password Page</h1>
  <hr/>
  <div class="center-block" style="margin-bottom:30px;width:80%">
    <form name="frmChangePassword">
      <div class="form-group">
        <label>Old Password: </label>
        <div class="input-group">
          <span class="input-group-addon">
            <span class="fa fa-key"> </span>
          </span>
          <input type="password" class="form-control" placeholder="Old Password" class="textbox" name="oldPassword" required=""  autofocus="" />
        </div>
      </div>
      <div class="form-group">
        <label>New Password: </label>
        <div class="input-group">
          <span class="input-group-addon">
            <span class="fa fa-key"> </span>
          </span>
          <input type="password" class="form-control" placeholder="New Password" class="textbox" name="newPassword" required="" />
        </div>
      </div>
      <div class="form-group">
        <label>Verify Password: </label>
        <div class="input-group">
          <span class="input-group-addon">
            <span class="fa fa-key"> </span>
          </span>
          <input type="password" class="form-control" placeholder="Verify Password" class="textbox" name="vPassword" required="" />
        </div>
      </div>
      <div class="btn-group pull-right">
        <button type="reset" class="btn btn-primary" style="margin-top:10px">Reset</button>
        <button type="submit" class="btn btn-primary" style="margin-top:10px">Change</button>
      </div>
    </form>
  </div>
</div>
<?php
require_once 'footer.php';
?>
