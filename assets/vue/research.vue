<template>
    <!-- Search Bar, input, and confirm -->
    <label for="filterMenuCheckbox" class="lg:hidden flex fixed bg-white rounded-full top-5 left-25% size-20 centered ">
        <img @click="toggleFilters()" class="small-img hover:cursor-pointer hover:scale-105 transition-all" src="/build/images/filter-solid.8f86f99e.png" alt="Filter Icon">
      </label>

      <div class="font-bold h-20 w-24 text-black pt-2 lg:hidden block fixed top-5 right-5 bg-white column rounded-full justify-center items-center text-sm text-center">
      <div class="mt-3">Search type</div>
      <select v-model="selectedResearchType" class="w-fit h-fit ">
          <option class="" value="product">Product</option>
          <option @click="blockFilters" class="" value="user">User</option>
      </select>
    </div>

    <div id="searchBarContainer" class="row h-30 px-5 py-4 rounded-r-full lg:w-9/12 w-11/12 bg-white opacity-100 fixed lg:top-[18vh] top-[22.5vh]">
      <input id="filterMenuCheckbox" class="hidden" type="checkbox">
      <label for="filterMenuCheckbox" class="lg:block hidden">
        <img @click="toggleFilters()" class="small-img hover:cursor-pointer hover:scale-105 transition-all" src="/build/images/filter-solid.8f86f99e.png" alt="Filter Icon">
      </label>

      <!-- input -->
      <input
          id="researchText"
          v-model="inputText"
          type="text"
          class="w-full h-full text-2xl border-y-0 border-black lg:px-10 px-1 pt-2 mx-4 border-solid"
      >
      <select v-model="selectedResearchType" class="w-fit h-full mr-5 font-bold text-black pt-2 lg:block hidden">
          <option class="" value="product">Product Research</option>
          <option @click="blockFilters" class="" value="user">User Research</option>
      </select>


      <!-- confirm button -->
      <div>
        <img
            id="magnifying-glass"
            class="small-img hover:cursor-pointer hover:scale-105 transition-all"
            src="/build/images/magnifying-glass-solid.2b32bcc1.png"
            alt="Magnifying Glass"
            @click="startResearch()"
        >
      </div>
    </div>

    <!-- Filter Menu -->
    <div v-show="!hiddenFilters" id="filterMenu" class="row bg-white bg-opacity-100 rounded-b-2xl p-1 w-8/12 border-t-2 fixed lg:top-[26.5vh] top-[31.7vh] lg:overflox-x-hidden overflow-x-scroll">
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

      <div class="column w-[17vh]">
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


<div @scroll="onScrollFunction()" id="scrollable-grid-products" :class="['lg:w-10/12', 'w-[95%]', 'min-h-[80vh]','rounded-xl','p-10','bg-[whitesmoke]','lg:mt-[30vh]','mt-[50vh]','z-50','top-[0vh]','overflow-y-scroll','overflow-x-hidden','items-center',{'hidden': (  counter<0 ),'sticky': (  counter>=0 )}]">
  <div id="products-grid" class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-3 w-full">
          <div v-if="url === '/api/getProductByResearch/12-'" v-for="product in cards" :key="product" class="animation-disappear" @click="productRedirect(product.id)">
          <div class="productCard relative rounded-2xl bg-semi-transparent-2 bg-no-repeat bg-center bg-cover size-[35vh] shadow-xl animation-appear shaddow-black" :style="{ backgroundImage: `url(${product.image})` }">
            <div class="row">
                <div class="priceContainer m-3 shadow-black shadow-sm">
                  €{{product.price}}
                </div>
                <div class="row priceContainer m-3 shadow-black shadow-sm">
                  <img class="lg:size-6 size-3 mr-1 " src="/build/images/boxes-stacked-solid.ede33cda.png">{{product.quantity}}
                </div>

              </div>

              <div class="productInfo absolute bottom-0 column w-full backdrop-blur-2xl p-3 opacity-0 transition-opacity hover:cursor-pointer">

                  <a class="h-full" :href="`/product/${product.id}`" >
                    <h1 class="text-xl underline">{{product.model}}</h1>
                    <p class="text-lg h-[12vh] overflow-y-scroll"> {{product.description}}</p>
                  </a>

                  <a class="underline mt-3" :href="`/user/${product.seller.username}`">{{product.seller.username}}</a>

              </div>
            </div>
          </div>
          <div v-else v-for="user in cards" :key="user" class="animation-disappear"  @click="userRedirect(user.username)">
            <div class="userCard column centered relative std-container bg-white shadow-xl shadow-shadow animation-appear" >
              <img
                  id="userImage"
                  class="bg-cover bg-no-repeat bg-center h-[22vh] w-[22vh] rounded-full bg-shadow mt-4"
                  src='/build/images/user-regularB.df1377b9.png'
                  alt="userImage"
              >
            <div class="w-9/12 text-center font-bold text-2xl mt-3">{{ user.username }}</div>
          
            <h1 class="my-3 ">Average rating</h1>
            <div class="row mb-3">
              <div class="row mb-3">
              <img v-for="img in user.average" :key="img" :src="img" class="small-img">
              </div>
            </div>
            <div class="column ml-5 mr-5 items-center gap-5">
              <div class="priceContainer bg-shadow">Selling products: {{ user.totalProd }}</div>
              <div class="priceContainer bg-shadow">You bought: {{ user.youBought }}</div>
            </div>
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
  @apply border-y-[1px] border-collapse border-black w-full m-0 p-0 text-center
}

#filterMenu>.column{
  @apply min-w-[17vh]
}

#filterMenu>.column>p{
  @apply text-base
}

#filterMenu>.column>label{
  @apply text-sm
}
</style>

<script>
export default {
  data() {
    return {
      url: '',
      parameters: [],
      selectedResearchType: 'product',
      cards: [],
      hasMoreproducts: true,
      isLoading: false,
      counter: -1,
      inputText: '',
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
    async startResearch() {
      this.cards = [];
      this.counter = 0;
      this.hasMoreproducts = true;
      console.log(this.selectedResearchType)

      if (this.selectedResearchType == 'product') {
        this.url = '/api/getProductByResearch/12-'
        this.parameters = {
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
        this.url = '/api/getUserByResearch/12-'
        this.parameters = {
          research: this.inputText,
        };
      }
      this.loadProducts()
    },

    async loadProducts() {
      if (this.isLoading || !this.hasMoreproducts) return;
      this.isLoading = true;
      try {
        
          const response = await fetch(this.url+this.counter, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.parameters),
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
                this.hasMoreproducts = hasMore;
                this.counter += 12;
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

    productRedirect(id){
      window.location.href="/product/"+id
    },
    userRedirect(username){
      window.location.href="/user/"+username
    },

    selectType(type) {
      this.toggleSelection(this.selectedTypes, type);
    },

    selectBrand(brand) {
      this.toggleSelection(this.selectedBrands, brand);
    },

    selectColor(color) {
      this.toggleSelection(this.selectedColors, color);
    },

    selectSize(size) {
      this.toggleSelection(this.selectedSizes, size);
    },

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

      if (scrollTop + clientHeight >= scrollHeight - 100) {
        this.loadProducts();
      }
    },
    blockFilters(){
     
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
