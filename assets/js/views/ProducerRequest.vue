<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
          :admin="admin"
        />


        <div class="mt-12 ml-12 text-center">
            <h1>Vous êtes producteur ?</h1>
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
            label="NOM Prénom"
            required
            @input="$v.name.$touch()"
            @blur="$v.name.$touch()"
            ></v-text-field>

            <v-textarea
            v-model="description"
            :error-messages="descriptionError"
            :counter="250"
            label="Présentez-vous et vos productions. Attention ! Ceci servira comme présentation de base dans votre profil Producteur"
            required
            outlined
            @input="$v.description.$touch()"
            @blur="$v.description.$touch()"
            ></v-textarea>

            <v-text-field
            v-model="phoneNumber"
            :type="'number'"
            :error-messages="phoneNumberError"
            :counter="35"
            label="Comment pouvons-nous vous joindre ?"
            required
            @input="$v.phoneNumber.$touch()"
            @blur="$v.phoneNumber.$touch()"
            ></v-text-field>

            <v-btn
            class="mr-4"
            @click="submit"
            >
                Envoyer
            </v-btn>
            <router-link :to="{ name: 'home'}">
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
import { required, maxLength, minLength } from 'vuelidate/lib/validators'

  export default {

    components: {
        AppBar,
        ProductCard,
    },
    
    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(35) },
      description: { required, maxLength: maxLength(250) },
      phoneNumber: { required, maxLength: maxLength(10), minLength: minLength(2) },
    },

    data() {
      return{
        nbCommands: 0,
        name: '',
        description: '',
        phoneNumber: '',
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
      phoneNumberError() {
        const errors = []
        if (!this.$v.phoneNumber.$dirty) return errors
        !this.$v.phoneNumber.maxLength && errors.push('Entrez un numéro de téléphone correct')
        !this.$v.phoneNumber.minLength && errors.push('Entrez un numéro de téléphone correct')
        !this.$v.phoneNumber.required && errors.push('Le numéro de téléphone est requis')
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

        var msg = axios.post('/account/producer-request/' + this.name + '/' + this.description+ '/' + this.phoneNumber)
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
        this.phoneNumber = ''
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