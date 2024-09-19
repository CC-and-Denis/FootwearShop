<template>
  <button @click="loadMore(-1)" :disabled="this.counter === 0" :class="{ 'opacity-50': this.counter === 0 }">
          <img class="small-img mr-3" src="/build/images/circle-chevron-left-solid.a5e7fe27.png">
      </button>
      <div ref="container" class="productCarouselInner w-full grid xl:grid-cols-3 grid-cols-1 h-[30vh] overflow-y-hidden">

        <div v-for="image in previewImages" class="generic-img h-[30vh] xl:w-[25vw] w-full rounded-2xl bg-semi-transparent-3"
        :style="{ backgroundImage: `url(${image})` }">
          </div>
      </div>
          
      <button @click="loadMore(1)" :disabled=" (counter+3 >= maxImages) || (counter+1>=maxImages && this.screenWidth>1280 )" :class="{ 'opacity-50': (this.counter+3) >= this.maxImages }">
        
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
        imageStorageElement:HTMLDivElement,
      };
    },
    methods:{
      applyNew(){

        let maxViewable=1
        let img:String;

        this.counter=0;
        this.imagesStorage=[];
        this.previewImages=[];

        this.maxImages = this.imageStorageElement.children.length;

        if(this.screenWidth > 1280){
          maxViewable=3
        }

        for(let i = 0;i<this.maxImages;i++){
          img=this.imageStorageElement.children[i].innerHTML
          this.imagesStorage.push(img)
            if(i<maxViewable){
              this.previewImages.splice(2,0,img)
            }
        }
      },

    loadMore(direction:number){
      let offeset=0;

      this.counter+=direction
      
      console.log(this.counter)

      if( direction==1 ){
        if(this.screenWidth > 1280){
          console.log("gigi")
          offeset+=2
        }
        this.previewImages.splice(0,1)
        this.previewImages.splice(2,0,this.imagesStorage[this.counter+offeset])

      }else{
        this.previewImages.splice(2,1)
       this.previewImages.splice(0,0,this.imagesStorage[this.counter])

      }

      
      




    },
  },

    mounted(){
      this.imageStorageElement=document.getElementById("hiddenOtherImages")
      this.screenWidth = window.innerWidth

      const observer = new MutationObserver(() => {
        this.applyNew()
      });


      if( ! this.imageStorageElement ){
        return
      }

      if(document.getElementById("product_form_otherImages")){
        observer.observe(document.getElementById("hiddenOtherImages"), {
          characterData: false, 
          childList: true, 
          attributes: false
        });
      }else{
        this.applyNew()
      }

    }
  
  };
  </script>