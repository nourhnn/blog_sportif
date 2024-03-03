function ready(callback) {
  // in case the document is already rendered
  if (document.readyState != "loading") callback();
  // modern browsers
  else if (document.addEventListener)
    document.addEventListener("DOMContentLoaded", callback);
  // IE <= 8
  else
    document.attachEvent("onreadystatechange", function () {
      if (document.readyState == "complete") callback();
    });
}

ready(function () {
  // votre code JS ici....
});


const hamburger = document.querySelector('.hamburger')
const linksContainer = document.querySelector('.links-container')

hamburger.addEventListener('click', ()=> {
  linksContainer.classList.toggle('active')
})
