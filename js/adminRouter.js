app.run(function($rootScope, $templateCache) {
  $rootScope.$on('$viewContentLoaded', function() {
    $templateCache.removeAll();
  });
});
app.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/', {
      title: 'Dashboard',
      templateUrl: 'dashboard.php'
    })
    .when('/reservation', {
      title: 'Reservation',
      templateUrl: 'reservation.php'
    })
    .when('/checkinandout', {
      title: 'Check In and Out',
      templateUrl: 'checkinandout.php'
    })
    .when('/accounts', {
      title: 'Accounts',
      templateUrl: 'accounts.php'
    })
    .when('/adminaccounts', {
      title: 'Admin Accounts',
      templateUrl: 'adminaccounts.php'
    })
    .when('/reports', {
      title: 'Reports',
      templateUrl: 'reports.php'
    })
    .when('/settings', {
      title: 'Settings',
      templateUrl: 'settings.php'
    })
    .when('/settings/rooms', {
      title: 'Rooms',
      templateUrl: 'settings/rooms.php'
    })
    .when('/settings/roomtypes', {
      title: 'Room Types',
      templateUrl: 'settings/roomtypes.php'
    })
    .when('/settings/logs', {
      redirectTo: '/settings/logs/user'
    })
    .when('/settings/logs/user', {
      title: 'User Logs',
      templateUrl: 'settings/logs/user.php'
    })
    .when('/settings/logs/admin', {
      title: 'Admin Logs',
      templateUrl: 'settings/logs/admin.php'
    })
    .when('/notification', {
      title: 'Notification',
      templateUrl: 'notification.php'
    });
  $locationProvider.hashPrefix('');
});
