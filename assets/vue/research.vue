<template>
    <!-- Search Bar, input, and confirm -->
    <div id="searchBarContainer" class="row h-30 px-5 py-4 rounded-r-full lg:w-9/12 w-11/12 bg-white opacity-100">
      <input id="filterMenuCheckbox" class="hidden" type="checkbox">
      <label for="filterMenuCheckbox">
        <img @click="toggleFilters()" class="small-img" src="/build/images/filter-solid.8f86f99e.png" alt="Filter Icon">
      </label>

      <!-- input -->
      <input
          id="researchText"
          v-model="inputText"
          type="text"
          class="w-11/12 h-full text-2xl border-y-0 border-black px-10 mx-4 border-solid"
      >

      <select v-model="selectedResearchType" class="w-fit h-full mr-5 font-bold text-black">
          <option class="" value="product">Product Research</option>
          <option @click="blockFilters" class="" value="user">User Research</option>
      </select>

      <div v-if="errorMessage" id="errorMessage" style="color: red;">Please fill in the text input.</div>

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
    <div v-show="!hiddenFilters" id="filterMenu" class="row bg-white bg-opacity-100 rounded-b-2xl p-1 w-8/12 border-t-2">
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
          <label v-for="type in types" :key="type" @click="selectType(type)" :class="{ 'bg-blue': selectedTypes.includes(type) }">
            {{ type }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleBrands" class="w-full">Brands ▼</button>
        <div v-show="!hiddenBrands" class="customChoices">
          <label v-for="brand in brands" :key="brand" @click="selectBrand(brand)" :class="{ 'bg-blue': selectedBrands.includes(brand) }">
            {{ brand }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleColors" class="w-full">Colors ▼</button>
        <div v-show="!hiddenColors" class="customChoices">
          <label v-for="color in colors" :key="color" @click="selectColor(color)" :class="{ 'bg-blue': selectedColors.includes(color) }">
            {{ color }}
          </label>
        </div>
      </div>

      <div class="column">
        <button @click="toggleSizes" class="w-full">Sizes ▼</button>
        <div v-show="!hiddenSizes" class="customChoices">
          <label v-for="size in sizes" :key="size" @click="selectSize(size)" :class="{ 'bg-blue': selectedSizes.includes(size) }">
            {{ size }}
          </label>
        </div>
      </div>

    </div>

    <div @scroll="onScrollFunction" id="scrollable-grid-products" class="centered overflow-y-scroll h-[85vh] mt-10 relative">
      <div id="products-grid" class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-3 absolute top-0">
          <div v-if="url === '/api/getProductByResearch/4-'" v-for="card in cards" class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover" :style="{ backgroundImage: `url(${card.image})` }">
              <div class="priceContainer m-3">
                €{{card.price}}
              </div>

              <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacity-0 transition-opacity hover:cursor-pointer">

                  <a class="h-full" :href="`/product/${card.id}`" >
                    <h1 class="text-xl underline">{{card.model}}</h1>
                    <p class="text-lg h-full"> {{card.description}}</p>
                  </a>

                  <a class="underline mt-3" :href="`/user/${card.seller.username}`">{{card.seller.username}}</a>

              </div>
          </div>
          <div v-else v-for="card in cards" class="userCard column centered relative bg-semi-transparent-2">
            <div>
              <img
                  id="userImage"
                  class="generic-img size-50 rounded-full  bg-semi-transparent-1 mt-4"
                  src='build/images/user-regularB.df1377b9.png'
                  alt="userImage"
                  @click="startResearch()"
              >
            </div>
            <h1 class="my-1">Average</h1>
            <div class="row mb-3">
              <div class="row mb-3">
              <img v-for="img in card.average" {{img}}>
              </div>
            </div>
            <div class="h-[5vh] w-9/12">{{ card.username }}</div>
            <div class="row h-[5vh]">
              <div class="column ml-5 mr-5">
                
              </div>
              <div class="column ml-5 mr-5">
                
              </div>
            </div>
              <div class="priceContainer m-3">
                ciao
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
      url: '',
      selectedResearchType: 'product',
      cards: [],
      hasMoreproducts: true,
      isLoading: false,
      counter: 0,
      inputText: '',
      errorMessage: false,
      isMaleChecked: false,
      isFemaleChecked: false,
      isUnisexChecked: false,
      isKidChecked: false,
      isAdultChecked: false,
      hiddenFilters: true,
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
      this.cards = [];
      this.counter = 0;

      if (this.selectedResearchType === 'product') {
        this.url = '/api/getProductByResearch/4-'
        var parameters = {
          research: this.inputText,
          gender: this.getSelectedGenders(),
          age: this.getSelectedAges(),
          types: this.selectedTypes,
          brands: this.selectedBrands,
          colors: this.selectedColors,
          sizes: this.selectedSizes,
        };
      }
      else {
        if (this.inputText.trim()) {
          this.errorMessage = false;
        }
        else{
          this.errorMessage = true;
          return;
        }
        this.url = '/api/getUserByResearch/4-'
        var parameters = {
          research: this.inputText,
        };
      }
      this.loadProducts(parameters)
    },

    async loadProducts(parameters) {

      if (this.isLoading || !this.hasMoreproducts) return; // Prevent multiple requests at the same time
      this.isLoading = true;

      try {
          const response = await fetch(this.url+this.counter, {
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
                const { cards, hasMore } = data;
                this.cards = this.cards.concat(cards);
                console.log(cards)
                this.hasMoreproducts = hasMore;
                this.counter += 4;
              })
        }
        catch (error) {
        console.error('Error loading products:', error);
      } finally {
        this.isLoading = false;
      }
      },

    toggleFilters() {
      if (this.selectedResearchType === 'product') {
        this.hiddenFilters = !this.hiddenFilters;
      }
    },
    toggleTypes() {
      this.hiddenTypes = !this.hiddenTypes;
    },
    toggleBrands() {
      this.hiddenBrands = !this.hiddenBrands;
    },
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
      if (this.isKidChecked) ages.push(1);
      if (this.isAdultChecked) ages.push(0);
      return ages;
    },
    onScrollFunction() {
      const scrollableElement = document.getElementById('scrollable-grid-products');
      const scrollTop = scrollableElement.scrollTop;
      const scrollHeight = scrollableElement.scrollHeight;
      const clientHeight = scrollableElement.clientHeight;

      // Check if scrolled near the bottom (e.g., within 100px)
      if (scrollTop + clientHeight >= scrollHeight - 100) {
        this.loadProductsForProductsPage();
      }
    },
    blockFilters(){
      console.log(1234);
      this.hiddenFilters = true;
    },
    displayFilters(){
      let element = document.getElementById("filterMenu")
      let filterMenuCheckbox = document.getElementById("filterMenuCheckbox").HTMLInputElement
      id = null
      clearInterval(id)
      id=setInterval(frame,10)
      if(filterMenuCheckbox.checked){
          step = 1
          end = 10
          opacity = 0
      }else{
          step = -1
          end = 0
          opacity = 10
      }
      function frame() {
          if (opacity==end) {
            clearInterval(id);
          } else {
            opacity+=step
            element.style.opacity=(opacity/10).toString()
          }

      }
    }
  },
};
</script>
