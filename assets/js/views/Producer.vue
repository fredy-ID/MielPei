<template>

<div>

    <AppBar 
        :nbCommands="nbCommands"
        @update-cart="updateCart"
        :user="user"
        :producer="producer"
        :admin="admin"
    />

  <v-card
    class="mx-auto"
    max-width="344"
  >
    <v-img
      src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
      height="200px"
    ></v-img>

    <v-card-title>
      Top western road trips
    </v-card-title>

    <v-card-subtitle>
      1,000 miles of wonder
    </v-card-subtitle>

    <v-card-actions>
      <v-btn
        color="orange lighten-2"
        text
      >
        Explore
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn
        icon
        @click="show = !show"
      >
        <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
      </v-btn>
    </v-card-actions>

    <v-expand-transition>
      <div v-show="show">
        <v-divider></v-divider>

        <v-card-text>
          I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape.
        </v-card-text>
      </div>
    </v-expand-transition>
  </v-card>
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
        show: false,
        nbCommands: 0,
        product: null,
        selection: 1,
        valid: false,
        quantity: 1,
        rules: [
          v => !!v || 'la quantité est obligatoire',
          v => /^[1-9]{1,3}$/.test(v) || 'Entrez un nombre valide',
        ],
        user: null,
        producer: false,
        admin: false,
      };
      
    },

    props: ["action"],

    name: "pInfo",

    methods: {
      
      async initialize() {
        const productId = this.$router.currentRoute.params.id;
        const response = await axios.get("/product/" + productId);
        
        this.updateCart()
        this.product = response.data.product;

        console.log(this.product)
      },

      async updateCart() {
        const response = await axios.get("/cart/all/commands");
        if(response.data.cart.length > 0) {
          var cart = response.data.cart[0].products;
          this.nbCommands = cart
        }
          this.user = response.data.user;
          this.producer = response.data.producer;
        
      },

      async reserve(productId) {
        this.loading = true;
        // /new/product/{id}/quantity/{quantity}
        const response = await axios.get("/cart/new/product/" + productId + '/quantity/' + this.quantity).then(() => {
            this.updateCart()
        } );
      },

    },

    created() {
      this.initialize()
    }
  }
</script>

<style>

</style>