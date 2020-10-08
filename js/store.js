"use strict";

var store = new Vuex.Store({
  state: {
    user: null,
  },
  actions: {
    fetch: function () {
      console.log(123123123);
    },
    // fetchItem({ commit }, id) {
    //   // return the Promise via `store.dispatch()` so that we know
    //   // when the data has been fetched
    //   return fetchItem(id).then((item) => {
    //     commit("setItem", { id, item });
    //   });
    // },
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
