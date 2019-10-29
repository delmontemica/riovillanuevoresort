const { localhost, io } = require('./index');
const moment = require('moment-timezone');
const $ = require('./functions');
const db = new (require('./db'))({
  host: 'localhost',
  user: localhost ? 'root' : 'cp466924',
  password: localhost ? '' : 'rio@9899',
  database: localhost ? 'riovillanuevo' : 'cp466924_riovillanuevo'
});

var Updater = {};

moment().tz('Asia/Manila');

Updater.dbBackup = () => {
  db.backup();
};

Updater.checkExpiredBooking = async io => {
  // console.log('Checking...');
  let rows = await db.query(
    'SELECT reservation.reservationID, dateCreated, dateCancelled, SUM(payment) as amountPaid FROM reservation LEFT JOIN reservation_cancelled ON reservation.reservationID=reservation_cancelled.reservationID LEFT JOIN reservation_transaction ON reservation.reservationID=reservation_transaction.reservationID GROUP BY reservation.reservationID'
  );

  if (rows.length > 0) {
    rows.forEach(async x => {
      let { reservationID, dateCreated, dateCancelled, amountPaid } = x;

      dateAlert = moment(dateCreated).add(47, 'hours');
      dateExpire = moment(dateCreated).add(48, 'hours');

      if (dateExpire < moment() && !dateCancelled && !amountPaid) {
        let timestamp = moment().format('YYYY-MM-DD hh:mm:ss A');
        await db.query('INSERT INTO reservation_cancelled VALUES(?, NOW())', [
          reservationID
        ]);
        let message = `The <a onclick='' href='#/reservation?s=${reservationID}' class="notification-item">Reservation ID ${reservationID}</a> has been expired.`;
        let { insertId: id } = await db.query(
          'INSERT INTO notification (message, timestamp) VALUES ?',
          [[[message, timestamp]]]
        );
        io.emit('addNotification', { id, message, timestamp });
        console.log(`${reservationID} has been cancelled.`);
      } else if (dateAlert < moment()) {
        let message = `The <a onclick='' href='#/reservation?s=${reservationID}' class="notification-item">Reservation ID ${reservationID}</a> will be expired after an hour.`;
        let checkMessage = await db.query(
          'SELECT * FROM notification WHERE message=?',
          [message]
        );

        if (checkMessage.length === 0) {
          let timestamp = moment().format('YYYY-MM-DD hh:mm:ss A');
          let { insertId: id } = await db.query(
            'INSERT INTO notification (message, timestamp) VALUES ?',
            [[[message, timestamp]]]
          );
          io.emit('addNotification', { id, message, timestamp });
        }
      }
    });
  }
};

module.exports = Updater;
