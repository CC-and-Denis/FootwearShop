var element=null;
    var step = 1;
    var end = 10;
    var opacity = 0;
    var id = null

    function displayFilters(){
        var element = document.getElementById("filterMenu")
        
        var id = null
        clearInterval(id)
        id=setInterval(frame,10)
        if(document.getElementById("filterMenuCheckbox").checked){
            var step = 1
            var end = 10
            var opacity = 0
        }else{
            var step = -1
            var end = 0
            var opacity = 10
        }
        function frame() {
            console.log(element)
            console.log(opacity)
            if (opacity==end) {
              clearInterval(id);
            } else {
              opacity+=step
              element.style.opacity=opacity/10
            }

        }
        }
        
        

        function displaySearch(){
        var element = document.getElementById("searchContainer")
        console.log(element)
        var id = null
        clearInterval(id)
        id=setInterval(frame,10)
        if(document.getElementById("searchContainerCheckbox").checked){
            var step = 1
            var end = 10
            var opacity = 0
            element.style.display="flex"
        }else{
            var step = -1
            var end = 0
            var opacity = 10
            
        }
       
        function frame() {
            console.log(element)
            console.log(opacity)
            if (opacity==end) {
              clearInterval(id);
              if(! document.getElementById("searchContainerCheckbox").checked){
                console.log("nascondi")
             element.style.display="none"
                }
            } else {
              opacity+=step
              element.style.opacity=opacity/10
            }
            

        }

        
        
        }