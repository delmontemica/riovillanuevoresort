const app = angular.module('myApp', ['ngRoute', 'ngResource']);

app.run([
  '$rootScope',
  function($rootScope) {
    $rootScope.$on('$routeChangeStart', function(event, next) {
      $('.content')
        .prev()
        .show();
    });

    $rootScope.$on('$routeChangeSuccess', function(event, current, previous) {
      $rootScope.title = current.$$route.title;
      $('.content')
        .prev()
        .fadeOut();
      if (typeof init === 'function') {
        setTimeout(init, 0);
      }
    });
  }
]);

app.controller('HomeNavbarController', function($scope, $location) {
  $scope.list = [
    {
      url: '#/',
      name: 'Home'
    },
    {
      url: '#/gallery',
      name: 'Gallery'
    },
    {
      url: '#/accommodation',
      name: 'Accommodation'
    },
    {
      url: '#/contactus',
      name: 'Contact Us'
    }
  ];
  $scope.isActive = function(viewLocation) {
    return viewLocation.replace('#', '') == $location.path() && 'active';
  };
  $scope.hideCollapse = function() {
    if ($(window).width() < 480 || $(window).height() < 480) {
      $('.collapse').collapse('hide');
    }
  };
});

app.controller('AdminNavbarController', [
  '$scope',
  '$route',
  '$location',
  function($scope, $route, $location) {
    window.$route = $route;
    $scope.list = [
      {
        name: 'Dashboard',
        url: '#/',
        icon: 'pe-7s-graph'
      },
      {
        name: 'Reservation',
        url: '#/reservation',
        icon: 'pe-7s-note2'
      },
      {
        name: 'Check In and Out',
        url: '#/checkinandout',
        icon: 'pe-7s-note2'
      },
      {
        name: 'Accounts',
        url: '#/accounts',
        icon: 'pe-7s-user'
      },
      {
        name: 'Admin Accounts',
        url: '#/adminaccounts',
        icon: 'pe-7s-user'
      },
      {
        name: 'Reports',
        url: '#/reports',
        icon: 'pe-7s-news-paper'
      },
      {
        name: 'Settings',
        url: '#/settings',
        icon: 'pe-7s-settings'
      }
    ];
    $scope.isActive = function(viewLocation) {
      viewLocation = viewLocation.replace('#', '');

      if (viewLocation == $location.path()) {
        return 'active';
      } else if (
        viewLocation != '/' &&
        $location.path().startsWith(viewLocation)
      ) {
        return 'active';
      }
    };
  }
]);

app.controller('EditProfileController', function($scope, $location) {
  $.post(
    'ajax/getUser.php',
    null,
    function(response) {
      $scope.firstName = response.firstName;
      $scope.lastName = response.lastName;
      $scope.contactNumber = response.contactNumber;
      $scope.address = response.address;
    },
    'json'
  );
});
