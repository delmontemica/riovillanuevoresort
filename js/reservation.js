$(document).ready(async function() {
  const config = {
    dateFormat: 'MMM DD, YYYY',
    minDate: 0, // days
    maxNights: 7, // days
    maxRooms: 5
  };

  var width = 0;
  var user = JSON.parse(await $.post('ajax/getUser.php'));
  var booking = JSON.parse(await $.post('ajax/getReservation.php'));
  var rooms = {};

  var loadedSavedReservation = booking.step == 0;

  console.log(booking);

  /**
   * Starts a smartwizard
   * Options are available in http://techlaboratory.net/smartwizard/documentation#paramdesc
   */
  $('#smartwizard').smartWizard({
    theme: 'circles',
    keyNavigation: false,
    showStepURLhash: false,
    useURLhash: false,
    transitionEffect: 'fade',
    anchorSettings: {
      anchorClickable: false
    }
  });

  $('.sticky-step>div').html($('#smartwizard>ul').clone());

  $('#loadingMode').fadeOut(1000);

  $('input[type=number]').on('keyup keydown', function(e) {
    if (e.keyCode === 69 || e.keyCode === 189) return false;
    var min = parseInt($(this).attr('min'));
    var max = parseInt($(this).attr('max'));
    if ($(this).val() > max && e.keyCode != 46 && e.keyCode != 8) {
      e.preventDefault();
      $(this).val(max);
    }
  });

  $('.btn-toolbar>.btn-group').prepend(
    '<button type="button" class="btn btn-secondary btnReset">Reset</button>'
  );

  $('.btnReset').click(function() {
    if (confirm('Are you sure do you want to reset the wizard?')) {
      clearReservation(() => {
        window.onbeforeunload = null;
        location.reload();
      });
    }
  });
  /**
   * What to do when leaving the step
   * @param  {[int]} stepNumber       [Step number when leaving the step]
   * @param  {String} stepDirection   [Step direction <backward | forward>]
   * @return {[boolean]}              [Whether allow to leave step or not]
   */
  $('#smartwizard').on('leaveStep', function(
    e,
    anchorObject,
    stepNumber,
    stepDirection
  ) {
    // ignore if step direction is backward
    if (stepDirection != 'forward') return;

    var supplyBookingSummary;

    switch (stepNumber) {
      // step 1 validation
      case 0:
        // get the values of step 1
        booking.checkInDate = $('input[name=txtCheckInDate]').val();
        booking.checkOutDate = $('input[name=txtCheckOutDate]').val();
        booking.numberOfNights = $('input[name=txtNoOfNights]').val();
        booking.adults = $('input[name=txtAdults]').val();
        booking.children = $('input[name=txtChildren]').val();
        booking.toddlers = $('input[name=txtToddlers]').val();
        let {
          checkInDate,
          checkOutDate,
          numberOfNights,
          adults,
          children,
          toddlers
        } = booking;
        if (
          !checkInDate ||
          !checkOutDate ||
          !moment(checkInDate, config.dateFormat).isValid() ||
          !moment(checkOutDate, config.dateFormat).isValid() ||
          moment(checkInDate) > moment(checkOutDate)
        ) {
          // validates if check in date and check out date has value
          swal('No date selected', 'Please select a date.', 'info');
          return false;
        } else if (numberOfNights > config.maxNights) {
          // validates if there are no adults
          swal(
            'Too much number of nights',
            `Number of day/s must not be greater than ${config.maxNights}.`,
            'info'
          );
          return false;
        } else if (adults <= 0) {
          // validates if there are no adults
          swal('Insufficient value', 'Adult/s must be more than zero.', 'info');
          return false;
        } else if (
          adults > +$('input[name=txtAdults]').attr('max') ||
          children > +$('input[name=txtChildren]').attr('max') ||
          toddlers > +$('input[name=txtToddlers]').attr('max')
        ) {
          swal(
            'Too much number of persons.',
            'Maximum number of persons exceeded.',
            'info'
          );
          return false;
        }
        supplyBookingSummary = (id, text) => {
          $('.bookingSummary')
            .find(`span[data-id=${id}]`)
            .text(text);
        };
        // starts supplying the book summary if everything is okay
        supplyBookingSummary('checkInDate', checkInDate);
        supplyBookingSummary('checkOutDate', checkOutDate);
        supplyBookingSummary('numberOfNights', numberOfNights);
        supplyBookingSummary('adults', adults);
        supplyBookingSummary('children', children);
        supplyBookingSummary('toddlers', toddlers);
        $('.bookingSummary').show();

        // gets room info and add it to view
        $.post(
          'ajax/getRooms.php',
          $('form[name=frmBook]').serialize(),
          response => {
            $('.roomList').html('');
            response = _.sortBy(
              _.each(response, x => (x.capacity = +x.capacity)),
              'capacity'
            );
            response.forEach(data => {
              rooms[data.name] = {
                available: data.available,
                selected: 0,
                rate: data.rate
              };
              addRoomList(data);
            });
            $('.btnSelect').click(function() {
              let list = Object.keys(rooms).map(x => rooms[x].selected);
              let numberOfRooms = list.reduce((x, y) => x + y);
              // sums the selected list
              if (numberOfRooms >= config.maxRooms) {
                swal(
                  'Room Exceeded',
                  `You are not allowed to reserve more than ${
                    config.maxRooms
                  } rooms.`,
                  'info'
                );
                return false;
              }

              // get name of the button eg: "Standard Room"
              let name = $(this).data('name');
              // get jquery of span with data-name room type
              let room = $(`span.selected[data-name="${name}"]`);
              // checks if the room already exists in bookingRoomList
              if (room.length == 0 && rooms[name].available > 0) {
                let selected = ++rooms[name].selected;
                // append a div to bookingRoomList
                $('.bookingRoomList').append(`
                  <div>
                    <strong>${name}<br>(<span class="total" data-name="${name}"></span>): </strong>
                    <button type="button" class="deleteRoom pull-right" data-name="${name}" style="border:none;border-radius:50%;background-color:#1abc9c;height:20px;width:20px;line-height:11px;color:white">
                      <i class="fa fa-minus pull-right" style="font-size:10px"></i>
                    </button>
                    <span class="pull-right selected" style="margin-right:5px" data-name="${name}">${selected}</span>
                  </div>
                `);
                refreshTotal(name);
                // adds a listener to this button
                $(`.deleteRoom[data-name="${name}"]`).click(function() {
                  // get name of the button eg: "Standard Room"
                  let name = $(this).data('name');
                  // checks if selected is less than or equal to 1 then remove the parent
                  if (rooms[name].selected <= 1) {
                    $(`span.selected[data-name="${name}"]`)
                      .parent()
                      .remove();
                    rooms[name].selected--;
                    if ($('.bookingRoomList').children().length == 0) {
                      $('.bookingRoomList').fadeOut();
                      $('.bookingTotal').fadeOut();
                    }
                  } else {
                    // reduce rooms selected if exists
                    $(`span.selected[data-name="${name}"]`).text(
                      --rooms[name].selected
                    );
                  }
                  refreshTotal(name);
                });
              } else {
                if (rooms[name].available == 0) {
                  swal('Notice', 'No rooms available.', 'info');
                } else if (rooms[name].selected == rooms[name].available) {
                  swal(
                    'Notice',
                    'You have exceeded the maximum number of rooms.',
                    'info'
                  );
                } else {
                  // else add the room selected
                  room.text(++rooms[name].selected);
                }
              }
              // shows if the bookingRoomList has a children
              if ($('.bookingRoomList').children().length > 0) {
                $('.bookingRoomList').fadeIn();
                $('.bookingTotal').fadeIn();
              }
              refreshTotal(name);
            });

            if (booking.rooms) {
              for (let key in booking.rooms) {
                if (+booking.rooms[key].selected > 0) {
                  for (var i = 0; i < +booking.rooms[key].selected; i++) {
                    $(`button.btnSelect[data-name="${key}"]`).click();
                  }
                }
              }
              let list = Object.keys(rooms).map(x => rooms[x].selected);
              let numberOfRooms = list.reduce((x, y) => x + y);
              if (numberOfRooms > 0) {
                checkStep(stepNumber + 1);
              }
            }
          },
          'json'
        );
        let addRoomList = data => {
          let {
            roomTypeID,
            name,
            description,
            capacity,
            rate,
            available,
            filename
          } = data;
          $('.roomList').append(`
<div class="panel panel-default shadow">
  <div class="panel-heading">${name.toUpperCase()}<span class="roomAvailable" style="float:right;color:#f8ffce">${available} ${
            available == 1 ? 'ROOM' : 'ROOMS'
          } AVAILABLE</span></div>
  <div class="panel-body">
    <div class="col-md-4">
      <img src="image/rooms/${filename}" alt="${name.toUpperCase()}" style="width:100%">
    </div>
    <div class="col-md-8">
      ${description}
      <br>
      MAX OCCUPANCY: ${capacity} ${capacity == 1 ? 'ADULT' : 'ADULTS'}
      <br>
      <a style="cursor:pointer" onclick="showAccommodationModal(${roomTypeID})">VIEW ROOM FEATURES &amp; AMENITIES</a>
      <br>
      <span data-id="price" style="font-size:30px">Php ${(+rate).formatMoney()}</span>
      <button type="button" class="btnSelect btn btn-primary pull-right" data-name="${name}">SELECT ROOM</button>
    </div>
  </div>
</div>
`);
        };
        break;
      // step 2 validation
      case 1:
        // get all rooms and return a selected array eg: [1,0,1,2,1,1,0]
        let list = Object.keys(rooms).map(x => rooms[x].selected);
        let numberOfRooms = list.reduce((x, y) => x + y);
        // sums the selected list
        if (numberOfRooms == 0) {
          swal('No rooms selected', 'Please select a room.', 'info');
          return false;
        }
        supplyBookingSummary = (id, text) => {
          $('#step-3')
            .find(`span[data-id=${id}]`)
            .text(text);
        };
        supplyBookingSummary('guestName', user.name);
        supplyBookingSummary('contactNumber', user.contactNumber);
        supplyBookingSummary('emailAddress', user.emailAddress);
        supplyBookingSummary('checkInDate', booking.checkInDate);
        supplyBookingSummary('checkOutDate', booking.checkOutDate);
        supplyBookingSummary('noOfNights', booking.numberOfNights);
        supplyBookingSummary('adults', booking.adults);
        supplyBookingSummary('children', booking.children);
        supplyBookingSummary('toddlers', booking.toddlers);
        let html = `
<thead>
  <th style="text-align:center">Room Type</th>
  <th style="text-align:center">Price</th>
  <th style="text-align:center">Quantity</th>
  <th style="text-align:center">Amount</th>
</thead>
<tbody>
        `;
        for (var i = 0; i < Object.keys(rooms).length; i++) {
          if (rooms[Object.keys(rooms)[i]].selected == 0) continue;
          html += `
<tr>
  <td style="text-align:center">${Object.keys(rooms)[i]}</td>
  <td style="text-align:center">Php ${(+rooms[Object.keys(rooms)[i]]
    .rate).formatMoney()}</td>
  <td style="text-align:center">${rooms[Object.keys(rooms)[i]].selected}</td>
  <td style="text-align:center">Php ${(
    +rooms[Object.keys(rooms)[i]].selected * +rooms[Object.keys(rooms)[i]].rate
  ).formatMoney(2, '.', ',')}</td>
</tr>
          `;
        }
        html += `
          <tr></tr>
          <tr>
            <td colspan="3" align="right" style="vertical-align:middle">Total:</td>
            <td align="center">
              Php ${_.reduce(
                rooms,
                (x, y) => x + +y.rate * y.selected,
                0
              ).formatMoney()} x ${booking.numberOfNights} night/s
              <br>
              <span style="font-weight:bold">
                Php ${(
                  _.reduce(rooms, (x, y) => x + +y.rate * y.selected, 0) *
                  booking.numberOfNights
                ).formatMoney()}
              </span>
            </td>
          </tr>
        </tbody>`;
        $('.tblRoomList').html(html);

        if (booking.paymentMethod) {
          $(
            `input[name=rdPaymentMethod][value="${booking.paymentMethod}"]`
          ).prop('checked', true);
        }

        booking.rooms = rooms;
        break;
    }
  });

  /**
   * What to do when showing the step
   * @param  {[int]} stepNumber       [Step number when showing the step]
   * @param  {String} stepDirection   [Step direction <backward | forward>]
   * @return {[boolean]}              [Whether allow to show step or not. Defaults to true]
   */
  $('#smartwizard').on('showStep', function(
    e,
    anchorObject,
    stepNumber,
    stepDirection
  ) {
    booking.step =
      !booking.step || booking.step < stepNumber ? stepNumber : +booking.step;

    $(window).scrollTop(0);

    switch (stepNumber) {
      case 0:
        $('.bookingSummary').hide();
        break;
      case 1:
        $('.roomList').scrollTop(0);
        if (stepDirection == 'forward') {
          $('.bookingRoomList').hide();
          $('.bookingRoomList').html('');
          $('.bookingTotal').hide();
          $('span.allTotal').html('');
        }
        $('.sw-btn-next').show();
        $('.sw-btn-group')
          .find('button[type=submit]')
          .remove();
        break;
      case 2:
        $('.sw-btn-next').hide();
        $('.sw-btn-next').after(
          "<button type='submit' class='btn btn-secondary'>Submit</button>"
        );
        break;
      case 3:
        $('.sw-btn-group').hide();
        $('.sw-btn-group').after(
          "<a href='./' class='btn btn-secondary pull-right'>Go back to homepage</a>"
        );
        window.onbeforeunload = null;
        break;
    }
    width += stepDirection == 'forward' ? 23 : -23;
    $('.glow').removeClass('glow');
    if (loadedSavedReservation && stepNumber != 3) saveReservation();
    $('.animate-step').animate(
      {
        width: `${width}%`
      },
      500,
      function() {
        if (stepNumber >= +booking.step) {
          $(`a[href="#step-${stepNumber + 1}"]`)
            .find('i')
            .addClass('glow');
          $('.sticky-step>div').html($('#smartwizard>ul').clone());
        }
      }
    );
  });

  /**
   * Starts a pignose calendar.
   * Documentations are available in https://github.com/KennethanCeyer/pg-calendar/wiki/Documentation
   */
  $('.calendar').pignoseCalendar({
    initialize: false,
    multiple: true,
    minDate: moment().add(config.minDate, 'days'),
    date: moment().add(config.minDate, 'days'),
    select: function(date, context) {
      if (date[0] == null || date[1] == null) return;
      var a = moment(date[0]._d).format(config.dateFormat);
      var b = moment(date[1]._d).format(config.dateFormat);
      var noOfNights = moment(b).diff(moment(a), 'days');
      $('input[name=txtCheckInDate]').val(a);
      $('input[name=txtCheckOutDate]').val(b);
      $('input[name=txtNoOfNights]').val(noOfNights);
    }
  });

  $('form[name=frmBook]').submit(function(e) {
    if (!$('input[name=rdPaymentMethod]:checked').val()) {
      swal('Oops...', 'Please select a payment method.', 'info');
      return false;
    } else if (!$('input[name=cbxTermsAndConditions]').prop('checked')) {
      swal('Oops...', 'Please check first the terms and conditions.', 'info');
      return false;
    }
    e.preventDefault();
    swal({
      title: 'Are you sure?',
      text: "You won't be able to revert this.",
      type: 'info',
      showCancelButton: true,
      confirmButtonColor: '#1abc9c',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then(result => {
      if (result.value) {
        booking.paymentMethod = $('input[name=rdPaymentMethod]:checked').val();
        let temp = {};
        Object.keys(rooms).forEach(x => {
          temp[x] = rooms[x].selected;
        });
        booking.rooms = _.pick(temp, x => x > 0);
        $.LoadingOverlay('show');
        $.ajax({
          url: 'ajax/processReservation.php',
          data: booking,
          success: function(response) {
            try {
              let { id, rooms } = JSON.parse(response);
              if (_.reduce)
                $('#step-4')
                  .find('span[data-id=reservationID]')
                  .text(id);
              let table = '';
              Object.keys(rooms).forEach(function(room) {
                table += `
                <tr>
                  <td>${room}</td>
                  <td>${rooms[room].join(', ')}</td>
                </tr>
              `;
              });
              $('#finalSummary>tbody').html(table);
              $('#finalGuestDetails').html($('#guestDetails').html());
              $('#finalGuestDetails')
                .find('div')
                .eq(1)
                .append(
                  `<strong>Payment Method:</strong> ${booking.paymentMethod.replace(
                    /^\w/g,
                    x => x.toUpperCase()
                  )}`
                );
              clearReservation(() => {
                $.LoadingOverlay('hide');
                swal({
                  title: 'Reserved Successfully!',
                  html:
                    'Please check your inbox in your email for reservation details.<br><br><small>Note: Please also check in spam inbox.</small>',
                  type: 'success',
                  allowOutsideClick: false,
                  allowEscapeKey: false
                }).then(result => {
                  if (result.value) {
                    $('#step-4')
                      .find('.tblRoomList')
                      .html(
                        $('#step-3')
                          .find('.tblRoomList')
                          .html()
                      );
                    $('#smartwizard').smartWizard('next');
                  }
                });
              });
              socket.emit('updateTable', {
                type: 'reservation',
                value: id
              });
            } catch (err) {
              $.LoadingOverlay('hide');
              swal('Opps...', response, 'error').then(result => {
                if (result.value) {
                  $('.btnReset').click();
                }
              });
            }
          }
        });
      }
    });
  });

  $('input[name=rdPaymentMethod]').change(function() {
    booking.paymentMethod = $('input[name=rdPaymentMethod]:checked').val();
    saveReservation();
  });

  window.refreshTotal = name => {
    let total = rooms[name].selected * +rooms[name].rate;
    $(`span.total[data-name="${name}"]`).text('₱ ' + total.formatMoney());
    total = 0;
    Object.keys(rooms).forEach(item => {
      total += rooms[item].selected * +rooms[item].rate;
    });
    $(`span.allTotal`).text('₱ ' + total.formatMoney());
  };

  var saveReservation = () => {
    console.log('saved');
    $.post('ajax/saveReservation.php', booking);
  };

  var clearReservation = cb => {
    console.log('cleared');
    $.post('ajax/clearReservation.php', null, () => {
      cb();
    });
  };

  var checkStep = step => {
    if (step < +booking.step) {
      $('#smartwizard').smartWizard('next');
      if (step + 1 == +booking.step) loadedSavedReservation = true;
    }
  };

  checkStep(0);

  $(window).on('scroll', function() {
    if ($(window).scrollTop() >= 300) {
      $('.sticky-step').slideDown();
      if (!isMobile) $('.bookingSummary>div').addClass('sticky');
    } else {
      $('.sticky-step').slideUp();
      if (!isMobile) $('.bookingSummary>div').removeClass('sticky');
    }
  });
});

window.onbeforeunload = () => {
  return 'Are you sure?';
};
