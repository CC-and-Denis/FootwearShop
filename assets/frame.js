import './styles/frame.css';

import './js/frame.ts';
import './js/user.ts';
import './js/product_cards_loaders.ts'

import { createApp } from 'vue';

// Import the existing carousel component
import carousel_products from './vue/carousel_products.vue';
import products_page from './vue/scroll_products_loader.vue';
// Import the new filter component
import filter_options from './vue/research.vue';
import chat from './vue/chat.vue';

// Mount carousel for popular products
const carousel_populars = createApp(carousel_products, { apiUrl: '/api/getProductByPopular', maxProducts: 4});
carousel_populars.mount('#carouselPopular');

const servizioDomande = createApp(chat);
servizioDomande.mount('#chat');

// Mount carousel for FY products
const carousel_Fy = createApp(carousel_products, { apiUrl: '/api/getProductsForYou', maxProducts: 4});
carousel_Fy.mount('#carouselFy');

// Mount carousel for FY products
const carousel_similar = createApp(carousel_products, { apiUrl: '/api/getSimilarProducts', maxProducts: 2});
carousel_similar.mount('#carouselSimilar');
const products_pageApp = createApp(products_page);
products_pageApp.mount('#scrollable-products');

// Mount the filter options component
const filterApp = createApp(filter_options);
filterApp.mount('#searchMountPoint'); // <-- Mount the filter component
