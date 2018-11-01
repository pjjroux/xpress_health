var showTimeout = setTimeout(function() {
  preloader()
}, 1500);

function preloader() {
  $('#preloader').addClass('se-pre-con');
}

$(window).on('load', function() {
  clearTimeout(showTimeout);
  $('.se-pre-con').fadeOut("slow");
});

