const isMobile = $(window).width() < 480 || $(window).height() < 480;

Number.prototype.formatMoney = function(c = 2, d = '.', t = ',') {
  var n = this,
    s = n < 0 ? '-' : '',
    i = String(parseInt((n = Math.abs(Number(n) || 0).toFixed(c)))),
    j = (j = i.length) > 3 ? j % 3 : 0;
  return (
    s +
    (j ? i.substr(0, j) + t : '') +
    i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) +
    (c
      ? d +
        Math.abs(n - i)
          .toFixed(c)
          .slice(2)
      : '')
  );
};

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
  },
  context: this,
  type: 'POST',
  xhrFields: {
    withCredentials: true
  },
  error: function() {
    swal('Oops...', 'There was an error.', 'error');
  }
});

$.LoadingOverlaySetup({
  image: 'image/loading.gif',
  imageAnimation: false,
  fade: [400, 0]
});

$('input[type=text][numberformat]').number(true, 2);

$(document).ready(function() {
  $('#myCarousel').carousel();
});

$(window).on('load', function() {
  setTimeout(function() {
    $('html, body').scrollTop(0);
    // Fades out after executing all the needs
    if (!/reservation.php/i.test(location.pathname)) {
      $('#loadingMode').fadeOut(1000);
    }
    if (getQuery('redirect') && $('#loginModal').length > 0) {
      $('#loginModal').modal('show');
      window.redirect = getQuery('redirect');
    }
    $('.verifyEmail').slideDown('slow');
  });
});

var backToTop = $('#backToTop').length > 0;

$(window).on('resize', function() {
  // if mobile
  if (!isMobile) {
    // hide the navbar collapse
    $('.navbar-collapse').collapse('hide');
  }
});

$(window).on('scroll', function() {
  if (backToTop) {
    if ($(window).scrollTop() >= 300) {
      $('#backToTop').fadeIn();
    } else {
      $('#backToTop').fadeOut();
    }
  }
});

$('#backToTop').click(function() {
  $('html, body').animate(
    {
      scrollTop: 0
    },
    600
  );
});

/**
 * Executes a AJAX when logging in
 */
$('form[name=frmLogin]').submit(function(e) {
  e.preventDefault();
  swal({
    title: 'Logging in...',
    onOpen: () => {
      swal.showLoading();
    }
  });
  $.ajax({
    url: 'ajax/login.php',
    data: $(this).serialize(),
    success: function(response) {
      swal.close();
      if (response == true) {
        swal({
          type: 'success',
          title: 'Log In Success!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          if (typeof redirect !== 'undefined') {
            location.href = './' + redirect;
          } else {
            location.reload();
          }
        }, 1500);
      } else {
        swal(
          'Invalid credentials!',
          'Invalid Username and/or Password.',
          'warning'
        );
      }
    }
  });
});

/**
 * Executes a AJAX when registering
 */
$('form[name=frmRegister]').submit(function(e) {
  e.preventDefault();
  let pass = [
    $(this)
      .find('input[name=password]')
      .val(),
    $(this)
      .find('input[name=vpassword]')
      .val()
  ];
  if (pass[0] != pass[1]) {
    return swal('Oops...', "The password doesn't match.", 'warning');
  } else if (!grecaptcha.getResponse()) {
    return swal(
      'Oops...',
      'Please check the captcha before proceeding.',
      'warning'
    );
  }
  $.LoadingOverlay('show');
  $.ajax({
    url: 'ajax/register.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal('Success!', 'You have successfully registered!', 'success').then(
          result => {
            if (result.value) {
              location.reload();
            }
          }
        );
      } else {
        swal('Oops...', response, 'warning');
      }
    }
  });
});

/**
 * Executes a AJAX when sending forgot password
 */
$('form[name=frmSendForgot]').submit(function(e) {
  e.preventDefault();
  $.LoadingOverlay('show');
  $.ajax({
    url: 'ajax/sendForgotPassword.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal('Success!', 'Email Sent!', 'success').then(result => {
          if (result.value) {
            location.reload();
          }
        });
      } else {
        swal('Opps...', response, 'error');
      }
    }
  });
});

/**
 * Executes a AJAX when processing forgot password
 */
$('form[name=frmForgotPassword]').submit(function(e) {
  e.preventDefault();
  let pass = [
    $(this)
      .find('input[name=newPassword]')
      .val(),
    $(this)
      .find('input[name=vPassword]')
      .val()
  ];
  if (pass[0] != pass[1]) {
    return swal('Oops...', "The password doesn't match.", 'warning');
  }
  $.LoadingOverlay('show');
  $.ajax({
    url: 'ajax/verifyForgotPassword.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal('Success!', 'Password Changed!', 'success').then(result => {
          if (result.value) {
            location.href = './';
          }
        });
      } else {
        swal('Opps...', response, 'error');
      }
    }
  });
});

