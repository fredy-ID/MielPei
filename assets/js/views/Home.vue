<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
          :admin="admin"
        />

        <v-sheet
          class="mx-auto ma-16"
          elevation="8"
          max-width="800"
        >
          <v-slide-group
            v-model="model"
            class="pa-4"
            show-arrows
          >
            <v-slide-item
              v-for="n in 4"
              :key="n"
              v-slot="{ active, toggle }"
            >
              <v-card
                :color="active ? 'primary' : 'grey lighten-1'"
                class="ma-4"
                height="200"
                width="100"
                @click="toggle"
              >
                <v-row
                  class="fill-height"
                  align="center"
                  justify="center"
                >
                  <v-scale-transition>
                    <v-icon
                      v-if="active"
                      color="white"
                      size="48"
                      v-text="'mdi-close-circle-outline'"
                    ></v-icon>
                  </v-scale-transition>
                </v-row>
              </v-card>
            </v-slide-item>
          </v-slide-group>

          <v-expand-transition>
            <v-sheet
              v-if="model != null"
              height="200"
              tile
            >
              <v-row
                class="fill-height"
                align="center"
                justify="center"
              >
                <h3 class="title">
                  Selected {{ model }}
                </h3>
              </v-row>
            </v-sheet>
          </v-expand-transition>
        </v-sheet>

        <!-- <div data-controller="hello"></div>
        <router-link to="/home">My Component</router-link> -->

        <div class="d-flex flex-row flex-wrap">
          <ProductCard
            v-for="product in this.products"
            v-bind:key="product.id"
            :product="product"
            :action="'achat'"
            :user="user"
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
        user: null,
        producer: false,
        admin: false,
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
          console.log(response.data)
          if(response.data.cart.length > 0) {
            var cart = response.data.cart[0].cartProducts;
            this.nbCommands = cart
          }
            this.user = response.data.user;
            this.producer = response.data.producer;
      },
      

    },

    created() {
      this.initialize()
    }
  }
</script>

<style scoped>

</style>