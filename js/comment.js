"use strict";

Vue.component("video-comments", {
  created: function () {
    this.load();
  },
  methods: {
    load: function () {
      this.reset();
      this.loadComments();
    },
    loadComments: function () {
      var self = this;
      fetch("api/comment.php?videoId=" + this.videoId)
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("comment::loadComments", json.data);
            self.setComments(json.data);
          }
        });
    },
    setComments: function (data) {
      this.comments = data.map(function (i) {
        i.publishedAt = new Date(i.publishedAt.replace(/ /, "T") + "Z");
        return i;
      });
      this.reset();
    },
    rate: function (v, comment) {
      var self = this;
      var formData = new FormData();
      formData.append("do", v === 1 ? "like" : "dislike");
      formData.append("id", comment.id);
      formData.append("videoId", this.videoId);
      fetch("api/comment.php", {
        method: "POST",
        body: formData,
      })
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("comment::rate", json.data);
            ["rate", "likes", "dislikes"].map(function (k) {
              comment[k] = json.data[k];
              return k;
            });
          }
        });
    },
    reset: function () {
      this.text = "";
      this.valid = true;
      this.$refs.form && this.$refs.form.reset();
    },
    submit: function (replyTo) {
      if (this.$refs.form.validate()) {
        var self = this;
        var formData = new FormData();
        formData.append("do", "comment");
        formData.append("videoId", this.videoId);
        formData.append("text", this.text);
        if (replyTo) {
          formData.append("replyTo", replyTo);
        }
        fetch("api/comment.php", {
          method: "POST",
          body: formData,
        })
          .then(function (resp) {
            return resp.json();
          })
          .then(function (json) {
            if (json.err) {
              alert(json.data);
            } else {
              console.debug("comment::submit", json.data);
              self.setComments(json.data);
            }
          });
      }
    },
  },
  props: ["videoId", "commentsCount"],
  watch: {
    videoId: function () {
      this.load();
    },
  },
  data: function () {
    return {
      comments: [],
      text: "",
      valid: true,
      rules: inputValidationRules,
    };
  },
  template: `
  <div>
    <v-col>
      <span v-if="commentsCount">{{ commentsCount }} comment(s)</span>
      <span v-if="!commentsCount">No comments yet</span>
    </v-col>
    <v-col v-if="$store.getters.userId">
      <v-form class="d-flex flex-row"
        ref="form"
        v-model="valid"
        @submit.prevent="submit()"
        lazy-validation
      >
        <v-avatar class="mr-2">
          <v-img :src="$store.getters.userInfo.pic"></v-img>
        </v-avatar>
        <div class="flex-grow-1">
          <v-text-field label="Add a public comment..."
            v-model="text"
            :rules=[rules.required]
            required
          ></v-text-field>
          <div class="d-flex justify-end">
            <v-btn type="submit" small :disabled="!valid" color="primary">Comment</v-btn>
          </div>
        </div>
      </v-form>
    </v-col>
    <v-col class="px-0">
      <v-list three-line>
        <v-list-item
          v-for="item in comments"
          :key="item.id"
        >
          <v-list-item-avatar size="50">
            <v-img :src="item.postedBy.pic"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="font-weight-regular">
              {{ item.postedBy.fullName }} <span class="">{{ item.publishedAt | moment("from") }}</span>
            </v-list-item-title>
            <v-list-item-subtitle class="my-2" v-html="item.body"></v-list-item-subtitle>
            <v-list-item-action class="mx-0 my-4">
              <span class="d-flex align-start justify-start">
                <span class="mx-1 grey--text">
                  <a @click="rate(1, item)">
                    <v-icon small color="grey" class="mx-1">mdi-thumb-up{{ item.rate !== 1 ? '-outline' : '' }}</v-icon>
                  </a>
                  {{ item.likes }}
                </span>
                <span class="mx-1 grey--text">
                  <a @click="rate(-1, item)">
                    <v-icon small color="grey" class="mx-1">mdi-thumb-down{{ item.rate !== -1 ? '-outline' : '' }}</v-icon>
                  </a>
                  {{ item.dislikes }}
                </span>
              </span>
            </v-list-item-action>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-col>
  </div>
  `,
});
