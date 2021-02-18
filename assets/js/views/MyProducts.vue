<template>
<div>
        <AppBar />

        <!-- <div data-controller="hello"></div>
        <router-link to="/home">My Component</router-link> -->

        <div class="d-flex flex-row flex-wrap">
          <ProductCard
            v-for="product in this.products"
            v-bind:key="product.id"
           />
        </div>
        
    </div>
</template>

<script>
const axios = require('axios');
import AppBar from '../stores/header';
import ProductCard from '../components/cards/product';

  export default {
    components: {
        AppBar,
        ProductCard,
    },
    data() {
      return{
        products: [],
      };
      
    },

    name: "Home",

    methods: {
      
      async initialize() {
        this.products = [];
        const response = await axios.get("/product/my-products");
        
        response.data.products.forEach(product => {
          this.products.push(product)
        });
      },

    },

    created() {
      this.initialize()
    }
  }
</script>

<style scoped> 

</style>