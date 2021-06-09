document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll('.add_item_link').forEach(item => {
    // init index
    item.dataset.index = document.querySelectorAll('div.' + item.dataset.collectionType).length;
    item.addEventListener("click", e => {
      let collectionHolder = e.currentTarget;
      // add a new collection form (see next code block)
      addFormToCollection(collectionHolder);
    })
  });

  document.querySelectorAll('.removeItem').forEach(item => {
    item.addEventListener('click', e => {
      let el = document.getElementById(e.target.dataset.removeItem);
      removeElement(el);
    })
  });

});

function addFormToCollection(collectionHolder) {

  // Get the data-prototype explained earlier
  let prototype = collectionHolder.dataset.prototype;
  // Get the new index
  let index = collectionHolder.dataset.index;
  let type = collectionHolder.dataset.collectionType;

  let newForm = prototype;

  // Replace '__name__' in the prototype's HTML to
  // instead be a number based on how many items we have
  newForm = newForm.replace(/__name__/g, index);

  // increase the index with one for the next item
  collectionHolder.dataset.index = Number(index) + 1;

  // Display the form in the page in a new div
  let newFormItem = document.createElement('div');
  newFormItem.setAttribute('class', 'col position-relative ' + type);
  newFormItem.setAttribute('id', type + 'Form-new' + index);
  newFormItem.innerHTML = newForm;

  // Get the last item of the collection type
  let all = document.querySelectorAll('div.' + type);
  let last = all[all.length - 1];
  // Add the new form after this last item
  last.after(newFormItem);
}

function removeElement(el) {
  el.parentNode.removeChild(el);
}