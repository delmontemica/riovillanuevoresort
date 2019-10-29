<?php
$includejs = [
  'js/reservation.js'
];

require_once 'header.php';
require_once 'modal.php';

if (!isLogged()) {
  echo "<script>alert('Login first.');location.href='./?redirect=reservation.php'</script>";
  die();
} else if (!isVerified()) {
  echo "<script>alert('Please verify your email first.');location.href='./'</script>";
  die();
}

$booking = $_SESSION['booking'] ?? [];
?>
<style>
  .panel {
    border-radius: 0
  }
  .panel-heading {
    background-color:#1abc9c !important;
    color:white !important;
    font-weight: bold;
    overflow: auto;
  }
  .bookingSummary {
    display: none;
    padding:  20px !important;
  }
</style>
<h1 style="width:100%;background-color:#1abc9c;text-align:center;margin:0;min-height:70px;line-height:66px;overflow:auto">
  <div class="col-md-12">
    <a href="./" onclick="return confirm('Are you sure you want to go back to the home page?')"><img src="image/logo.png" alt=""></a>
  </div>
  <span class="login-text">Logged in as: <b><?php echo getUserInfo()['name'] ?></b></span>
</h1>
<div class="sticky-step shadow">
  <div class="sw-main sw-theme-circles center-block"></div>
