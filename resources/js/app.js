import "./bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "toastr/build/toastr.min.css";

import { createApp } from "vue";

import Home from "./Pages/Home.vue";
createApp(Home).mount("#app");
