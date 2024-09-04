var element=null;
var step = 1;
var end = 10;
var opacity = 0;
var id = null
const buttonsForSearch = document.querySelectorAll('.buttonForSearch');
    
    function displayFilters(){
        element = document.getElementById("filterMenu")
        id = null
        clearInterval(id)
        id=setInterval(frame,10)
        if(document.getElementById("filterMenuCheckbox").checked){
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
        
        

        function displaySearch(){

        // for some reason the listener fuck everything up if the clicked element is a label for an input so we needs to do this
        document.getElementById("searchContainerCheckbox").checked = ! document.getElementById("searchContainerCheckbox").checked


        element = document.getElementById("searchContainer")
        document.getElementById("searchBarContainer").style.display="flex"
        id = null
        clearInterval(id)
        id=setInterval(frame,10)
    
        if(document.getElementById("alertBox1")){
            document.getElementById("alertBox1").style.display="none"
        }
        if(document.getElementById("searchContainerCheckbox").checked){
            step = 1
            end = 10
            opacity = 0
            element.style.display="flex"
        }else{
            step = -1
            end = 0
            opacity = 10
        }
       
        function frame() {
            if (opacity==end) {
              clearInterval(id);
              if(! document.getElementById("searchContainerCheckbox").checked){
                element.style.display="none"
                }
            } else {
              opacity+=step
              element.style.opacity=opacity/10
            }
        
        }
        }

buttonsForSearch.forEach(button => {
    button.addEventListener('click',displaySearch);
});
document.getElementById("filterMenuCheckbox").addEventListener('onChange',displayFilters);

