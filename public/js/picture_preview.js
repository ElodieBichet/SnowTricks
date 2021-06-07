document.addEventListener("DOMContentLoaded", () => {

  // get all radio buttons for mai pictures
  let radios = document.getElementsByName("trick[mainPicture]");
  // add onchange event listener
  radios.forEach(item => {
    item.addEventListener('change', () => {
      // get the preview image element
      let img = document.querySelector('#mainPicPreview > img');
      // get the current src of the image
      let currentSrc = img.getAttribute('src');
      // get the new checked picture filename
      let newFilename = document.querySelector('input[name="trick[mainPicture]"]:checked').dataset.filename;
      // get the new src replacing previous filename with the new one
      let newSrc = currentSrc.replace(currentSrc.split("/").pop(), newFilename);
      // replace preview src with the new one
      img.setAttribute('src', newSrc);
    })
  });

});