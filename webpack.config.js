const path = require('path');
const miniCss = require('mini-css-extract-plugin');

module.exports = {
	mode: 'development',
	entry: './q-roll-src/index.js',

	output: {
		path: path.resolve(__dirname, 'public_html/wp-content/themes/q-roll', 'assets'),
		filename: 'scripts.js'
	},

	module: {
		rules: [{
			test: /\.scss$/,
			use: [
				miniCss.loader,
				'css-loader',
				'sass-loader'
			]
		}]
	},

	plugins: [
		new miniCss({
			filename: '../style.css',
		}),
	]
};
