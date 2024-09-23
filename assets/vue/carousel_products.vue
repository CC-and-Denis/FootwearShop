<template>
    
    <button @click="loadMore(-1)" :disabled="isLoading || counter === 0" :class="{ 'opacity-50': counter === 0 }">
        <img class="small-img mr-3" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
    </button>


    <div ref="container" :class="['productCarouselInner', 'grid-cols-1', 'm-2', 'h-[30vh]', 'gap-2', 'overflow-y-hidden', { 'xl:grid-cols-4': maxProducts === 4, 'xl:grid-cols-2': maxProducts === 2 }]">
      <div v-for="product in products" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover w-[30vh] h-[30vh]"

      :style="{ backgroundImage: `url(${product.image})` }">
              <div class="priceContainer m-3">
                  €{{product.price}}
              </div>
            
              <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacity-0 transition-opacity hover:cursor-pointer">

                  <a class="h-full" :href="`/product/${product.id}`" >
                      <h1 class="text-lg underline">{{product.model}}</h1>
                      <p class="text-md break-all h-[12vh] w-[28vh] overflow-y-scroll"> {{product.description}}</p>
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
      productType: HTMLInputElement,
      productMaterial: HTMLInputElement,
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

      if (this.$props.apiUrl == '/api/getSimilarProducts') {

        const parameters = {
          'type': this.productType.value,
          'material': this.productMaterial.value,
        }
        try {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(parameters),
          })
              .then(response => {
                if (response.status === 200) {
                  return response.json();
                } else {
                  throw new Error(`!response.ok - HTTP error! status: ${response.status}`);
                }
              })
              .then(data => {
                console.log(data);
                this.updateproducts(data, direction);
              })
        }
        catch (error) {
          console.error('Error loading products:', error);
        }
        finally {
          this.isLoading = false;
        }
      }
      else {

        try {
          const response = await fetch(url, {method: 'GET'});
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }

          const data = await response.json();
          console.log(data);
          this.updateproducts(data, direction);
        }
        catch (error) {
          console.error('Error loading products:', error);
        }
        finally {
          this.isLoading = false;
        }
      }
    },

    getApiUrl(direction) {
      const baseApiUrl = this.$props.apiUrl;
      
      if(direction==0){
        if(window.innerWidth<1000){
        return `${baseApiUrl}/1-0`
        }
        console.log(this.$props.maxProducts);
        return `${baseApiUrl}/${this.$props.maxProducts}-0`
      }
      
      if(direction>0){
        if( window.innerWidth<1280){
          return `${baseApiUrl}/1-${this.counter + 1}`
        }
        return `${baseApiUrl}/1-${this.counter + this.$props.maxProducts}`
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
    },
    maxProducts: {
      type: Number,
      required: true
    }
  },
  mounted() {
    this.productType = document.getElementById('product_type');
    this.productMaterial = document.getElementById('product_material');
    this.loadMore(0);
  }
};
</script>

