"use strict";

var routes = [
  { path: "/signin", component: Login },
  {
    path: "/setting",
    component: ProfileSetting,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/userinfo",
    component: UserInfo,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/userinfo/:userId",
    component: UserInfo,
    props: true,
  },
  {
    path: "/search",
    component: Search,
    props: function (route) {
      return {
        term: route.query.term,
      };
    },
  },
  {
    path: "/trending",
    component: Search,
    props: {
      title: "Trending videos uploaded in the last week",
      filter: "trending",
    },
  },
  {
    path: "/subscriptions",
    component: Search,
    props: {
      title: "My subscriptions",
      filter: "subscriptions",
    },
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/liked",
    component: Search,
    props: {
      title: "Videos I like",
      filter: "liked",
    },
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/watch/:videoId",
    component: WatchVideo,
    props: true,
  },
  {
    path: "/edit/:videoId",
    component: EditVideo,
    props: true,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/upload",
    component: Upload,
    meta: {
      requiresAuth: true,
    },
  },
  { path: "/", component: Home },
];

var router = new VueRouter({
  routes: routes,
});

router.beforeEach(function (to, from, next) {
  console.log('beforeEach',to);
  if (to.meta.requiresAuth) {
    if (store.getters.userId) {
      next();
    } else {
      next({
        path: "/signin",
      });
    }
  } else {
    next();
  }
});
