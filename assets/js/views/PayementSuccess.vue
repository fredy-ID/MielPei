<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
          :admin="admin"
        />

        <div class="pa-16">
          <div class="mb-10">
              <h2>Commande reçu</h2>
          </div>

            <div>
                <v-card
                class="mx-auto my-12 pa-5"
                max-width="374"
                min-height="374"
                >
                    <div class="mb-10">
                        <h2>Merci de nous avoir choisi</h2>
                    </div>
                    <p>Nombre d'articles : {{ nbArticles }} </p>
                    <p>Montant : {{ total }} </p>
                    <p>Etat : En attente</p>
                    <p>Facture : <a :href="this.file_path" target="_blank" rel="facture">Consulter</a></p>
                    <router-link :to="{ name: 'home'}">
                        <v-btn block class="mt-10">
                            Continuer mon shopping
                        </v-btn>
                    </router-link>
                </v-card>
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
        products: [],
        nbCommands: 0,
        user: null,
        producer: false,
        admin: false,
        file_path: '',
        total : 0,
        nbArticles: 0,

        items: [],
        totalPrice: 0,
      };
    },

    name: "Home",

    methods: {
      
      async initialize() {
        this.updateCart()
      },

      async updateCart() {
          const command = await axios.get("/command/new");
          console.log(command.data.file)
          this.file_path = command.data.file;
          this.total = command.data.montant;
          this.nbArticles = command.data.nbArticles;

          const response = await axios.get("/cart/all/commands");
          if(response.data.cart.length > 0) {
            var cart = response.data.cart[0].cartProducts;
            this.nbCommands = cart
          }
            this.user = response.data.user;
            this.producer = response.data.producer;
            
            this.items = [];
            this.nbCommands.forEach(element => {
              var item = {
                name: element.product.name,
                image: 'image',
                description: element.product.description,
                price: element.product.price,
                quantity: element.quantity,
                total: (element.product.price * element.quantity),
              }
              this.items.push(item);
              this.totalPrice = this.totalPrice + item.total;
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