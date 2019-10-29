<?php
require_once '../backend.php';
$adminInfo = getAdminInfo();
?>
<div id="editProfileModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Edit Profile</h4>
      </div>
      <form name="frmEditProfile">
        <input type="hidden" name="username" value="<?php echo $adminInfo['username']; ?>">
        <div class="modal-body">
          <div class="form-group">
            <label>First Name: </label>
            <input type="text" class="form-control" name="firstName" value="<?php echo $adminInfo['firstName']; ?>">
          </div>
          <div class="form-group">
            <label>Last Name: </label>
            <input type="text" class="form-control" name="lastName" value="<?php echo $adminInfo['lastName']; ?>">
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="changePasswordModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Change Password</h4>
      </div>
      <form name="frmChangePassword">
        <input type="hidden" name="username" value="<?php echo $adminInfo['username']; ?>">
        <div class="modal-body">
          <div class="form-group">
            <label>Old Password: </label>
            <input type="password" class="form-control" name="oldPass">
          </div>
          <div class="form-group">
            <label>New Password: </label>
            <input type="password" class="form-control" name="newPass">
          </div>
          <div class="form-group">
            <label>Retype New Password: </label>
            <input type="password" class="form-control" name="vNewPass">
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="addRoomIDModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Add Room ID</h4>
      </div>
      <form name="frmAddRoomID">
        <div class="modal-body">
          <div class="form-group">
            <label>Room ID: </label>
            <input type="number" name="roomID" class="form-control">
          </div>
          <div class="form-group">
            <label>Room Type: </label>
            <select class="form-control" name="cmbRoomType">
<?php
$result = $db->query('SELECT * FROM room_types');
while ($row = $result->fetch_assoc()) {
  echo "<option value='{$row['roomTypeID']}'>{$row['name']}</option>";
}
?>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="editRoomIDModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Room ID: <span data-id="roomID"></span></h4>
      </div>
      <form name="frmEditRoomID">
        <input type="hidden" name="roomID">
        <div class="modal-body">
          <div class="form-group">
            <label>Room Type: </label>
            <select class="form-control" name="cmbRoomType">
<?php
$result = $db->query('SELECT * FROM room_types');
while ($row = $result->fetch_assoc()) {
  echo "<option value='{$row['roomTypeID']}'>{$row['name']}</option>";
}
?>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="addRoomTypeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Add Room Type</h4>
      </div>
      <form name="frmAddRoomType">
        <div class="modal-body">
          <div class="form-group">
            <label>Name: </label>
            <input type="text" name="txtName" class="form-control">
          </div>
          <div class="form-group">
            <label>Description: </label>
            <textarea name="txtDescription" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Feature: </label>
            <textarea name="txtFeature" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Capacity: </label>
            <input type="text" class="form-control" name="txtCapacity" numberformat>
          </div>
          <div class="form-group">
            <label>Room Number: </label>
            <textarea name="txtRoomNumber" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Rate: </label>
            <input type="text" class="form-control" name="txtRate" numberformat>
          </div>
          <div class="form-group">
            <label>Image: </label>
            <input type="file" class="form-control" name="imgImage">
            <img src="" style="width:100%;max-height:300px;object-fit:cover">
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="editRoomTypeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Room Type: <span data-id="roomType"></span></h4>
      </div>
      <form name="frmEditRoomType">
        <input type="hidden" name="roomTypeID">
        <div class="modal-body">
          <div class="form-group">
            <label>Name: </label>
            <input type="text" name="txtName" class="form-control">
          </div>
          <div class="form-group">
            <label>Description: </label>
            <textarea name="txtDescription" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Feature: </label>
            <textarea name="txtFeature" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Capacity: </label>
            <input type="text" class="form-control" name="txtCapacity" numberformat>
          </div>
          <div class="form-group">
            <label>Room Number: </label>
            <textarea name="txtRoomNumber" class="form-control" rows="5" style="resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label>Rate: </label>
            <input type="text" class="form-control" name="txtRate" numberformat data-decimal="false">
          </div>
          <div class="form-group">
            <label>Image: </label>
            <input type="file" class="form-control" name="imgImage">
            <img src="" style="width:100%;max-height:300px;object-fit:cover">
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="editAccountTypeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Username: <span data-id="username"></span></h4>
      </div>
      <form name="frmEditAccountType">
        <input type="hidden" name="username">
        <div class="modal-body">
          <div class="form-group">
            <label>Account Type: </label>
            <select name="cmbAccountType" class="form-control">
              <option>Front-desk</option>
              <option>Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="verifyPasswordModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Verify Password</h4>
      </div>
      <form name="frmVerifyPassword">
        <input type="hidden" name="name">
        <input type="hidden" name="type">
        <input type="hidden" name="status">
        <div class="modal-body">
          <div class="form-group">
            <label>Password: </label>
            <input type="password" class="form-control" name="password" autofocus>
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary">Go</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="registerModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Admin Registration</h4>
      </div>
      <form name="frmRegister">
        <input type="hidden" name="admin">
        <div class="modal-body">
          <div class="col-md-6">
            <div class="form-group" ng-class="{'has-error': frmRegister.username.$touched && frmRegister.username.$invalid}">
              <label>Username: </label>
              <input type="text" name="username" class="form-control" ng-model="username" required>
              <span ng-show="frmRegister.username.$touched && frmRegister.username.$error.required" class="text-danger">The username is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.password.$touched && frmRegister.password.$invalid}">
              <label>Password: </label>
              <input type="password" name="password" class="form-control" ng-model="password" required>
              <span ng-show="frmRegister.password.$touched && frmRegister.password.$error.required" class="text-danger">The password is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.vpassword.$touched && frmRegister.vpassword.$invalid}">
              <label>Verify Password: </label>
              <input type="password" name="vpassword" class="form-control" ng-model="vpassword" ng-pattern="(password)" required>
              <span ng-show="frmRegister.vpassword.$touched && frmRegister.vpassword.$error.required" class="text-danger">The verify password is required.</span>
              <span ng-show="frmRegister.vpassword.$touched && frmRegister.vpassword.$error.pattern" class="text-danger">The password is not match.</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" ng-class="{'has-error': frmRegister.accountType.$touched && frmRegister.accountType.$invalid}">
              <label>Account Type: </label>
              <select name="accountType" class="form-control" ng-model="accountType" required>
                <option disabled></option>
                <option>Front-desk</option>
                <option>Admin</option>
              </select>
              <span ng-show="frmRegister.accountType.$touched && frmRegister.accountType.$error.required" class="text-danger">The account type is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.firstName.$touched && frmRegister.firstName.$invalid}">
              <label>First Name: </label>
              <input type="text" name="firstName" class="form-control" ng-model="firstName" required>
              <span ng-show="frmRegister.firstName.$touched && frmRegister.firstName.$error.required" class="text-danger">The first name is required.</span>
            </div>
            <div class="form-group" ng-class="{'has-error': frmRegister.lastName.$touched && frmRegister.lastName.$invalid}">
              <label>Last Name: </label>
              <input type="text" name="lastName" class="form-control" ng-model="lastName" required>
              <span ng-show="frmRegister.lastName.$touched && frmRegister.lastName.$error.required" class="text-danger">The last name is required.</span>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-primary" ng-disabled="frmRegister.$invalid">Register</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="reservationModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Reservation ID: <span data-id="reservationID"></span></h4>
      </div>
      <form name="frmReservation">
        <input type="hidden" name="reservationID">
        <input type="hidden" name="type">
        <div class="modal-body" style="padding-bottom:20px">
          <div style="width:100%;overflow:auto;border-bottom: 1px solid #eee;margin-bottom:20px;display:none" class="guestform">
            <input type="hidden" name="register" disabled>
            <div class="col-md-6">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" name="firstName" disabled>
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" name="lastName" disabled>
              </div>
              <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="emailAddress" disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Number</label>
                <input type="text" class="form-control" name="contactNumber" disabled>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="address" disabled>
              </div>
              <button type="button" class="btn btn-primary pull-right" onclick="hideRegistrationInfo()">Cancel</button>
            </div>
          </div>
          <div style="width:100%;overflow:auto;border-bottom: 1px solid #eee;margin-bottom:20px" class="emailform">
            <div class="col-md-12" style="overflow:auto">
              <div class="form-group">
                <label>Email Address</label>
                <select name="emailAddress" class="form-control">
