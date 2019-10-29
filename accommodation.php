<?php require_once 'backend.php';?>

<div class="topMargin"></div>
<!-- <div id="myCarousel" class="carousel slide">
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1" ></li>
    <li data-target="#myCarousel" data-slide-to="2" ></li>
  </ol>
  <div class ="carousel-inner">
    <div class="item active">
      <img src="image/room1.jpg" width="100%" class="img-responsive">
    </div>
    <div class="item ">
      <img src="image/room2.jpg" width="100%" class="img-responsive">
    </div>
   <div class="item ">
      <img src="image/room3.jpg" width="100%" class="img-responsive">
    </div>
  </div>
  <a class="carousel-control left" href="accommodation.php/#myCarousel" data-slide="prev">
    <span class= "icon-prev"> </span>
  </a>
  <a class="carousel-control right" href="accommodation.php/#myCarousel" data-slide="next">
    <span class="icon-next"></span>
  </a>
</div> -->
<br>
<div class="section">
  <div class="container">
    <h1  style="color:#1abc9c;text-align:center;">Rooms</h1>
  <hr>
  <strong class="accommodationDesc" >
    Rio Villa Nuevo offers a wide variety of rooms and accomodations in order to properly cater to the needs of our guests. We assure that our guests receive only the best services during their stay in our resort. Enjoy your stay!</strong>
  </div>
</div>
<br><br><br>
<div class="section roomType">
  <div class="center-block">
<?php
$result = $db->query('SELECT * FROM room_types');
for ($i = 0; $row = $result->fetch_assoc(); $i++):
?>
    <div class="accommodationImg" style="float:<?php echo $i % 2 == 0 ? 'left' : 'right' ?>">
      <img src="<?php echo $row['filename'] ? "image/rooms/{$row['filename']}" : '' ?>">
    </div>
    <div class="accommodationDetails" style="float:<?php echo $i % 2 == 1 ? 'left' : 'right' ?>">
      <h2 class="subheading"><?php echo $row['name']; ?></h2>
      <p style="color:white;">
        <?php echo $row['description'] ?>
      </p>
      <span align="left">
        <nav class="cl-effect-11">
          <a class="btnAccommodationRoom" onclick="showAccommodationModal(<?php echo $row['roomTypeID'] ?>)" style="cursor:pointer" data-hover="MORE DETAILS"><span>MORE DETAILS</span></a>
        </nav>
      </span>
    </div>
<?php endfor;?>
  </div>
</div>
