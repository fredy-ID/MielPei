
<template>
    <div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
        />

        <div class="mb-10">
            <h2>Récapitulatif de la commande</h2>
        </div>
        <v-data-table
            :headers="headers"
            :items="products"
            :items-per-page="10"
            class="elevation-1"
        ></v-data-table>
  </div>

</template>

<script>
  export default {
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
        products: [
          {
            name: 'Nom du produit',
            image: 'Image du produit',
            description: 'Description du produit',
            price: 'prix unitaire du produit',
            quantity: 'quantité voulu',
            total: 'total',
          },
          
        ],
      }
    },

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
          if(response.data.cart.length > 0) {
            var cart = response.data.cart[0].products;
            this.nbCommands = cart
          }
            this.user = response.data.user;
            this.producer = response.data.producer;
      },
    
    },
  }
</script>