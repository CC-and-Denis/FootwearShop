 // Select all elements with the class 'my-button'
 
 const buttons = document.querySelectorAll('.productContainer');

 // Loop through each button and add an event listener
 buttons.forEach(button => {
     button.addEventListener('click', function() {
        let url = "/product/" + button.id;
        window.location.replace(url);
     });
 });