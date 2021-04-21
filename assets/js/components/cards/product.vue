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

    <v-card-actions>
      <div v-if="user === null">
        <a href="/login"
            color="deep-purple lighten-2"
            class="v-btn v-btn--text theme--light v-size--default deep-purple--text text--lighten-2"
          >
            Ajouter au panier
        </a>
      </div>
      
      <div v-else>
        <v-btn
          color="deep-purple lighten-2"
          text
          @click="reserve(product['id'])"
          v-if="action == 'achat'"
        >
          Ajouter au panier
        </v-btn>
      </div>

      <router-link :to="{ name: 'product-info', params: { id: product['id'] }}">
        <v-btn
          color="deep-purple lighten-2"
          text
          outlined
        >
          Consulter
        </v-btn>
      </router-link>

      <div v-if="privileges">
        <router-link :to="{ name: 'product-edit', params: { id: product['id'] }}">
          <v-btn
            color="deep-purple lighten-2"
            text
            outlined
          >
            Modifier
          </v-btn>
        </router-link>
        <div>
          <v-btn
          v-if="product['isActive'] === true"
            color="error"
            outlined
            text
            @click="desactivate(product['id'])"
          >
            Désactiver
          </v-btn>
          <v-btn
            v-else
            color="error"
            outlined
            text
            @click="activate(product['id'])"
          >
            Activer
          </v-btn>
        </div>
        <div>
          <v-btn
            color="error"
            text
            outlined
            @click="remove(product['id'])"
          >
            Supprimer
          </v-btn>
        </div>
      </div>

      <v-form v-model="valid" v-if="action == 'achat'">
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

    props: ["product", "action", "user", "privileges"],

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

      async desactivate(productId) {
        this.loading = true;
        // /new/product/{id}/quantity/{quantity}
        const response = await axios.get("/product/desactivate/" + productId).then(() => {
            this.$emit('update-state')
            this.loading = false
        });
      },

      async activate(productId) {
        this.loading = true;
        // /new/product/{id}/quantity/{quantity}
        const response = await axios.get("/product/activate/" + productId).then(() => {
            this.$emit('update-state')
            this.loading = false
        });
      },

      async remove(productId) {
        this.loading = true;
        // /new/product/{id}/quantity/{quantity}
        console.log(productId)
        const response = await axios.get("/product/delete/"+productId).then(() => {
            this.$emit('update-state')
            this.loading = false
        });
        console.log(response)
      },

      test(a) {
          console.log(a)
      }
    },
  }
</script>