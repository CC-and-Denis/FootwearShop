const colorSelection = document.getElementById("product_form_colors") as HTMLSelectElement;
const mainImage = document.getElementById("product_form_mainImage") as HTMLInputElement;
const mainImagePreview = document.getElementById("mainImagePreview") as HTMLDivElement;
const quantity = document.getElementById("product_form_quantity") as HTMLInputElement


function setQuantityToMin(){
    if( quantity && ! quantity.value || quantity.valueAsNumber<1){
        quantity.value="1"    
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
    console.log("src")
    console.log(mainImagePreview.style.backgroundImage)

    if(! mainImagePreview.style.backgroundImage){
        mainImagePreview.style.backgroundImage='url("build/Images/file-arrow-up-solid.bca87184.png")'
    }
    mainImage.addEventListener('change',()=>{
        console.log(mainImage.files[0].type)
        if(["image/png","image/jpg","image/jpeg"].includes(mainImage.files[0].type)){
            console.log(URL.createObjectURL(mainImage.files[0]))
            mainImagePreview.style.backgroundImage=`url(${URL.createObjectURL(mainImage.files[0])})`
        }else{
            mainImage.value=""
            mainImage.files[0]=null
            mainImagePreview.style.backgroundImage='url("build/Images/file-arrow-up-solid.bca87184.png")'
            alert("file format not supported")
        }
    })
}

if(quantity){
    setQuantityToMin()
    quantity.addEventListener("change",setQuantityToMin)
}



