import './styles/frame.css';

import './js/frame.ts';
import './js/userPage.ts'
import './js/homePage.ts'
import './js/populars.ts'
import './js/cookie.js'
import './js/research.ts'

import { createApp } from 'vue'

import carousel_products from './vue/carousel_products.vue'

const carousel_populars = createApp(carousel_products,{apiUrl:'/api/getProductByPopular'})
carousel_populars.mount('#carouselPopular')

const carousel_Fy = createApp(carousel_products,{apiUrl: '/api/fyp-function'})
carousel_Fy.mount('#carouselFy')