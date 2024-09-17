<template>

<div @scroll="onScrolFunction" class="overflow-y-scroll">
    <div v-for="product in Products">

    </div>
</div>

</template>

<script>

function onScrolFunction(){
    console.log("scrolled")
}

</script>