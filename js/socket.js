const socket = io(location.hostname + ':3556');

socket.on('notification', function(data) {
  Snarl.addNotification({
    title: data.title,
    text: data.text
  });
});

socket.on('reload', async function(data) {
  if (data.type == 'admin') {
    var { username: user } = JSON.parse(
      await $.post(main_url + 'ajax/getAdminUser.php')
    );
  } else if (data.type == 'user') {
    var { emailAddress: user } = JSON.parse(
      await $.post(main_url + 'ajax/getUser.php')
    );
  }
  if (user && user == data.user) {
    location.reload();
  }
});

socket.on('addNotification', function(data) {
  if ($('.notification-body').length > 0) {
    $('.notification-body')
      .prepend(
        '<li>' +
          data.message.replace(
            "onclick=''",
            "onclick='readNotification(" + data.id + ")'"
          ) +
          '<span class="timestamp">' +
          data.timestamp +
          '</span></li>'
      )
      .hide()
      .fadeIn();
    updateNotificationIcon();
  }
});

function notif(text, title = 'Notification') {
  socket.emit('notification', {
    title,
    text
  });
}

/**
 * ON = receive
 * EMIT = send
 */
