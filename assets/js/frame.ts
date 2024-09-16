import { displayOverlay, displayFilters, displayMenu } from './animations';


var screenType="lg"
const displayMenuBtn = document.getElementById("show-navigation")
const sidebar= document.getElementById("sidebar")



const buttonsForSearch = document.querySelectorAll('.buttonForSearch');

// Function to check screen width and perform actions
function checkScreenWidth() {
    const screenWidth = window.innerWidth;
    console.log(screenWidth)
        
    if (screenWidth < 1000 && screenType!="md" ) {
        screenType="md" 
        displayMenuBtn.style.display="block"
        sidebar.style.display="none"
        sidebar.style.position="absolute"
        displayMenuBtn.addEventListener('click',()=>{
            displayMenu()
        })
    } else if(screenWidth>=1000 && screenType!="lg") {
        screenType="lg" 
        displayMenuBtn.style.display="none"
        sidebar.style.display="flex"
        sidebar.style.position="static"
        sidebar.style.width="9rem"
        displayMenuBtn.removeEventListener("click",()=>{
            displayMenu()})
        

        // Perform actions for large screens
    }
}



checkScreenWidth();

buttonsForSearch.forEach( (button) => {
    button.addEventListener('click',()=>{
        displayOverlay()
    });
});


// Event listener for window resize
window.addEventListener('resize', checkScreenWidth);

// Initial check on page load
document.getElementById("filterMenuCheckbox").addEventListener('change',()=>{
    displayFilters()
});

