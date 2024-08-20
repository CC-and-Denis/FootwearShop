var element=null;
var step = 1;
var end = 10;
var opacity = 0;
var id = null
    
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
        element = document.getElementById("searchContainer")
        id = null
        clearInterval(id)
        id=setInterval(frame,10)
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

document.getElementById("searchContainerCheckbox").addEventListener('change',displaySearch);
document.getElementById("filterMenuCheckbox").addEventListener('change',displayFilters);

