<?php
@session_start();
date_default_timezone_set('Asia/Manila');

use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/** Dotenv [Load Environment Variables from .env file] */
checkEnv();
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if (getenv('DEBUG') == 'true') {
  error_reporting(E_ALL);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
}

/**
 * VARIABLES
 */
$main_url       = stripos(getenv('MAIN_URL'), 'localhost') !== false ? '../' : getenv('MAIN_URL');
$admin_url      = getenv('ADMIN_URL');
$base_url       = getenv('BASE_URL');
$base_url_admin = getenv('BASE_URL_ADMIN');
$maintenance    = getenv('MAINTENANCE') == 'true';

$db = new mysqli(
  getenv('DB_HOST'),
  getenv('DB_USERNAME'),
  getenv('DB_PASSWORD'),
  getenv('DB_DATABASE'),
  getenv('DB_PORT')
);

$db->query("SET time_zone = '+08:00'");

$encrypt_keyword       = 'AFQnESvgCxXFNj7WkK9zVjxh9BAUD7Hd';
$initialization_vector = '4aM7Pp5pCCGFTXWy';

$gcaptcha = [
  'site'   => '6LeUH2MUAAAAAO9Kdv45_PmoIQYECmlCHVpKmQ-H',
  'secret' => '6LeUH2MUAAAAACj4Oor_l2q6XceWshKr1sqvxBIN'
];

$table = [
  'guest'     => 'guest',
  'admin'     => 'admin',
  'room'      => 'room',
  'roomTypes' => 'room_types'
];
$mailbox = [
  'host'  => 'ssl://cpanel03wh.sin1.cloud.z.com:465',
  'email' => 'admin@riovillanuevoresort.com',
  'pass'  => 'rio@9899'
];

$domPDFOptions = new Options();
$domPDFOptions->setDpi(150);
$domPDFOptions->set('DOMPDF_ENABLE_REMOTE', true);
$domPDFOptions->set('defaultMediaType', 'all');
$domPDFOptions->set('isFontSubsettingEnabled', true);

/**
 * @param Array $credentials ["username", "password"]
 * @return Boolean [if login successfully]
 */
function login($credentials, $admin = false) {
  global $db, $table;

  if (is_array($credentials)) {
    $credentials = escape($credentials);

    $password = $credentials['password'];

    if (!$admin) {
      $email = $credentials['emailAddress'];

      $row = $db->query("SELECT * FROM {$table['guest']} WHERE emailAddress='{$email}'")->fetch_assoc();
    } else {
      $username = $credentials['username'];

      $row = $db->query("SELECT * FROM {$table['admin']} WHERE username='{$username}'")->fetch_assoc();
    }

    if ($row && password_verify($password, $row['password']) && $row['status'] != 0) {
      if (!$admin) {
        $_SESSION['account']['email'] = $email;
        createLog('User Logged In', $email);
      } else {
        $_SESSION['admin']['username'] = $username;
        createLog('Admin Logged In', $username, true);
      }

      return true;
    } else {
      return 'Invalid ' . ($admin ? 'Username' : 'Email') . ' and/or Password!';
    }
  } else {
    throw new InvalidArgumentException(__FUNCTION1__ . ' expecting array, got ' . gettype($credentials));
  }
}

/**
 * @param $admin
 */
function logout($admin = false) {
  if (!$admin) {
    if (isset($_SESSION['account'])) {
      unset($_SESSION['booking']);
      unset($_SESSION['account']);
    }
  } else {
    if (isset($_SESSION['admin'])) {
      unset($_SESSION['admin']);
    }
  }
}

/**
 * @param Array $credentials
 *   [
 *     'emailAddress'  => '',
 *     'password'      => '',
 *     'firstName'     => '',
 *     'lastName'      => '',
 *     'contactNumber' => '',
 *     'address'       => '',
 *   ]
 * @return Boolean [if registered successfully]
 */
