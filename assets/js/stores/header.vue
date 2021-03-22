<template>
<div>
  <v-app-bar
      color="deep-purple"
      dark
    >
      <v-app-bar-nav-icon @click="drawer = true"></v-app-bar-nav-icon>

      <v-toolbar-title>MielPéï</v-toolbar-title>
      <v-spacer></v-spacer>

      <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">

        <v-btn icon
            dark
            v-bind="attrs"
            v-on="on"
          >
          <v-badge
          :content="nbCommands.length"
          :value="nbCommands.length"
          color="green"
          overlap
          >
            <v-icon medium>
              mdi-cart
            </v-icon>
          </v-badge>
        </v-btn>

      </template>
        <v-list>
          <v-list-item
            v-for="(item, index) in nbCommands"
            :key="index"
            link
            @click="remove(item.id)"
          >
            <v-icon >close-circle</v-icon>
            <v-list-item-title>
              {{item.name}}
            </v-list-item-title>
          </v-list-item>
        </v-list>
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
    },
  }
</script>

<style scoped>

</style>      