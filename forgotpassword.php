<?php
$title = 'Forgot Password';

require_once 'header.php';
require_once 'modal.php';
?>
<h1 style="width:100%;background-color:#1abc9c;text-align:center;margin:0;height:70px;line-height:66px">
  <a style="cursor:pointer" onclick="if(confirm('Are you sure you want to go back to the home page?')) location.href='./'"><img src="image/logo.png" alt=""></a>
</h1>
<div style="padding:0 30px;overflow:auto">
  <h1>Forgot Password Page</h1>
  <hr/>
  <div class="center-block" style="margin-bottom:30px;width:80%">
<?php if (isset($_GET['token']) && isset($_GET['email'])): ?>
    <form name="frmForgotPassword">
      <input type="hidden" name="token" value="<?php echo $_GET['token'] ?>">
      <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>">
      <div class="form-group">
        <label>New Password: </label>
        <div class="input-group">
          <span class="input-group-addon">
            <span class="fa fa-key"> </span>
          </span>
          <input type="password" class="form-control" placeholder="Password" class="textbox" name="newPassword" required="" />
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
<?php else: ?>
    <form name="frmSendForgot">
      <div class="form-group">
        <label>Email Address: </label>
        <div class="input-group">
          <span class="input-group-addon">
            <span class="fa fa-envelope"> </span>
          </span>
          <input type="email" class="form-control" placeholder="Email Address" class="textbox" name="emailAddress" required="" />
        </div>
      </div>
      <div class="g-recaptcha" data-sitekey="<?php echo $gcaptcha['site']; ?>"></div>
      <div class="btn-group pull-right">
        <button type="reset" class="btn btn-primary" style="margin-top:10px">Reset</button>
        <button type="submit" class="btn btn-primary" style="margin-top:10px">Send</button>
      </div>
    </form>
<?php endif;?>
  </div>
</div>
<?php
require_once 'footer.php';
?>
