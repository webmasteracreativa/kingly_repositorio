<?php
define('WP_CACHE', false);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'kingly_wp80');

/** MySQL database username */
define('DB_USER', 'kingly_wp80');

/** MySQL database password */
define('DB_PASSWORD', '8Pp.8SG[e2');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'oxgijn1v7f1rpfykworhggduewdooxqsfopvgvbzviygmv0fal0as0xmp5hvj4kf');
define('SECURE_AUTH_KEY',  'iyd3zruotkjmalqckkdrcrwrpopasgnwmcibdxammc3mykqzimsml0oywcwgngaj');
define('LOGGED_IN_KEY',    'nwcs3qek1ghaey3xeyadivv7kmrlm8l49ekmbscvf1iizljvak5g4ymvujv3ymc2');
define('NONCE_KEY',        's94yrdnnitzgajcbqeplmvidqagmkwn5utnfihigjsufbigasf3eqgqnnpt0l0ns');
define('AUTH_SALT',        'tl8zdlvjbljdvgb04lltzyjxwaay8flieweqluor34teiisokqm7foewwdo84uqk');
define('SECURE_AUTH_SALT', 'ivlhocljkuyynqqyrczfdqikio0vzrmngrwhkh5hudwzmde0luacp4u3fpis8pd9');
define('LOGGED_IN_SALT',   '67av7bbpcl0lbaznsranu2qetpysixrekwuga6w6kr7xuf7khk5zrdl7k6vjntep');
define('NONCE_SALT',       'hui1rfs4b0lcs5mvcijntevlzurshkijqcqrql3otij8pg0uue2pyjzh6xdsrvxe');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpf3_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
