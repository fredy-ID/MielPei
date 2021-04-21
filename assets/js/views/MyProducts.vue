<template>
<div>
        <AppBar 
          :nbCommands="nbCommands"
          @update-cart="updateCart"
          :user="user"
          :producer="producer"
          :admin="admin"
        />

        <!-- <div data-controller="hello"></div>
        <router-link to="/home">My Component</router-link> -->
        
        <div class="pa-sm-16">
          <div class="mb-16">
            <div class="mb-10">
                <h2>Introduction</h2>
            </div>
            <div>
              <v-textarea
                v-model="producerDescription"
                :error-messages="descriptionError"
                :counter="250"
                label="Décrivez votre passion !"
                solo
                @keyup="verifyInput()"
                @input="$v.description.$touch()"
                @blur="$v.description.$touch()"
              ></v-textarea>
              <v-btn
                v-if="changes"
                depressed
                color="primary"
                @click="updateProducer"
              >
                Modifier
              </v-btn>
            </div>
          </div>


          <div class="mb-10 d-flex">
            <div class="mr-5">
              <h2>Mes produits</h2>
            </div>
            
            <router-link :to="{ name: 'product-creation'}">
              <v-btn
                color="primary"
                dark
              >
                Ajouter un produit
              </v-btn>
            </router-link>
          </div>
        
        
          <v-row>

            <v-col
              cols=6
              sm="8"
              class="flex-grow-0 flex-shrink-0"
            > 
              <div class="d-flex flex-row flex-wrap">
                <ProductCard
                  v-for="product in this.products"
                  v-bind:key="product.id"
                  :product="product"
                  :action="'mes-produits'"
                  @update-cart="updateCart"
                  @update-state="initialize"
                  :privileges="privileges"
                />
              </div>
            </v-col>

            <v-col
              cols=6
              sm="4"
              class="flex-grow-0 flex-shrink-0"
            >
            
              <div class="mb-10">
                  <h2>Commandes en cours</h2>
                  <p v-if="activeCommands.length === 0">Aucune commande en cours de traitement</p>
              </div>

                <v-card
                  class="mx-auto"
                  tile
                  v-for="command in activeCommands"
                  v-bind:key="command.id"
                >
                  <v-list-item four-line>
                    <v-list-item-content>
                      <v-list-item-title>{{command.product.name}} x{{command.quantity}} ({{ command.product.price * command.quantity }}€)</v-list-item-title>

                      <v-list-item-subtitle class="mt-3">
                        Commandé le : {{ date(command.createdAt) }}
                      </v-list-item-subtitle>
                      <v-list-item-subtitle class="mt-3">
                        <div>
                          Adresse de livraison : {{ command.adress }} <br />
                          Complément d'adresse : {{ command.secAdress }} <br />
                          Code postale : {{ command.postcode }} <br />
                          Région : {{ command.region }} <br />
                          Pays : {{ command.country }} <br />
                        </div>
                      </v-list-item-subtitle>

                      <v-list-item-subtitle class="mt-10">
                        Etat d'avancement : {{ command.etat }}
                        <v-select
                          @change="updateState(command.id)"
                          class="mt-5"
                          :hint="`Choisissez un état`"
                          v-model="select"
                          :value="command.etat"
                          :items="state"
                          solo
                          :disabled="(update === true) ? true : false"
                        ></v-select>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                </v-card>

                <div class="mt-16 mb-10">
                    <h2>Commandes traitées </h2>
                    <p v-if="oldCommands.length === 0">Aucune commande traitée à ce jour</p>
                </div>

                <v-card
                  class="mx-auto"
                  tile
                  v-for="command in oldCommands"
                  v-bind:key="command.id"
                >
                  <v-list-item four-line>
                    <v-list-item-content>
                      <v-list-item-title>{{command.product.name}} x{{command.quantity}} ({{ command.product.price * command.quantity }}€)</v-list-item-title>

                      <v-list-item-subtitle class="mt-3">
                        Commandé le : {{ date(command.createdAt) }}
                      </v-list-item-subtitle>
                      <v-list-item-subtitle class="mt-3">
                        <div>
                          Adresse de livraison : {{ command.adress }} <br />
                          Complément d'adresse : {{ command.secAdress }} <br />
                          Code postale : {{ command.postcode }} <br />
                          Région : {{ command.region }} <br />
                          Pays : {{ command.country }} <br />
                        </div>
                      </v-list-item-subtitle>

                      <v-list-item-subtitle class="mt-10">
                        Etat d'avancement : {{ command.etat }}
                        <v-select
                          @change="updateState(command.id)"
                          class="mt-5"
                          :hint="`Choisissez un état`"
                          v-model="select"
                          :value="command.etat"
                          :items="state"
                          solo
                          :disabled="(update === true) ? true : false"
                        ></v-select>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                </v-card>
            </v-col>
            
          </v-row>
        </div>
        

      <v-snackbar
        v-model="snackbar"
      >
        {{ snackbarText }}

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
import moment from 'moment';