</div>
<div style="background-color:white;padding:5%">
  <form name="frmBook">
    <div id="smartwizard">
      <ul>
        <div class="animate-step" style="position:absolute;background-color:#5cb85c;height:5px;top:0;top:66px;left:13%"></div>
        <li class="step"><a href="#step-1"><i class="fa fa-calendar-o fa-2x glow" style="line-height:20px"></i><br /><small>CHECK IN &amp; CHECK OUT</small></a></li>
        <li class="step"><a href="#step-2"><i class="fa fa-bed fa-2x" style="line-height:20px"></i><br /><small>ROOM &amp; RATES</small></a></li>
        <li class="step"><a href="#step-3"><i class="fa fa-user fa-2x" style="line-height:20px"></i><br /><small style="left:1px"><span style="margin-left:2px">PAYMENT</span><br>METHOD</small></a></li>
        <li class="step"><a href="#step-4"><i class="fa fa-check fa-2x" style="line-height:20px"></i><br /><small style="left:-6px"><span style="margin-left:12px">BOOKING</span><br>INFORMATION</small></a></li>
      </ul>
      <div>
        <div id="step-1">
          <div class="noteReservation">
            Please choose your desired date in the calendar and enter the number of guests.
          </div>
          <div class="col-md-9">
            <div class="calendar"></div>
          </div>
          <div class="col-md-3">
            <table class="leftSideTable shadow">
              <thead>
                <th colspan="2">BOOKING SUMMARY</th>
              </thead>
              <tbody>
                <tr>
                  <td>Check-in</td>
                  <td><input name="txtCheckInDate" class="form-control" value="<?php echo $booking['checkInDate'] ?? ''; ?>" readonly></td>
                </tr>
                <tr>
                  <td>Check-out</td>
                  <td><input name="txtCheckOutDate" class="form-control" value="<?php echo $booking['checkOutDate'] ?? ''; ?>" readonly></td>
                </tr>
                <tr>
                  <td>Number of Nights</td>
                  <td><input name="txtNoOfNights" class="form-control" value="<?php echo $booking['numberOfNights'] ?? ''; ?>" readonly></td>
                </tr>
              </tbody>
            </table>
            <br>
            <table class="leftSideTable shadow">
              <thead>
                <th colspan="2">GUEST INFORMATION</th>
              </thead>
              <tbody>
                <tr>
                  <td>Adults</td>
                  <td><input type="number" name="txtAdults" class="form-control" min="1" max="50" value="<?php echo $booking['adults'] ?? 1; ?>"></td>
                </tr>
                <tr>
                  <td>Children (4 ft. below)</td>
                  <td><input type="number" name="txtChildren" class="form-control" min="0" max="10" value="<?php echo $booking['children'] ?? 0; ?>"></td>
                </tr>
                <tr>
                  <td>Toddlers (1-3 y/o)</td>
                  <td><input type="number" name="txtToddlers" class="form-control" min="0" max="10" value="<?php echo $booking['toddlers'] ?? 0; ?>"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div id="step-2">
          <div class="noteReservation">
            Please select a room by clicking the "SELECT ROOM" button.<br>
            Your selected rooms are listed on the booking summary.
          </div>
          <div class="bookingSummary col-md-3">
            <div class="panel panel-default shadow">
              <div class="panel-heading">BOOKING SUMMARY</div>
              <div class="panel-body">
                <strong>Check-in</strong>: <span data-id="checkInDate"></span><br>
                <strong>Check-out</strong>: <span data-id="checkOutDate"></span><br>
                <strong>Number of Nights</strong>: <span data-id="numberOfNights"></span><br>
                <strong>Number of Guests</strong>: <br>
                <div style="text-indent: 2em"><span data-id="adults"></span> Adult/s</div>
                <div style="text-indent: 2em"><span data-id="children"></span> Children</div>
                <div style="text-indent: 2em"><span data-id="toddlers"></span> Toddlers</div>
                <div class="bookingRoomList" style="border-top: 1px solid #aaa;margin:10px 0;display:none">

                </div>
                <div class="bookingTotal" style="border-top: 1px solid #aaa;margin:10px 0;display:none">
                  Total: <span class="allTotal pull-right" style="font-weight:bold"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-9">
            <h3>SELECT YOUR ROOM</h3>
            <h5>ROOMS AVAILABLE FOR SELECTED DATE</h5>
            <div class="roomList">
              <div id="loading" style="height:400px"></div>
              <!-- TO BE SUPPLIED VIA AJAX -->
            </div>
          </div>
        </div>
        <div id="step-3">
          <div class="noteReservation">
            This is your reservation summary.<br>
            Please select a payment method and check the terms and conditions.
          </div>
          <div class="panel panel-default shadow">
            <div class="panel-heading">RESERVATION DETAILS</div>
            <div class="panel-body">
              <div id="guestDetails" style="padding:5px 30px">
                <div class="col-md-6">
                  <strong>Guest Name:</strong> <span data-id="guestName"></span><br>
                  <strong>Contact Number:</strong> <span data-id="contactNumber"></span><br>
                  <strong>Email Address:</strong> <span data-id="emailAddress"></span><br>
                </div>
                <div class="col-md-6">
                  <strong>Check-in Date:</strong> <span data-id="checkInDate"></span><br>
                  <strong>Check-out Date:</strong> <span data-id="checkOutDate"></span><br>
                  <strong>Number of Nights:</strong> <span data-id="noOfNights"></span><br>
                  <strong>Number of Guests:</strong><br>
                  <div style="padding-left:10px">
                    Adults: <span data-id="adults"></span><br>
                    Children: <span data-id="children"></span><br>
                    Toddlers: <span data-id="toddlers"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-9">
              <div class="panel panel-default shadow">
                <div class="panel-heading">ROOM LIST</div>
                <div class="panel-body">
                  <div class="table-responsive">
                    <table class="tblRoomList table table-stripped table-hover">

                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="panel panel-default shadow">
                <div class="panel-heading">PAYMENT METHOD</div>
                <div class="panel-body">
                  <label style="cursor:pointer">
                    <input type="radio" name="rdPaymentMethod" value="Cash"/>
                    <i class="fa fa-money" style="font-size: 1.125em;"></i>
                    &nbsp;Cash
                  </label>
                  <small>(Pay at the resort)</small>
                  <br>
                  <label style="cursor:pointer">
                    <input type="radio" name="rdPaymentMethod" value="Bank"/>
                    <i class="fa fa-bank" style="font-size: 1.125em;"></i>
                    &nbsp;Bank
                  </label>
                  <small>(Pay at the bank)</small>
                </div>
              </div>
            </div>
          </div>
          <div class="checkbox center-block text-center">
            <label style="font-size:16px;line-height:25px">
              <input type="checkbox" name="cbxTermsAndConditions" style="width:20px;height:20px">
              <span style="margin-left:10px">
                I've read the <a style="text-decoration:underline;text-decoration-style:dotted;cursor:pointer" data-toggle="modal" data-target="#termsAndConditionsModal">Terms and Conditions</a>
              </span>
            </label>
          </div>
        </div>
        <div id="step-4">
          <div class="noteReservation">
            Your reservation is now saved.<br>
            Please check your email for reservation summary.<br>
            Kindly print the attached pdf as it will serve as your reservation confirmation.
          </div>
          <div class="panel panel-default shadow">
            <div class="panel-heading text-center"><h3>RESERVATION ID: <b><span data-id="reservationID"></span></b></h3></div>
            <div class="panel-body">
              <div id="finalGuestDetails" style="margin-bottom:20px;overflow:auto">

              </div>
              <table class="tblRoomList table table-bordered table-hover">

              </table>
              <table id="finalSummary" class="table table-bordered table-hover">
                <thead>
                  <th align="center">ROOM TYPES</th>
                  <th align="center">ROOM NUMBER(s)</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<?php
define('REMOVE_FOOTER', true);
require_once 'footer.php';
?>
<script>
  window.beforeunload = () => {
    return false
  };
</script>