<?php
$result = $db->query("SELECT * FROM guest WHERE status='1'");
while ($row = $result->fetch_assoc()):
?>
                  <option><?php echo $row['emailAddress']; ?></option>
<?php endwhile;?>
                </select>
              </div>
              <button type="button" class="btn btn-primary pull-right" style="margin-bottom:10px" onclick="showRegistrationInfo()">Add Guest</button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Check In Date</label>
              <input type="date" name="checkInDate" min="<?php echo date('Y-m-d'); ?>"class="form-control">
            </div>
            <div class="form-group">
              <label>Check Out Date</label>
              <input type="date" name="checkOutDate" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="form-group col-md-4">
                <label>Adults</label>
                <input type="number" name="adults" min="1" value="1" class="form-control">
              </div>
              <div class="form-group col-md-4">
                <label>Children</label>
                <input type="number" name="children" min="0" value="0" class="form-control">
              </div>
              <div class="form-group col-md-4">
                <label>Toddlers</label>
                <input type="number" name="toddlers" min="0" value="0" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label>Payment Method</label>
              <select name="paymentMethod" class="form-control">
                <option>Cash</option>
                <option>Bank</option>
              </select>
            </div>
          </div>
          <hr width="100%">
          <div style="min-height:200px;position:relative">
            <div id="loadingMode" style="height:100%;width:98%;position:absolute"></div>
            <div class="roomList row" style="margin:0">
<?php
$result = $db->query('SELECT * FROM room_types ORDER BY capacity, name ASC');
while ($row = $result->fetch_assoc()):
?>
              <div class="col-md-4">
                <h5 align="center"><?php echo $row['name'] ?></h5>
                <select data-name="<?php echo $row['name'] ?>" class="form-control cmbRooms" multiple>
                  <!-- To be supplied through AJAX -->
                  <option value="1">1</option>
                  <option value="1">1</option>
                  <option value="1">1</option>
                </select>
              </div>
<?php endwhile;?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="expensesModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Expenses List</h4>
      </div>
      <div class="modal-body" style="overflow:auto">
        <input type="hidden" name="reservationID">
        <table class="table table-hover table-striped table-bordered" style="margin-bottom:0">
          <thead>
            <th>Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Action</th>
          </thead>
          <tbody>
            <!-- TO BE SUPPLIED VIA AJAX -->
          </tbody>
        </table>
        <div style="cursor:pointer;border: 1px dashed #ccc;padding:10px" width="100%" align="center" onclick="addRow(this)">Add</div>
        <div style="float:right;font-size:16px;margin-top:10px">Total Expenses: ₱ <span data-id="totalExpenses" style="font-weight:bold"></span></div>
      </div>
    </div>
  </div>
</div>
<div id="billModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Reservation ID: <span data-id="reservationID"></span></h4>
      </div>
      <div class="modal-body" style="overflow:auto">
        <input type="hidden" name="reservationID">
        <iframe name="billPDF" src="" frameborder="0" width="100%" height="500px"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="window.frames.billPDF.focus();window.frames.billPDF.print();" class="btn btn-primary">Print</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
