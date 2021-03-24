<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
        />

        <div class="d-flex justify-center">
          <div class="col-4 product d-flex flex-column">

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

          <div class="product-description col-6 my-12 mx-12 d-flex flex-column"
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

              <v-text-field
              v-model="phoneNumber"
              :error-messages="phoneNumberError"
              :counter="10"
              label="Numéro de téléphone"
              required
              @change="verifyInput()"
              @input="$v.phoneNumber.$touch()"
              @blur="$v.phoneNumber.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="adress"
              :error-messages="adressError"
              :counter="200"
              label="Adresse"
              required
              @change="verifyInput()"
              @input="$v.adress.$touch()"
              @blur="$v.adress.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="secAdress"
              :error-messages="secAdressError"
              :counter="200"
              label="Adresse secondaire"
              required
              @change="verifyInput()"
              @input="$v.secAdress.$touch()"
              @blur="$v.secAdress.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="postcode"
              :error-messages="postcodeError"
              :counter="5"
              label="Code postale"
              required
              @change="verifyInput()"
              @input="$v.postcode.$touch()"
              @blur="$v.postcode.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="region"
              :error-messages="regionError"
              :counter="35"
              label="Région"
              required
              @change="verifyInput()"
              @input="$v.region.$touch()"
              @blur="$v.region.$touch()"
              ></v-text-field>

              <v-text-field
              v-model="country"
              :error-messages="countryError"
              :counter="35"
              label="Pays"
              required
              @change="verifyInput()"
              @input="$v.country.$touch()"
              @blur="$v.country.$touch()"
              ></v-text-field>

              <v-btn
              v-if="changes"
              class="mr-4"
              @click="submit"
              >
                  Sauvegarder
              </v-btn>
          </form>

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
      phoneNumber: { required, maxLength: maxLength(10), minLength: minLength(10) },
      adress: { required, maxLength: maxLength(200), minLength: minLength(5) },
      secAdress: { required, maxLength: maxLength(200), minLength: minLength(5) },
      postcode: { required, maxLength: maxLength(5), minLength: minLength(5) },
      region: { required, maxLength: maxLength(35), minLength: minLength(5) },
      country: { required, maxLength: maxLength(35), minLength: minLength(5) },
    },

    data() {
      return{
        nbCommands: 0,
        firstName: '',
        lastName: '',
        phoneNumber: '',
        adress: '',
        secAdress: '',
        postcode: '',
        region: '',
        country: '',
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
      phoneNumberError () {
        const errors = []
        if (!this.$v.phoneNumber.$dirty) return errors
        !this.$v.phoneNumber.maxLength && errors.push('Numéro invalide')
        !this.$v.phoneNumber.minLength && errors.push('Numéro invalide')
        !this.$v.phoneNumber.required && errors.push('Champ requis')
        return errors
      },
      adressError () {
        const errors = []
        if (!this.$v.adress.$dirty) return errors
        !this.$v.adress.minLength && errors.push('Adresse trop court')
        !this.$v.adress.maxLength && errors.push('erreur')
        !this.$v.adress.required && errors.push('Champ requis')
        return errors
      },
      secAdressError () {
        const errors = []
        if (!this.$v.secAdress.$dirty) return errors
        !this.$v.secAdress.minLength && errors.push('Adresse trop court')
        !this.$v.secAdress.maxLength && errors.push('erreur')
        !this.$v.secAdress.required && errors.push('Champ requis')
        return errors
      },
      postcodeError () {
        const errors = []
        if (!this.$v.postcode.$dirty) return errors
        !this.$v.postcode.minLength && errors.push('Code postal invalide')
        !this.$v.postcode.maxLength && errors.push('Code postal invalide')
        !this.$v.postcode.required && errors.push('Champ requis')
        return errors
      },
      regionError () {
        const errors = []
        if (!this.$v.region.$dirty) return errors
        !this.$v.region.minLength && errors.push('erreur')
        !this.$v.region.maxLength && errors.push('erreur')
        !this.$v.region.required && errors.push('Champ requis')
        return errors
      },
      countryError () {
        const errors = []
        if (!this.$v.country.$dirty) return errors
        !this.$v.country.minLength && errors.push('erreur')
        !this.$v.country.maxLength && errors.push('erreur')
        !this.$v.country.required && errors.push('Champ requis')
        return errors
      },
    },

    methods: {

      async initialize() {
        const productId = this.$router.currentRoute.params.id;
        const response = await axios.get("/product/" + productId);
        
        this.updateCart()
        this.product = response.data.product;
      },

      async updateCart() {
        const response = await axios.get("/cart/all/commands");
        if(response.data.cart.length > 0) {
          var cart = response.data.cart[0].cartProducts;
          this.nbCommands = cart
        }
          this.user = response.data.user;
          this.producer = response.data.producer;

          console.log(response)

          this.firstName = response.data.user_firstName;
          this.lastName = response.data.user_lastName;
          this.adress = response.data.user_adress;
          this.secAdress = response.data.user_secAdress;
          this.postcode = response.data.user_postcode;
          this.region = response.data.user_region;
          this.country = response.data.user_country;
          this.phoneNumber = response.data.user_phoneNumber;

          this.password = response.data.user_password;
          this.plainPassword = response.data.user_plainPassword;
        
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

        var msg = axios.post('/modify-profile/' 
        + this.firstName + '/' 
        + this.lastName + '/'
        + this.adress + '/'
        + this.secAdress + '/'
        + this.postcode + '/'
        + this.region + '/'
        + this.country + '/'
        + this.phoneNumber
        )
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

          this.firstName = '';
          this.lastName = '';
          this.adress = '';
          this.secAdress = '';
          this.postcode = '';
          this.region = '';
          this.country = '';
          this.phoneNumber = '';

      },
      verifyInput() {

        if((this.firstName != '' && this.firstName != null) && (this.lastName != '' && this.lastName != null)) {

          if((this.plainPassword === null && this.password === null) || (this.plainPassword !== null && this.plainPassword === this.password)) {
            this.changes = true;
          }

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