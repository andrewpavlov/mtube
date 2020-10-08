"use strict";

var Home = Vue.component("video-card", {
  data: function () {
    return {
    };
  },
  props: [
    'large',
    'videoId',
    'thumb',
    'title',
    'author',
    'authorId',
    'views',
    'length',
    'date',
  ],
  template: `
<v-card
  class="text-3"
  min-width="250px"
  :max-width="large?'auto':'500px'"
>
  <v-row class="mx-0">
    <v-col class="py-0 px-0" v-bind:class="{'flex-grow-0':large}">
      <router-link :to="'/watch/' + videoId">
        <v-img
          class="align-end"
          :src="thumb"
          height="150px"
          :width="large?'300px':'auto'"
        >
          <v-card-title
            class="py-1 text-body-2 float-right grey darken-2 white--text"
            v-text="length"
          >
          </v-card-title>
        </v-img>
      </router-link>
    </v-col>
    
    <v-col :cols="large?'':12" class="py-0 px-0">
      <v-card-title class="text-body-2">
        {{ title }}
      </v-card-title>
      
      <v-card-subtitle class="grey--text">
        <router-link
          class="text-decoration-none grey--text text--darken-2"
          :to="'/userinfo/' + authorId"
        >
          {{ author }}
        </router-link> <br/>
        {{ views }} view(s) - {{ date }}
      </v-card-subtitle>
    </v-col>
  </v-row>
</v-card>
  `,
});
