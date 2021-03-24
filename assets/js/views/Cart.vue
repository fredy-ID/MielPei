
<template>
    <div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="userId"
          :producer="producer"
        />

        <div class="pa-16">
          <div class="mb-10">
              <h2>Récapitulatif de la commande</h2>
          </div>
          <v-data-table
              :headers="headers"
              :items="items"
              class="elevation-1"
              disable-items-per-page
              disable-pagination
          ></v-data-table>

        <div class="mb-10">
              <h2>Facturation</h2>
        </div>
        
        <v-card
          class="mt-16"
          max-width="400"
          tile
        >

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>Total produit TTC: {{ totalPrice }} €</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>Total HT:  {{ totalPrice }} €</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>Taxes: 0 €</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-divider></v-divider>

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title
              class="text-right"
              ><span>Total -------- </span>   {{ totalPrice }} €</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

        </v-card>
        
        <div class="my-10">
          <div>
                <h2>Adresse de livraison</h2>
          </div>
          <div class="text-uppercase">

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="(user['lastName'] && user['first']) !== null">
                  NOM PRENOM : {{ user['lastName'] }} {{ user['firstName'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  NOM PRENOM : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="(user['adress'] || user['secAdress']) !== null">
                  ADRESSE : {{ user['adress'] }} {{ user['secAdress'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  ADRESSE : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="user['region'] !== null">
                  REGION : {{ user['region'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  REGION : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="user['postcode'] !== null">
                  CODE POSTAL : {{ user['postcode'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  CODE POSTAL : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="user['country'] !== null">
                  PAYS : {{ user['country'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  PAYS : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title v-if="user['number'] !== null">
                  TELEPHONE : {{ user['number'] }}
                </v-list-item-title>
                <v-list-item-title v-else>
                  TELEPHONE : 
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            
          </div>

          <router-link to="/compte">
            <v-btn
            color="primary"
            text
          >
            Modifier
          </v-btn>
          </router-link> 
        </div>


        <div class="text-right">
          <router-link to="/payement-success">
            <v-btn
            class="ma-2"
            color="indigo"
          >
            Acheter
          </v-btn>
          </router-link>
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
    data () {
      return {
        headers: [
          {
            text: 'Mon panier',
            align: 'start',
            sortable: false,
            value: 'name',
          },
          { text: 'Produit', value: 'image' },
          { text: 'Description', value: 'description' },
          { text: 'Prix unitaire (€)', value: 'price' },
          { text: 'Quantité', value: 'quantity' },
          { text: 'Total', value: 'total' },
        ],
        items: [],
        totalPrice: 0,
        

        nbCommands: 0,
        userId: null,
        user: [],
        producer: false,
      }
    },

    methods: {
      
      async initialize() {
        const response = await axios.get("/profile/info");
        this.user = response.data.user
      console.log(this.user)
        this.updateCart()
      },

      async updateCart() {
          const response = await axios.get("/cart/all/commands");
          if(response.data.cart.length > 0) {
            var cart = response.data.cart[0].cartProducts;
            this.nbCommands = cart
          }
            this.userId = response.data.user;
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