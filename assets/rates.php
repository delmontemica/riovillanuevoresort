<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>
<body>

  <div class="center-block text-center" style="overflow:auto;position:relative;">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <div class="col-md-3">
        <img src="image/orig-logo.jpg" alt="logo" style="height: 200px"/>
      </div>
      <div class="col-md-9 pull-right" style="padding:30px 50px 0 0">
        <div style="font-size:40px;font-style:italic;font-family:Times New Roman;font-weight:bold;">Rio Villa Nuevo</div>
        <div style="font-size:15px;text-transform:uppercase;letter-spacing:3px;margin-top: -10px;font-weight:bold;">Mineral Water Resort</div>
        <div>
          Tambo M. Kulit, Indang, Cavite<br />
          (046) 417 1234 / (+63) 917 123 4567<br />
          admin@riovillanuevoresort.com
        </div>
      </div>
    </div>
    <div class="col-md-3"></div>
    <div style="margin-top: 200px;text-align: left;">
  		<h1 style="font-size:25px;text-align:center;font-family:Helvetica; text-transform:uppercase">
  		  <b>Rates and Services</b>
  		</h1>
  		<br>
  	
  	</div>
  </div>
</body>
</html>
<?php
return ob_get_clean();
?>