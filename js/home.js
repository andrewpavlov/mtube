"use strict";

var Home = Vue.component("home", {
  created: function () {
    this.load();
  },
  data: function () {
    return {
      recommended: null,
    };
  },
  methods: {
    load: function (un) {
      var self = this;
      fetch("api/grid.php", {
        method: "GET",
        credentials: "same-origin",
      })
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("home::load", json.data);
            self.recommended = json.data.recommended;
          }
        });
    },
  },
  template: `
    <v-row class="d-flex flex-row">
      <v-col v-for="v in recommended" :key="v.id">
        <video-card
          :video-id="v.id"
          :thumb="v.thumb"
          :title="v.title"
          :author="v.user.username"
          :author-id="v.user.id"
          :views="v.views"
          :length="v.duration"
          :date="v.timestamp"
        ></video-card>
      </v-col>
    </v-row>
  `,
});
