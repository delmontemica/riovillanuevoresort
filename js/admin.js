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
  image: '../image/loading.gif',
  imageAnimation: false,
  fade: [400, 0]
});

$(window).on('load', function() {
  setTimeout(function() {
    $('html, body').scrollTop(0);
    // Fades out after executing all the needs
    $('#loadingMode').fadeOut(1000);
  });
});

$('[datepicker]').datepicker({
  format: 'mmm/d/yyyy',
  startDate: '0d'
});

$('#verifyPasswordModal').on('shown.bs.modal', function() {
  $(this)
    .find('input:visible:first')
    .focus();
});

$('form[name=frmLogin]').submit(function(e) {
  e.preventDefault();
  swal({
    title: 'Logging in...',
    onOpen: () => {
      swal.showLoading();
    }
  });
  $.ajax({
    context: this,
    url: main_url + 'ajax/adminLogin.php',
    data: $(this).serialize(),
    success: function(response) {
      swal.close();
      if (response == true) {
        swal({
          type: 'success',
          title: 'Login Successfully!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          location.href = './';
        }, 1500);
        notif(
          null,
          $(this)
            .find('input[name=username]')
            .val() + ' has logged in.'
        );
      } else {
        swal('Oops', response, 'error');
      }
    }
  });
});

$('form[name=frmVerifyPassword]').submit(function(e) {
  e.preventDefault();
  $.ajax({
    context: this,
    url: main_url + 'ajax/changeAccountStatus.php',
    data: $(this).serialize(),
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Account Status Updated!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        if (
          $(this)
            .find('input[name=status]')
            .val() == 'false'
        ) {
          socket.emit('reload', {
            user: $(this)
              .find('input[name=name]')
              .val(),
            type: $(this)
              .find('input[name=type]')
              .val()
          });
        }
        setTimeout(() => {
          $('#verifyPasswordModal').unbind('hidden.bs.modal');
          $('#verifyPasswordModal').modal('hide');
          $(this).trigger('reset');
          modalShown = false;
        }, 1500);
      } else {
        swal('Opps...', response, 'error');
      }
    }
  });
});

$('.btnUpdate').click(function() {
  $(this).prop('disabled', true);
  var notification = Snarl.addNotification({
    title: 'GitLab Update',
    text: 'Updating...',
    icon: "<i class='fa fa-refresh fa-spin fa-fw'></i>",
    timeout: null,
    dismissable: false
  });
  $.post(main_url + 'ajax/update.php', null, response => {
    $(this).prop('disabled', false);
    if (response) {
      console.log(response);
      Snarl.editNotification(notification, {
        text: /file(s)? changed/.test(response)
          ? 'Updated Successfully!'
          : response.indexOf('Already up to date.') > -1
            ? 'Already up to date.'
            : response,
        dismissable: true,
        timeout: response.indexOf('Already up to date.') > -1 ? 2000 : null,
        icon: "<i class='fa fa-check'></i>",
        action: () => {
          location.reload(true);
        }
      });
    } else {
      Snarl.editNotification(notification, {
        text: 'Not Allowed!',
        dismissable: true,
        icon: "<i class='fa fa-warning'></i>",
        timeout: 2000,
        action: id => {
          Snarl.removeNotification(id);
        }
      });
    }
  });
});