$('form[name=frmChangePassword]').submit(function(e) {
  e.preventDefault();
  let pass = [
    $(this)
      .find('input[name=newPassword]')
      .val(),
    $(this)
      .find('input[name=vPassword]')
      .val()
  ];
  if (pass[0] != pass[1]) {
    return swal('Oops...', "The password doesn't match.", 'warning');
  }
  $.LoadingOverlay('show');
  $.ajax({
    url: 'ajax/changePassword.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal('Success!', 'Password Changed!', 'success').then(result => {
          if (result.value) {
            location.href = './';
          }
        });
      } else {
        swal('Opps...', response, 'error');
      }
    }
  });
});

/**
 * Executes a AJAX when editing profile
 */
$('form[name=frmEditAcc]').submit(function(e) {
  e.preventDefault();
  $.LoadingOverlay('show');
  $.ajax({
    url: 'ajax/editProfile.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal('Success!', 'Update Successfully!', 'success').then(result => {
          if (result.value) {
            location.reload();
          }
        });
      } else {
        swal('Opps...', response, 'error');
      }
    }
  });
});

$('.btnUpload').click(function() {
  let id = $(this).data('id');
  $('#uploadImageModal')
    .find('span[data-id=reservationID]')
    .text(id);
  $('#uploadImageModal')
    .find('input[name=reservationID]')
    .val(id);
  $('input[name=imgUpload]').click();
});

$('input[name=imgUpload]').change(function() {
  var reader = new FileReader();
  reader.onload = e => {
    $('#uploadImageModal')
      .find('img')
      .attr('src', e.target.result);
    $('#uploadImageModal').modal('show');
  };
  reader.readAsDataURL(this.files[0]);
});

$('form[name=frmReservationUpload]').submit(function(e) {
  e.preventDefault();
  let id = $(this)
    .find('input[name=reservationID]')
    .val();
  let form_data = new FormData();
  form_data.append('id', id);
  form_data.append('file', $('input[name=imgUpload]').prop('files')[0]);
  $.ajax({
    url: 'ajax/uploadBankImage.php',
    data: form_data,
    contentType: false,
    processData: false,
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Bank Image Uploaded!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          location.reload();
        }, 1500);
        notif(`Reservation ID: ${id} uploaded a picture`);
        socket.emit('uploadBankImage', id);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

function init() {
  Waves.attach('.btn', ['waves-effect']);

  Waves.init();

  baguetteBox.run('.baguetteBox', {
    animation: 'fadeIn'
  });
}

function showAccommodationModal(roomTypeID) {
  $.ajax({
    url: 'ajax/fetchRoomType.php',
    data: {
      roomTypeID
    },
    dataType: 'json',
    success: function(response) {
      let { name, description, capacity, rate, filename, feature } = response;
      feature = feature
        ? `<li>${feature.split('\n').join('</li><li>')}</li>`
        : '';

      $('#accommodationRoomModal')
        .find('.modal-title')
        .text(name);
      $('#accommodationRoomModal').find('.modal-body').html(`
<img src="image/rooms/${filename}" style="width:100%;max-height:300px;object-fit:cover;">
<div style="border: 3px solid black;overflow:auto;margin-top:10px;padding:30px">
  <div style="text-align:center;font-style:italic;font-size:22px">${description}</div>
  <hr class="center-block" width="50%" style="border-color:black">
  <h3 align="center">ROOM FEATURES</h3>
  <div style="text-transform:uppercase">
    ${feature}
    <span class="pull-right" style="font-size:16px;margin-top:10px">Room Rate: <b>₱ ${parseInt(
      rate
    ).formatMoney()}</b></span>
  </div>
</div>
`);
      $('#accommodationRoomModal').modal('show');
    }
  });
}

function blockNumbers(e) {
  if (!(e.keyCode >= 48 && e.keyCode <= 57)) return false;
  return true;
}

function logout() {
  swal({
    title: 'Are you sure\nyou want to logout?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No'
  }).then(result => {
    if (result.value) {
      swal({
        title: 'Logging out...',
        onOpen: () => {
          swal.showLoading();
        }
      });
      $.get('ajax/logout.php', null, function() {
        location.reload();
      });
    }
  });
}

function getQuery(name) {
  let url = window.location.href;
  name = name.replace(/[\[\]]/g, '\\$&');
  let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
