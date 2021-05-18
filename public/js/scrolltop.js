document.addEventListener("DOMContentLoaded", function () {

  // Display top link button while scroll to the page bottom
  window.addEventListener('scroll', function () {
    let posScroll = window.pageYOffset;
    let topLink = document.getElementById('top_link');
    if (posScroll >= 800)
      fadeIn(topLink);
    else
      fadeOut(topLink);
  });

  // ** FADE OUT FUNCTION **
  function fadeOut(el) {
    el.style.opacity = 1;
    (function fade() {
      if ((el.style.opacity -= .1) < 0) {
        el.style.display = "none";
      } else {
        requestAnimationFrame(fade);
      }
    })();
  };

  // ** FADE IN FUNCTION **
  function fadeIn(el, display) {
    el.style.opacity = 0;
    el.style.display = display || "block";
    (function fade() {
      var val = parseFloat(el.style.opacity);
      if (!((val += .1) > 1)) {
        el.style.opacity = val;
        requestAnimationFrame(fade);
      }
    })();
  };


});