function register($credentials, $admin = false, $verification = false) {
  global $db, $table;

  if (is_array($credentials)) {
    if (!$admin) {
      $email         = escape($credentials['emailAddress'], true);
      $password      = password_hash(escape($credentials['password']), PASSWORD_DEFAULT);
      $firstName     = escape($credentials['firstName'], true);
      $lastName      = escape($credentials['lastName'], true);
      $contactNumber = escape($credentials['contactNumber'], true);
      $address       = escape($credentials['address'], true);
      $isVerified    = intval($verification);

      $db->query("INSERT INTO {$table['guest']} VALUES('{$email}', '{$password}', '{$firstName}', '{$lastName}', '{$contactNumber}', '{$address}', '{$isVerified}', 1)");
    } else {
      $username    = escape($credentials['username'], true);
      $password    = password_hash(escape($credentials['password'], PASSWORD_DEFAULT));
      $firstName   = escape($credentials['firstName'], true);
      $lastName    = escape($credentials['lastName'], true);
      $accountType = escape($credentials['accountType'], true);

      $db->query("INSERT INTO {$table['admin']} VALUES('{$username}', '{$password}', '{$accountType}', '{$firstName}', '{$lastName}', 1)");
    }

    if (!$db->error || $db->errno == 1062) {
      createLog('Account Registered', ($credentials['emailAddress'] ?? $credentials['username']), $admin);
      return $db->affected_rows > 0;
    } else {
      throw new Exception($db->error);
    }
  } else {
    throw new InvalidArgumentException(__FUNCTION1__ . ' expecting array, got ' . gettype($credentials));
  }
}

/**
 * @param $admin
 */
function checkUpdatedStatus($admin = false) {
  if (!$admin) {
    if (getUserInfo()['status'] == 0) {
      logout();
      header('Refresh:0');
    }
  } else {
    if (getAdminInfo()['status'] == 0) {
      logout(true);
      header('Refresh:0');
    }
  }
}
/**
 * @param String $email
 */
function verifyEmail($email) {
  global $db, $table;

  $db->query("UPDATE {$table['guest']} SET verified=1 WHERE emailAddress='{$email}'");

  if (!$db->error) {
    createLog('Account Verified', $email);
    return $db->affected_rows > 0;
  } else {
    throw new Exception($db->error);
  }
}

/**
 * @param $password
 */
function verifyPassword($password) {
  global $db, $table;

  if ($username = $_SESSION['admin']['username']) {
    $row = $db->query("SELECT * FROM {$table['admin']} WHERE username='{$username}'")->fetch_assoc();

    return $row && password_verify($password, $row['password']);
  }
}

/**
 * @return Boolean [if $_SESSION['account'] has value]
 */
function isLogged($admin = false) {
  return !$admin ? isset($_SESSION['account']) : isset($_SESSION['admin']);
}

/**
 * @return Boolean [if email is verified]
 */
function isVerified() {
  global $db, $table;

  if (isset($_SESSION['account'])) {
    return (bool) $db->query("SELECT * FROM {$table['guest']} WHERE emailAddress='{$_SESSION['account']['email']}'")->fetch_assoc()['verified'];
  } else {
    return false;
  }
}

/**
 * @param String $email
 * @param Array $credentials
 *   [
 *     'emailAddress'  => '',
 *     'password'      => '',
 *     'firstName'     => '',
 *     'lastName'      => '',
 *     'contactNumber' => '',
 *     'address'       => '',
 *   ]
 * @return Boolean [if edit profile successfully]
 */
function editProfile($email, $credentials) {
  global $db, $table;

  if (is_array($credentials)) {
    $credentials = escape($credentials, true);
    $email       = escape($email);

    $vars = [];

    foreach ($credentials as $key => $value) {
      $vars[] = "{$key}='{$value}'";
    }

    $db->query("UPDATE {$table['guest']} SET " . join(', ', $vars) . " WHERE emailAddress='$email'");

    if (!$db->error) {
      if ($db->affected_rows > 0) {
        createLog('User edit profile', $email);
        return true;
      } else {
        return 'Nothing Changed!';
      }
    } else {
      throw new Exception($db->error);
    }
  } else {
    throw new InvalidArgumentException(__FUNCTION1__ . ' expecting array, got ' . gettype($credentials));
  }
}

/**
 * @param String $password [Old Password]
 * @param String $newPassword [New Password]
 * @return Boolean [if change password successfully]
 */
function changePassword($email = null, $password, $newPassword) {
  global $db, $table;

  $email       = $email ?? $_SESSION['account']['email'];
  $newPassword = password_hash(escape($newPassword), PASSWORD_DEFAULT);

  if ($password) {
    $password = escape($password);
    $row      = $db->query("SELECT * FROM {$table['guest']} WHERE emailAddress='{$email}'")->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
      $db->query("UPDATE {$table['guest']} SET password='{$newPassword}' WHERE emailAddress='{$email}'");
    } else {
      return 'Invalid Password!';
    }
  } else {
    $db->query("UPDATE {$table['guest']} SET password='{$newPassword}' WHERE emailAddress='{$email}'");
  }
  if (!$db->error) {
    createLog('User password changed', $email);
    return $db->affected_rows > 0;
  } else {
    throw new Exception($db->error);
  }
}

/**
 * @param $email
 * @param $status
 */
