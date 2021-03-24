<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
        />


        <div class="mt-12 ml-12 text-center">
            <h1>Ajouter un produit</h1>
        </div>

        <v-card
            class="mx-auto my-12 pa-5"
            max-width="600"
        >

        <form>

            <v-text-field
            v-model="name"
            :error-messages="nameErrors"
            :counter="35"
            label="Nom du produit"
            required
            @input="$v.name.$touch()"
            @blur="$v.name.$touch()"
            ></v-text-field>

            <v-textarea
            v-model="description"
            :error-messages="descriptionError"
            :counter="250"
            label="Description"
            required
            outlined
            @input="$v.description.$touch()"
            @blur="$v.description.$touch()"
            ></v-textarea>

            <v-text-field
            v-model="price"
            type="number"
            :error-messages="priceErrors"
            label="Prix"
            required
            @input="$v.price.$touch()"
            @blur="$v.price.$touch()"
            prepend-icon="mdi-currency-eur"
            ></v-text-field>

            <v-file-input
                :rules="rules"
                accept="image/png, image/jpeg"
                placeholder="Sélectionnez une photo"
                prepend-icon="mdi-camera"
                label="Photo"
            ></v-file-input>

            <v-btn
            class="mr-4"
            @click="submit"
            >
                Sauvegarder
            </v-btn>
            <router-link :to="{ name: 'my-products'}">
              <v-btn @click="clear">
                Annuler
            </v-btn>
            </router-link>
        </form>
        </v-card>

        <!-- <v-btn
        dark
        @click="snackbar = true"
        >
        Open Snackbar
        </v-btn> -->

        <v-snackbar
            v-model="snackbar"
            >
            {{ text }}

            <template v-slot:action="{ attrs }">
                <v-btn
                color="pink"
                text
                v-bind="attrs"
                @click="snackbar = false"
                >
                Close
                </v-btn>
            </template>
        </v-snackbar>
        
    </div>
</template>

<script>
const axios = require('axios');
import AppBar from '../stores/header';
import ProductCard from '../components/cards/product';

import { validationMixin } from 'vuelidate'
import { required, maxLength } from 'vuelidate/lib/validators'

  export default {

    components: {
        AppBar,
        ProductCard,
    },
    
    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(35) },
      description: { required, maxLength: maxLength(250) },
      price: { required },
    },

    data() {
      return{
        nbCommands: 0,
        name: '',
        description: '',
        price: 0,
        rules: [
            value => !value || value.size < 5000000 || "L'image ne doit pas éxéder 5MB!",
        ],
        snackbar: false,
        text: ``,
        user: null,
        producer: false,
      };
    },

    props: ["action"],

    name: "pCreate",

    computed: {
      descriptionError () {
        const errors = []
        if (!this.$v.description.$dirty) return errors
        !this.$v.description.maxLength && errors.push('La description ne devrait pas exéder les 250 caractères maximum')
      },
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push('le nom ne devrait pas exéder les 15 caractères maximum')
        !this.$v.name.required && errors.push('Le nom est requis')
        return errors
      },
      priceErrors () {
        const errors = []
        if (!this.$v.price.$dirty) return errors
        !this.$v.price.required && errors.push('Vous devez indiquer le prix pour le produit')
        return errors
      },
      
    },

    methods: {
      
      async initialize() {
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

      submit () {
        this.$v.$touch()

        var msg = axios.post('/product/new/' + this.name + '/' + this.description+ '/' + this.price)
        .then(function (response) {
            console.log(response.data);

            return response.data.msg;
        })
        .catch(function (error) {
            console.log("Une erreur est survenue", error);
            return 'erreur'
        });

        this.text = msg;
        console.log(msg)
        this.snackbar = true;


        this.name = ''
        this.description = ''
      },
      clear () {
        this.$v.$reset()
        this.name = ''
        this.description = ''
      },

    },

    created() {
      this.initialize()
    }
  }
</script>

<style scoped> 

</style>