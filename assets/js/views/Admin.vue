
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
              <h2>Liste des utilisateurs</h2>
          </div>
          <v-data-table
              :headers="headers"
              :items="users"
              class="elevation-1"
              disable-items-per-page
              disable-pagination
          >
            <template v-slot:top>
              <v-toolbar
                flat
              >
                <v-divider
                  class="mx-4"
                  inset
                  vertical
                ></v-divider>
                <v-spacer></v-spacer>
                <v-dialog
                  v-model="dialog"
                  max-width="500px"
                  
                >
                  <!-- <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      dark
                      class="mb-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      Modifier l'email utilisateur
                    </v-btn>
                  </template> -->
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>

                    <v-card-text>
                      <v-container>
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-text-field
                              v-model="editedItem.email"
                              label="Email"
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                          <div class="text-center">
                            <v-container fluid>
                              <v-switch
                                @change="updateRoles(editedItem.id, editedItem.role)"
                                v-model="switch1"
                                color="primary"
                                label="Producteur"
                                :loading="switchLoading"
                                :inset="true"
                              ></v-switch>
                              <!-- <v-switch
                                v-model="curentRole"
                                color="primary"
                                label="Jacob"
                                value="Jacob"
                              ></v-switch> -->
                            </v-container>
                          </div>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="close"
                      >
                        Annuler
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="save"
                      >
                        Sauvegarder
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-dialog v-model="dialogDelete" max-width="500px">
                  <v-card>
                    <v-card-title class="headline">Voulez-vous réellement désactiver cet utilisateur ?</v-card-title>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                      <v-btn color="blue darken-1" text @click="desactivate(editedItem.id)">OK</v-btn>
                      <v-spacer></v-spacer>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                @click="editItem(item)"
              >
                mdi-pencil
              </v-icon>
              <v-icon
                small
                @click="deleteItem(item)"
              >
                mdi-delete
              </v-icon>
            </template>
            <template v-slot:no-data>
              <v-btn
                color="primary"
                @click="initialize"
              >
                Réinitialiser
              </v-btn>
            </template>

          </v-data-table>

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
      dialog: false,
      dialogDelete: false,

      // To verify
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },

      switch1: false,
      isProducer: "",
      switchLoading: false,

      // Original
        headers: [
          { text: 'ID', value: 'id' },
          {
            text: 'Nom / Prénom',
            align: 'start',
            sortable: false,
            value: 'name',
          },
          { text: 'Email', value: 'email' },
          { text: 'Email vérifié', value: 'verifiedEmail' },
          { text: 'Numéro de téléphone', value: 'number' },
          { text: 'Rôle', value: 'role' },
          { text: 'Compte Actif', value: 'state' },
          { text: 'Actions', value: 'actions', sortable: false },
        ],
        items: [],
        totalPrice: 0,
        

        nbCommands: 0,
        userId: null,
        user: [],
        users: [],
        producer: false,
      }
    },

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },

    created () {
      this.initialize()
    },

    methods: {
      
      async initialize() {
        const response = await axios.get("/users");
        var users = response.data.users
        console.log(users) 

        this.users = [];
        users.forEach(element => {
          var user = {
              name: element.lastName + ' ' + element.firstName,
              email: element.email,
              verifiedEmail: (element.isVerified === true ? 'Oui' : 'Non'),
              state: (element.isActive === true ? 'Oui' : 'Non'),
              number: element.number,
              role: element.roles,
              id: element.id,
          }
          this.users.push(user);
        })
        console.log(this.users)

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
    


      async updateRoles($userId, roles) {
        console.log(roles)
        var isProducer = false;
        var isAdmin = false;

        roles.forEach(role => {
          if(role === "ROLE_PRODUCER") {
            isProducer = true
          }
        })

        roles.forEach(role => {
          if(role === "ROLE_ADMIN") {
            isAdmin = true
          }
        })

        if(!isAdmin && isProducer){
          const response = await axios.get(`/users/${$userId}/role/${0}`);
          console.log(response)

        } else if (!isAdmin && !isProducer) {
          const response = await axios.get(`/users/${$userId}/role/${1}`);
        }

        this.initialize();

      },


      editItem (item) {
        this.switch1 = false
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)

        this.editedItem.role.forEach(role => {
          console.log(role == "ROLE_PRODUCER")
          if(role === "ROLE_PRODUCER") {
            this.switch1 = true
          }
        })
        console.log("1 - " + this.switch1)

        this.dialog = true
      },

      deleteItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      deleteItemConfirm () {
        this.desserts.splice(this.editedIndex, 1)
        this.closeDelete()
      },

      async desactivate($userId) {
        await axios.get(`/users/${$userId}/state-change`);
        this.initialize();
        this.closeDelete()
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      },
    },

    created() {
      this.initialize()
    }
  }
</script>