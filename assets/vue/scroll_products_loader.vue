<template>
<div @scroll="onScrollFunction" id="scrollable-grid-products" class="centered overflow-y-scroll h-[85vh] mt-10 relative">
  <div id="products-grid" class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-3 absolute top-0">

      <div v-for="product in products" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover" :style="{ backgroundImage: `url(${product.image})` }">
        <div class="row">
                <div class="priceContainer m-3 shadow-black shadow-sm">
                  €{{product.price}}
              </div>
              <div class="row priceContainer m-3 shadow-black shadow-sm">
                <img class="lg:size-6 size-3 mr-1 " src="/build/images/boxes-stacked-solid.ede33cda.png">{{product.quantity}}
              </div>
        </div>

        <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacity-0 transition-opacity hover:cursor-pointer border-shadow border-t-2">

<a class="h-full" :href="`/product/${product.id}`" >
    <h1 class="text-md underline">{{product.model}}</h1>
    <p class="text-sm break-all h-[12vh] w-full overflow-y-scroll"> {{product.description}}</p>
</a>

<a class="underline mt-3" :href="`/user/${product.seller.username}`">{{product.seller.username}}</a>

</div>
      </div>
  </div>
</div>

</template>

<script>
export default {
  data() {
    return {
      counter: 0,
      isLoading: false,
      products: [], // This will store the products loaded
      pageName: HTMLInputElement,
      hasMoreproducts: true,
    };
  },
  methods: {
    async loadProductsForProductsPage(url) {

      if (this.isLoading || !this.hasMoreproducts) return; // Prevent multiple requests at the same time
      this.isLoading = true;

      try {

        const response = await fetch(url + this.counter);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const data = await response.json();
        const { products, hasMore } = data;
        this.products = this.products.concat(products);
        console.log(products)
        this.hasMoreproducts = hasMore;

        if (document.getElementById("end-message")) {
          document.getElementById("end-message").remove();
        }

        this.counter += 8; // Load the next set
      } catch (error) {
        console.error('Error loading products:', error);
      } finally {
        this.isLoading = false;
      }
    },

    onScrollFunction() {
      console.log(10)
      const scrollableElement = document.getElementById('scrollable-grid-products');
      const scrollTop = scrollableElement.scrollTop;
      const scrollHeight = scrollableElement.scrollHeight;
      const clientHeight = scrollableElement.clientHeight;

      // Check if scrolled near the bottom (e.g., within 100px)
      if (scrollTop + clientHeight >= scrollHeight - 100) {
        if (this.pageName === 'populars') {
          this.loadProductsForProductsPage('/api/getProductByPopular/8-');
        } else {
          this.loadProductsForProductsPage('/api/getProductsForYou/8-');
        }
      }
    },
  },
  mounted() {
    // Load initial products when the component is mounted
    this.pageName = document.getElementById('page_name');
    console.log("a", this.pageName);
    if (this.pageName.value === 'populars') {
      this.loadProductsForProductsPage('/api/getProductByPopular/8-');
    } else {
      this.loadProductsForProductsPage('/api/getProductsForYou/8-');
    }
  }
};
</script>