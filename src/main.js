'use strict';

import './style.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import cart from './cart.js';
cart(Alpine);

Alpine.start();