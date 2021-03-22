<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
        />

        <div class="d-flex justify-center">
          <div class="product d-flex flex-column">

            <v-card
              class="mx-auto my-12 pa-5"
              max-width="374"
              min-height="374"
            >
              <div class="mb-10">
                <h2>Informations personnelles</h2>
              </div>
              <p>Producteur : <span class="text-light">Non</span></p>
              <p>Courriel : <span class="text-light">freddaou@hotmail.fr</span></p>
              <p>Email vérifié : <span class="text-light">Non</span></p>
              <p>Producteur : <span class="text-light">Non</span></p>
              <router-link :to="{ name: 'producer-request'}">
                <v-btn block class="mt-10">
                  Devenir producteur
                </v-btn>
              </router-link>
            </v-card>
          </div>

          <div class="product-description my-12 mx-12 d-flex flex-column"
          >
            <form>

              <v-text-field
              v-model="lastName"
              :error-messages="lastNameErrors"
              :counter="35"
              label="Nom"
              required
              @change="verifyInput()"
              @input="$v.lastName.$touch()"
              @blur="$v.lastName.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="firstName"
              :error-messages="lastNameErrors"
              :counter="35"
              label="Prénom"
              required
              @change="verifyInput()"
              @input="$v.firstName.$touch()"
              @blur="$v.firstName.$touch()"
              ></v-text-field>

              <v-btn
              v-if="changes"
              class="mr-4"
              @click="submit"
              >
                  Sauvegarder
              </v-btn>
          </form>

          <div class="mt-10">
            <h2>Toutes mes commandes</h2>
          </div>

            <v-expansion-panels class="mt-10">
              <v-expansion-panel
                v-for="(item,i) in 5"
                :key="i"
              >
                <v-expansion-panel-header>
                  Nom du produit
                </v-expansion-panel-header>
                <v-expansion-panel-content>
                  <div class="w-100 d-flex">
                    <div class="col-5">
                      <v-card
                        class="mx-auto my-5"
                        max-width="150"
                        max-height="150"
                      >
                        <v-img
                          height="150"
                          src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
                        ></v-img>
                      </v-card>
                    </div>
                    <div class="col-7">
                      <p class="my-5">ddd</p>
                    </div>
                    
                  </div>
                  
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>

          </div>

        </div>

          
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
      firstName: { required, maxLength: maxLength(35), minLength: minLength(2) },
      lastName: { required, maxLength: maxLength(35), minLength: minLength(2) },
    },

    data() {
      return{
        nbCommands: 0,
        firstName: '',
        lastName: '',
        product: null,
        valid: false,
        changes: false,
        user: null,
        producer: false,
        snackbar: false,
        text: ``,
      };
      
    },

    props: ["action"],

    name: "pInfo",

    computed: {
      firstNameErrors () {
        const errors = []
        if (!this.$v.firstName.$dirty) return errors
        !this.$v.firstName.maxLength && errors.push('Ce champ ne devrait pas exéder 35 caractères maximum')
        !this.$v.firstName.minLength && errors.push('Vous devez renseigner minimum 2 caractères')
        !this.$v.firstName.required && errors.push('Champ requis')
        return errors
      },
      lastNameErrors () {
        const errors = []
        if (!this.$v.lastName.$dirty) return errors
        !this.$v.lastName.maxLength && errors.push('Ce champ ne devrait pas exéder 35 caractères maximum')
        !this.$v.lastName.minLength && errors.push('Vous devez renseigner minimum 2 caractères')
        !this.$v.lastName.required && errors.push('Champ requis')
        return errors
      },
    },

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
          this.firstName = response.data.user_firstName;
          this.lastName = response.data.user_lastName;
        
      },

      async reserve(productId) {
        this.loading = true;
        // /new/product/{id}/quantity/{quantity}
        const response = await axios.get("/cart/new/product/" + productId + '/quantity/' + this.quantity).then(() => {
            this.updateCart()
        } );
      },

      submit () {
        this.$v.$touch()

        console.log(this.lastName, this.firstName);

        var msg = axios.post('/modify-profile/' + this.firstName + '/' + this.lastName)
        .then(function (response) {
            console.log(response.data);

            return response.data.msg;
        })
        .catch(function (error) {
            console.log("Une erreur est survenue", error);
            return 'erreur'
        });

        this.text = msg;
        this.changes = true;
        console.log(msg)
        this.snackbar = true;

      },

      clear () {
        this.$v.$reset()
        this.name = ''
        this.description = ''
      },
      verifyInput() {

        if((this.firstName != '' && this.firstName != null) && (this.lastName != '' && this.lastName != null)) {
          this.changes = true;
        } else {
          this.changes = false;
        }

      }
    },

    created() {
      this.initialize()
    }
  }
</script>

<style scoped> 

</style>