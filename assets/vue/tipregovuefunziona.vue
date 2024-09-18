<script>
export default {
  data() {
    return {
      inputText: '',               // Bound to the search bar input
      errorMessage: false,          // Error message visibility
      isMaleChecked: false,         // Gender filters
      isFemaleChecked: false,
      isUnisexChecked: false,
      isKidChecked: false,          // Age filters
      isAdultChecked: false,
      hiddenTypes: true,            // Visibility of types, brands, and colors
      hiddenBrands: true,
      hiddenColors: true,
      types: ['Trekking', 'Running', 'Hiking', 'Sandals', 'Heels', 'Boots', 'Ankle Boots', 'Sneakers', 'Formal', 'Flip Flops', 'Others'], // List of types
      brands: ['Nike', 'Adidas', 'Puma', 'Asics', 'Converse', 'New Balance', 'Scarpa', 'La Sportiva', 'Hoka', 'Salomon'],                 // List of brands
      colors: ['White', 'Yellow', 'Orange', 'Red', 'Green', 'Blue', 'Violet', 'Pink', 'Cyan', 'Gray', 'Black'],                         // List of colors
      selectedTypes: [],            // Selected types
      selectedBrands: [],
      selectedColors: []
    };
  },
  methods: {
    // Handle the magnifying glass click event
    async handleImageClick() {
      if (!this.inputText.trim()) {
        this.errorMessage = true;  // Show error if input is empty
      }
      else {
        this.errorMessage = false; // Hide error if input is valid
        console.log('Search Query:', this.inputText);
        console.log('Selected Types:', this.selectedTypes);
        console.log('Selected Brands:', this.selectedBrands);
        console.log('Selected Colors:', this.selectedColors);
        console.log('Male:', this.isMaleChecked);
        console.log('Female:', this.isFemaleChecked);
        console.log('Unisex:', this.isUnisexChecked);
        console.log('Kid:', this.isKidChecked);
        console.log('Adult:', this.isAdultChecked);

        const parameters = {
          research: this.inputText,
          gender: this.getSelectedGenders(),
          age: this.getSelectedAges(),
          types: this.selectedTypes,
          brands: this.selectedBrands,
          colors: this.selectedColors,
        };

        try {
          const response = await fetch('/api/getProductByResearch/4-0', {
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
                console.log(data);
              })
        }
        catch (error) {
          console.error('Error fetching products:', error);
        }
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
  }
};
</script>

<template>
  <div>
    <!-- Search Bar with Input and Magnifying Glass Button -->
    <div id="searchBarContainer" class="row h-30 px-5 py-4 rounded-r-full lg:w-9/12 w-11/12 bg-white opacity-100">
      <input id="filterMenuCheckbox" class="hidden" type="checkbox">
      <label for="filterMenuCheckbox">
        <img class="small-img" src="/build/images/filter-solid.8f86f99e.png" alt="Filter Icon">
      </label>

      <!-- Input Text for Research -->
      <input
          id="researchText"
          v-model="inputText"
          type="text"
          class="w-11/12 h-full text-2xl border-y-0 border-black px-10 mx-4 border-solid"
      >

      <!-- Error Message -->
      <div v-if="errorMessage" id="errorMessage" style="color: red;">Please fill in the text input.</div>

      <!-- Magnifying Glass Button -->
      <div>
        <img
            id="magnifying-glass"
            class="small-img"
            src="/build/images/magnifying-glass-solid.2b32bcc1.png"
            alt="Magnifying Glass"
            @click="handleImageClick"
        >
      </div>
    </div>

    <!-- Filter Menu (Gender, Age, and Others) -->
    <div id="filterMenu" class="row bg-white bg-opacity-100 rounded-b-2xl p-1 w-8/12 border-t-2">
      <!-- Gender Filters -->
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

      <!-- Age Filters -->
      <div class="column">
        <p>Age</p>
        <label>
          <input type="checkbox" v-model="isKidChecked"> Kid
        </label>
        <label>
          <input type="checkbox" v-model="isAdultChecked"> Adult
        </label>
      </div>

      <!-- Dynamic Type, Brand, and Color Filters -->
      <div class="column">
        <p>Types</p>
        <button @click="toggleTypes">Types ▼</button>
        <div v-show="!hiddenTypes" class="filter-choices">
          <label v-for="type in types" :key="type" class="type_label" :data-genre="type" @click="selectType(type)">
            {{ type }}
          </label>
        </div>
      </div>

      <div class="column">
        <p>Brands</p>
        <button @click="toggleBrands">Brands ▼</button>
        <div v-show="!hiddenBrands" class="filter-choices">
          <label v-for="brand in brands" :key="brand" class="brand_label" :data-genre="brand" @click="selectBrand(brand)">
            {{ brand }}
          </label>
        </div>
      </div>

      <div class="column">
        <p>Colors</p>
        <button @click="toggleColors">Colors ▼</button>
        <div v-show="!hiddenColors" class="filter-choices">
          <label v-for="color in colors" :key="color" class="color_label" :data-genre="color" @click="selectColor(color)">
            {{ color }}
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
@tailwind base;
@tailwind components;
@tailwind utilities;
</style>