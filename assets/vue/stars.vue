<template>

<img v-for="n in 5" @mouseleave="hovered = 0" @mouseover="hovered = n" @click="updateRating(n)" :class="['small-img', 'hover:cursor-pointer', {'star': (  n>hovered || hovered==0 && n>rating ), 'fullStar': (n<=hovered || hovered==0 && n<=rating)}]">

</template>

<script lang="ts">

export default {
  data() {
    return {
      hovered: 0,
      rating:1,
      scoreInput:HTMLInputElement,
    };
  },
  methods: {
    updateRating(n){

        this.rating = n;

        if( this.scoreInput ){
          this.scoreInput.value=n
        }
    }
  },
  mounted(){
    this.scoreInput = document.getElementById("review_form_score");
    if( this.scoreInput && this.scoreInput.value){
      this.rating=this.scoreInput.value
      }else if( this.scoreInput){
          this.scoreInput.value = 1
        }
  }
}
</script>

<style scoped>

.star{
    content:url('/public/build/images/star-regular.6a90c9dc.png')
}

.fullStar{
    content:url('/public/build/images/star-solid.86d1454e.png')
}




</style>