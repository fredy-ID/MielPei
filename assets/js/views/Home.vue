<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
        />

        <!-- <div data-controller="hello"></div>
        <router-link to="/home">My Component</router-link> -->

        <div class="d-flex flex-row flex-wrap">
          <ProductCard
            v-for="product in this.products"
            v-bind:key="product.id"
            :product="product"
            :action="'achat'"
            @update-cart="updateCart"
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
        nbCommands: 0,
      };
      
    },

    name: "Home",

    methods: {
      
      async initialize() {
        this.products = [];
        const response = await axios.get("/product/");

        response.data.products.forEach(product => {
          this.products.push(product)
        });
        this.updateCart()
      },

      async updateCart() {
          const response = await axios.get("/cart/all/commands");
          console.log(response)
          var cart = response.data.cart[0].products;
          console.log(cart)
            this.nbCommands = cart
          console.log(this.nbCommands)
      },

    },

    created() {
      this.initialize()
    }
  }
</script>

<style scoped>

</style>