function changeUserStatus($name, $status, $admin = false) {
  global $db, $table;

  if (!$admin) {
    $db->query("UPDATE {$table['guest']} SET status='{$status}' WHERE emailAddress='{$name}'");
    if ($db->affected_rows > 0) {
      createLog("User ({$name}) account " . ($status == 0 ? 'Deactivated' : 'Activated'), null, true);
      return true;
    } else {
      return false;
    }
  } else {
    $db->query("UPDATE {$table['admin']} SET status='{$status}' WHERE username='{$name}'");

    if ($db->affected_rows > 0) {
      createLog("Admin ({$name}) account " . ($status == 0 ? 'Deactivated' : 'Activated'), null, true);
      return true;
    } else {
      return false;
    }
  }
}

/**
 * @param $username
 * @param $type
 */
function editAccountType($username, $type) {
  global $db, $table;

  $db->query("UPDATE {$table['admin']} SET type='{$type}' WHERE username='{$username}'");

  if ($db->affected_rows > 0) {
    createLog("Admin ({$username}) account type changed to {$type}", null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param String $email
 */
function sendForgotPassword($email) {
  global $db, $table, $base_url;

  $email       = escape($email);
  $credentials = getUserInfo($email);
  $token       = generateRandomString(64);
  $db->query("INSERT INTO forgot_password VALUES('{$email}', '{$token}', 0)");

  if (!$db->error) {
    if ($db->affected_rows > 0) {
      createLog('User requested forgot password token', $email);
      return $token;
    } else {
      return false;
    }
  } else {
    throw new Exception($db->error);
  }
}

/**
 * @param $token
 */
function verifyForgotPasswordToken($email, $token) {
  global $db;

  $result = $db->query("SELECT * FROM forgot_password WHERE emailAddress='{$email}' AND token='{$token}' AND used=0");

  return $result->num_rows > 0;
}

/**
 * @param String $email
 * @param String $token
 */
function useForgotPasswordToken($email, $token) {
  global $db;

  $db->query("UPDATE forgot_password SET used=1 WHERE emailAddress='{$email}' AND token='{$token}'");

  if ($db->affected_rows > 0) {
    createLog('User used forgot password token', $email);
    return true;
  } else {
    return false;
  }
}

/**
 * @param String $email
 * @return Boolean [is email exists?]
 */
function isEmailExists($email) {
  global $db, $table;

  $email = escape($email);

  return (bool) $db->query("SELECT * FROM {$table['guest']} WHERE emailAddress='{$email}'")->num_rows;
}

/**
 * @param String $role
 * @return Boolean [if account has privilege]
 */
function hasPrivilege($role) {
  $levels = ['Front-desk', 'Admin'];

  return array_search(getAdminInfo()['type'], $levels) >= array_search($role, $levels);
}

/**
 * @param String $email
 * @return Array [User Info]
 */
function getUserInfo($email = null) {
  global $db, $table;

  $email  = $email ?? $_SESSION['account']['email']; // $email != null ? $email : $_SESSION['account']['email']
  $result = $db->query("
    SELECT * FROM {$table['guest']}
    WHERE emailAddress='{$email}'
  ");

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    unset($row['password']);
    $row['name'] = $row['firstName'] . ' ' . $row['lastName'];

    return $row;
  } else {
    return false;
  }
}

/**
 * @param String $email
 * @return Array [User Info]
 */
function getAdminInfo($username = null) {
  global $db, $table;

  $username = $username ?? $_SESSION['admin']['username'];

  $row = $db->query("
    SELECT * FROM {$table['admin']}
    WHERE username='{$username}'
  ")->fetch_assoc();

  unset($row['password']);
  $row['name'] = $row['firstName'] . ' ' . $row['lastName'];

  return $row;
}

/**
 * @param $roomType
 */
function getRoomTypeInfo($roomTypeID = null, $date = null) {
  global $db, $table;

  $arr = [];

  $result = $db->query("SELECT * FROM {$table['roomTypes']} WHERE " . ($roomTypeID ? "roomTypeID='{$roomTypeID}'" : 1));
  while ($row = $result->fetch_assoc()) {
    $row['available'] = count(generateRoomID($row['roomTypeID'], null, $date));
    $row['roomIDs']   = getAllRoomsInType($row['roomTypeID']);
    $arr[]            = $row;
  }

  return count($arr) > 1 ? $arr : $arr[0];
}

/**
 * @param $roomID
 * @return mixed
 */
function getRoomIDInfo($roomID) {
  global $db, $table;

  return $db->query("
    SELECT * FROM room
    JOIN room_types
    ON room.roomTypeID=room_types.roomTypeID
    WHERE roomID='{$roomID}'
  ")->fetch_assoc();
}

/**
 * @param $roomTypeID
 */
function getAllRoomsInType($roomTypeID) {
  global $db, $table;

  $rooms = [];

  $result = $db->query("SELECT * FROM room WHERE roomTypeID='{$roomTypeID}'");

  while ($row = $result->fetch_assoc()) {
    $rooms[] = trim($row['roomID']);
  }

  sort($rooms);

  return $rooms;
}

/**
 * @param String $roomType
 * @param Integer $quantity
 * @param Array $date
 *  [
 *    "checkIn" => date,
 *    "checkOut" => date
 *  ]
 * @return Array [list of rooms]
 */
function generateRoomID($roomTypeID, $quantity = null, $date = null) {
  global $db, $table;

  $arr = [];

  $queryWhere = $roomTypeID ? "{$table['roomTypes']}.roomTypeID = '{$roomTypeID}'" : 1;

  $result = $db->query("
    SELECT roomID FROM {$table['room']}
    JOIN {$table['roomTypes']}
    ON {$table['room']}.roomTypeID = {$table['roomTypes']}.roomTypeID
    WHERE {$queryWhere}
  ");

  while ($row = $result->fetch_assoc()) {
    if (isRoomAvailableInDate($row['roomID'], $date)) {
      $arr[] = $row['roomID'];
    }
  }

  shuffle($arr);
  return count($arr) > 0 ? array_slice($arr, 0, $quantity) : [];
}

/**
 * @param String $room
 * @param Array $date
 *  [
 *    "checkIn" => date,
 *    "checkOut" => date
 *  ]
 * @return Boolean [is room available? depending on dates]
 */
function isRoomAvailableInDate($room, $date) {
  global $db, $table;

  if (!$date) {
    return true;
  }

  $checkIn  = $date['checkIn']; //07-22-2018
  $checkOut = $date['checkOut']; //07-23-2018

  $result = $db->query("
    SELECT checkIn, checkOut FROM room
    JOIN reservation_room
    ON room.roomID=reservation_room.roomID
    JOIN reservation
    ON reservation_room.reservationID=reservation.reservationID
    LEFT JOIN reservation_cancelled
    ON reservation.reservationID=reservation_cancelled.reservationID
    WHERE reservation_room.roomID='{$room}' AND reservation_cancelled.dateCancelled IS NULL
  ");

  while ($row = $result->fetch_assoc()) {
    $reservationDates = getDatesFromRange($row['checkIn'], date('Y-m-d', strtotime($row['checkOut']) - 86400));
    $requestDates     = getDatesFromRange($checkIn, date('Y-m-d', strtotime($checkOut) - 86400));
    foreach ($requestDates as $requestDate) {
      // 2018-07-22 // ["2018-07-22","2018-07-23"]
      if (in_array($requestDate, $reservationDates)) {
        return false;
      }
    }
  }

  return true;
}

/**
 * @param Integer $reservationID
 */
function checkIn($reservationID) {
  global $db;

  $db->query("INSERT INTO reservation_check VALUES('{$reservationID}', NOW(), null)");

  if ($db->error) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Reservation ID: {$reservationID} checked in", null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param Integer $reservationID
 */
function checkOut($reservationID, $amountPaid) {
  global $db;

  $db->query("UPDATE reservation_check SET checkOut=NOW() WHERE reservationID='{$reservationID}'");

  if ($amountPaid > 0) {
    $db->query("INSERT INTO reservation_transaction VALUES('{$reservationID}', '{$amountPaid}', '0', NOW())");
  }

  if ($db->error) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Reservation ID: {$reservationID} checked out", null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param $id
 * @return mixed
 */
function getAmountPaid($id) {
  global $db;

  return $db->query("
    SELECT SUM(payment) as total
    FROM reservation_transaction
    WHERE reservationID='{$id}'")->fetch_assoc()['total'] ?? 0;
}

/**
 * @param $id
 */
function getTotalExpenses($id) {
  global $db;

  $total = 0;

  $result = $db->query("
    SELECT price, quantity
    FROM reservation_expense
    WHERE reservationID='{$id}'
  ");

  while ($row = $result->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
  }

  return $total;
}

/**
 * @param $id
 * @param $includeExpenses
 */
function getTotalAmount($id, $includeExpenses = true) {
  global $db;

  $total = 0;

  $date       = $db->query("SELECT * FROM reservation WHERE reservationID='{$id}'")->fetch_assoc();
  $noOfNights = count(getDatesFromRange($date['checkIn'], date('Y-m-d', strtotime($date['checkOut']) - 86400)));

  $result = $db->query("
    SELECT roomRate
    FROM reservation_room
    WHERE reservationID='{$id}'
  ");

  while ($row = $result->fetch_assoc()) {
    $total += $row['roomRate'] * $noOfNights;
  }

  if ($includeExpenses) {
    $result = $db->query("
      SELECT price, quantity
      FROM reservation_expense
      WHERE reservationID='{$id}'
    ");

    while ($row = $result->fetch_assoc()) {
      $total += $row['price'] * $row['quantity'];
    }
  }

  return $total;
}

/**
 * @param $id
 */
function getDownPayment($id) {
  global $db;

  $result = $db->query("SELECT * FROM reservation_transaction WHERE reservationID='{$id}' AND isDownPayment='1'");

  $payment = 0;

  while ($row = $result->fetch_assoc()) {
    $payment += $row['payment'];
  }

  return $payment;
}

/**
 * @param $id
 */
function getRemainingPayment($id) {
  global $db;

  $result = $db->query("SELECT * FROM reservation_transaction WHERE reservationID='{$id}' AND isDownPayment='0'");

  $payment = 0;

  while ($row = $result->fetch_assoc()) {
    $payment += $row['payment'];
  }

  return $payment;
}

/**
 * @param $id
 * @param $amount
 */
function addPayment($id, $amount) {
  global $db;

  $isDownPayment = intval($db->query("SELECT * FROM reservation_check WHERE reservationID='{$id}'")->num_rows == 0);

  $db->query("INSERT INTO reservation_transaction VALUES('{$id}', '{$amount}','{$isDownPayment}', NOW())");

  if ($db->affected_rows > 0) {
    createLog("Reservation ID: {$id} added an amount of " . pesoFormat($amount), null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param Integer $roomTypeID
 * @param Integer $id
 */
function addRoom($id, $roomTypeID) {
  global $db;

  $db->query("INSERT INTO room VALUES('{$id}', '{$roomTypeID}')");

  if ($db->error) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Room {$id} added", null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param Integer $id
 * @param Integer $newID
 */
function editRoom($id, $roomTypeID) {
  global $db;

  $db->query("UPDATE room SET roomTypeID='{$roomTypeID}' WHERE roomID='{$id}'");

  $roomType = getRoomTypeInfo($roomTypeID)['name'];

  if ($db->error) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Room ID: {$id} changed room type to {$roomType}", null, true);
    return true;
  } else {
    return 'Nothing Changed!';
  }
}

/**
 * @param Integer $id
 */
function deleteRoom($id) {
  global $db;

  $db->query("DELETE FROM room WHERE roomID='{$id}'");

  if ($db->error) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Room {$id} deleted", null, true);
    return true;
  } else {
    return false;
  }
}

/**
 * @param Array $infos
 */
function addRoomType($infos, $rooms, $image) {
  global $db;

  $saved = false;
  if ($image) {
    $filename = $infos['name'] . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $saved    = saveImage($image, __DIR__ . '/image/rooms/', $filename);
  }

  $info = [
    $infos['name'],
    $saved ? $filename : '',
    $infos['description'],
    $infos['feature'],
    $infos['capacity'],
    $infos['rate']
  ];

  $db->query("INSERT INTO room_types VALUES(null, '" . join("','", $info) . "')");
  echo $db->error;
  $id = $db->insert_id;

  $roomsCannotAdd = [];

  foreach ($rooms as $room) {
    $isExists = $db->query("SELECT * FROM room WHERE roomID='{$room}'")->num_rows > 0;
    if (!$isExists) {
      $db->query("INSERT INTO room VALUES('{$room}','{$id}')");
      if ($db->affected_rows > 0) {
        createLog("Room ID: {$room} added.");
      }
    } else {
      $roomsCannotAdd[] = $room;
    }
  }

  if ($db->error) {
    throw new Exception($db->error);
  }

  $output = '';

  if (count($roomsCannotAdd) > 0) {
    $output .= 'Room Number/s (' . join(', ', $roomsCannotAdd) . ') cannot be added. ';
  }

  $success = false;

  if ($db->affected_rows > 0) {
    createLog("Room Type: {$id} added", null, true);
    $success = true;
  }
  return json_encode([
    'success' => $success,
    'message' => $output
  ]);
}

/**
 * @param Integer $roomTypeID
 * @param Array $infos
 */
function editRoomType($roomTypeID, $infos, $rooms, $image) {
  global $db;

  $rooms = array_map(function ($value) {
    return trim($value);
  }, $rooms);

  $rooms = array_filter($rooms, function ($value) {
    return trim($value) != '';
  });

  $vars = [];

  foreach ($infos as $key => $value) {
    $vars[] = "{$key}='{$value}'";
  }

  $saved    = false;
  $roomType = getRoomTypeInfo($roomTypeID)['name'];

  if ($image) {
    $filename = $roomType . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $saved    = saveImage($image, __DIR__ . '/image/rooms/', $filename);
    if ($saved) {
      $vars[] = "filename='{$filename}'";
    }
  }

  $roomsCannotAdd    = [];
  $roomsCannotDelete = [];

  // deleting rooms that are not in the database
  $allRooms = getAllRoomsInType($roomTypeID);

  foreach ($allRooms as $room) {
    if (!in_array($room, $rooms)) {
      $db->query("DELETE FROM room WHERE roomID='{$room}'");
      if ($db->affected_rows > 0) {
        createLog("Room ID: {$room} deleted", null, true);
        $saved = true;
      } else {
        $roomsCannotDelete[] = $room;
      }
    }
  }

  // inserting rooms that are not in the database
  foreach ($rooms as $room) {
    $result   = $db->query("SELECT * FROM room WHERE roomID='{$room}'");
    $isExists = $result->num_rows > 0;

    if (!$isExists) {
      $db->query("INSERT INTO room VALUES('{$room}','{$roomTypeID}')");
      if ($db->affected_rows > 0) {
        createLog("Room ID: {$room} updated", null, true);
        $saved = true;
      } else {
        $roomsCannotAdd[] = $room;
      }
    } else {
      while ($row = $result->fetch_assoc()) {
        if (in_array($row['roomID'], $rooms) && $row['roomTypeID'] != $roomTypeID) {
          $roomsCannotAdd[] = $room;
        }
      }
    }
  }

  $db->query('UPDATE room_types SET ' . join(',', $vars) . " WHERE roomTypeID='{$roomTypeID}'");

  if ($db->error) {
    throw new Exception($db->error);
  }

  $output = '';
  if (count($roomsCannotAdd) > 0) {
    $output .= 'Room Number/s (' . join(', ', $roomsCannotAdd) . ') cannot be added. ';
  }
  if (count($roomsCannotDelete) > 0) {
    $output .= 'Room Number/s (' . join(', ', $roomsCannotDelete) . ') cannot be deleted. ';
  }

  $success = false;

  if ($db->affected_rows > 0 || $saved) {
    createLog("Room Type: {$roomType} updated", null, true);
    $success = true;
  }
  return json_encode([
    'success' => $success,
    'message' => $output
  ]);
}

/**
 * @param Integer $roomTypeID
 */
function deleteRoomType($roomTypeID) {
  global $db;

  $roomType = getRoomTypeInfo($roomTypeID)['name'];

  $db->query("DELETE FROM room_types WHERE roomTypeID='{$roomTypeID}'");

  if ($db->error && $db->errno != 1451) {
    throw new Exception($db->error);
  }

  if ($db->affected_rows > 0) {
    createLog("Room Type: {$roomType} deleted", null, true);
    return true;
  } else if ($db->errno == 1451) {
    return 'Please delete the rooms first.';
  } else {
    return false;

  }
}

/**
 * @return mixed
 */
function getAllRooms($id) {
  global $db;

  $result = $db->query("SELECT * FROM reservation_room WHERE reservationID='{$id}'");

  $roomIDs = [];
  while ($row = $result->fetch_assoc()) {
    $roomIDs[] = $row['roomID'];
  }
  return $roomIDs;
}

/**
 * @param $id
 * @return mixed
 */
function getReservationOwner($id) {
  global $db;

  return $db->query("
    SELECT guest.emailAddress FROM guest
    JOIN reservation
    ON guest.emailAddress=reservation.emailAddress
    WHERE reservation.reservationID='$id'
  ")->fetch_assoc()['emailAddress'];
}

/**
 * @param $checkIn
 * @param $checkOut
 */
function getReservationOnDate($checkIn, $checkOut) {
  global $db;

  $rows = [];

  $result = $db->query('SELECT * FROM reservation');
  while ($row = $result->fetch_assoc()) {
    $dateRange = getDatesFromRange($row['checkIn'], date('Y-m-d', strtotime($row['checkOut']) - 86400));
    if (in_array(date('Y-m-d'), $dateRange)) {
      $rows[] = $row;
    }
  }

  return $rows;
}

/**
 * @param $id
 * @param $image
 */
function uploadImageToReservation($id, $image) {
  global $db;

  do {
    $filename = generateRandomString(16);
  } while (file_exists(__DIR__ . "/image/bankimage/{$filename}"));

  $saved = saveImage($image, __DIR__ . '/image/bankimage/', $filename);

  $isExists = $db->query("SELECT * FROM reservation_bank WHERE reservationID='{$id}'")->num_rows > 0;

  if ($isExists) {
    $db->query("UPDATE reservation_bank SET filename='{$filename}' WHERE reservationID='{$id}'");
  } else {
    $db->query("INSERT INTO reservation_bank VALUES('{$id}','{$filename}')");
  }

  if ($db->affected_rows > 0) {
    createLog("Reservation ID: {$id} uploaded a picture");
    return true;
  } else {
    return false;
  }
}

/**
 * @param $id
 */
function cancelReservation($id) {
  global $db;

  $db->query("INSERT INTO reservation_cancelled VALUES('{$id}', NOW())");

  if ($db->affected_rows > 0) {
    createLog("Reservation ID: {$id} has cancelled");
    return true;
  } else {
    return false;
  }
}

/**
 * @param $id
 */
function sendEmailReservation($id) {
  global $db, $domPDFOptions;

  $row = $db->query("SELECT * FROM reservation WHERE reservationID='{$id}'")->fetch_assoc();

  $userInfo = getUserInfo($row['emailAddress']);

  $details = [
    'guestName'      => $userInfo['name'],
    'contactNumber'  => $userInfo['contactNumber'],
    'checkInDate'    => dateFormat($row['checkIn'], 'M d, Y'),
    'checkOutDate'   => dateFormat($row['checkOut'], 'M d, Y'),
    'numberOfNights' => count(getDatesFromRange($row['checkIn'], $row['checkOut'])) - 1,
    'paymentMethod'  => $row['paymentMethod'],
    'adults'         => $row['adults'],
    'children'       => $row['children'],
    'toddlers'       => $row['toddlers']
  ];

  $domPDF = new Dompdf($domPDFOptions);
  $domPDF->setPaper('letter');
  $domPDF->setBasePath(__DIR__);
  $domPDF->loadHtml(require __DIR__ . '/assets/reservation_pdf.php');
  $domPDF->render();

  $result = $db->query("
    SELECT * FROM reservation_room
    JOIN room
    ON reservation_room.roomID=room.roomID
    JOIN room_types
    ON room.roomTypeID=room_types.roomTypeID
    WHERE reservationID='{$id}'
  ");

  $rooms = [];

  while ($row = $result->fetch_assoc()) {
    $rooms[$row['name']][] = $row['roomID'];
  }

  $content = '';

  foreach ($rooms as $key => $roomIDs) {
    $content .= "
        <tr>
          <td>{$key}</td>
          <td>" . join(', ', $roomIDs) . '</td>
        </tr>';
  }

  return sendEmail([
    'email'      => $userInfo['emailAddress'],
    'subject'    => 'Reservation Summary',
    'body'       => require __DIR__ . '/assets/reservation_email.php',
    'attachment' => $domPDF->output()
  ]);
}

/**
 * @param $date
 */
function getIncomeFromDate($date) {
  global $db;

  $result = $db->query('
    SELECT * FROM reservation
    JOIN reservation_check
    ON reservation.reservationID=reservation_check.reservationID
    WHERE reservation_check.checkOut IS NOT NULL
  ');

  $total = 0;

  while ($row = $result->fetch_assoc()) {
    $dates = getDatesFromRange($row['checkIn'], $row['checkOut']);
    if (in_array($date, $dates)) {
      $total = getTotalAmount($row['reservationID']);
    }
  }

  return $total;
}

/**
 * @param $id
 */
function readNotification($id) {
  global $db;

  $db->query("UPDATE notification SET unread='0' WHERE ID='{$id}'");

  if ($db->affected_rows > 0) {
    createLog("Notification ID: {$id} has been read.");
  }
}

/**
 * @param $action
 * @param $type
 */
function createLog($action, $name = null, $admin = false) {
  global $db;

  if (!$name) {
    $name = !$admin ? ($_SESSION['account']['email'] ?? 'N/A') : ($_SESSION['admin']['username'] ?? 'N/A');
  }
  $type = !$admin ? 'User' : 'Admin';

  $db->query("INSERT INTO logs VALUES(null, '{$type}', '{$name}', '{$action}', NOW())");

  if ($db->error) {
    throw new Exception($db->error);
  }
}

/**
 * @param $id
 * @param $reverse
 */
function formatReservationID($id, $reverse = false) {
  global $db;

  if (!$reverse) {
    $row = $db->query("SELECT * FROM reservation WHERE reservationID='{$id}'")->fetch_assoc();
    return 'RVN' . dateFormat($row['dateCreated'], 'mdy') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }
}

/**
 * @param $captcha
 */
function verifyGCaptcha($captcha) {
  global $gcaptcha;

  $recaptcha = new \ReCaptcha\ReCaptcha($gcaptcha['secret']);
  return $recaptcha->verify($captcha, $_SERVER['REMOTE_ADDR'])->isSuccess();
}

/**
 * @param Object $image
 * @param String $directory
 * @param String $filename
 * @param int $size
 * @return Boolean [if upload successfully]
 */
function saveImage($image, $directory, $filename) {
  if (file_exists($directory . $filename)) {
    unlink($directory . $filename);
  }
  return move_uploaded_file($image['tmp_name'], $directory . $filename);
}

/**
 * @param Array $info
 * [
 *   "email"   => "",
 *   "subject" => "",
 *   "body"    => "",
 *   "title"   => ""
 * ]
 */
function sendEmail($info) {
  global $mailbox;

  $mail = new PHPMailer(true);

  try {
    $mail->SMTPOptions = [
      'ssl' => [
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true
      ]
    ];
    $mail->isSMTP();
    $mail->Host     = $mailbox['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mailbox['email'];
    $mail->Password = $mailbox['pass'];

    $mail->setFrom($mailbox['email'], $info['title'] ?? 'Rio Villa Nuevo Mineral Water Resort');
    $mail->addAddress($info['email']);

    if (isset($info['attachment'])) {
      $mail->addStringAttachment($info['attachment'], 'Reservation Confirmation.pdf');
    }

    $mail->isHTML(true);
    $mail->Subject = $info['subject'];
    $mail->Body    = $info['body'];

    $mail->send();
    return true;
  } catch (Exception $e) {
    return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  }
}

/**
 * @param String $path
 * @return String [Base64 Image]
 */
function imageToBase64($path) {
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

/**
 * @return String [CSRF Token]
 */
function getToken() {
  $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? generateRandomString(32);
  return $_SESSION['csrf_token'];
}

/**
 * @return Boolean [if token is valid]
 */
function verifyToken() {
  return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === decrypt($_SERVER['HTTP_X_CSRF_TOKEN']);
}

function supplyHeaders() {
  if ($_SERVER['SERVER_NAME'] == 'localhost') {
    header('Access-Control-Allow-Origin: *');
  } else if (preg_match('/(riovillanuevoresort.com|localhost)$/', $_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
  }
  header('Access-Control-Allow-Headers: x-csrf-token');
  header('Access-Control-Request-Method: POST');
  header('Access-Control-Allow-Credentials: true');
}

function checkEnv() {
  if (!file_exists(__DIR__ . '/.env')) {
    copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
  }
}

/**
 * @param String $date
 * @param String $format
 */
function dateFormat($date, $format) {
  return $date == null ? '' : date($format, strtotime($date));
}

/**
 * @param $value
 */
function pesoFormat($value, $prefix = '₱') {
  return $prefix . ' ' . number_format($value, 2, '.', ',');
}

/**
 * @param String|Array $x
 * @return String [Escaped String]
 */
function escape($x, $htmlentities = false) {
  global $db;

  if (is_array($x)) {
    return array_map(function ($i) {
      global $db, $htmlentities;
      if ($htmlentities) {
        $i = htmlentities($i);
      }
      return $db->real_escape_string($i);
    }, $x);
  } else if (is_string($x)) {
    if ($htmlentities) {
      $x = htmlentities($x);
    }
    return $db->real_escape_string($x);
  } else {
    throw new InvalidArgumentException(__FUNCTION1__ . ' expecting string or array, got ' . gettype($x));
  }
}

/**
 * @param $start
 * @param $end
 * @return mixed
 */
function getDatesFromRange($start, $end) {
  $dates = [];
  if ($start != $end) {
    $end = new DateTime($end);
    $end->add(new DateInterval('P1D'));

    $period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), $end);

    foreach ($period as $date) {
      $dates[] = $date->format('Y-m-d');
    }
  } else {
    $dates[] = date('Y-m-d', strtotime($start));
  }
  return $dates;
}

/**
 * @param $length
 * @return String [Generated String]
 */
function generateRandomString($length = 32) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string     = '';

  for ($i = 0; $i < $length; $i++) {
    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }

  return $string;
}

/**
 * @param $string
 * @return String [Encrypted String]
 */
function encrypt($string) {
  global $encrypt_keyword, $initialization_vector;
  return openssl_encrypt($string, 'AES-256-CTR', $encrypt_keyword, OPENSSL_ZERO_PADDING, $initialization_vector);
}

/**
 * @param $string
 * @return String [Decrypted String]
 */
function decrypt($string) {
  global $encrypt_keyword, $initialization_vector;
  return openssl_decrypt(str_replace(' ', '+', $string), 'AES-256-CTR', $encrypt_keyword, OPENSSL_ZERO_PADDING, $initialization_vector);
}
?>
