<template>
    
    <button @click="loadMore(-1)" :disabled="isLoading || counter === 0" :class="{ 'opacity-50': counter === 0 }">
        <img class="small-img mr-3" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
    </button>
    <div ref="container" class="productCarouselInner">
      <div v-for="product in products" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover mr-5"

      :style="{ backgroundImage: `url(${product.image})` }">
              <div class="priceContainer m-3">
                  €{{product.price}}
              </div>
            
              <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacit
y-0 transition-opacity hover:cursor-pointer">

                  <a class="h-full" :href="`/product/${product.id}`" >
                      <h1 class="text-xl underline">{{product.model}}</h1>
                      <p class="text-lg h-full"> {{product.description}}</p>
                  </a>
                
                  <a class="underline mt-3" :href="`/user/${product.seller.username}`">{{product.seller.username}}</a>
                  
              </div>
          </div>
      </div>
        
    <button @click="loadMore(1)" :disabled="isLoading || !hasMoreproducts" :class="{ 'opacity-50': !hasMoreproducts || isLoading }">
        <img class="small-img rotate-180" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
    </button>

  
</template>

<script>
export default {
  data() {
    return {
      counter: 0,
      products: [],
      isLoading: false,
      hasMoreproducts: true
    };
  },
  methods: {
    async loadMore(direction) {
      if (this.isLoading || (direction < 0 && this.counter === 0) || (direction > 0 && !this.hasMoreproducts)) {
        return;
      }

      this.isLoading = true;

      const url = this.getApiUrl(direction);

      try {
        const response = await fetch(url, { method: 'GET' });
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        this.updateproducts(data, direction);
      } catch (error) {
        console.error('Error loading products:', error);
      } finally {
        this.isLoading = false;
      }
    },

    getApiUrl(direction) {
      const baseApiUrl = this.$props.apiUrl;
      
      if(direction==0){
        if(window.innerWidth<1000){
        return `${baseApiUrl}/1-0`
        }
        return `${baseApiUrl}/4-0`
      }
      
      if(direction>0){
        if( window.innerWidth<1000){
          return `${baseApiUrl}/1-${this.counter + 1}`
        }
        return `${baseApiUrl}/1-${this.counter + 4}`
      }

      return `${baseApiUrl}/1-${this.counter - 1}`;
    },

    updateproducts(data, direction) {
      const { products, hasMore } = data;

      if (direction < 0) {
        this.products = [...products, ...this.products.slice(0, -products.length)];
      } else if (direction > 0) {
        this.products = [...this.products.slice(products.length), ...products];

      } else {
        this.products = products; 
      }
      this.counter+=direction;
      this.hasMoreproducts = hasMore;

    }
  },
  props: {
    apiUrl: {
      type: String,
      required: true
    }
  },
  mounted() {
    this.loadMore(0); 
  }
};
</script>

