var counter=0;
var isLoading = false;

const scrollableElement = document.getElementById('scrollable');

// Function to fetch products from the server
async function loadProductsForPopularPage() {
    let container = document.getElementById("grid-container");
    if (isLoading) return; // Prevent multiple requests at the same time
    isLoading = true;

    //document.getElementById('loader').style.display = 'block';

    try {
        
        const response = await fetch(`/api/getProductByPopular/8-`+counter);
        const html = await response.text();
        const productList = document.getElementById('productList');

        let tempContainer = document.createElement('div')
        tempContainer.innerHTML = html;

       
        Array.from(tempContainer.children).forEach(element => {
            container.appendChild(element);
          })
        
        if (document.getElementById("end-message")) {
            scrollableElement.removeEventListener('scroll', onScroll);
            document.getElementById("end-message").remove()
        }
        counter+=8


    } catch (error) {
        console.error('Error loading products:', error);
    } finally {
        isLoading = false;
        //document.getElementById('loader').style.display = 'none';
    }
}

// Detect when the user scrolls near the bottom
function onScroll() {
    const scrollTop = scrollableElement.scrollTop;
    const scrollHeight = scrollableElement.scrollHeight;
    const clientHeight = scrollableElement.clientHeight;

    // Check if scrolled near the bottom (e.g., within 100px)
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadProductsForPopularPage();
    }
}


if(scrollableElement){
    // Add scroll listener to the scrollable element
    scrollableElement.addEventListener('scroll', onScroll);
    // Load initial products
    loadProductsForPopularPage();

}
