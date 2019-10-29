<?php
$styles = [
  'th' => 'border: 2px solid #ddd;border-bottom: 2px solid #ddd;padding: 8px;line-height: 1.42857143;',
  'td' => 'padding: 8px;line-height: 1.42857143;vertical-align: top;text-align:center;border: 1px solid #ddd;'
];
ob_start();
?>
<div style="font-family: Century Gothic">
  <div style="width:100%;background-color:#1abc9c;margin:0;padding:20px 0">
    <img src="https://i.imgur.com/RbaCDK7.png" alt="Rio Villa Nuevo Logo" style="display:block;margin-left:auto;margin-right:auto">
  </div>
  <h1 style="text-align:center">Reservation Summary</h1>
  <hr style="border-top: 1px solid #ddd">
  <p>Thank you for booking with us!<br>
  This email is to confirm your reservation with the information as follows:</p>
  <div style="overflow:auto">
    <table style="float:left;width:50%">
      <tr>
        <td style="font-weight:bold;width:30%">Guest Name:</td>
        <td><?php echo $details['guestName']; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;width:30%">Contact Number:</td>
        <td><?php echo $details['contactNumber']; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;width:30%">Check-in Date:</td>
        <td><?php echo $details['checkInDate']; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;width:30%">Check-out Date:</td>
        <td><?php echo $details['checkOutDate']; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;width:30%">Number of Nights:</td>
        <td><?php echo $details['numberOfNights']; ?></td>
      </tr>
    </table>
    <table style="float:left;width:50%">
      <tr>
        <td style="font-weight:bold;width:30%">Payment Method:</td>
        <td><?php echo $details['paymentMethod']; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;width:30%" colspan="2">Number of Guests:</td>
      </tr>
      <tr>
        <td style="padding-left:20px" colspan="2">
          Adults: <?php echo $details['adults']; ?><br>
          Children: <?php echo $details['children']; ?><br>
          Toddlers: <?php echo $details['toddlers']; ?>
        </td>
      </tr>
    </table>
  </div>
  <table width="100%" style="border-spacing: 0;border-collapse: collapse;">
    <thead>
      <th style="<?php echo $styles['th']; ?>">Room Types</th>
      <th style="<?php echo $styles['th']; ?>">Room Number/s</th>
    </thead>
    <tbody>
      <?php echo str_replace('<td>', "<td style='{$styles['td']}'>", $content); ?>
    </tbody>
  </table>
  <p>Please kindly download the file attached to the email and read through the details specified in the PDF for more information regarding your reservation.<br><br>

  Warmest regards, <br>
  Rio Villa Nuevo Mineral Water Resort Management</p>
</div>
<?php
return ob_get_clean();
?>
