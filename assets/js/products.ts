import { displayPayment } from './animations';


const colorSelection = document.getElementById("product_form_color") as HTMLSelectElement;
const mainImage = document.getElementById("product_form_mainImage") as HTMLInputElement;
const mainImagePreview = document.getElementById("mainImagePreview") as HTMLDivElement;
const quantity = document.getElementById("product_form_quantity") as HTMLInputElement
const otherImagesInput = document.getElementById("product_form_otherImages") as HTMLInputElement;
const otherImagesPreview = document.getElementById("slideShowContainer2") as HTMLDivElement;
const otherImagesHidden = document.getElementById("hiddenOtherImages") as HTMLDivElement;

var minqta=1;
if(document.getElementById("itmesSoldAtEditTime") && (+ document.getElementById("itmesSoldAtEditTime").innerText)!=0){
    console.log(document.getElementById("itmesSoldAtEditTime"))
    minqta= + document.getElementById("itmesSoldAtEditTime").innerText;
}

var counter = 0;

function setQuantityToMin(){
    
    if( quantity && ! quantity.value || quantity.valueAsNumber<minqta){
        quantity.value=minqta.toString();   
    }
}

if(colorSelection){
    for (let i = 0; i < colorSelection.children.length - 1; i++) {
        const child = colorSelection.children[i] as HTMLOptionElement;    
        child.style.backgroundColor=child.value;
       if(["black","brown","purple","blue"].includes(child.value)){
        child.style.color="rgba(255,255,255,0.7)"
       }else{
        child.style.color="rgba(0,0,0,0.7)"
       }
    }
    if(!colorSelection.value){
        colorSelection.value="White";
    }
    colorSelection.addEventListener('change', ()=>{
        colorSelection.style.backgroundColor = colorSelection.value;
        if(["black","brown","purple","blue"].includes(colorSelection.value)){
            colorSelection.style.color="White"
        }else{
            colorSelection.style.color="Black"
        }
    });
}

if( mainImage && mainImagePreview){
    if(! mainImagePreview.style.backgroundImage){
        mainImagePreview.style.backgroundImage='url("/build/images/file-arrow-up-solid.bca87184.png")'
    }
    mainImage.addEventListener('change',()=>{
        if(["image/png","image/jpg","image/jpeg"].includes(mainImage.files[0].type)){
            mainImagePreview.style.backgroundImage=`url(${URL.createObjectURL(mainImage.files[0])})`
        }else{
            mainImage.value=""
            mainImage.files[0]=null
            mainImagePreview.style.backgroundImage='url("build/images/file-arrow-up-solid.bca87184.png")'
            alert("file format not supported")
        }
    })
}


if( otherImagesInput && otherImagesPreview ){

    document.getElementById("goBack").addEventListener('click',()=>{
        scrollOtherImages(-1)
    })
    document.getElementById("goForward").addEventListener('click',()=>{
        scrollOtherImages(1)
    })


    otherImagesInput.addEventListener('change',()=>{

        let invalids = false;
        let full = false;
        let cleanFileList = new DataTransfer();
        for( let i=0;i<otherImagesInput.files.length && ! full;i++){
            if(["image/png","image/jpg","image/jpeg"].includes(otherImagesInput.files[i].type)){
                cleanFileList.items.add(otherImagesInput.files[i])
                full = cleanFileList.files.length>=10;
            }else{
                invalids=true;
            }
        }
        otherImagesInput.files=cleanFileList.files;
        if(invalids){
            alert("All the files that ere not JPG,JPEG or PNG where not uploaded")
        }
        if(full){
            //otherImagesInput.disabled=true
            alert("you reached tha max number of Images(10)")
        }
        
        counter = 0;
       
        otherImagesPreview.replaceChildren();

        if(cleanFileList.files.length>3){
            document.getElementById("goForward").style.opacity="1"
        }
        for(let i =0;i<3 && i<cleanFileList.files.length;i++){
            let e =document.createElement('div')
            e.classList.add("previewImages");
            e.style.backgroundImage=`url(${URL.createObjectURL(cleanFileList.files[i])})`
            otherImagesPreview.appendChild(e)
        }
        
        
    })
}

if(quantity){
    setQuantityToMin()
    quantity.addEventListener("change",setQuantityToMin)
}


function loadOtherImages(){
    counter = 0;
    document.getElementById("goForward").style.opacity="0.5"
    document.getElementById("goBack").style.opacity="0.5"
    otherImagesPreview.replaceChildren();
        
    if(otherImagesInput.files.length>3){
        document.getElementById("goForward").style.opacity="1"
    }
    for(let i =0;i<3 && i<otherImagesInput.files.length;i++){
        let e =document.createElement('div')
        e.classList.add("previewImages");
        e.style.backgroundImage=`url(${URL.createObjectURL(otherImagesInput.files[i])})`
        otherImagesPreview.appendChild(e)
    }
}

function scrollOtherImages(direction){
    if(direction>0){
        if(counter==(otherImagesInput.files.length - 3)){
            return;
        }
        otherImagesPreview.removeChild(otherImagesPreview.firstChild)
        counter++
        let e =document.createElement('div')
        e.classList.add("previewImages");
        e.style.backgroundImage=`url(${URL.createObjectURL(otherImagesInput.files[counter+2])})`
        otherImagesPreview.appendChild(e)
        document.getElementById("goBack").style.opacity="1"
        if(counter==otherImagesInput.files.length - 3){
            document.getElementById("goForward").style.opacity="0.5"
        }
        
    }
    else{
        if(! counter){
            return;
        }
        otherImagesPreview.removeChild(otherImagesPreview.lastChild)
        counter--
        let e =document.createElement('div')
        e.classList.add("previewImages");
        e.style.backgroundImage=`url(${URL.createObjectURL(otherImagesInput.files[counter])})`
        otherImagesPreview.insertBefore(e,otherImagesPreview.firstChild)
        document.getElementById("goForward").style.opacity="1"
        if(! counter){
            document.getElementById("goBack").style.opacity="0.5"
        }
    }
}

if(document.getElementById("buyButton1") && document.getElementById("alertsContainer") as HTMLDivElement){

        if(document.getElementById("errorsBox")){
            displayPayment()
        }
    
        document.getElementById("buyButton1").addEventListener("click",()=>{
            displayPayment()
        })

}






