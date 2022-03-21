const path = require('path');
const miniCss = require('mini-css-extract-plugin');

module.exports = {
	mode: 'production',
	entry: './q-roll-src/index.js',

	output: {
		path: path.resolve(__dirname, 'public_html/wp-content/themes/q-roll', 'assets'),
		filename: 'script.js'
	},

	module: {
		rules: [
			{
				test: /\.(s*)css$/i,
				use: [
					miniCss.loader,
					'css-loader',
					{
						loader: "sass-loader",
						options: {
							implementation: require('dart-sass')
						}
					}
				]
			}
		]
	},

	plugins: [
		new miniCss({
			filename: '../style.css',
		}),
	]
};
