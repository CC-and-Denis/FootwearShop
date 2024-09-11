const errors = document.getElementById("errorsBox") as HTMLDivElement


if(errors){

    document.getElementById("closeErrorsBtn").addEventListener("click",()=>{
        let position=0;
        let id = null
        clearInterval(id)
        id=setInterval(frame,1)
        
        function frame() {
    
           if(position==-400){
            clearInterval(id)
            errors.style.display="none"
           }else{
            position-=40
            errors.style.right=position+"px"
           }
        
        }
        })
}