function init() {
  Waves.attach('.btn', ['waves-effect']);
  Waves.attach('ul.nav>li>a', ['waves-effect']);

  $('ul.nav>li>a').addClass('hvr-bounce-to-right');

  Waves.init();

  $('[data-toggle=toggle]').bootstrapToggle({
    on: 'Activated',
    off: 'Deactivated'
  });

  $('.content>#loadingMode').fadeOut();

  $('input[daterangepicker]').daterangepicker({
    startDate: moment().subtract(1, 'month')
  });

  $('input[type=text][numberformat]').number(true);

  baguetteBox.run('.bbImage', {
    animation: 'fadeIn'
  });

  $('.content').click(function() {
    hideNavbar();
  });

  $('.btnCheckIn').click(function() {
    let reservationID = $(this).data('id');
    swal({
      title: `Are you sure do you want to check in?`,
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#1abc9c',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      allowOutsideClick: false
    }).then(result => {
      if (result.value) {
        $.ajax({
          context: this,
          url: main_url + 'ajax/check.php',
          data: {
            reservationID,
            type: 'checkIn'
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $(this).prop('disabled', true);
              swal(`Checked In!`, `Time: ${response.time}`, 'success').then(
                result => {
                  if (result.value) {
                    $route.reload();
                  }
                }
              );
            } else {
              swal('Oops', response, 'error');
            }
          }
        });
      }
    });
  });

  $('.btnCheckOut').click(function() {
    let reservationID = $(this).data('id');
    let balance = +$(this).data('balance');
    let remainingBalance = Math.max(balance, 0);
    let change = Math.abs(Math.min(balance, 0));
    swal({
      title: `Please enter the remaining amount to check out.`,
      type: 'info',
      html: `
        <input type="text" name="swal-payment" class="swal2-input" data-balance="${balance}">
        <div style="text-align:center">Remaining Balance: ₱ <span id="swal-balance">${remainingBalance.formatMoney()}</span></div>
        <div style="text-align:center">Change: ₱ <span id="swal-change">${change.formatMoney()}</span></div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#1abc9c',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Check Out',
      cancelButtonText: 'Cancel',
      allowOutsideClick: false,
      preConfirm: () => {
        let value = $("input[name='swal-payment']").val();
        return new Promise(resolve => {
          if (value < balance) {
            swal.showValidationError(
              'You must enter amount greater than the balance.'
            );
          }
          resolve(value);
        });
      },
      onOpen: () => {
        let input = $("input[name='swal-payment']");
        input.number(true);
        input.focus();
        input.on('keyup', function() {
          swal.resetValidationError();
          let value = +$(this).val();
          let bal = +$(this).data('balance');
          let change = bal - value;
          $('span#swal-balance').text(
            change < 0 ? '0.00' : change.formatMoney()
          );
          $('span#swal-change').text(
            change < 0 ? Math.abs(change).formatMoney() : '0.00'
          );
        });
      }
    }).then(result => {
      if (result.value) {
        $.ajax({
          context: this,
          url: main_url + 'ajax/check.php',
          data: {
            reservationID,
            type: 'checkOut',
            amountPaid: result.value
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $(this).prop('disabled', true);
              swal(`Checked Out!`, `Time: ${response.time}`, 'success').then(
                result => {
                  if (result.value) {
                    $route.reload();
                  }
                }
              );
            } else {
              swal('Oops', response, 'error');
            }
          }
        });
      }
    });
  });

  $('.btnEditRoomID').click(function() {
    let roomID = $(this).data('id');
    $.ajax({
      url: main_url + 'ajax/fetchRoomID.php',
      data: {
        roomID
      },
      dataType: 'json',
      success: function(response) {
        $('#editRoomIDModal')
          .find('span[data-id=roomID]')
          .text(roomID);
        $('#editRoomIDModal')
          .find('input[name=roomID]')
          .val(roomID);
        $('select[name=cmbRoomType]').val(response.roomTypeID);
        $('#editRoomIDModal').modal('show');
      }
    });
  });

  $('.btnAddRoomType').click(function() {
    let modal = $('#addRoomTypeModal');
    modal.find('form').trigger('reset');
    modal.find('img').attr('src', '');
    modal.modal('show');
  });

  $('.btnEditRoomType').click(function() {
    let roomTypeID = $(this).data('id');
    $.ajax({
      url: main_url + 'ajax/fetchRoomType.php',
      data: {
        roomTypeID
      },
      dataType: 'json',
      success: function(response) {
        let {
          name,
          description,
          capacity,
          rate,
          filename,
          feature,
          roomIDs
        } = response;

        $('#editRoomTypeModal')
          .find('span[data-id=roomType]')
          .text(name);
        $('#editRoomTypeModal')
          .find('input[name=roomTypeID]')
          .val(roomTypeID);
        $('input[name=txtName]').val(name);
        $('textarea[name=txtDescription]').val(description);
        $('textarea[name=txtFeature]').val(feature);
        $('input[name=txtCapacity]').val(capacity);
        $('textarea[name=txtRoomNumber]').val(roomIDs.join('\n'));
        $('input[name=txtRate]').val(rate);
        $('input[name=imgImage]')
          .next('img')
          .attr('src', main_url + 'image/rooms/' + filename);
        $('#editRoomTypeModal').modal('show');
      }
    });
  });

  $('.btnDeleteRoomID').click(function() {
    swal({
      title: 'Are you sure do you want to delete this room?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#1abc9c',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then(result => {
      if (result.value) {
        $.ajax({
          url: main_url + 'ajax/aedRoomID.php',
          data: {
            roomID: $(this).data('id'),
            type: 'delete'
          },
          success: function(response) {
            if (response == true) {
              swal({
                type: 'success',
                title: 'Room Deleted!',
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                timer: 1500
              });
              setTimeout(() => {
                $route.reload();
              }, 1500);
            } else {
              swal('Oops...', response, 'error');
            }
          }
        });
      }
    });
  });

  $('.btnDeleteRoomType').click(function() {
    swal({
      title: 'Are you sure do you want to delete this room type?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#1abc9c',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      allowOutsideClick: false
    }).then(result => {
      if (result.value) {
        let form_data = new FormData();
        form_data.append('type', 'delete');
        form_data.append('data', 'roomTypeID=' + $(this).data('id'));
        $.ajax({
          url: main_url + 'ajax/aedRoomType.php',
          data: form_data,
          contentType: false,
          processData: false,
          success: function(response) {
            if (response == true) {
              swal({
                type: 'success',
                title: 'Room Type Deleted!',
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                timer: 1500
              });
              setTimeout(() => {
                $route.reload();
              }, 1500);
            } else {
              swal('Oops...', response, 'error');
            }
          }
        });
      }
    });
  });

  $('.btnEditAccountType').click(function() {
    let username = $(this).data('id');
    $.ajax({
      url: main_url + 'ajax/getAdminUser.php',
      data: {
        username
      },
      dataType: 'json',
      success: function(response) {
        $('#editAccountTypeModal')
          .find('span[data-id=username]')
          .text(username);
        $('#editAccountTypeModal')
          .find('input[name=username]')
          .val(username);
        $('#editAccountTypeModal')
          .find('select[name=cmbAccountType]')
          .val(response.type);
        $('#editAccountTypeModal').modal('show');
      }
    });
  });

  var modalShown = false;

  $('input[name=chkStatus]').change(function() {
    if (modalShown) return;
    $('#verifyPasswordModal').modal('show');

    $('#verifyPasswordModal').on('hidden.bs.modal', function() {
      let name = $(this)
        .find('input[name=name]')
        .val();
      $(`input[data-id="${name}"][name=chkStatus]`).bootstrapToggle('toggle');
      $(this).unbind('hidden.bs.modal');
      modalShown = false;
    });

    modalShown = true;
    $('#verifyPasswordModal')
      .find('input[name=status]')
      .val($(this).prop('checked'));
    $('#verifyPasswordModal')
      .find('input[name=name]')
      .val($(this).data('id'));
    $('#verifyPasswordModal')
      .find('input[name=type]')
      .val($(this).data('type'));
    $('#verifyPasswordModal')
      .find('button[type=submit]')
      .text($(this).prop('checked') ? 'Activate' : 'Deactivate');
  });

  window.oTable = $('table.dt').DataTable({
    scrollX: true,
    scrollY: $(window).height() - $('.navbar').height() - 180 + 'px',
    scrollCollapse: true
  });
  let sort = $('table.dt').data('sort') || 0;
  let sortby = $('table.dt').data('sort-by') || 'asc';
  oTable.order([sort, sortby]).draw();

  var query = getQuery('s');

  if (query) {
    oTable
      .column(0)
      .search('^' + query + '$', true, false)
      .draw();
    $('.dataTables_wrapper')
      .find('input[type=search]')
      .val(query);
  }
}

$('form[name=frmEditAccountType]').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: main_url + 'ajax/editAccountType.php',
    data: $(this).serialize(),
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Account Type Updated!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('form[name=frmRegister]').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: main_url + 'ajax/register.php',
    data: $(this).serialize(),
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Account Registered!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          $route.reload();
          $('.modal').modal('hide');
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('form[name=frmEditProfile]').submit(function(e) {
  e.preventDefault();
  $.LoadingOverlay('show');
  $.ajax({
    context: this,
    type: 'POST',
    url: main_url + 'ajax/editProfileAdmin.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal({
          type: 'success',
          title: 'Profile Updated!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('form[name=frmChangePassword]').submit(function(e) {
  e.preventDefault();
  var pass = [
    $(this)
      .find('input[name=newPass]')
      .val(),
    $(this)
      .find('input[name=vNewPass]')
      .val()
  ];

  if (pass[0] != pass[1]) {
    swal('Oops', 'Password not match!', 'error');
    return false;
  }

  $.LoadingOverlay('show');
  $.ajax({
    context: this,
    type: 'POST',
    url: main_url + 'ajax/changePasswordAdmin.php',
    data: $(this).serialize(),
    success: function(response) {
      $.LoadingOverlay('hide');
      if (response == true) {
        swal({
          type: 'success',
          title: 'Password Updated!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          $(this).trigger('reset');
          $('#changePasswordModal').modal('hide');
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('input[name=imgImage]').change(function() {
  var reader = new FileReader();
  reader.onload = e => {
    $(this)
      .next('img')
      .attr('src', e.target.result);
  };
  reader.readAsDataURL(this.files[0]);
});

$('form[name=frmAddRoomID]').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: main_url + 'ajax/aedRoomID.php',
    data: $(this).serialize() + '&type=add',
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Room ID Added!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          $route.reload();
          $('.modal').modal('hide');
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('form[name=frmAddRoomType]').submit(function(e) {
  e.preventDefault();
  let form_data = new FormData();
  form_data.append('type', 'add');
  form_data.append('data', $(this).serialize());
  if (
    $(this)
      .find('input[type=file]')
      .prop('files')[0]
  ) {
    form_data.append(
      'file',
      $(this)
        .find('input[type=file]')
        .prop('files')[0]
    );
  }
  $.ajax({
    url: main_url + 'ajax/aedRoomType.php',
    data: form_data,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(response) {
      if (response.success == true) {
        swal({
          type: 'success',
          title: 'Room Type Added!',
          text: response.message || undefined,
          showConfirmButton: response.message == '' ? false : true,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: response.message == '' ? 1500 : 0
        }).then(result => {
          if (result.value) {
            location.reload();
          }
        });
        if (response.message == '') {
          setTimeout(() => {
            $route.reload();
            $('.modal').modal('hide');
          }, 1500);
        }
      } else {
        swal('Oops...', response.message, 'error');
      }
    }
  });
});

$('form[name=frmEditRoomID]').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: main_url + 'ajax/aedRoomID.php',
    data: $(this).serialize() + '&type=edit',
    success: function(response) {
      if (response == true) {
        swal({
          type: 'success',
          title: 'Room ID Updated!',
          showConfirmButton: false,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 1500
        });
        setTimeout(() => {
          $route.reload();
          $('.modal').modal('hide');
        }, 1500);
      } else {
        swal('Oops...', response, 'error');
      }
    }
  });
});

$('form[name=frmEditRoomType]').submit(function(e) {
  e.preventDefault();
  let form_data = new FormData();
  form_data.append('type', 'edit');
  form_data.append('data', $(this).serialize());
  if (
    $(this)
      .find('input[type=file]')
      .prop('files')[0]
  ) {
    form_data.append(
      'file',
      $(this)
        .find('input[type=file]')
        .prop('files')[0]
    );
  }
  $.ajax({
    url: main_url + 'ajax/aedRoomType.php',
    data: form_data,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(response) {
      if (response.success == true) {
        swal({
          type: 'success',
          title: 'Room Type Updated!',
          text: response.message || undefined,
          showConfirmButton: response.message == '' ? false : true,
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: response.message == '' ? 1500 : 0
        }).then(result => {
          if (result.value) {
            location.reload();
          }
        });
        if (response.message == '') {
          setTimeout(() => {
            $route.reload();
            $('.modal').modal('hide');
          }, 1500);
        }
      } else {
        swal('Oops...', response.message, 'error');
      }
    }
  });
});

$('#billModal').on('hidden.bs.modal', function() {
  $(this)
    .find('iframe')
    .attr('src', '');
});

function showReport() {
  let field = $('.reportField');
  let type = field.find('select option:selected').val();
  let date = field
    .find('input')
    .val()
    .split(' - ');

  window.open(
    `${main_url}ajax/report.php?type=${type}&from=${date[0]}&to=${date[1]}`,
    '_blank',
    'height=650,width=1000'
  );
}

function showBill(reservationID) {
  let modal = $('#billModal');
  modal.find('span[data-id=reservationID]').text(reservationID);
  modal
    .find('iframe')
    .attr('src', main_url + 'ajax/invoice.php?id=' + reservationID);
  modal.modal('show');
}

function addPayment(reservationID, amountPaid, total) {
  swal({
    title: 'Enter payment:',
    type: 'info',
    html: '<input type="text" name="swal-payment" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Add',
    allowOutsideClick: false,
    preConfirm: () => {
      let value = $("input[name='swal-payment']").val();
      return new Promise(resolve => {
        if (amountPaid == 0 && total / 2 > value) {
          swal.showValidationError(
            'You must put more than the half of the total amount.'
          );
        }
        resolve(value);
      });
    },
    onOpen: () => {
      let input = $("input[name='swal-payment']");
      input.number(true);
      input.focus();
      input.on('keyup', function() {
        swal.resetValidationError();
      });
    }
  }).then(result => {
    if (result.value) {
      $.ajax({
        url: main_url + 'ajax/addPayment.php',
        data: {
          reservationID,
          payment: result.value
        },
        success: function(response) {
          if (response == true) {
            swal({
              type: 'success',
              title: `Successfully Paid!`,
              html: 'Payment Added: ₱ ' + Number(result.value).formatMoney(),
              showConfirmButton: false,
              allowEscapeKey: false,
              allowOutsideClick: false,
              timer: 1500
            });
            setTimeout(() => {
              $route.reload();
            }, 1500);
          } else {
            swal('Oops', response, 'error');
          }
        }
      });
    }
  });
}

// $('input[name=checkInDate]').change(async function() {
//   let modal = $('#reservationModal');
//   let checkIn = $(this).val();
//   let checkOut = $('input[name=checkOutDate]').val();
//   modal.find('#loadingMode').fadeIn();

//   let rooms = await getRoomsBasedOnDate(checkIn, checkOut);
//   for (var key in rooms) {
//     fillHtml(key, rooms[key]);
//   }

//   modal.find('#loadingMode').fadeOut();
// });

$('input[name=checkOutDate]').change(async function() {
  let modal = $('#reservationModal');
  let checkIn = $('input[name=checkInDate]').val();
  let checkOut = $(this).val();
  modal.find('#loadingMode').fadeIn();

  let rooms = await getRoomsBasedOnDate(checkIn, checkOut);
  for (var key in rooms) {
    fillHtml(key, rooms[key]);
  }

  modal.find('#loadingMode').fadeOut();
});

function getRoomsBasedOnDate(checkIn, checkOut) {
  let modal = $('#reservationModal');
  return new Promise(resolve => {
    $.ajax({
      url: main_url + 'ajax/getRoomsBasedOnDate.php',
      data: {
        checkIn,
        checkOut
      },
      dataType: 'json',
      success: function(response) {
        resolve(response);
      }
    });
  });
}

function fillHtml(key, values) {
  let modal = $('#reservationModal');
  let html = '';
  values.forEach(value => {
    html += `<option value="${value}">${value}</option>`;
  });
  modal.find(`select.cmbRooms[data-name="${key}"]`).html(html);
  modal
    .find(`select.cmbRooms[data-name="${key}"]>option`)
    .mousedown(function(e) {
      e.preventDefault();
      $(this).prop('selected', !$(this).prop('selected'));
      return false;
    });
}

function addReservation() {
  let modal = $('#reservationModal');
  modal.find('.emailform').show();
  modal.find('input[name=type]').val('add');
  modal.find('.modal-title').text('Add Reservation');
  modal
    .find('input[name=checkInDate]')
    .val(moment().format('YYYY-MM-DD'))
    .change();
  modal
    .find('input[name=checkOutDate]')
    .val(
      moment()
        .add(1, 'days')
        .format('YYYY-MM-DD')
    )
    .change();
  modal.modal('show');
}

function editReservation(reservationID) {
  let modal = $('#reservationModal');
  modal.find('.emailform').hide();
  modal.find('input[name=type]').val('edit');
  modal.find('.modal-title').text('Reservation ID: ' + reservationID);
  modal.find('input[name=reservationID]').val(reservationID);
  $('.guestform').hide();
  $('.guestform')
    .find('input')
    .prop('disabled', true);
  $.ajax({
    url: main_url + 'ajax/getReservationInfo.php',
    data: {
      reservationID
    },
    dataType: 'json',
    success: async function(response) {
      let {
        emailAddress,
        checkIn,
        checkOut,
        adults,
        children,
        toddlers,
        paymentMethod
      } = response;

      modal.find('input[type=hidden][name=emailAddress]').val(emailAddress);
      modal.find('input[name=checkInDate]').val(checkIn);
      modal.find('input[name=checkOutDate]').val(checkOut);
      modal.find('input[name=adults]').val(adults);
      modal.find('input[name=children]').val(children);
      modal.find('input[name=toddlers]').val(toddlers);
      modal.find('select[name=paymentMethod]').val(paymentMethod);

      let rooms = await getRoomsBasedOnDate(checkIn, checkOut);

      $.ajax({
        url: main_url + 'ajax/getReservationRooms.php',
        data: {
          reservationID
        },
        dataType: 'json',
        success: function(response) {
          for (let key in rooms) {
            let room = rooms[key].concat(response[key] || []).sort();
            fillHtml(key, room);
            if (response[key]) {
              response[key].forEach(item => {
                $(
                  `select.cmbRooms[data-name="${
                    response[key]
                  }"] option[value="${item}"]`
                ).prop('selected', true);
              });
            }
            modal
              .find(`select.cmbRooms[data-name="${key}"]`)
              .val(response[key] || 0);
          }
          modal.find('#loadingMode').fadeOut();
          modal.modal('show');
        }
      });
    }
  });
}

