<template>
    <!-- Search Bar, input, and confirm -->
    <div id="searchBarContainer" class="row h-30 px-5 py-4 rounded-r-full lg:w-9/12 w-11/12 bg-white opacity-100">
      <input id="filterMenuCheckbox" class="hidden" type="checkbox">
      <label for="filterMenuCheckbox">
        <img class="small-img" src="/build/images/filter-solid.8f86f99e.png" alt="Filter Icon">
      </label>

      <!-- input -->
      <input
          id="researchText"
          v-model="inputText"
          type="text"
          class="w-11/12 h-full text-2xl border-y-0 border-black px-10 mx-4 border-solid"
      >

      <!-- confirm button -->
      <div>
        <img
            id="magnifying-glass"
            class="small-img"
            src="/build/images/magnifying-glass-solid.2b32bcc1.png"
            alt="Magnifying Glass"
            @click="startResearch()"
        >
      </div>
    </div>

    <!-- Filter Menu -->
    <div id="filterMenu" class="row bg-white bg-opacity-100 rounded-b-2xl p-1 w-8/12 border-t-2">
      <!-- gender filter -->
      <div class="column">
        <p>Gender</p>
        <label>
          <input type="checkbox" v-model="isMaleChecked"> Male
        </label>
        <label>
          <input type="checkbox" v-model="isFemaleChecked"> Female
        </label>
        <label>
          <input type="checkbox" v-model="isUnisexChecked"> Unisex
        </label>
      </div>

      <!-- age filter -->
      <div class="column">
        <p>Age</p>
        <label>
          <input type="checkbox" v-model="isKidChecked"> Kid
        </label>
        <label>
          <input type="checkbox" v-model="isAdultChecked"> Adult
        </label>
      </div>

      <!-- type, brand and colors filter (dinamic) -->
      <div class="column">
        <button @click="toggleTypes" class="w-full">Types ▼</button>
        <div v-show="!hiddenTypes" class="customChoices">
          <label v-for="type in types" :key="type" class="type_label" :data-genre="type" @click="selectType(type)">
            {{ type }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleBrands" class="w-full">Brands ▼</button>
        <div v-show="!hiddenBrands" class="customChoices">
          <label v-for="brand in brands" :key="brand" class="brand_label" :data-genre="brand" @click="selectBrand(brand)">
            {{ brand }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleColors" class="w-full">Colors ▼</button>
        <div v-show="!hiddenColors" class="customChoices">
          <label v-for="color in colors" :key="color" class="color_label" :data-genre="color" @click="selectColor(color)">
            {{ color }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleSizes" class="w-full">Sizes ▼</button>
        <div v-show="!hiddenSizes" class="customChoices">
          <label v-for="size in sizes" :key="size" :data-genre="size" @click="selectSize(size)">
            {{ size }}
          </label>
        </div>
      </div>

    </div>

    <div @scroll="onScrollFunction" id="scrollable-grid-products" class="centered overflow-y-scroll h-[85vh] mt-10 relative">
      <div id="products-grid" class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-3 absolute top-0">

          <div v-for="product in products" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover" :style="{ backgroundImage: `url(${product.image})` }">
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
    </div>
</template>

<style lang="css" scoped>

.customChoices{
  @apply size-full flex flex-col bg-white border-[1px] border-black overflow-y-scroll pl-0
}

.customChoices>label{
  @apply border-y-[1px] border-collapse border-black w-[11vh] m-0 p-0 text-center
}
</style>
<script>
export default {
  data() {
    return {
      products: [],
      counter: 0,
      inputText: '',
      isMaleChecked: false,
      isFemaleChecked: false,
      isUnisexChecked: false,
      isKidChecked: false,
      isAdultChecked: false,
      hiddenTypes: true,
      hiddenBrands: true,
      hiddenColors: true,
      hiddenSizes: true,
      types: ['Trekking', 'Running', 'Hiking', 'Sandals', 'Heels', 'Boots', 'Ankle Boots', 'Sneakers', 'Formal', 'Flip Flops', 'Others'],
      brands: ['Nike', 'Adidas', 'Puma', 'Asics', 'Converse', 'New Balance', 'Scarpa', 'La Sportiva', 'Hoka', 'Salomon', 'Others'],
      colors: ['White', 'Yellow', 'Orange', 'Red', 'Green', 'Blue', 'Violet', 'Pink', 'Cyan', 'Gray', 'Black', 'Others'],
      sizes: Array.from({ length: 38 }, (_, i) => i+15),
      selectedTypes: [],
      selectedBrands: [],
      selectedColors: [],
      selectedSizes: [],
    };
  },
  methods: {
    // Handle the magnifying glass click event
    async startResearch() {

        const parameters = {
          research: this.inputText,
          gender: this.getSelectedGenders(),
          age: this.getSelectedAges(),
          types: this.selectedTypes,
          brands: this.selectedBrands,
          colors: this.selectedColors,
          sizes: this.selectedSizes,
        };

        try {
          const response = await fetch('/api/getProductByResearch/4-'+this.counter, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(parameters),
          })
              .then(response => {
                if (response.status === 200) {
                  return response.json();
                }
                else{
                  throw new Error(`!response.ok - HTTP error! status: ${response.status}`);
                }
              })
              .then(data => {
                const { products, hasMore } = data;
                this.products = this.products.concat(products);
                console.log(products)
                this.hasMoreproducts = hasMore;
                this.counter += 4;
              })
        }
        catch (error) {
          console.error('Error fetching products:', error);
        }
    },
    // Toggle visibility of types
    toggleTypes() {
      this.hiddenTypes = !this.hiddenTypes;
    },

    // Toggle visibility of brands
    toggleBrands() {
      this.hiddenBrands = !this.hiddenBrands;
    },

    // Toggle visibility of colors
    toggleColors() {
      this.hiddenColors = !this.hiddenColors;
    },

    toggleSizes() {
      this.hiddenSizes = !this.hiddenSizes;
    },

    // Handle selecting a type
    selectType(type) {
      this.toggleSelection(this.selectedTypes, type);
    },

    // Handle selecting a brand
    selectBrand(brand) {
      this.toggleSelection(this.selectedBrands, brand);
    },

    // Handle selecting a color
    selectColor(color) {
      this.toggleSelection(this.selectedColors, color);
    },

    selectSize(size) {
      this.toggleSelection(this.selectedSizes, size);
    },

    // Generic function to toggle selection of types, brands, and colors
    toggleSelection(selectionArray, item) {
      const index = selectionArray.indexOf(item);
      if (index === -1) {
        selectionArray.push(item);
      } else {
        selectionArray.splice(index, 1);
      }
    },
    getSelectedGenders() {
      const genders = [];
      if (this.isMaleChecked) genders.push('male');
      if (this.isFemaleChecked) genders.push('female');
      if (this.isUnisexChecked) genders.push('unisex');
      return genders;
    },
    getSelectedAges() {
      const ages = [];
      if (this.isKidChecked) ages.push('kid');
      if (this.isAdultChecked) ages.push('adult');
      return ages;
    },
    onScrollFunction() {
      const scrollableElement = document.getElementById('scrollable-grid-products');
      const scrollTop = scrollableElement.scrollTop;
      const scrollHeight = scrollableElement.scrollHeight;
      const clientHeight = scrollableElement.clientHeight;

      // Check if scrolled near the bottom (e.g., within 100px)
      if (scrollTop + clientHeight >= scrollHeight - 100) {
        this.loadProductsForProductsPage('/api/getProductByResearch/4-');
      }
    },
  },
};
</script>
