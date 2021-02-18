<template>
  <v-card
    :loading="loading"
    class="mx-auto my-12"
    max-width="374"
  >
    <template slot="progress">
      <v-progress-linear
        color="deep-purple"
        height="10"
        indeterminate
      ></v-progress-linear>
    </template>

    <v-img
      height="250"
      src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
    ></v-img>

    <v-card-title>{{product['name']}}</v-card-title>

    <v-card-text @click="test(product)" class="d-flex flex-row">
      <div class="col-10">{{product['description']}}</div>
      <div class="col-2">{{product['price']}} €</div>
      
    </v-card-text>

    <v-card-actions v-if="action == 'achat'">
      <v-btn
        color="deep-purple lighten-2"
        text
        @click="reserve(product['id'])"
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
</template>

<script>
const axios = require('axios');

  export default {
    data: () => ({
      loading: false,
      selection: 1,
      valid: false,
      quantity: 1,
      rules: [
        v => !!v || 'la quantité est obligatoire',
        v => /^[1-9]{1,3}$/.test(v) || 'Entrez un nombre valide',
      ],
    }),

    props: ["product", "action"],

    methods: {
      reserve () {
        this.loading = true

        setTimeout(() => (this.loading = false), 2000)
      },
      async reserve(productId) {
            this.loading = true;
            // /new/product/{id}/quantity/{quantity}
            const response = await axios.get("/cart/new/product/" + productId + '/quantity/' + this.quantity).then(() => {
                this.$emit('update-cart')
                this.loading = false
            } );


      },
      test(a) {
          console.log(a)
      }
    },
  }
</script>