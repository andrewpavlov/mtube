var Login = Vue.component("login-form", {
  created: function () {
    this.$store.commit('signout');
    fetch("api/signout.php");
  },
  computed: {
    passwordMatch: function () {
      return () => this.password === this.verify || "Password must match";
    },
  },
  methods: {
    login: function () {
      if (this.$refs.loginForm.validate()) {
        var formData = new FormData();
        formData.append("email", this.loginEmail);
        formData.append("password", this.loginPassword);
        this.submit("api/signin.php", formData);
      }
    },
    register: function () {
      if (this.$refs.registerForm.validate()) {
        var formData = new FormData();
        formData.append("firstName", this.firstName);
        formData.append("lastName", this.lastName);
        formData.append("username", this.username);
        formData.append("email", this.email);
        formData.append("password", this.password);
        formData.append("verify", this.verify);
        this.submit("api/signup.php", formData);
      }
    },
    submit: function (url, formData) {
      var self = this;
      fetch(url, {
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
            console.debug("sign in", json);
            self.$store.commit({
              type: "signin",
              user: json.data,
            });
            router.push("/userinfo");
          }
        });
    },
  },
  data: function () {
    return {
      dialog: true,
      tab: 0,
      tabs: [
        { name: "Login", icon: "mdi-account" },
        { name: "Register", icon: "mdi-account-outline" },
      ],
      valid: true,

      firstName: "",
      lastName: "",
      username: "",
      email: "",

      password: "",
      verify: "",
      loginPassword: "",
      loginEmail: "",

      show1: false,
      show2: false,
      show3: false,
      rules: inputValidationRules,
    };
  },

  template: `
  <v-container max-width="600px" min-width="360px">
    <v-tabs v-model="tab" show-arrows icons-and-text grow>
      <v-tabs-slider></v-tabs-slider>
      <v-tab v-for="(tab, index) in tabs" :key="index">
        <v-icon large>{{ tab.icon }}</v-icon>
        <div class="caption py-1">{{ tab.name }}</div>
      </v-tab>
      <v-tab-item>
        <v-card class="px-4">
          <v-card-text>
            <v-form ref="loginForm"
              v-model="valid"
              @submit.prevent="login"
              lazy-validation
            >
              <v-row>
                <v-col cols="12">
                  <v-text-field label="E-mail"
                    v-model="loginEmail"
                    :rules="[rules.required, rules.email]"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="Password"
                    hint="At least 8 characters"
                    v-model="loginPassword"
                    :append-icon="show1?'mdi-eye':'mdi-eye-off'"
                    :rules="[rules.required, rules.min]"
                    :type="show1 ? 'text' : 'password'"
                    counter @click:append="show1 = !show1"
                  ></v-text-field>
                </v-col>
                <v-col class="d-flex" cols="12" sm="6" xsm="12">
                </v-col>
                <v-col class="d-flex justify-end">
                  <v-btn type="submit" large :disabled="!valid" color="primary">Sign in</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item>
        <v-card class="px-4">
          <v-card-text>
            <v-form ref="registerForm"
              v-model="valid"
              @submit.prevent="register"
              lazy-validation
            >
              <v-row>
                <v-col cols="12" sm="6" md="6">
                  <v-text-field label="First Name"
                    v-model="firstName"
                    :rules="[rules.required]"
                    maxlength="20"
                    required
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <v-text-field label="Last Name"
                    v-model="lastName"
                    :rules="[rules.required]"
                    maxlength="20"
                    required
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="Username"
                    v-model="username"
                    :rules="[rules.required]"
                    required
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="E-mail"
                    v-model="email"
                    :rules="[rules.required, rules.email]"
                    required
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <v-text-field label="Password"
                    v-model="password"
                    :append-icon="show2 ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[rules.required, rules.min]"
                    :type="show2 ? 'text' : 'password'"
                    hint="At least 8 characters"
                    counter
                    @click:append="show2 = !show2"
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <v-text-field label="Confirm Password"
                    block
                    v-model="verify"
                    :append-icon="show3 ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[rules.required, passwordMatch]"
                    :type="show3 ? 'text' : 'password'"
                    counter
                    @click:append="show3 = !show3"
                  ></v-text-field>
                </v-col>
                <v-col class="d-flex justify-end">
                  <v-btn type="submit" large :disabled="!valid" color="primary">Register</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </v-container>
  `,
});
