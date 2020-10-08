"use strict";

var subscribed = function (userTo) {
  return fetch("api/subscribe.php?id=" + userTo).then(function (resp) {
    return resp.json();
  });
};

var subscribe = function (userTo) {
  var formData = new FormData();
  formData.append("id", userTo);
  return fetch("api/subscribe.php", {
    method: "POST",
    body: formData,
  }).then(function (resp) {
    return resp.json();
  });
};

var userInfo = function (id) {
  var url = "api/user.php?user";
  if (id) {
    url += "=" + encodeURIComponent(id);
  }
  return fetch(url).then(function (resp) {
    return resp.json();
  });
};

var inputValidationRules = {
  email: (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
  required: (v) => !!(v && v.replace(/ /g, "")) || "Required.",
  isset: (v) => !!v || "Required.",
  min: (v) => (!v && v.length >= 8) || "Min 8 characters",
  match: (v1, v2) => v1 === v2 || "Password must match",
};