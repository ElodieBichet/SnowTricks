$(document).ready(function () {

  // When click on "Load more" button, load next page items in ajax just before the button
  $("#loadmore").on("click", function (event) {
    event.preventDefault();
    const el = this;
    axios.get(el.href).then(function (response) {
      $(el).before(response.data);
    });
  });

});