function cancelReservation(reservationID) {
  swal({
    title: 'Are you sure\nyou want to cancel this reservation?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    allowOutsideClick: false
  }).then(result => {
    if (result.value) {
      $.ajax({
        url: main_url + 'ajax/cancelReservation.php',
        data: {
          reservationID
        },
        success: function(response) {
          if (response == true) {
            swal({
              type: 'success',
              title: `Reservation ID: ${reservationID}\n cancelled!`,
              showConfirmButton: false,
              allowEscapeKey: false,
              allowOutsideClick: false,
              timer: 1500
            });
            setTimeout(() => {
              $route.reload();
            }, 1500);
          } else {
            swal('Oops...', response, 'error');
          }
        }
      });
    }
  });
}

$('form[name=frmReservation]').submit(function(e) {
  e.preventDefault();
  let type = $(this)
    .find('input[name=type]')
    .val();
  let rooms = {};
  $(this)
    .find('select.cmbRooms')
    .each(function() {
      if ($(this).val().length > 0) {
        rooms[$(this).data('name')] = $(this).val();
      }
    });
  if (Object.keys(rooms).length === 0) {
    swal('Oops', 'You must select at least one room', 'error');
    return;
  }
  $(this)
    .find('button[type=submit]')
    .prop('disabled', true);
  $(this)
    .find('button[type=submit]')
    .text('Saving...');
  $.LoadingOverlay('show');
  $.ajax({
    context: this,
    url:
      main_url +
      (type == 'add'
        ? 'ajax/processReservation.php'
        : 'ajax/editReservation.php'),
    data: $(this).serialize() + '&rooms=' + JSON.stringify(rooms),
    success: function(response) {
      $.LoadingOverlay('hide');
      try {
        let data = JSON.parse(response);
        let rooms = Object.values(data.rooms);
        if (type == 'add') {
          socket.emit('updateTable', {
            type: 'reservation',
            value: data.id
          });
        }
        swal({
          title: (type == 'add' ? 'Added' : 'Updated') + ' Successfully!',
          html: 'Your new room IDs are: ' + rooms,
          type: 'success',
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then(result => {
          if (result.value) {
            $('#reservationModal')
              .find('form')
              .trigger('reset');
            $('#reservationModal').modal('hide');
            $route.reload();
            $('.modal').modal('hide');
          }
        });
      } catch (err) {
        swal('Oops', response, 'error');
      }
    },
    complete: function() {
      $(this)
        .find('button[type=submit]')
        .prop('disabled', false);
      $(this)
        .find('button[type=submit]')
        .text('Save Changes');
    }
  });
});

$('#expensesModal').on('hidden.bs.modal', function() {
  if (expensesEdited) {
    $route.reload();
  }
});

$('#reservationModal')
  .find('input[name=checkInDate]')
  .change(function() {
    let date = moment($(this).val())
      .add(1, 'days')
      .format('YYYY-MM-DD');
    $(this)
      .parent()
      .parent()
      .find('input[name=checkOutDate]')
      .attr('min', date);
    $(this)
      .parent()
      .parent()
      .find('input[name=checkOutDate]')
      .val(date)
      .change();
  });

function showRegistrationInfo() {
  $('.emailform')
    .find('select')
    .prop('disabled', true);
  $('.guestform')
    .find('input')
    .prop('disabled', false);

  $('.emailform').hide();
  $('.guestform').show();
}

function hideRegistrationInfo() {
  $('.emailform')
    .find('select')
    .prop('disabled', false);
  $('.guestform')
    .find('input')
    .prop('disabled', true);

  $('.emailform').show();
  $('.guestform').hide();
}

function showExpenses(id) {
  let modal = $('#expensesModal');
  modal.find('input[name=reservationID]').val(id);
  $.ajax({
    url: main_url + 'ajax/fetchExpenses.php',
    data: {
      id
    },
    dataType: 'json',
    success: function(response) {
      modal.find('tbody').html('');
      if (response.length > 0) {
        let html = '';
        response.forEach(x => {
          let { id, name, quantity, price } = x;
          html += `
          <tr data-id="${id}">
            <td class="editable" width="30%" data-type="name">${name}</td>
            <td class="editable" width="30%" data-type="quantity">${quantity}</td>
            <td class="editable" width="30%" data-type="price">${(+price).formatMoney()}</td>
            <td width="10%">
              <button class="btn btn-primary btn-sm" onclick="deleteExpenses(${id})">Delete</button>
            </td>
          </tr>
        `;
        });
        modal.find('tbody').prepend(html);
        refreshTotalExpenses();
        $('td.editable').each(function() {
          addDblClick($(this));
        });
      } else {
        addRow(modal.find('tbody'));
      }
      modal.modal('show');
    }
  });
}

function addDblClick(e) {
  let id = $(e)
    .parent()
    .data('id');
  let type = $(e).data('type');
  $(e).dblclick(function() {
    if ($(e).find('input').length > 0) return;

    let inputType = type != 'name' ? 'number' : 'text';

    $(this).html(
      `
      <div class="form-group has-feedback">
        <input type='${inputType}' data-id="${id}" data-type="${type}" class="form-control" value="${$(
        this
      )
        .text()
        .replace(/,/g, '')}">
      </div>`
    );
    $(this)
      .find('input[type=number]')
      .attr('type', 'text')
      .number(true);
    $(this)
      .find('input')
      .focus();
    $(this)
      .find('input')
      .blur(function(e) {
        let reservationID = $('#expensesModal')
          .find('input[name=reservationID]')
          .val();
        let id = $(this).data('id');
        let type = $(this).data('type');
        let value = $(this).val();

        $(this).prop('readonly', true);
        $(this)
          .parent()
          .append(
            "<span class='form-control-feedback' style='right:0;top:28%'><i class='fa fa-circle-o-notch fa-spin' style='font-size:18px'></i></span>"
          );

        $.ajax({
          context: this,
          url: main_url + 'ajax/updateExpenses.php',
          data: {
            reservationID,
            id,
            type,
            value
          },
          success: function(response) {
            window.expensesEdited = true;

            let formattedValue =
              type == 'quantity' ? value : (+value).formatMoney();

            let html = $(this)
              .parent()
              .parent()
              .html(
                formattedValue +
                  "<span class='pull-right'><i class='fa fa-check' style='color:green'></i></span>"
              );

            refreshTotalExpenses();

            setTimeout(() => {
              $(html)
                .find('span')
                .fadeOut();
            }, 2000);
            addDblClick(
              $(this)
                .parent()
                .parent()
            );
          }
        });
      });
    $(this)
      .find('input')
      .keydown(function(e) {
        let key = e.keyCode || e.which;
        if (key == 13) {
          $(this).blur();
        }
      });
  });
}

function addRow(e) {
  let createdTbody = $(e)
    .parent()
    .find('tbody').append(`
    <tr>
      <td class="editable" width="25%" data-type="name"><input type='text' data-type="name" class="form-control"></td>
      <td class="editable" width="25%" data-type="quantity"><input type='number' data-type="quantity" class="form-control"></td>
      <td class="editable" width="25%" data-type="price"><input type='number' data-type="price" class="form-control"></td>
      <td width="25%">
        <button class="btn btn-primary btn-sm">Save</button>
      </td>
    </tr>`);
  $(createdTbody)
    .find('input[type=number]')
    .attr('type', 'text')
    .number(true);
  $(createdTbody)
    .find('button')
    .click(function() {
      window.expensesEdited = true;
      let reservationID = $('#expensesModal')
        .find('input[name=reservationID]')
        .val();
      let name = $(this)
        .parent()
        .parent()
        .find('input[data-type=name]')
        .val();
      let quantity = $(this)
        .parent()
        .parent()
        .find('input[data-type=quantity]')
        .val();
      let price = $(this)
        .parent()
        .parent()
        .find('input[data-type=price]')
        .val();

      $(this).text('Saving...');
      $(this).prop('disabled', true);

      $.ajax({
        context: this,
        url: main_url + 'ajax/addExpenses.php',
        data: {
          reservationID,
          name,
          quantity,
          price
        },
        dataType: 'json',
        success: function(response) {
          if (response.aff == true) {
            let { id } = response;
            $(this)
              .parent()
              .parent()
              .attr('data-id', id);
            $(this)
              .parent()
              .parent()
              .find('td.editable')
              .each(function() {
                let formattedValue =
                  $(this).data('type') == 'price'
                    ? (+$(this)
                        .find('input')
                        .val()).formatMoney()
                    : $(this)
                        .find('input')
                        .val();

                addDblClick($(this));
                $(this).text(formattedValue);
              });
            $(this).after(
              `<button class="btn btn-primary btn-sm" onclick="deleteExpenses(${id})">Delete</button>`
            );
            $(this).remove();
            refreshTotalExpenses();
          }
        }
      });
    });
}

function refreshTotalExpenses() {
  let total = 0;

  $('td[data-type=price]').each(function() {
    let price = +$(this)
      .text()
      .replace(/,/g, '');

    let quantity = +$(this)
      .parent()
      .find('td[data-type=quantity]')
      .text()
      .replace(/,/g, '');

    total += price * quantity;
  });

  $('span[data-id=totalExpenses]').text(total.formatMoney());
}

function deleteExpenses(id) {
  swal({
    title: 'Are you sure\nyou want to delete it?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    allowOutsideClick: false
  }).then(result => {
    if (result.value) {
      window.expensesEdited = true;
      $.ajax({
        url: main_url + 'ajax/deleteExpenses.php',
        data: {
          id
        },
        success: function(response) {
          $(`tr[data-id=${id}]`).remove();
          refreshTotalExpenses();
          if (response != true) {
            swal('Oops', response, 'error');
          }
        }
      });
    }
  });
}

function readNotification(id) {
  let a = $(`a[onclick="readNotification(${id})"]`);
  $.ajax({
    url: main_url + 'ajax/readNotification.php',
    data: {
      id
    }
  });
  a.attr('onclick', '');
  a.parent().removeClass('unread');
  updateNotificationIcon();

  location.href = a.attr('href');
}

function updateNotificationIcon() {
  $('.notification-icon').hide();
  $('.notification-icon').text($('.notification-body>li.unread').length / 2);
  $('.notification-icon').fadeIn();
}

function logout() {
  swal({
    title: 'Are you sure\nyou want to logout?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    allowOutsideClick: false
  }).then(result => {
    if (result.value) {
      swal({
        title: 'Logging out...',
        onOpen: () => {
          swal.showLoading();
        }
      });
      $.get(main_url + 'ajax/logout.php', 'admin=true', function() {
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

socket.on('updateTable', function(data) {
  if ($route.current.$$route.title == 'Reservation') {
    $.ajax({
      url: main_url + 'ajax/getReservationRow.php',
      data: {
        id: data.value
      },
      dataType: 'json',
      success: function(response) {
        oTable.row.add(response).draw();
      }
    });
  }
});

socket.on('uploadBankImage', function(id) {
  if ($route.current.$$route.title == 'Reservation') {
    if (
      !$(`button[onclick='editReservation(${id})']`)
        .prev()
        .hasClass('btn')
    ) {
      $(`button[onclick='editReservation(${id})']`).before(`
      <button onclick='window.open(\"${main_url}image/bankimage/?reservationID=${id}\",\"_blank\",\"height=650,width=1000\")' class='btn btn-primary btn-xs btn-block'>Show Image</button>
    `);
    }
  }
});
