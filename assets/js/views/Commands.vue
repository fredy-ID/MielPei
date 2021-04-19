
<template>
    <div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="userId"
          :producer="producer"
          :admin="admin"
        />

        <div class="pa-16">
          <div class="mb-10">
              <h2>Commandes en cours</h2>
          </div>
          <v-data-table
              :headers="headers"
              :items="activeCommands"
              class="elevation-1 mb-10"
              disable-items-per-page
              disable-pagination
          ></v-data-table>

          <div class="mb-10">
              <h2>Historique des commandes</h2>
          </div>
          <v-data-table
              :headers="headers"
              :items="oldCommands"
              class="elevation-1"
              disable-items-per-page
              disable-pagination
          ></v-data-table>
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
          { text: '', value: 'image' },
          { text: 'Facture', value: 'invoice' },
          { text: 'Etat de la commande', value: 'state' },
          { text: 'Quantité', value: 'quantity' },
          { text: 'Total', value: 'total' },
        ],
        activeCommands: [],
        oldCommands: [],
        totalPrice: 0,
        

        nbCommands: 0,
        userId: null,
        user: [],
        producer: false,
        admin: false,
      }
    },

    methods: {
      
      async initialize() {
          const response = await axios.get("/profile/info");
          this.user = response.data.user;

          const result = await axios.get("/command/user/" + this.user.id);
          var commands = result.data.commands;

          this.activeCommands = [];
          this.oldCommands = [];
          commands.forEach(element => {
            
            if(element.isActive === true) {
                var command = {
                  name: element.product.name,
                  image: 'image',
                  invoice: element.invoice,
                  state: element.etat,
                  quantity: element.quantity,
                  total: (element.product.price * element.quantity),
              }
              this.activeCommands.push(command);
            } else {
               var command = {
                  name: element.product.name,
                  image: 'image',
                  invoice: element.invoice,
                  state: element.etat,
                  quantity: element.quantity,
                  total: (element.product.price * element.quantity),
              }
              this.oldCommands.push(command);
            }
            this.totalPrice = this.totalPrice + command.total;
          });
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

        if(this.nbCommands !== 0) {
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
          }
      },
    
    },

    created() {
      this.initialize()
    }
  }
</script>