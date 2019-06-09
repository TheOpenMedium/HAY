/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
global.$ = require('jquery');

// Requiring node modules.
// Highlight.js
require('../../node_modules/highlight.js/styles/atom-one-dark.css');
global.hljs = require('highlight.js');

// KaTeX
require('../../node_modules/katex/dist/katex.css');
global.katex = require('katex');

// Chart.js
const chartjs = require('chart.js');
