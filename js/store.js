"use strict";

var store = new Vuex.Store({
  state: {
    user: null,
  },
  actions: {
  },
  mutations: {
    signin: function (state, payload) {
      state.user = Object.assign({}, state.user, payload.user);
    },
    signout: function (state) {
      state.user = null;
    },
  },
  getters: {
    userInfo: function (state) {
      return state.user ? Object.assign({}, state.user) : null;
    },
    userId: function (state) {
      return state.user ? state.user.id : null;
    },
  },
});
