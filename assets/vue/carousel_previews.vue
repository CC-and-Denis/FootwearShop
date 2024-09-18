<template>
  <button @click="loadMore(-1)" :disabled="counter === 0" :class="{ 'opacity-50': counter === 0 }">
          <img class="small-img mr-3" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
      </button>
      <div ref="container" class="productCarouselInner">
        <div v-for="image in previewImages" class="genericImg"
        :style="{ backgroundImage: `url(${image})` }">
          </div>
      </div>
          
      <button @click="loadMore(1)" :disabled=" counter+3 == maxImages" :class="{ 'opacity-50': counter==maxImages }">
          <img class="small-img rotate-180" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
      </button>
  </template>
  <script lang="ts">
  export default {
    data() {
      return {
        counter: 0,
        maxImages:0,
        previewImages:[],
        imagesStorage:[],
        imageStorageElement: document.getElementById('hiddenOthersImages'),
      };
    },
    methods:{
      loadMore(direction:number){
  
      },

      applyNew(){

        let img:String;

        for(let i = 0;i<this.imageStorageElement.children.length;i++){
        img=this.imageStorageElement.children[i].innerHTML
        console.log(img)
        this.imagesStorage.push(img)
        if(i<3){
          this.previewImages.push(img)
        }
      }
        
      }
    },
    mounted(){
    const observer = new MutationObserver(() => {
      this.applyNew()
    });
      let img:String;

      if(! this.imageStorageElement){
        return
      }

      if(document.getElementById("")){
        observer.observe(element, {
          childList: false,
          subtree: true,
        attributeFilter: ["innerHTML"]
        });
      }else{
        this.applyNew()
      }

      



    }
  
  };
  </script>