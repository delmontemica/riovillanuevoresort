<?php
@session_start();
$login = true;
$title = 'Admin | Login';
if (isset($_SESSION['admin'])) {
  header('Location: ./');
}
require_once 'header.php';
?>
<style>
  body {
    background: url("<?php echo $main_url; ?>image/loginpage.jpg");
    background-attachment: fixed;
    background-size: cover;
  }

  #content{
    width:30%;
  }

  @media only screen and (max-width: 767px) {
    #content {
      width:100%;
    }
  }
</style>
<div style="background-color:rgba(255,255,255,0.7);width:100%;padding:20px;">
  <img src="<?php echo $main_url; ?>image/logo2.png" class="center-block">
</div>
<div id="content" style="background-color:rgba(255,255,255,0.6);padding:50px;margin-top:5%;overflow:auto;border-radius:10px;box-shadow:1px 1px 30px #000" class="center-block">
  <h2 class="text-center">Admin Login</h2>
  <form name="frmLogin">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" class="form-control" autofocus required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary pull-right" style="opacity:1">Login</button>
    </div>
  </form>
</div>
<?php
require_once 'footer.php';
?>
