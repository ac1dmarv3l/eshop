'use strict';

import './styles/style.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import cart from './js/cart.js';
cart(Alpine);

Alpine.start();
