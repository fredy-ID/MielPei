
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
                        :disabled="!btnActive"
                        text
                        @click="modifyUserRole"
                      >
                        Sauvegarder
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-dialog v-model="dialogDelete" max-width="500px">
                  <v-card>
                    <v-card-title class="headline">{{ (editedItem.state === 'Oui') ? 'Désactiver' : 'Activer' }} cet utilisateur ?</v-card-title>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                      <v-btn color="blue darken-1" :disabled="!btnActive" text @click="desactivate(editedItem.id)">OK</v-btn>
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

          <div class="mt-16">
            <div class="mb-10">
                <h2>Requêtes Producteurs</h2>
            </div>
              <v-expansion-panels class="px-sm-16" v-if="producerRequests.length > 0">
                <v-expansion-panel
                  @click="editItem(request.user, true)"
                  v-for="request in producerRequests"
                  :key="request.id"
                >
                  <v-expansion-panel-header class="d-flex flex-column">
                    <p><b>Utilisateur :</b> <span class="text-uppercase">{{request.user.lastName}}</span> {{request.user.firstName}} (ID : {{request.user.id}})</p>
                    <p><b>Email :</b> {{request.user.email}}</p>
                    <p><b>Etat de Compte :</b> {{request.user.isActive === true ? 'Actif' : 'Inactif'}}</p>
                    <p><b>Numéro de téléphone associé au compte :</b> {{request.user.number}}</p>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content>
                    <p><b>NOM Prénom :</b> {{request.name}}</p>
                    <p><b>Description :</b> {{request.description}}</p>
                    <p><b>Numéro de téléphone fournie :</b> {{request.phoneNumber}}</p>

                    <v-container fluid>
                      <v-switch
                        v-model="switch2"
                        @change="updateRoles(request.user.id, request.user.roles, true, request.id)"
                        color="primary"
                        label="Producteur"
                        :loading="switchLoading"
                        :inset="true"
                      ></v-switch>
                      <v-btn
                        depressed
                        color="error"
                        :disabled="!btnActive"
                        @click="refuseRequest(request.id)"
                      >
                        Refuser
                      </v-btn>
                      <v-btn
                        depressed
                        outlined
                        color="error"
                        :disabled="!btnActive"
                        @click="deleteRequest(request.id)"
                      >
                        Supprimer
                      </v-btn>
                    </v-container>
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
              <p v-else>Aucune requête pour le moment</p>
          </div>


          <div class="mt-16">
            <div class="mb-10">
                <h2>Requêtes Refusées</h2>
            </div>
              <v-expansion-panels class="px-sm-16" v-if="refusedProducerRequests.length > 0">
                <v-expansion-panel
                  @click="editItem(request.user, true)"
                  v-for="request in refusedProducerRequests"
                  :key="request.id"
                >
                  <v-expansion-panel-header class="d-flex flex-column">
                    <p><b>Utilisateur :</b> <span class="text-uppercase">{{request.user.lastName}}</span> {{request.user.firstName}} (ID : {{request.user.id}})</p>
                    <p><b>Email :</b> {{request.user.email}}</p>
                    <p><b>Etat de Compte :</b> {{request.user.isActive === true ? 'Actif' : 'Inactif'}}</p>
                    <p><b>Numéro de téléphone associé au compte :</b> {{request.user.number}}</p>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content>
                    <p><b>NOM Prénom :</b> {{request.name}}</p>
                    <p><b>Description :</b> {{request.description}}</p>
                    <p><b>Numéro de téléphone fournie :</b> {{request.phoneNumber}}</p>

                    <v-container fluid>
                      <v-btn
                        depressed
                        color="primary"
                        :disabled="!btnActive"
                        @click="activateRequest(request.id)"
                      >
                        Réactiver
                      </v-btn>
                      <v-btn
                        depressed
                        outlined
                        color="error"
                        :disabled="!btnActive"
                        @click="deleteRequest(request.id)"
                      >
                        Supprimer
                      </v-btn>
                    </v-container>
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
              <p v-else>Aucune requête pour le moment</p>
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

  export default {
    components: {
        AppBar,
        ProductCard,
    },
    data () {
      return {
      dialog: false,
      dialogDelete: false,
      btnActive: true,

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
      switch2: false,
      isProducer: "",
      switchLoading: false,

      snackbar: false,
      text: '',

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
        producerRequests: [],
        refusedProducerRequests: [],
        producer: false,

        selectedUserId : null, 
        selectedUserisAdmin: false, 
        selectedUserisProducer: false,
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
        this.btnActive = false;
        const response = await axios.get("/users");
        var users = response.data.users

        this.refusedProducerRequests = []
        this.producerRequests = []
        response.data.producerRequests.forEach(request => {
          if(request.state === "En attente") {
            this.producerRequests.push(request)
          } else if(request.state === "Refusée") {
            this.refusedProducerRequests.push(request)
          }
        })


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

        this.updateCart()
        this.btnActive = true;
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
    


      updateRoles(userId, roles, swtich2 = false, producerRequestId = null) {
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

        this.selectedUserId = userId;
        this.selectedUserisAdmin = isAdmin;
        this.selectedUserisProducer = isProducer;

        if(swtich2) {
          this.modifyUserRole(true, producerRequestId);
        }
      },
      
     async modifyUserRole(switch2 = false, producerRequestId = null) {
        this.btnActive = false;
        
        var userId = this.selectedUserId;
        var isAdmin = this.selectedUserisAdmin;
        var isProducer =this.selectedUserisProducer;

        console.log(isAdmin)
        if((!isAdmin && isProducer)){
          const response = await axios.get(`/users/${userId}/role/${0}`);
          this.text = response.data.msg;

        } else if (!isAdmin && !isProducer) {
          const response = await axios.get(`/users/${userId}/role/${1}`);
          this.text = response.data.msg;

          if(switch2 && producerRequestId != null) {
            const response = await axios.get(`/users/remove/producer-request/${producerRequestId}`);
            this.text = response.data.msg;
          } 
        }

        this.snackbar = true;
        
        this.initialize();
        this.dialog = false
        this.btnActive = true;
      },

      async deleteRequest(producerRequestId){
        this.btnActive = false;
        const response = await axios.get(`/users/remove/producer-request/${producerRequestId}`);
        this.text = "La requête a bien été supprimée. L'utilisateur pourra à nouveau envoyer une nouvelle requête";

        this.initialize();
        this.snackbar = true;
        this.btnActive = true;
      },

      async refuseRequest(producerRequestId){
        this.btnActive = false;
        const response = await axios.get(`/users/refuse/producer-request/${producerRequestId}`);
        this.text = response.data.msg;

        this.initialize();
        this.snackbar = true;
        this.btnActive = true;
      },

      async activateRequest(producerRequestId){
        this.btnActive = false;
        const response = await axios.get(`/users/activate/producer-request/${producerRequestId}`);
        this.text = response.data.msg;

        this.initialize();
        this.snackbar = true;
        this.btnActive = true;
      },


      editItem (item, switch2 = false) {
        this.switch1 = false
        this.switch2 = false

        if(!switch2) {
          this.editedIndex = this.desserts.indexOf(item)
          this.editedItem = Object.assign({}, item)
          
          this.editedItem.role.forEach(role => {
            if(role === "ROLE_PRODUCER") {
              this.switch1 = true
            }
          })
          this.dialog = true
        } else {
          item.roles.forEach(role => {
            if(role === "ROLE_PRODUCER") {
              this.switch2 = true
            }
          })
        }

        // this.updateRoles(this.editedItem.id, this.editedItem.role)
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
        this.btnActive = false;
        await axios.get(`/users/${$userId}/state-change`);
        this.initialize();
        this.closeDelete()
        this.btnActive = true;
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