<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
          :admin="admin"
        />

        <div class="d-flex justify-center">
          <div class="product d-flex flex-column">

            <v-card
              class="mx-auto my-12"
              max-width="374"
            >

              <v-img
                height="250"
                src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
              ></v-img>
            </v-card>

            <div class="mt-12">
              <h2>Producteur</h2>


            </div>


          </div>
          <div class="product-description my-12 mx-12 d-flex flex-column" v-if="product !== null">
            <div class="">
              <h2>{{product.name}} - {{product.price}} €</h2>
              <p>{{product.description}}</p>
            </div>
            

            <div class="commands" v-if="action == 'achat'">
              <v-card
              class="my-12 px-10 py-10"
              max-width="374"
            >
            <h2>Passer commande</h2>
              <v-card-actions>
                <v-btn
                  color="deep-purple lighten-2"
                  text
                  @click="reserve(product.id)"
                >
                  Ajouter au panier
                </v-btn>
                <v-form v-model="valid">
                  <v-container>
                      <v-text-field
                          v-model="quantity"
                          :rules="rules"
                          label="Quantité"
                          required
                      ></v-text-field>
                  </v-container>
                </v-form>
              </v-card-actions>
            </v-card>
            </div>

          </div>
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

<style scoped> 

</style>