"use strict";

var Search = Vue.component("search-video", {
  created: function () {
    this.load();
  },
  props: [
    'title',
    'filter',
    'term',
    'sortBy',
  ],
  data: function () {
    return {
      videos: null,
    };
  },
  methods: {
    load: function () {
      var self = this;
      var url = "api/search.php?";
      var params = [];
      this.filter && params.push([this.filter, true].join("="));
      this.term && params.push(["term", encodeURIComponent(this.term)].join("="));
      this.sortBy && params.push(["sortBy", encodeURIComponent(this.sortBy)].join("="));
      url += params.join("&");
      fetch(url, {
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
            console.debug("search::load", json.data);
            self.videos = json.data;
          }
        });
    },
  },
  watch: {
    filter: function () {
      this.load();
    },
    term: function () {
      this.load();
    },
    sortBy: function () {
      this.load();
    },
  },
  template: `
  <div>
    <v-row v-if="title" class="d-flex flex-row">
      <v-col cols="12" class="grey--text text-h6">
        {{ title }}
        <v-divider></v-divider>
      </v-col>
    </v-row>
    <v-row class="d-flex flex-row">
      <v-col v-if="videos && !videos.length" cols="12">
        No items found
      </v-col>
      <v-col cols="12" v-for="v in videos" :key="v.id">
        <video-card
          large="true"
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
  </div>
  `,
});
