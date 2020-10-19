"use strict";

var Upload = Vue.component("profile", {
  methods: {
    upload() {
      if (this.$refs.uploadForm.validate()) {
        var formData = new FormData();
        formData.append("file", this.file, this.file.name);

        var self = this;
        this.uploading = true;
        fetch("api/upload.php", {
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
              console.debug("upload", json.data);
              router.push("/edit/" + json.data);
            }
          })
          .finally(function () {
            self.uploading = false;
          });
      }
    },
  },
  data: function () {
    return {
      valid: true,
      file: null,
      uploading: false,
      rules: inputValidationRules,
    };
  },
  template: `
  <v-container max-width="500px" min-width="360px">
    <v-card class="px-4" flat tile>
      <v-card-title>
        Upload new video
      </v-card-title>
      <v-card-text>
        <v-form ref="uploadForm"
          v-model="valid"
          @submit.prevent="upload"
          enctype="multipart/form-data"
          lazy-validation
        >
          <v-col cols="12">
            <v-file-input
              v-model="file"
              name="file"
              accept="video/*"
              label="Video file"
              prepend-icon="mdi-video"
              :rules="[rules.isset]"
              required
            ></v-file-input>
          </v-col>
          <v-col cols="12" class="d-flex justify-end">
            <v-btn type="submit" :disabled="!valid || uploading" color="primary">Upload</v-btn>
          </v-col>
        </v-form>
      </v-card-text>
    </v-card>
    <v-overlay :value="uploading" z-index=10>
      <v-progress-circular
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>
  </v-container>
  `,
});
