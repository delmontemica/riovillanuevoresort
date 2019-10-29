var searchVisible = 0;
var transparent = true;

var transparentDemo = true;
var fixedTop = false;

var navbar_initialized = false;
var mobile_menu_initialized = false;

var lbd = {
  misc: {
    navbar_menu_visible: 0
  },

  checkSidebarImage: function() {
    var image_src = $('.sidebar').data('image');

    if (image_src !== undefined) {
      $('.sidebar').append(
        '<div class="sidebar-background" style="background-image: url(' +
          image_src +
          ') "/>'
      );
    }
  },

  initRightMenu: debounce(function() {
    if (!navbar_initialized) {
      var nav_content =
        '<ul class="nav nav-mobile-menu">' +
        $('nav')
          .find('.navbar-collapse')
          .html() +
        '</ul>';

      $(nav_content).insertBefore($('.sidebar-wrapper').find(' > .nav'));

      $('.sidebar-wrapper .dropdown .dropdown-menu > li > a').click(function(
        event
      ) {
        event.stopPropagation();
      });

      mobile_menu_initialized = true;
    } else {
      if ($(window).width() > 991) {
        $('.sidebar-wrapper')
          .find('.nav-mobile-menu')
          .remove();

        mobile_menu_initialized = false;
      }
    }
  }, 200)
};

$(document).ready(function() {
  lbd.checkSidebarImage();

  lbd.initRightMenu();

  $('[rel="tooltip"]').tooltip();

  $('.form-control')
    .on('focus', function() {
      $(this)
        .parent('.input-group')
        .addClass('input-group-focus');
    })
    .on('blur', function() {
      $(this)
        .parent('.input-group')
        .removeClass('input-group-focus');
    });

  $('body').on('touchstart.dropdown', '.dropdown-menu', function(e) {
    e.stopPropagation();
  });

  $('.nav>li').click(function() {
    hideNavbar();
  });

  $('.navbar-toggle').click(function() {
    if (lbd.misc.navbar_menu_visible == 1) {
      hideNavbar();
    } else {
      showNavbar();
    }
  });
});

$(window).on('resize', function() {
  if (navbar_initialized) {
    lbd.initRightMenu();
    navbar_initialized = true;
  }
});

function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this,
      args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    }, wait);
    if (immediate && !timeout) func.apply(context, args);
  };
}

function showNavbar() {
  $('html').addClass('nav-open');
  lbd.misc.navbar_menu_visible = 1;
}

function hideNavbar() {
  $('html').removeClass('nav-open');
  lbd.misc.navbar_menu_visible = 0;
}
