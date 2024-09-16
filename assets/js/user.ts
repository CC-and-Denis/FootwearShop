
 
 const buttons = document.querySelectorAll('.productContainer');
 const deleteButtons1 = document.querySelectorAll('.deleteButton1');

 var element=null;
 var step = 1;
 var end = 10;
 var opacity = 0;
 var id = null


 buttons.forEach(button => {
     button.addEventListener('click', function() {
        let url = "/product/" + button.id;
        window.location.replace(url);
     });
 });

deleteButtons1.forEach(button=>{
    button.addEventListener('click',()=>{
        this.stopPropagation()
        displayDeleteAlert(button.id,button.getAttribute("name"))
    } )
})

 function displayDeleteAlert(productId ="unknown", model="unknown"){
    element = document.getElementById("overlay")
    let chekboxElement =document.getElementById("searchContainerCheckbox") as HTMLInputElement
    chekboxElement.checked= ! chekboxElement.checked
    id = null
    document.getElementById("itemToBeDeleted").innerText=model
    document.getElementById("searchBarContainer").style.display="none"
    document.getElementById("alertsContainer").style.display="block"
    document.getElementById("buttonDelete2").setAttribute("name",productId)
    clearInterval(id)
    id=setInterval(frame,10)
    if(chekboxElement.checked){
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
          if(! chekboxElement.checked){
         element.style.display="none"
            }
        } else {
          opacity+=step
          element.style.opacity=opacity/10
        }
    }
}
if(document.getElementById("noDelete") && document.getElementById("buttonDelete2")){
    document.getElementById("noDelete").addEventListener("click",()=>{
        displayDeleteAlert()
    })
    document.getElementById("buttonDelete2").addEventListener("click",()=>{
        fetch("/deleteproduct/"+document.getElementById("buttonDelete2").getAttribute("name"))
        .then(function(response) {                     
            if(response.ok)
            {
              document.getElementById(document.getElementById("buttonDelete2").getAttribute("name")).remove()
              displayDeleteAlert()
    
            }else{
              document.getElementById("errorsBox").style.display="flex"
            }
            })
    })
}

