"use strict";

var App = Vue.component("my-tube", {
  methods: {
    search: function () {
      router.push({
        path: "/search",
        query: {
          term: this.term,
        },
      });
    },
  },
  data: function () {
    return {
      drawer: null,
      title: "myTube",
      term: "",
      sideMenu: [
        ["mdi-home", "Home", "/"],
        ["mdi-fire", "Trending", "/trending"],
        ["mdi-youtube-subscription", "Subscriptions", "/subscriptions"],
        ["mdi-thumb-up", "Liked videos", "/liked"],
        ["mdi-upload", "Upload new video", "/upload"],
        ["mdi-account-circle", "My profile", "/userinfo"],
      ],
    };
  },
  template: `
    <v-app>
      <v-app-bar app>
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-avatar class="ml-2 mr-2" color="grey darken-1" size="32">
          <img src="assets/logo.png" />
        </v-avatar>

        <v-toolbar-title>{{ title }}</v-toolbar-title>

        <v-responsive class="ml-10 mr-2" max-width="350">
          <v-text-field dense flat hide-details rounded solo-inverted
            v-on:keyup.enter="search"
            v-model="term"
          >
          </v-text-field>
        </v-responsive>

        <v-spacer class="d-none d-sm-flex"></v-spacer>

        <v-btn class="d-none d-sm-flex" title="Upload" to="/upload">
          <v-icon>mdi-upload</v-icon>
        </v-btn>
        <v-btn v-if="$store.getters.userId" title="My profile" to="/userinfo" tag="v-btn">
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
        <v-btn v-if="!$store.getters.userId" title="Sign in" to="/signin" tag="v-btn">
          <v-icon>mdi-login-variant</v-icon>
        </v-btn>
      </v-app-bar>

      <v-navigation-drawer app v-model="drawer">
        <v-list>
          <v-list-item v-for="[icon, text, link] in sideMenu" :key="icon" :to="link">
            <v-list-item-icon>
              <v-icon>{{ icon }}</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>{{ text }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item link to="/signin">
            <v-list-item-icon>
              <v-icon>mdi-{{ $store.getters.userId ? 'logout' : 'login-variant' }}</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Sign {{ $store.getters.userId ? 'out' : 'in' }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-main>
        <v-container class="py-8 px-6" fluid>
          <router-view></router-view>
        </v-container>
      </v-main>

      <v-footer app absolute>
        <v-col class="text-center" cols="12">
          {{ new Date().getFullYear() }} - <strong>{{ title }}</strong>
        </v-col>
      </v-footer>
    </v-app>
  `,
});

userInfo().then(function (json) {
  if (!json.err) {
    store.commit({
      type: "signin",
      user: json.data,
    });
  }
  new Vue({
    el: "#app",
    router: router,
    store: store,
    vuetify: new Vuetify(),
  });
});
