"use strict";

var WatchVideo = Vue.component("watch-video", {
  created: function () {
    this.load();
  },
  methods: {
    load: function () {
      this.loadVideo();
      this.loadRecommended();
      window.scrollTo(0, 0);
    },
    loadVideo: function () {
      var self = this;
      fetch("api/watch.php?id=" + this.videoId)
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("watch::loadVideo", json.data);
            self.video = json.data;
            subscribed(self.video.user.id).then(function (json) {
              if (!json.err) {
                self.subscribed = json.data;
              }
            });
          }
        });
    },
    loadRecommended: function () {
      var self = this;
      fetch("api/search.php?recommended=" + this.videoId)
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("watch::loadRecommended", json.data);
            self.videos = json.data;
          }
        });
    },
    rate: function (v) {
      var self = this;
      var formData = new FormData();
      formData.append("id", this.videoId);
      if (v === 1) {
        formData.append("do", "like");
      } else {
        formData.append("do", "dislike");
      }
      fetch("api/watch.php", {
        method: "POST",
        credentials: "same-origin",
        body: formData,
      })
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("watch::rate", json.data);
            ["rate", "likes", "dislikes"].map(function (k) {
              self.video[k] = json.data[k];
              return k;
            });
          }
        });
    },
    rateComment: function (v, commentId) {

    },
    subscribe: function () {
      var self = this;
      subscribe(this.video.user.id).then(function (json) {
        if (!json.err) {
          self.subscribed = json.data;
          if (json.data) {
            self.video.user.subscriberCount++;
          } else {
            self.video.user.subscriberCount--;
          }
        }
      });
    },
  },
  props: ["videoId"],
  watch: {
    videoId: function () {
      this.load();
    },
  },
  data: function () {
    return {
      video: null,
      videos: null,
      userId: null,
      subscribed: null,
    };
  },
  template: `
  <v-row class="">
    <v-col>
      <v-col v-if="video" cols="12" class="py-0">
        <video class="width-100" controls autoplay :src="video.url">
          Your browser does not support the video tag
        </video>
      </v-col>
      <v-col v-if="video" cols="12" class="d-flex flex-row py-0">
        <v-col>
          <v-row class="text-h6">
            {{ video.title }}
          </v-row>
          <v-row class="grey--text text--darken-1 text-subtitle-2">
            {{ video.views }} view(s), Published on {{ video.timestamp }}
          </v-row>
        </v-col>
        <v-col class="d-flex align-start justify-end" style="max-width: 150px">
          <span class="mx-2">
            <a @click="rate(1)">
              <v-icon class="mx-1">mdi-thumb-up{{ video.rate !== 1 ? '-outline' : '' }}</v-icon>
            </a>
            {{ video.likes }}
          </span>
          <span class="mx-2">
            <a @click="rate(-1)">
              <v-icon class="mx-1">mdi-thumb-down{{ video.rate !== -1 ? '-outline' : '' }}</v-icon>
            </a>
            {{ video.dislikes }}
          </span>
        </v-col>
      </v-col>
      <v-col>
        <v-divider></v-divider>
      </v-col>
      <v-col v-if="video" cols="12" class="d-flex flex-row py-0">
        <v-col class="flex-grow-0 pl-0">
          <v-avatar size=60>
            <v-img
              :alt="video.user.username"
              :src="video.user.pic"
            ></v-img>
          </v-avatar>
        </v-col>
        <v-col>
          <v-row class="text-h7">
            <router-link
              class="text-decoration-none grey--text text--darken-3"
              :to="'/userinfo/' + video.user.id"
            >
              {{ video.user.fullName }}
            </router-link>
          </v-row>
          <v-row class="grey--text text--darken-1 text-subtitle-2">
            {{ video.user.subscriberCount }} subscriber(s)
          </v-row>
        </v-col>
        <v-col v-if="subscribed !== null" class="d-flex justify-end align-start">
          <v-btn
            v-if="!subscribed"
            color="error"
            @click="subscribe"
          >
            Subscribe
          </v-btn>
          <v-btn
            v-if="subscribed"
            @click="subscribe"
          >
            Unsubscribe
          </v-btn>
        </v-col>
      </v-col>
      <v-col v-if="video" class="ml-8 pl-8" cols=12>
        {{ video.description }}
      </v-col>
      <v-col>
        <v-divider></v-divider>
      </v-col>
      <v-col v-if="video">
        <span v-if="video.commentsCount">{{ video.commentsCount }} comment(s)</span>
        <span v-if="!video.commentsCount">No comments yet</span>
      </v-col>
      <v-col v-if="video" class="px-0">
        <v-list three-line>
          <v-list-item
            v-for="item in video.comments"
            :key="item.id"
            ripple
          >
            <v-list-item-avatar size="60">
              <img :src="item.postedBy.pic">
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title class="font-weight-regular" v-html="item.postedBy.fullName"></v-list-item-title>
              <v-list-item-subtitle class="my-2" v-html="item.body"></v-list-item-subtitle>
              <v-list-item-action class="mx-0 my-4">
                <span class="d-flex align-start justify-start">
                  <span class="mx-1 grey--text">
                    <a @click="rateComment(1, item.id)">
                      <v-icon small color="grey" class="mx-1">mdi-thumb-up</v-icon>
                    </a>
                    {{ item.likes }}
                  </span>
                  <span class="mx-1 grey--text">
                    <a @click="rateComment(-1, item.id)">
                      <v-icon small color="grey" class="mx-1">mdi-thumb-down</v-icon>
                    </a>
                    {{ item.disLikes }}
                  </span>
                </span>
              </v-list-item-action>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-col>
    </v-col>
    <v-col v-if="videos" style="max-width: 350px;">
      <v-col cols="12" v-for="v in videos" :key="v.id">
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
    </v-col>
  </v-row>
  `,
});
