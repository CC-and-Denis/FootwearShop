<template>
    
    <button @click="loadMore(-1)" :disabled="isLoading || counter === 0">
        <img class="small-img opacity-50" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
    </button>
    <div ref="container" class="carousel-container row">
      <!-- Dynamically render carousel items -->
      <div v-for="product in items" :key="product.id" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover mr-5"
      :style="{ backgroundImage: `url(${product.image})` }">
              <div class="priceContainer m-3">
                  €{{product.price}}
              </div>
            
              <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacity-0 transition-opacity hover:cursor-pointer">
                  <a class="h-full" :href="`/product/${product.id}`" >
                      <h1 class="text-xl underline">{{product.model}}</h1>
                      <p class="text-lg h-full"> {{product.description}}</p>
                  </a>
                
                  <a class="underline mt-3" :href="`/user/${product.seller.username}`">{{product.seller.username}}</a>
                  
              </div>
          </div>
      </div>
        
    <button @click="loadMore(1)" :disabled="isLoading || !hasMoreItems">
        <img class="small-img opacity-50 rotate-180" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
    </button>
  
</template>

<script>
export default {
  data() {
    return {
      counter: 0,
      items: [],
      isLoading: false,
      hasMoreItems: true
    };
  },
  methods: {
    async loadMore(direction) {
      if (this.isLoading || (direction < 0 && this.counter === 0) || (direction > 0 && !this.hasMoreItems)) {
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
        this.updateItems(data, direction);
      } catch (error) {
        console.error('Error loading products:', error);
      } finally {
        this.isLoading = false;
      }
    },

    getApiUrl(direction) {
      const baseApiUrl = this.$props.apiUrl;
      if(direction==0){
        return `${baseApiUrl}/4-0`
      }
      
      if(direction>0){
        return `${baseApiUrl}/1-${this.counter + 4}`
      }

      return `${baseApiUrl}/1-${this.counter - 1}`;
    },

    updateItems(data, direction) {
      const { products, hasMore } = data;

      if (direction < 0) {
        this.items = [...products, ...this.items.slice(0, -products.length)];
        this.counter -= 4; 
      } else if (direction > 0) {
        this.items = [...this.items.slice(products.length), ...products];
        this.counter += 4; 
      } else {
        this.items = products; 
      }
      this.hasMoreItems = hasMore;
    }
  },
  props: {
    apiUrl: {
      type: String,
      required: true
    }
  },
  mounted() {
    this.loadMore(0); // Initial load
  }
};
</script>

