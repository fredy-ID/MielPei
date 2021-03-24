<template>
<div>
  <v-app-bar
      color="deep-purple"
      dark
    >
      <v-app-bar-nav-icon @click="drawer = true"></v-app-bar-nav-icon>
      <router-link to="/">
        <v-toolbar-title>MielPéï</v-toolbar-title>
      </router-link>
      <v-spacer></v-spacer>

      <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-width="200"
      offset-x
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon
            dark
            v-bind="attrs"
            v-on="on"
          >
          <v-badge
          :content="(nbCommands ? nbCommands.length : 0)"
          :value="(nbCommands ? nbCommands.length : 0)"
          color="green"
          overlap
          >
            <v-icon medium>
              mdi-cart
            </v-icon>
          </v-badge>
        </v-btn>
      </template>

      <v-card>
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <!-- <img
                src="#"
                alt="Une image de profil"
              > -->
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>Mon panier</v-list-item-title>
              <v-list-item-subtitle>Mes envies</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn
                :class="'blue--text'"
                icon
              >
                <v-icon>mdi-cart</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list v-if="nbCommands && nbCommands.length >= 1">
          <v-list-item
            v-for="(item, index) in nbCommands"
            :key="index"
            three-line
          >
          <v-list-item-content>
            <v-list-item-title>
              {{item.product.name}}
            </v-list-item-title>
            <v-list-item-subtitle>
              Quantité : {{ item.quantity }}
            </v-list-item-subtitle>
            <v-list-item-subtitle>
              {{ item.product.price * item.quantity }} €
            </v-list-item-subtitle>
          </v-list-item-content>
            <v-btn
              :class="'red--text pr-10'"
              text
              @click="remove(item.product.id)"
            >
              <!-- <v-icon>mdi-close</v-icon> -->
              Retirer
            </v-btn>
          </v-list-item>
        </v-list>
        <v-list v-else>
          <v-list-item>
          <v-list-item-title>
              Aucun produit selectionné
          </v-list-item-title>
          </v-list-item>
        </v-list>

        <v-spacer></v-spacer>

        <v-card-actions>
          <router-link to="/cart">
            <v-btn
            color="primary"
            text
          >
            Voir le panier
          </v-btn>
          </router-link> 
        </v-card-actions>
      </v-card>
    </v-menu>

        
    </v-app-bar>

    <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
    >
      <v-list
        nav
        dense
      >
        <v-list-item-group
          v-model="group"
          active-class="deep-purple--text text--accent-4"
        >
          <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <router-link to="/">
                  Accueil
                </router-link> 
              </v-list-item-title>
          </v-list-item>
          
          <v-list-item v-if="user !== null">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <router-link to="/compte">
                  Compte
                </router-link> 
              </v-list-item-title>
          </v-list-item>

          <v-list-item v-if="user !== null && producer === true">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <router-link to="/product/my-products">
                  Ma boutique
                </router-link> 
              </v-list-item-title>
          </v-list-item>

          <v-list-item v-if="user !== null">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <router-link to="/commands">
                  Mes commandes
                </router-link> 
              </v-list-item-title>
          </v-list-item>

          <v-list-item v-if="user !== null">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <a href="/logout">Déconnexion</a>
              </v-list-item-title>
          </v-list-item>

          <v-list-item v-if="user === null">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <a href="/login">Connexion</a>
              </v-list-item-title>
          </v-list-item>
          <v-list-item v-if="user === null">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                <a href="/register">Créer un compte</a>
              </v-list-item-title>
          </v-list-item>

        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>

<script>
const axios = require('axios');
  export default {
    data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,
      
      drawer: false,
      group: null,
      messages: 0,
      // show: false,
      items: [
        { title: 'Click Me' },
        { title: 'Click Me' },
        { title: 'Click Me' },
        { title: 'Click Me 2' },
      ],
    }),
    props: ["nbCommands", "user", "producer"],
    methods: {
     async remove(itemId) {
        const response = await axios.get("/cart/remove/item/" + itemId);
        this.$emit('update-cart')
        console.log('ok')
      },
      test(a) {
        console.log(a)
      },
    }, 
    
  }
</script>

<style scoped>

</style>      