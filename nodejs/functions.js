const moment = require('moment-timezone');

var $ = {};

$.formatDate = (date, format = 'MMM DD, YYYY') => {
  return moment(date || undefined)
    .tz('Asia/Manila')
    .format(format);
};

$.formatMoney = (number, c = 2, d = '.', t = ',') => {
  var n = number,
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

$.log = message => {
  console.log(
    $.formatDate(null, 'MMM DD, YYYY hh:mm:ss A') +
      ' | ' +
      (typeof message == 'object' ? JSON.stringify(message) : message)
  );
};

module.exports = $;
