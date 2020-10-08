"use strict";

var UserInfo = Vue.component("user-info", {
  created: function () {
    this.loadUser(this.userId);
  },
  computed: {
    thatsMe: function () {
      return !this.userId || this.userId === this.$store.getters.userId;
    },
  },
  watch: {
    userId: function (id) {
      this.loadUser(id);
    },
  },
  props: ["userId"],
  data: function () {
    return {
      videos: null,
      info: null,
      subscribed: null,
    };
  },
  methods: {
    fullName: function () {
      return [this.info.firstName, this.info.lastName].join(" ");
    },
    loadUser: function (id) {
      this.loadUserInfo(id);
      this.loadUserVideos(id);
    },
    loadUserInfo: function (un) {
      var self = this;
      userInfo(un)
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("loadUserInfo", json.data);
            self.info = json.data;
            self.info.fullName = self.fullName();
          }
        });
      if (un) {
        subscribed(un).then(function (json) {
          if (!json.err) {
            self.subscribed = json.data;
          }
        });
      }
    },
    loadUserVideos: function (un) {
      var self = this;
      var url = "api/search.php?user";
      if (un) {
        url += "=" + encodeURIComponent(un);
      }
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
            console.debug("loadUserVideos", json.data);
            self.videos = json.data;
          }
        });
    },
    subscribe: function () {
      var self = this;
      subscribe(this.userId).then(function (json) {
        if (!json.err) {
          self.subscribed = json.data;
          if (self.subscribed) {
            self.info.subscriberCount++;
          } else {
            self.info.subscriberCount--;
          }
        }
      });
    },
  },
  template: `
  <v-container v-if="info">
    <v-row class="d-flex flex-row">
      <v-col class="flex-grow-0">
        <v-avatar size=70>
          <v-img
            :alt="info.username"
            :src="info.pic"
          ></v-img>
        </v-avatar>
      </v-col>
      <v-col>
        <v-row class="text-h5 mt-1 mb-1">{{ info.fullName }}</v-row>
        <v-row class="grey--text text--darken-1 text-subtitle-2">
          {{ info.subscriberCount }} subscriber(s)
        </v-row>
      </v-col>
      <v-col v-if="subscribed !== null && !thatsMe" class="d-flex justify-end">
        <v-btn color="error" @click="subscribe" v-if="!subscribed">Subscribe</v-btn>
        <v-btn @click="subscribe" v-if="subscribed">Unsubscribe</v-btn>
      </v-col>
    </v-row>
    <v-tabs show-arrows icons-and-text grow>
      <v-tabs-slider></v-tabs-slider>
      <v-tab>
        <v-icon large>mdi-account</v-icon>
        <div class="caption py-1">User info</div>
      </v-tab>
      <v-tab v-if="videos">
        <v-icon large>mdi-video</v-icon>
        <div class="caption py-1">Videos</div>
      </v-tab>
      <v-tab-item>
        <v-card flat tile class="px-4 py-4">
          <v-card-text>
            Name: {{ info.fullName }} <br/>
            Username: {{ info.username }} <br/>
            Subscribers: {{ info.subscriberCount }} <br/>
            Total views: {{ info.totalViews }} <br/>
            Sign up date: {{ info.signUpDate }}
          </v-card-text>
          <v-card-actions v-if="thatsMe">
            <v-btn color="primary" to="/setting">Edit</v-btn>
          </v-card-actions>            
        </v-card>
      </v-tab-item>
      <v-tab-item>
        <v-card flat tile class="px-4 py-4">
          <v-row v-if="!userId">
            <v-col>
              <v-btn color="error" to="/upload">Upload new</v-btn>
            </v-col>
          </v-row>
          <v-row v-if="videos && !videos.length">
            <v-col class="grey--text text-center">
              No uploaded videos yet
            </v-col>
          </v-row>
          <v-row class="d-flex flex-row">
            <v-col v-for="v in videos" :key="v.id" class="flex-grow-0">
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
        </v-card>
      </v-tab-item>
    </v-tabs>
  </v-container>
  `,
});
