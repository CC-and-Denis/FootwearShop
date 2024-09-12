const colorSelection = document.getElementById("product_form_colors") as HTMLSelectElement;
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
        document.getElementById("goForward").style.opacity="0.5"
        document.getElementById("goBack").style.opacity="0.5"
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
}else if(otherImagesHidden && otherImagesPreview){
    document.getElementById("goBack").addEventListener('click',()=>{
        scrollOtherImagesHidden(-1)
    })
    document.getElementById("goForward").addEventListener('click',()=>{
        scrollOtherImagesHidden(1)
    })


    for(let i = 0;i< 3 && i<otherImagesHidden.children.length;i++){
        otherImagesPreview.appendChild(otherImagesHidden.children[i].cloneNode())
    }

    document.getElementById("goBack").style.opacity="0.5";
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

function scrollOtherImagesHidden(direction){
    if(direction>0){
        if(counter==(otherImagesHidden.children.length - 3)){
            return;
        }
        otherImagesPreview.removeChild(otherImagesPreview.firstChild)
        counter++
        let e = otherImagesHidden.children[counter+2].cloneNode()
        otherImagesPreview.appendChild(e)
        document.getElementById("goBack").style.opacity="1"
        if(counter==otherImagesHidden.children.length - 3){
            document.getElementById("goForward").style.opacity="0.5"
        }
        
    }
    else{
        if(! counter){
            return;
        }
        otherImagesPreview.removeChild(otherImagesPreview.lastChild)
        counter--
        let e =otherImagesHidden.children[counter].cloneNode()
        otherImagesPreview.insertBefore(e,otherImagesPreview.firstChild)
        document.getElementById("goForward").style.opacity="1"
        if(! counter){
            document.getElementById("goBack").style.opacity="0.5"
        }
        
    }

}

const preview = document.getElementById("otherImagesPreview") as HTMLDivElement;
const list = document.getElementById("otherImagesList") as HTMLDivElement;
var counter = 0;
const searchbar = document.getElementById("searchBarContainer") as HTMLDivElement
const paymentFormContainer = document.getElementById("alertsContainer") as HTMLDivElement
var element=null;
var step = 1;
var end = 10;
var opacity = 0;
var id = null

if (preview && list) {
    const goBackButton = document.getElementById("goBack");
    const goForwardButton = document.getElementById("goForward");

    if (goBackButton) {
        goBackButton.addEventListener('click', () => {
            scrollOtherImagesInProductPage(-1);
        });
        goBackButton.style.opacity = "0.5";
    }

    if (goForwardButton) {
        goForwardButton.addEventListener('click', () => {
            scrollOtherImagesInProductPage(1);
        });

        // Initialize preview with the first 3 images
        updatePreview();
        updateButtonOpacity();
    }

    function updatePreview() {
        preview.innerHTML = '';
        for (let i = counter; i < counter + 3 && i < list.children.length; i++) {
            let e = list.children[i].cloneNode(true);
            preview.appendChild(e);
        }
    }

    function updateButtonOpacity() {
        if (counter === 0) {
            if (goBackButton) goBackButton.style.opacity = "0.5";
        } else {
            if (goBackButton) goBackButton.style.opacity = "1";
        }

        if (counter >= list.children.length - 3) {
            if (goForwardButton) goForwardButton.style.opacity = "0.5";
        } else {
            if (goForwardButton) goForwardButton.style.opacity = "1";
        }
    }

    function scrollOtherImagesInProductPage(direction: number) {
        if (direction > 0) {
            if (counter >= list.children.length - 3) {
                return;
            }
            counter++;
        } else {
            if (counter <= 0) {
                return;
            }
            counter--;
        }

        updatePreview();
        updateButtonOpacity();
    }

}

if(paymentFormContainer){
    if(document.getElementById("errorsBox")){
        displayPayment()
    }

    document.getElementById("buyButton1").addEventListener("click",()=>{
        displayPayment()
    })


}


function displayPayment(){
    element = document.getElementById("searchContainer")
    let chekboxElement =document.getElementById("searchContainerCheckbox") as HTMLInputElement
    chekboxElement.checked= ! chekboxElement.checked
    paymentFormContainer.style.display="block"
    searchbar.style.display="none"
    id = null
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

