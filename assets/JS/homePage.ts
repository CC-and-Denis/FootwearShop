


class Carousel {
    public counter: number;
    public container: HTMLDivElement;
    public forward: HTMLImageElement;
    public back: HTMLImageElement;
  
    constructor() {
      // Initialize the counter but do not automatically set elements
      this.counter = 0; 
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


  
  // Example usage:
  // Assuming the elements are already in the document with IDs 'container', 'forward', and 'back'
  const carouselPopular = new Carousel();
  carouselPopular.setContainer("popularSlideshow");   // Set the div element by its ID
  carouselPopular.setForward("goForwardPopular");       // Set the forward image by its ID
  carouselPopular.setBack("goBackPopular");             // Set the back image by its ID

  const carouselFy = new Carousel();
  carouselFy.setContainer("fySlideshow");   // Set the div element by its ID
  carouselFy.setForward("goForwardFy");       // Set the forward image by its ID
  carouselFy.setBack("goBackFy");             // Set the back image by its ID

if( carouselPopular.container ){
    fetch("/api/getProductByPopular/4-0")
    .then(response => response.text())
    .then(html=>{
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = html;

        // Add each child element of tempContainer to the productContainer
        Array.from(tempContainer.children).forEach(element => {
        // You can manipulate each element here if needed before appending
        carouselPopular.container.appendChild(element);
        })
    })
    .catch(error => console.error('Error loading products:', error));


}


function loadMoreProducts(direction:number,carousel:Carousel){

    let url = "/api/getProductByPopular/2-"
   
    if(carousel==carouselFy){
        url="/api/fyp-function"
    }

    if( (direction<0 && carousel.counter==0) || (direction>0 && carousel.forward.style.opacity=="0.5") ){
        return;
    }else if(direction>0){
        url+=(carousel.counter+4)
    }else{
        url+=(carousel.counter-1)
    }

    let errors = false;
    carousel.forward.style.opacity="0.5"
    fetch(url)
    .then(function(response) {                      // first then()
        if(response.ok)
        {
          return response.text();         
        }
        throw new Error('Something went wrong.');})

    .then(html=>{
        let tempContainer = document.createElement('div')
        tempContainer.innerHTML = html;

        let newElement = tempContainer.firstElementChild;
        let newElement1 = tempContainer.children[1];

        if(tempContainer.children.length>2){
            carousel.forward.style.opacity="1"
        }


            if (direction < 0) {
                //tje elements are counted as two: <a> and <div>
                carousel.container.removeChild(carousel.container.lastChild);
                carousel.container.removeChild(carousel.container.lastChild);
                carousel.container.insertBefore(newElement1, carousel.container.firstChild);
                carousel.container.insertBefore(newElement, carousel.container.firstChild);
            } 
            else{
                carousel.container.removeChild(carousel.container.children[0]);
                carousel.container.removeChild(carousel.container.children[0]);
                carousel.container.appendChild(newElement);
                carousel.container.appendChild(newElement1);
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
    
}