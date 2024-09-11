


class Carousel {
    public counter: number;
    public container: HTMLDivElement;
    public forward: HTMLImageElement;
    public back: HTMLImageElement;
    public semaphore: boolean;
  
    constructor() {
      // Initialize the counter but do not automatically set elements
      this.counter = 0; 
      this.semaphore = false;
    }
  
    // Method to assign the container div element
    public setContainer(elementId: string): void {
      const element = document.getElementById(elementId);
      if (element && element instanceof HTMLDivElement) {
        this.container = element;
      } else {
        console.error(`Element with ID ${elementId} is not a valid HTMLDivElement`);
      }
    }
  
    // Method to assign the forward image element
    public setForward(elementId: string): void {
      const element = document.getElementById(elementId);
      if (element && element instanceof HTMLImageElement) {
        this.forward = element;
        this.forward.addEventListener('click', () => loadMoreProducts(1,this));
      } else {
        console.error(`Element with ID ${elementId} is not a valid HTMLImageElement`);
      }
    }
  
    // Method to assign the back image element
    public setBack(elementId: string): void {
      const element = document.getElementById(elementId);
      if (element && element instanceof HTMLImageElement) {
        this.back = element;
        this.back.addEventListener('click', () => loadMoreProducts(-1,this));
      } else {
        console.error(`Element with ID ${elementId} is not a valid HTMLImageElement`);
      }
    }
  
    // Method to set the counter value
    public setCounter(value: number): void {
      this.counter = value;
    }
  
  
    // Method to increment the counter
    public incrementCounter(): void {
      this.counter++;
    }
  
    // Method to decrement the counter
    public decrementCounter(): void {
      this.counter--;
    }
  }


  

  const carouselPopular = new Carousel();
  carouselPopular.setContainer("popularSlideshow");   
  carouselPopular.setForward("goForwardPopular");       
  carouselPopular.setBack("goBackPopular");           

  const carouselFy = new Carousel();
  carouselFy.setContainer("fySlideshow");   
  carouselFy.setForward("goForwardFy");       
  carouselFy.setBack("goBackFy");             

if( carouselPopular.container ){
    fetch("/api/getProductByPopular/5-0")
    .then(response => response.text())
    .then(html=>{
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = html;

        let i = 0;
        Array.from(tempContainer.children).forEach(element => {
          if(i<4){
            carouselPopular.container.appendChild(element);
          }
          i++
          
        })
        if(document.getElementById("end-message")){
          document.getElementById("end-message").remove();
        }
       
        if(! tempContainer.children.length){
          carouselPopular.forward.style.opacity="0.5"
        }
    })
    .catch(error => console.error('Error loading products:', error));


}
if( carouselFy.container ){
  fetch("/api/fyp-function/5-0")
  .then(response => response.text())
  .then(html=>{
      const tempContainer = document.createElement('div');
      tempContainer.innerHTML = html;

      let i = 0;
      Array.from(tempContainer.children).forEach(element => {
        if(i<4){
          carouselFy.container.appendChild(element);
        }
        i++
        
      })
      if(document.getElementById("end-message")){
        document.getElementById("end-message").remove();
      }
     
      if(! tempContainer.children.length){
        carouselFy.forward.style.opacity="0.5"
      }
  })
  .catch(error => console.error('Error loading products:', error));


}

function loadMoreProducts(direction:number,carousel:Carousel){

    let url = "/api/getProductByPopular/2-"

    
   
    if(carousel==carouselFy){
        url="/api/fyp-function/2-"
    }

    if( carousel.semaphore || (direction<0 && carousel.counter==0) || (direction>0 && carousel.forward.style.opacity=="0.5") ){
        return;
    }
    else if(direction>0){
        url+=(carousel.counter+4)
    }
    else{
        url+=(carousel.counter-1)
    }
    carousel.semaphore=true;

    let errors = false;
    carousel.forward.style.opacity="0.5"
    
    fetch(url)
    .then(function(response) {                     
        if(response.ok)
        {
          return response.text();         
        }
        throw new Error('Something went wrong.');
    })
    .then(html=>{
        let tempContainer = document.createElement('div')
        tempContainer.innerHTML = html;

        let newElement = tempContainer.firstElementChild;
        
        if(! tempContainer.getElementsByClassName("end-message").length){
            carousel.forward.style.opacity="1"
        }


        if (direction < 0) {
            //the elements are counted as two: <a> and <div>
            carousel.container.removeChild(carousel.container.lastChild);
            carousel.container.insertBefore(newElement, carousel.container.firstChild);
        } 
        else{
            carousel.container.removeChild(carousel.container.children[0]);
            carousel.container.appendChild(newElement);
        }
    })
    .catch(error => {
        errors = true
        console.log(error)
    })

    if(errors){return}
    if(direction < 0){
        carousel.counter-=1
        carousel.forward.style.opacity="1"
        if(carousel.counter==0){
            carousel.back.style.opacity="0.5"
            }
    }else{
        carousel.counter+=1
        carousel.back.style.opacity="1"
    }
    carousel.semaphore=false;
    
}