<?php
require_once '../backend.php';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <style>
    body{
      font-size:15px;
      font-weight:bold;
    }
  </style>
</head>
<body>
  <div align="center">
    <div style="font-size:25px">RIO VILLA NUEVO INC</div>
    <div>
      MINERAL WATER RESORT
      <br>
      Tambo Kulit, Indang, Cavite
    </div>
  </div>
  <div align="right" style="margin-top:20px">
    Date____________
  </div>
  <div>
    Name:<span style="float:right">____________________________________________</span><br>
    Address:<span style="float:right">__________________________________________</span><br>
    ID / Plate #:<span style="float:right">_______________________________________</span><br>
  </div>
  <div>
    Entrance Fee:<br>
    <div align="right">
      Adult_____________ x P_________=___________<br>
      Children_____________ x P_________=___________<br>
      P___________<br>
      _________________________=___________<br>
      P___________<br>
      Space Tables:________ x P_________=___________<br>
      Pavillion:________ x P_________=___________<br>
      Cottage:________ x P_________=___________<br>
      Corkage: Beer________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________=___________<br>
      Softdrinks________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________=___________<br><br>
      Others:_________________________=___________<br><br>
      TOTAL P____________
    </div>
    <div>
      Remarks:
    </div>
    <div style="float:right;text-align:center;border-top:1px solid black;width:200px;margin-top:30px">
        Cashier
    </div>
  </div>
</body>
</html>
<?php
return ob_get_clean();
?>
