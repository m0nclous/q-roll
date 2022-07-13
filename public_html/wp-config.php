<?php

/** The name of the database for WordPress */
define('DB_NAME', 'm0nclous_qroll');

/** Database username */
define('DB_USER', 'm0nclous_qroll');

/** Database password */
define('DB_PASSWORD', 'UrYx&r2H');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+ Authentication unique keys and salts. */
define('AUTH_KEY',         ',@Gv1g|b:::?9`d hRfLnvTP0:)AK-#0o#YWraY0b=WO,:+^74YE_r/_Mf.ODNH?');
define('SECURE_AUTH_KEY',  '6a?j4s*1tUip?&<iMq$}a|kD}3j|Kd|OBB4q0+|a5|)oC(am>ic,z-M-s?U=3HPg');
define('LOGGED_IN_KEY',    '~,:V0s|u`%PS,0 B$G3{+4d1IyE:gt[nOS|N<wOitx&u5XLUd-`>v0EgwU4t{q%k');
define('NONCE_KEY',        '/B<b5SQ-;9N)&Hel7p=?|an2K/*+|oSm8f+>`_x(rz$+{MCS+aK#->TJ}/CqZ3k/');
define('AUTH_SALT',        'g~(^r%fpfulgY-ex$+W25Cd~H_gQ$}dul[4#[N+_Ol<2L%{ :j;h+[JAyFg)e(hd');
define('SECURE_AUTH_SALT', ' XH7=4W+L$*Y6{w-j[sz[By7A8:$z-(Z|.@zFgmdPlHQ|?(j<Z#/?+LWC(F9G3Uk');
define('LOGGED_IN_SALT',   ';>J8DI,I?U|-a`aJW7U7>3|F5[q0Vc-iN>o5vV`ARdnMZ+JdBya3xB?2vA`vZY!$');
define('NONCE_SALT',       'R,9T-).e/>)v jD]XNGYfl(XcY,cb*9MlN6.-io{l493dzuJ]DEp+|`fn<F`qkZx');
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/** Кэширование Redis */
define('WP_CACHE_KEY_SALT', 'q-roll.ru:');








/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
