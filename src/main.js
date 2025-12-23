'use strict';

import './style.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import form from './form.js';
form(Alpine);

Alpine.start();