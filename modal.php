<?php
require_once 'backend.php';

if (!isLogged()):
?>
<div id="loginModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:400px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Login</h4>
      </div>
      <form name="frmLogin">
        <div class="modal-body">
          <div class="row" style="margin-bottom:10px">
            <div class="col-md-4 text-center" style="line-height:30px;padding:0 10px">
              Email Address
            </div>
            <div class="col-md-8">
              <input type="email" name="email" class="form-control" maxlength="100" required autofocus>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 text-center" style="line-height:30px">
              Password
            </div>
            <div class="col-md-8">
              <input type="password" name="password" class="form-control" maxlength="100" required>
            </div>
          </div>
          <a href="forgotpassword.php" class="pull-right" style="margin: 10px 0">Forgot Password?</a>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="button" onclick="location.href='registration.php'" class="btn btn-default pull-left">Register</button>
          <button type="submit" class="btn btn-default">Login</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php else: ?>
<div id="editAccModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Edit Account</h4>
      </div>
      <form name="frmEditAcc" ng-controller="EditProfileController">
        <div class="form-group" ng-class="{'has-error': frmEditAcc.firstName.$touched && frmEditAcc.firstName.$invalid}" style="line-height:30px;padding:0 30px">
          <label>First Name: </label>
          <div class="input-group">
            <span class="input-group-addon">
              <span class="fa fa-user"> </span>
            </span>
            <input type="text" class="form-control" placeholder="First Name" name="firstName" ng-model="firstName" maxlength="100" required />
          </div>
          <span ng-show="frmEditAcc.firstName.$touched && frmEditAcc.firstName.$error.required" class="text-danger">The first name is required.</span>
        </div>
        <div class="form-group" ng-class="{'has-error': frmEditAcc.lastName.$touched && frmEditAcc.lastName.$invalid}" style="line-height:30px;padding:0 30px">
          <label>Last Name: </label>
          <div class="input-group">
            <span class="input-group-addon">
              <span class="fa fa-user"> </span>
            </span>
            <input type="text" class="form-control" placeholder="Last Name" name="lastName" ng-model="lastName" maxlength="100" required />
          </div>
          <span ng-show="frmEditAcc.lastName.$touched && frmEditAcc.lastName.$error.required" class="text-danger">The last name is required.</span>
        </div>
        <div class="form-group" ng-class="{'has-error': frmEditAcc.contactNumber.$touched && frmEditAcc.contactNumber.$invalid}" style="line-height:30px;padding:0 30px">
          <label> Contact number: </label>
          <div class="input-group">
            <span class="input-group-addon">
              <span class="fa fa-mobile"> </span>
            </span>
            <input type="text" class="form-control" placeholder="Contact Number" name="contactNumber" ng-model="contactNumber" onkeypress="return blockNumbers(event)" maxlength="11" required />
          </div>
          <span ng-show="frmEditAcc.contactNumber.$touched && frmEditAcc.contactNumber.$error.required" class="text-danger">The contact number is required.</span>
        </div>
        <div class="form-group" ng-class="{'has-error': frmEditAcc.address.$touched && frmEditAcc.address.$invalid}" style="line-height:30px;padding:0 30px">
          <label>Address: </label>
          <div class="input-group">
            <span class="input-group-addon">
              <span class="fa fa-map-pin"> </span>
            </span>
            <input type="text" class="form-control" placeholder="Address" name="address" ng-model="address" maxlength="100" required />
          </div>
          <span ng-show="frmEditAcc.address.$touched && frmEditAcc.address.$error.required" class="text-danger">The address is required.</span>
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="reset" class="btn btn-default">Reset</button>
          <button type="submit" class="btn btn-default" ng-disabled="frmEditAcc.$invalid">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="resHistoryModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Reservation History</h4>
      </div>
      <div class="modal-body">
        <input type="file" name="imgUpload" style="display:none">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <th style="width:5%">Reservation ID</th>
              <th style="width:10%">Check In</th>
              <th style="width:10%">Check Out</th>
              <th style="width:10%">Adults</th>
              <th style="width:10%">Children</th>
              <th style="width:10%">Toddlers</th>
              <th style="width:10%">Room ID</th>
              <th style="width:15%">Date Created</th>
              <th style="width:10%">Total Amount</th>
            </thead>
