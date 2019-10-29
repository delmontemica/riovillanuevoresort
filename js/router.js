app.run(function($rootScope, $templateCache) {
  $rootScope.$on('$viewContentLoaded', function() {
    $templateCache.removeAll();
  });
});
app.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/', {
      title: 'Home',
      templateUrl: 'home.php'
    })
    .when('/gallery', {
      title: 'Gallery',
      templateUrl: 'gallery.php'
    })
    .when('/accommodation', {
      title: 'Accommodation',
      templateUrl: 'accommodation.php'
    })
    .when('/contactus', {
      title: 'Contactus',
      templateUrl: 'contact.php'
    });
  $locationProvider.hashPrefix('');
});
