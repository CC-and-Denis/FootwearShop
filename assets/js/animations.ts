var step = 1;
var end = 10;
var opacity = 0;
var id = null
var stop=false


export function displayFilters(){
    let element = document.getElementById("filterMenu")
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
          element.style.opacity=(opacity/10).toString()
        }

    }
}

export function displayOverlay(optionalElements=document.getElementById("searchBarContainer")){
    let overlay = document.getElementById("overlay") as HTMLDivElement;
    let searchCheckbox = document.getElementById("searchContainerCheckbox") as HTMLInputElement


    // for some reason the listener fuck everything up if the clicked element is a label for an input so we needs to do this
    searchCheckbox.checked = !searchCheckbox.checked

    id = null
    clearInterval(id)
    id=setInterval(frame,10)
    document.getElementById("alertsContainer").style.display="none"
    
    if(searchCheckbox.checked){
        optionalElements.style.display="flex"
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

export function displayMenu(){
    let menu = document.getElementById("sidebar")
    let step = -5;
    let width = 100;
    let end = 0
    if(menu.style.display=="none"){
        menu.style.width="0%"
        width=0
        menu.style.display="flex"
        end=100
        step=5
    }
    
    stop=false
    id = null
    clearInterval(id)
    id=setInterval(frame,1)
    
    
   
    function frame() {
        if (width==end || stop) {
          if(width<=0 && !stop){
            menu.style.display="none"
          }else if(!stop){
            menu.style.display="flex"
          }
          stop=true
          clearInterval(id);

          
        }else {
          width+=step
          menu.style.width=width+"%"
        }
    
    }
    
    
}


export function displayPayment(){

  let paymentFormContainer = document.getElementById("alertsContainer") as HTMLDivElement
  let searchbar = document.getElementById("searchBarContainer") as HTMLDivElement
  let searchCheckbox = document.getElementById("searchContainerCheckbox") as HTMLInputElement
  let overlay = document.getElementById("overlay") as HTMLDivElement;
  
  ( document.getElementById("filterMenuCheckbox") as HTMLInputElement ).checked =false;
  document.getElementById("filterMenu").style.opacity="0"

  searchCheckbox.checked = ! searchCheckbox.checked
  paymentFormContainer.style.display="flex"
  searchbar.style.display="none"
  id = null
  clearInterval(id)
  id=setInterval(frame,10)
  if(searchCheckbox.checked){
      step = 1
      end = 10
      opacity = 0
      overlay.style.display="flex"
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
        overlay.style.opacity=(opacity/10).toString()
      }
  }
}


export function displayDeleteAlert(productId ="unknown", model="unknown"){
  let overlay = document.getElementById("overlay")
  let chekboxElement =document.getElementById("searchContainerCheckbox") as HTMLInputElement
  chekboxElement.checked= ! chekboxElement.checked
  id = null
  document.getElementById("itemToBeDeleted").innerText=model;
  document.getElementById("searchBarContainer").style.display="none";
  (document.getElementById("filterMenuCheckbox") as HTMLInputElement).checked =false;
  document.getElementById("filterMenu").style.opacity="0"
  document.getElementById("alertsContainer").style.display="block"
  document.getElementById("buttonDelete2").setAttribute("name",productId)
  clearInterval(id)
  id=setInterval(frame,10)
  if(chekboxElement.checked){
      step = 1
      end = 10
      opacity = 0
      overlay.style.display="flex"
  }else{
      step = -1
      end = 0
      opacity = 10
  }
 
  function frame() {

      if (opacity==end) {
        clearInterval(id);
        if(! chekboxElement.checked){
          overlay.style.display="none"
          }
      } else {
        opacity+=step
        overlay.style.opacity = (opacity/10).toString()
      }
  }
}