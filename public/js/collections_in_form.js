document.addEventListener("DOMContentLoaded", () => {
  // Get the html element that holds the collection of pictures
  let picturesCollectionHolder = document.getElementById('picturesList');
  // count the current form inputs we have (e.g. 2), use that as the new index when inserting a new item (e.g. 2)
  picturesCollectionHolder.dataset.index = picturesCollectionHolder.querySelectorAll('div.col.position-relative').length;

  document.querySelector('.add_item_link').addEventListener("click", e => {
    let collectionHolderId = e.currentTarget.dataset.collectionHolderId;
    // add a new picture form (see next code block)
    addFormToCollection(collectionHolderId);
  });

  document.querySelectorAll('.removePicture').forEach(item => {
    item.addEventListener('click', e => {
      let el = document.getElementById(e.target.dataset.picture);
      removeElement(el);
    })
  });

});

function addFormToCollection(collectionHolderId) {
  // Get the html element that holds the collection of pictures
  let collectionHolder = document.getElementById(collectionHolderId);

  // Get the data-prototype explained earlier
  let prototype = collectionHolder.dataset.prototype;
  // get the new index
  let index = collectionHolder.dataset.index;

  let newForm = prototype;
  // Replace '__name__label__' in the prototype's HTML to
  // instead be a number based on how many items we have
  // newForm = newForm.replace(/__name__label__/g, index);

  // Replace '__name__' in the prototype's HTML to
  // instead be a number based on how many items we have
  newForm = newForm.replace(/__name__/g, index);

  // increase the index with one for the next item
  collectionHolder.dataset.index = Number(index) + 1;

  // Display the form in the page in a new div
  let newFormItem = document.createElement('div');
  newFormItem.setAttribute('class', 'col position-relative');
  newFormItem.setAttribute('id', 'pictureForm-new' + index);
  newFormItem.innerHTML = newForm;
  // Add the new form at the end of the list
  collectionHolder.append(newFormItem);
}

function removeElement(el) {
  el.parentNode.removeChild(el);
}