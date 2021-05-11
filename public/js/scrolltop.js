$(document).ready(function () {

  // Display top link button while scroll to the page bottom
  $(window).scroll(function () {
    posScroll = $(document).scrollTop();
    if (posScroll >= 800)
      $('#top_link').fadeIn(600);
    else
      $('#top_link').fadeOut(600);
  });

});