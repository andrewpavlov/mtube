"use strict";

var EditVideo = Vue.component("edit-video", {
  created: function () {
    this.load();
  },
  methods: {
    submit: function () {
      if (this.$refs.form.validate()) {
        var self = this;

        var formData = new FormData();
        formData.append("id", this.videoId);
        ["title", "description", "privacy"].map(function (f) {
          if (self.video[f] !== null) {
            formData.append(f, self.video[f]);
          }
        });
        formData.append("category", self.video.category.id);

        this.loading = true;
        fetch("api/video.php", {
          method: "POST",
          credentials: "same-origin",
          body: formData
        })
          .then(function (resp) {
            return resp.json();
          })
          .then(function (json) {
            if (json.err) {
              alert(json.data);
            } else {
              console.debug("editvideo::save", json.data);
              self.okbar = true;
            }
          })
          .finally(function () {
            self.loading = false;
          });
      }
    },
    load: function () {
      this.loadVideo();
      this.loadOptions();
    },
    loadOptions: function () {
      var self = this;
      fetch("api/categories.php")
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("editvideo::categories", json.data);
            self.categories = json.data.map(function (i) {
              return {
                value: i.id,
                text: i.name,
              };
            });
          }
        });
    },
    loadVideo: function () {
      var self = this;
      this.loading = true;
      fetch("api/watch.php?id=" + this.videoId)
        .then(function (resp) {
          return resp.json();
        })
        .then(function (json) {
          if (json.err) {
            alert(json.data);
          } else {
            console.debug("editvideo::load", json.data);
            self.video = json.data;
          }
        })
        .finally(function () {
          self.loading = false;
        });
    },
  },
  props: ["videoId"],
  data: function () {
    return {
      loading: false,
      okbar: false,
      valid: true,
      video: null,
      categories: [],
      rules: inputValidationRules,
    };
  },
  watch: {
    videoId: function () {
      this.loadVideo();
    },
  },
  template: `
  <v-container max-width="500px" min-width="360px">
    <v-card class="px-4" flat tile>
      <v-card-title>
        Edit video
      </v-card-title>
      <v-card-text>
        <v-form v-if="video" ref="form" v-model="valid" lazy-validation>
          <v-col cols="12">
            <v-text-field
              label="Title"
              v-model="video.title"
              :rules="[rules.required]"
              maxlength="20"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-select
              v-model="video.category.id"
              :items="categories"
              label="Filled style"
            ></v-select>
          </v-col>
          <v-col cols="12">
            <v-textarea
              label="Description"
              v-model="video.description"
              auto-grow
            ></v-textarea>
          </v-col>
          <v-col cols="12">
            <v-radio-group
              v-model="video.privacy"
              row
            >
              <v-radio
                label="Public"
                value="1"
              ></v-radio>
              <v-radio
                label="Private"
                value="0"
              ></v-radio>
            </v-radio-group>
          </v-col>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-col cols="12" class="d-flex justify-end">
          <v-btn :disabled="!valid || loading" color="primary" @click="submit">Save</v-btn>
        </v-col>
      </v-card-actions>
    </v-card>

    <v-snackbar
      v-model="okbar"
    >
      Profile is updated
      <template v-slot:action="{ attrs }">
        <v-btn
          color="blue"
          text
          v-bind="attrs"
          @click="okbar = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
  `,
});
