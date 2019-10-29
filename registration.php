<?php
$title     = 'Registration';
$includejs = [
  'js/controller.js'
];
require_once 'header.php';
?>
<h1 style="width:100%;background-color:#1abc9c;text-align:center;margin:0;height:70px;line-height:66px">
  <a href="./" onclick="return confirm('Are you sure you want to go back to the home page?')"><img src="image/logo.png" alt=""></a>
</h1>
<div class="regPanel bg" >
  <div class="panel registration shadow col-md-12">
    <div class="panel-heading" style="color:white; background-color: #1abc9c;">
      <h4 align="center">REGISTRATION PAGE</h4>
    </div>
    <div class="panel-body">
      <div class="col-md-12" style="margin-bottom:30px">
        <form name="frmRegister">
          <div class="col-md-6">
            <div class="form-group" ng-class="{'has-error': frmRegister.firstName.$touched && frmRegister.firstName.$invalid}">
              <label>First Name: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-user"> </span>
                </span>
                <input type="text" class="form-control" placeholder="First Name" name="firstName" ng-model="firstName" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.firstName.$touched && frmRegister.firstName.$error.required" class="text-danger">The first name is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.lastName.$touched && frmRegister.lastName.$invalid}">
              <label>Last Name: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-user"> </span>
                </span>
                <input type="text" class="form-control" placeholder="Last Name" name="lastName" ng-model="lastName" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.lastName.$touched && frmRegister.lastName.$error.required" class="text-danger">The last name is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.contactNumber.$touched && frmRegister.contactNumber.$invalid}">
              <label> Contact number: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-mobile"> </span>
                </span>
                <input type="text" class="form-control" placeholder="Contact Number" name="contactNumber" ng-model="contactNumber" onkeypress="return blockNumbers(event)" maxlength="11" required />
              </div>
              <span ng-show="frmRegister.contactNumber.$touched && frmRegister.contactNumber.$error.required" class="text-danger">The contact number is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.address.$touched && frmRegister.address.$invalid}">
              <label>Address: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-map-pin"> </span>
                </span>
                <input type="text" class="form-control" placeholder="Address" name="address" ng-model="address" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.address.$touched && frmRegister.address.$error.required" class="text-danger">The address is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.emailAddress.$touched && frmRegister.emailAddress.$invalid}">
              <label>Email Address: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-envelope"> </span>
                </span>
                <input type="email" class="form-control" placeholder="Email Address" name="emailAddress" ng-model="emailAddress" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.emailAddress.$touched && frmRegister.emailAddress.$error.required" class="text-danger">The email is required.</span>
              <span ng-show="frmRegister.emailAddress.$touched && frmRegister.emailAddress.$invalid" class="text-danger">The email is invalid.</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" ng-class="{'has-error': frmRegister.password.$touched && frmRegister.password.$invalid}">
              <label>Password: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-key"> </span>
                </span>
                <input type="password" class="form-control" placeholder="Password" name="password" ng-model="password" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.password.$touched && frmRegister.password.$error.required" class="text-danger">The password is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.vpassword.$touched && frmRegister.vpassword.$invalid}">
              <label>Verify Password: </label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-key"> </span>
                </span>
                <input type="password" class="form-control" placeholder="Verify Password" name="vpassword" ng-model="vpassword" ng-pattern="(password)" maxlength="100" required />
              </div>
              <span ng-show="frmRegister.vpassword.$touched && frmRegister.vpassword.$error.required" class="text-danger">The verify password is required.</span>
              <span ng-show="frmRegister.vpassword.$touched && frmRegister.vpassword.$error.pattern" class="text-danger">The password is not match.</span>
            </div>
            <div class="g-recaptcha" data-sitekey="<?php echo $gcaptcha['site']; ?>"></div>
          </div>
          <div class="btn-group pull-right">
            <button type="reset" class="btn btn-primary" style="margin-top:10px">Reset</button>
            <button type="submit" class="btn btn-primary" style="margin-top:10px" ng-disabled="frmRegister.$invalid">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'footer.php';
?>
