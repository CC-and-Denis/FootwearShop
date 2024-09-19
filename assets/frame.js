import './styles/frame.css';

import './js/frame.ts';
import './js/user.ts';
import './js/product_cards_loaders.ts'

import { createApp } from 'vue';

// Import the existing carousel component
import carousel_products from './vue/carousel_products.vue';
import products_page from './vue/scroll_products_loader.vue';
// Import the new filter component
import filter_options from './vue/research.vue'; // <-- New component import

// Mount carousel for popular products
const carousel_populars = createApp(carousel_products, { apiUrl: '/api/getProductByPopular' });
carousel_populars.mount('#carouselPopular');

const products_pageApp = createApp(products_page);
products_pageApp.mount('#scrollable-grid-products');

// Mount carousel for FY products
const carousel_Fy = createApp(carousel_products, { apiUrl: '/api/fyp-function' });
carousel_Fy.mount('#carouselFy');

// Mount the filter options component
const filterApp = createApp(filter_options);
filterApp.mount('#filterOptions'); // <-- Mount the filter component
