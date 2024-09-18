import './js/products.ts'

import { createApp } from 'vue'


import carousel_products from './vue/carousel_previews.vue'

const carousel_populars = createApp(carousel_products)
carousel_populars.mount('#carouselPreviews')