<?php
$result = $db->query("
  SELECT
  reservation.reservationID,
  guest.emailAddress,
  reservation_check.checkIn,
  reservation_check.checkOut,
  adults,
  children,
  toddlers,
  dateCancelled,
  dateCreated
  FROM guest
  JOIN reservation
  ON guest.emailAddress=reservation.emailAddress
  LEFT JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
  LEFT JOIN reservation_cancelled
  ON reservation.reservationID=reservation_cancelled.reservationID
  WHERE guest.emailAddress='" . getUserInfo()['emailAddress'] . "' ORDER BY reservation.reservationID DESC
");

while ($row = $result->fetch_assoc()) {
  if (!($row['checkOut'] != null || $row['dateCancelled'] != null)) {
    continue;
  }

  $roomIDs = getAllRooms($row['reservationID']);

  sort($roomIDs);

  echo '<tr' . ($row['dateCancelled'] ? " style='color:red'" : '') . '>';
  echo "<td>{$row['reservationID']}</td>";
  echo '<td>' . dateFormat($row['checkIn'], 'M d, Y') . '<br>' . dateFormat($row['checkIn'], 'h:i:s A') . '</td>';
  echo '<td>' . dateFormat($row['checkOut'], 'M d, Y') . '<br>' . dateFormat($row['checkOut'], 'h:i:s A') . '</td>';
  echo "<td>{$row['adults']}</td>";
  echo "<td>{$row['children']}</td>";
  echo "<td>{$row['toddlers']}</td>";
  echo '<td>' . join(', ', $roomIDs) . '</td>';
  echo '<td>' . dateFormat($row['dateCreated'], 'M d, Y h:i:s A') . '</td>';
  echo '<td>' . pesoFormat(getTotalAmount($row['reservationID'])) . '</td>';
  echo '</tr>';
}
?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="resListModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Reservation List</h4>
      </div>
      <div class="modal-body">
        <input type="file" name="imgUpload" style="display:none">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <th style="width:5%">Reservation ID</th>
              <th style="width:10%">Adults</th>
              <th style="width:10%">Children</th>
              <th style="width:10%">Toddlers</th>
              <th style="width:10%">Room ID</th>
              <th style="width:15%">Date Created</th>
              <th style="width:10%">Total Amount</th>
              <th style="width:10%">Action</th>
            </thead>
<?php
$result = $db->query("
  SELECT
  reservation.reservationID,
  guest.emailAddress,
  reservation_check.checkIn,
  reservation_check.checkOut,
  adults,
  children,
  toddlers,
  dateCancelled,
  dateCreated
  FROM guest
  JOIN reservation
  ON guest.emailAddress=reservation.emailAddress
  LEFT JOIN reservation_check
  ON reservation.reservationID=reservation_check.reservationID
  LEFT JOIN reservation_cancelled
  ON reservation.reservationID=reservation_cancelled.reservationID
  WHERE guest.emailAddress='" . getUserInfo()['emailAddress'] . "' ORDER BY reservation.reservationID DESC
");

while ($row = $result->fetch_assoc()) {
  if (!($row['checkOut'] == null && $row['dateCancelled'] == null)) {
    continue;
  }

  $roomIDs = getAllRooms($row['reservationID']);

  sort($roomIDs);

  echo '<tr' . ($row['dateCancelled'] ? " style='color:red'" : '') . '>';
  echo "<td>{$row['reservationID']}</td>";
  echo "<td>{$row['adults']}</td>";
  echo "<td>{$row['children']}</td>";
  echo "<td>{$row['toddlers']}</td>";
  echo '<td>' . join(', ', $roomIDs) . '</td>';
  echo '<td>' . dateFormat($row['dateCreated'], 'M d, Y h:i:s A') . '</td>';
  echo '<td>' . pesoFormat(getTotalAmount($row['reservationID'], false)) . '</td>';
  echo '<td>';
  if ($row['checkOut'] == null && $row['dateCancelled'] == null) {
    echo "<button class='btn btn-primary btnUpload' data-id='{$row['reservationID']}'>UPLOAD</button>";
  }
  echo '</td>';
  echo '</tr>';
}
?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="uploadImageModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Reservation ID: <span data-id="reservationID"></span></h4>
      </div>
      <form name="frmReservationUpload">
        <div class="modal-body">
          <input type="hidden" name="reservationID">
          <img src="" style="height:500px;width:100%;object-fit:cover" class="center-block">
        </div>
        <div class="modal-footer" style="clear:both">
          <button type="submit" class="btn btn-default">Upload</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif;?>
<div id="accommodationRoomModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center"></h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer" style="clear:both">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="termsAndConditionsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Terms and Conditions</h4>
      </div>
      <div class="modal-body">
        <ol>
          <li>Smoking is not permitted in any accommodation managed by Rio Villa Nuevo Mineral Water Resort at any time.</li><br>
          <li>Pets are strictly prohibited within the resort premises.</li><br>
          <li>Foods and Drinks are not allowed in the pool area.</li><br>
          <li>Resort guests are obliged to make an effort to keep the surroundings clean. Guests should refrain from throwing rubbish anywhere other than the strategically located trash bins.</li><br>
          <li>Entrance fee is to be paid in the resort and is not counted in the sum of payment for the reservation.</li><br>
          <li>Keys must be returned to front-desk upon check-out. If lost, the guest needs to pay a fine of P100.00.</li><br>
          <li>The guest will be responsible for any breakages, losses or damage during his stay in accommodation. The additional expenses will be computed upon check-out.</li><br>
          <li>The guest has the right to charge a check-out earlier that the expected check-out date.</li><br>
          <li>The guest must inform the front-desk for late check out.</li><br>
          <li>In case of emergencies requiring medical attention, the guest can inform the front-desk to bring the ailing guest to the resort’s clinic.</li><br>
        </ol>
      </div>
      <div class="modal-footer" style="clear:both">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
