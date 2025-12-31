"use strict";

import "./styles/style.css";

import Alpine from "alpinejs";
window.Alpine = Alpine;

import axios from "axios";

import cart from "./js/cart.js";
cart(Alpine, axios);

Alpine.start();
