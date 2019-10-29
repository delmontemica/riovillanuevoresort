<div style="padding:0 30px">
  <div class="nav-cover">
    <div style="overflow:auto">
      <h2 class="welcomeNote">POOLS</h2>
      <div class="baguetteBox">
<?php
foreach (glob('image/gallery/pools/*.{jpeg,jpg,gif,png,JPEG,JPG,GIF,PNG}', GLOB_BRACE) as $image):
?>
          <div class="col-md-3">
            <div class="image-hover">
              <a href="<?php echo $image ?>">
                <img src="<?php echo $image ?>" class="img" style="width:100%;height:300px;object-fit:cover;"/>
              </a>
            </div>
          </div>
  <?php endforeach;?>
      </div>
    </div>
    <div style="overflow:auto">
      <h2 class="welcomeNote">COTTAGES</h2>
      <div class="baguetteBox">
<?php
foreach (glob('image/gallery/cottages/*.{jpeg,jpg,gif,png,JPEG,JPG,GIF,PNG}', GLOB_BRACE) as $image):
?>
        <div class="col-md-3">
          <div class="image-hover">
            <a href="<?php echo $image ?>">
              <img src="<?php echo $image ?>" class="img" style="width:100%;height:300px;object-fit:cover;"/>
            </a>
          </div>
        </div>
  <?php endforeach;?>
      </div>
    </div>
    <div style="overflow:auto">
      <h2 class="welcomeNote">ROOMS</h2>
      <div class="baguetteBox">
<?php
foreach (glob('image/rooms/*.{jpeg,jpg,gif,png,JPEG,JPG,GIF,PNG}', GLOB_BRACE) as $image):
?>
        <div class="col-md-3">
          <div class="image-hover">
            <a href="<?php echo $image ?>">
              <img src="<?php echo $image ?>" class="img" style="width:100%;height:300px;object-fit:cover;"/>
            </a>
          </div>
        </div>
  <?php endforeach;?>
      </div>
    </div>
    <div style="overflow:auto">
      <h2 class="welcomeNote">FACILITIES</h2>
      <div class="baguetteBox">
<?php
foreach (glob('image/gallery/facilities/*.{jpeg,jpg,gif,png,JPEG,JPG,GIF,PNG}', GLOB_BRACE) as $image):
?>
        <div class="col-md-3">
          <div class="image-hover">
            <a href="<?php echo $image ?>">
              <img src="<?php echo $image ?>" class="img" style="width:100%;height:300px;object-fit:cover;"/>
            </a>
          </div>
        </div>
  <?php endforeach;?>
      </div>
    </div>
  </div>
</div>
