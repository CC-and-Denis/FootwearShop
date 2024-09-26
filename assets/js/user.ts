
import { displayDeleteAlert,toggleContainer } from './animations';


 const buttons = document.querySelectorAll('.productContainer');
 const deleteButtons1 = document.querySelectorAll('.deleteButton1');


 buttons.forEach(button => {
     button.addEventListener('click', function(e) {
        let targetElement = e.target as HTMLElement
        if(targetElement.classList.contains('aButton')){
            return
        }
        let url = "/product/" + button.id;
        window.location.href=url;
     });
 });

deleteButtons1.forEach(button=>{

    button.addEventListener('click',()=>{


        displayDeleteAlert(button.id,button.getAttribute("name"))
    } )
})


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

if ( document.getElementById("switchCheckbox") ){

    let switchTPCheckbox = document.getElementById("switchCheckbox") as HTMLInputElement;

    if( ( ! buttons.length ) && document.getElementById("ordersContainer") ){
        document.getElementById("productsContainer").style.display="none";
        document.getElementById("ordersContainer").style.display="flex";
        switchTPCheckbox.checked = ! switchTPCheckbox.checked;

    }

    document.getElementById("switchCheckbox").addEventListener('change',()=>{
        if(switchTPCheckbox.checked){
            toggleContainer(document.getElementById("productsContainer"),-10)
            toggleContainer(document.getElementById("ordersContainer"),10)
        }else{
            toggleContainer(document.getElementById("ordersContainer"),-10)
            toggleContainer(document.getElementById("productsContainer"),10)
        }
    })
}