import { validationMixin } from 'vuelidate'
import { required, maxLength } from 'vuelidate/lib/validators'

  export default {
    components: {
        AppBar,
        ProductCard,

    },

    mixins: [validationMixin],

    validations: {
      description: { required, maxLength: maxLength(250) },
    },

    data() {
      return{
        activeCommands: [],
        oldCommands: [],
        totalPrice: 0,
        products: [],
        nbCommands: 0,
        select: "",
        update: false,
        changes: false,
        state: [
          "En attente",
          "En cours d'acheminement",
          "En cours d'envoi",
          "Envoyé",
        ],
        user: null,
        producer: false,
        producerDescription: null,
        initialContent: null,
        admin: false,
        privileges: false,

        snackbarText: '',
        snackbar: false,
      };
    },

    name: "Home",

    computed: {
      descriptionError () {
        const errors = []
        if (!this.$v.description.$dirty) return errors
        !this.$v.description.maxLength && errors.push('La description ne devrait pas exéder les 250 caractères maximum')
      },
    },

    methods: {

      async updateState(commandId) {
        this.update = true;
        const response = await axios.get("/command/"+commandId+"/state/"+this.select);
        this.initialize();

        this.update = false;
      },

      date: function (date) {
        return moment(date).locale("fr").format('Do MMMM YYYY');
      },
      
      async initialize() {
        this.products = [];
        const response = await axios.get("/product/iventaire");

        var commands = response.data.commands;
        console.log(response)
        response.data.products.forEach(product => {
          this.products.push(product)
        });

        this.activeCommands = [];
        this.oldCommands = [];
        commands.forEach(element => {
          if(element.isActive) {
            this.activeCommands.push(element);
          } else {
            this.oldCommands.push(element)
          }
          
          this.totalPrice = this.totalPrice + element.total;
        });

        this.producerDescription = response.data.producer.introduction;
        this.initialContent = this.producerDescription;

        this.updateCart()
      },

      async updateProducer() {
        const response = await axios.get("/producer/update", {
          params: {
            description: this.producerDescription
          }
        }).catch((error) => {
          console.log(error)
        });

        console.log(response.data.d)
        
        if(response.data.msg === "success") {
          this.snackbarText = "Introduction mis à jour";
          this.snackbar = true;
          this.initialContent = this.producerDescription;
        }
        
        this.changes = false;
      },

      async updateCart() {
        const response = await axios.get("/cart/all/commands");
        if(response.data.cart.length > 0) {
          var cart = response.data.cart[0].products;
          this.nbCommands = cart
        }
        this.user = response.data.user;
        this.producer = response.data.producer;
        this.privileges = this.producer;
      },

      verifyInput() {
        if((this.producerDescription === this.initialContent)) {

          this.changes = false;
          
        } else {
          this.changes = true;
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