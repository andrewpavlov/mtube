"use strict";

var ProfileSetting = Vue.component("profile-settings", {
  created: function () {
    this.load();
  },
  methods: {
    load: function () {
      var self = this;
      userInfo().then(function (json) {
        if (!json.err) {
          self.info.firstName = json.data.firstName;
          self.info.lastName = json.data.lastName;
          self.info.username = json.data.username;
          self.info.email = json.data.email;
        }
      });
    },
    changeDetails: function () {
      if (this.$refs.changeDetails.validate()) {
        var formData = new FormData();
        formData.append("do", "changeDetails");
        formData.append("firstName", this.info.firstName);
        formData.append("lastName", this.info.lastName);
        formData.append("email", this.info.email);
        this.submit(formData);
      }
    },
    changePassword: function () {
      if (this.$refs.changePassword.validate()) {
        var formData = new FormData();
        formData.append("do", "changePassword");
        formData.append("password", this.password.password);
        formData.append("newPassword", this.password.newPassword);
        formData.append("verifyPassword", this.password.verifyPassword);
        this.submit(formData);
      }
    },
    submit: function (formData) {
      var self = this;
      fetch("api/profile.php", {
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
            console.debug("profile update", json);
            self.form.okbar = true;
          }
        });
    },
  },
  data: function () {
    return {
      info: {
        firstName: "",
        lastName: "",
        username: "",
        email: "",
      },
      password: {
        password: "",
        newPassword: "",
        verifyPassword: "",
      },
      form: {
        valid1: true,
        valid2: true,
        show1: false,
        show2: false,
        show3: false,
        okbar: false,
      },
      rules: inputValidationRules,
    };
  },
  template: `
  <v-container max-width="600px" min-width="360px">
    <v-tabs show-arrows icons-and-text grow>
      <v-tabs-slider></v-tabs-slider>
      <v-tab>
        <div class="caption py-1">Personal info</div>
        <v-icon large>mdi-account</v-icon>
      </v-tab>
      <v-tab>
        <div class="caption py-1">Change password</div>
        <v-icon large>mdi-key-variant</v-icon>
      </v-tab>

      <v-tab-item>
        <v-card class="px-4">
          <v-card-text>
            <v-form ref="changeDetails"
              v-model="form.valid1"
              @submit.prevent="changeDetails"
              lazy-validation
            >
              <v-row>
                <v-col cols="12">
                  <v-text-field label="First Name"
                    v-model="info.firstName"
                    :rules="[rules.required]"
                    maxlength="20" required>
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="Last Name"
                    v-model="info.lastName"
                    :rules="[rules.required]"
                    maxlength="20" required>
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="E-mail"
                    v-model="info.email"
                    :rules="[rules.required, rules.email]"
                    required>
                  </v-text-field>
                </v-col>
                <v-spacer></v-spacer>
                <v-col class="d-flex justify-end" cols="12">
                  <v-btn color="primary"
                    type="submit"
                    :disabled="!form.valid1"
                  >Update</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-tab-item>

      <v-tab-item>
        <v-card class="px-4">
          <v-card-text>
            <v-form ref="changePassword"
              v-model="form.valid2"
              @submit.prevent="changePassword"
              lazy-validation
            >
              <v-row>
                <v-col cols="12">
                  <v-text-field label="Current password"
                    hint="Type your current password"
                    v-model="password.password"
                    :rules="[rules.required]"
                    :type="form.show1 ? 'text' : 'password'"
                    :append-icon="form.show1 ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="form.show1 = !form.show1"
                    required>
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="New password"
                    hint="At least 8 characters"
                    v-model="password.newPassword"
                    :rules="[rules.required, rules.min]"
                    :type="form.show2 ? 'text' : 'password'"
                    :append-icon="form.show2 ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="form.show2 = !form.show2"
                    counter
                    required>
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="Confirm password"
                    hint="Confirm new password"
                    v-model="password.verifyPassword"
                    :rules="[rules.required, rules.match(password.verifyPassword, password.newPassword)]"
                    :type="form.show3 ? 'text' : 'password'"
                    :append-icon="form.show3 ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="form.show3 = !form.show3"
                    required>
                  </v-text-field>
                </v-col>
                <v-col class="d-flex justify-end">
                  <v-btn color="primary"
                    type="submit"
                    :disabled="!form.valid2"
                  >Update</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>

    <v-snackbar
      v-model="form.okbar"
    >
      Profile is updated
      <template v-slot:action="{ attrs }">
        <v-btn
          color="blue"
          text
          v-bind="attrs"
          @click="form.okbar = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
  `,
});
