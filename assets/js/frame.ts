var element=null;
var step = 1;
var end = 10;
var opacity = 0;
var id = null
const overlay = document.getElementById("searchContainer") as HTMLDivElement;
const buttonsForSearch = document.querySelectorAll('.buttonForSearch');
    
    function displayFilters(){
        element = document.getElementById("filterMenu")
        let filterMenuCheckbox = document.getElementById("filterMenuCheckbox") as HTMLInputElement
        id = null
        clearInterval(id)
        id=setInterval(frame,10)
        if(filterMenuCheckbox.checked){
            step = 1
            end = 10
            opacity = 0
        }else{
            step = -1
            end = 0
            opacity = 10
        }
        function frame() {
            if (opacity==end) {
              clearInterval(id);
            } else {
              opacity+=step
              element.style.opacity=opacity/10
            }

        }
        }
        
        

        function displayOverlay(){

        console.log("ciao1")
            
        let searchCheckbox = document.getElementById("searchContainerCheckbox") as HTMLInputElement

        // for some reason the listener fuck everything up if the clicked element is a label for an input so we needs to do this
        searchCheckbox.checked = !searchCheckbox.checked


        id = null
        clearInterval(id)
        id=setInterval(frame,10)
        document.getElementById("alertsContainer").style.display="none"
        
        if(searchCheckbox.checked){
            console.log("hi")
            document.getElementById("searchBarContainer").style.display="flex"
            overlay.style.display='flex'
            step = 1
            end = 10
            opacity = 0
        }else{
            step = -1
            end = 0
            opacity = 10
        }
       
        function frame() {
            if (opacity==end) {
              clearInterval(id);
              if(! searchCheckbox.checked){
                overlay.style.display="none"
                }
            } else {
              opacity+=step
              overlay.style.opacity= (opacity/10).toString() 
            }
        
        }
        }

buttonsForSearch.forEach( (button) => {
    button.addEventListener('click',displayOverlay);
});

document.getElementById("filterMenuCheckbox").addEventListener('change',displayFilters);

