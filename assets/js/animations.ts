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
  document.getElementById("searchMountPoint").style.display="flex";



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
    let step = menu.style.display=="none" ? 5 : -5;
    let width = menu.style.display=="none" ? 0 : 100;
    let end = menu.style.display=="none" ? 100 : 0
    let id=null

    if(menu.style.display=="none"){
        menu.style.width="0%"
        menu.style.display="flex"
    }

    clearInterval(id)
    id=setInterval(frame,1)
    
    function frame() {
        if (width==end) {
          if(end==0 ){
            menu.style.display="none"
          }else{
            menu.style.display="flex"
          }
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
  let id = null;
  
  ( document.getElementById("filterMenuCheckbox") as HTMLInputElement ).checked =false;
  document.getElementById("filterMenu").style.opacity="0"

  searchCheckbox.checked = ! searchCheckbox.checked
  paymentFormContainer.style.display="flex"
  document.getElementById("searchMountPoint").style.display="none";


  clearInterval(id)
  id=setInterval(frame,10)

  step = searchCheckbox.checked ? 1 : -1
  end =  searchCheckbox.checked ? 10 : 0
  opacity = searchCheckbox.checked ? 0 : 10
  overlay.style.display= searchCheckbox.checked ? "flex" : "none"
 
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
  let id=null;

  chekboxElement.checked = ! chekboxElement.checked

  step = chekboxElement.checked ? 1 : -1
  end = chekboxElement.checked ? 10 : 0
  opacity = chekboxElement.checked ? 0 : 10
  overlay.style.display= chekboxElement.checked ? "flex" : "none"

  
  
  document.getElementById("itemToBeDeleted").innerText=model;
  document.getElementById("searchMountPoint").style.display="none";
  document.getElementById("alertsContainer").style.display="block";
  document.getElementById("buttonDelete2").setAttribute("name",productId)

  clearInterval(id)
  id=setInterval(frame,10)
 
  function frame() {

      if (opacity==end) {
        clearInterval(id);
        if(! chekboxElement.checked){
          overlay.style.display="none"
          document.getElementById("searchMountPoint").style.display="flex";

          }
      } else {
        opacity+=step
        overlay.style.opacity = (opacity/10).toString()
      }
  }
}

export function toggleContainer(element:HTMLElement,direction:number){
  
  let height = direction > 0 ? 0 : 90; 
  let end = direction > 0 ? 90 : 0;   

  element.style.height = height.toString()+"%";
  element.style.display = "flex";

  let id=null;

  clearInterval(id!); 

  id = window.setInterval(frame, 20);

  function frame() {
    // Check if the animation should stop
    if ((direction > 0 && height >= end) || (direction < 0 && height <= end)) {
      clearInterval(id!);  
      if (direction < 0) {
        element.style.display = "none"; // Hide element after collapse
      }
      return;
    }

    height += direction;  
    element.style.height = height.toString()+"%";
  }
}
  
