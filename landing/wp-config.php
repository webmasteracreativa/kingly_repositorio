<?php
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
define( 'DB_NAME', 'kingly_form' );

/** MySQL database username */
define( 'DB_USER', 'kingly_wp80' );

/** MySQL database password */
define( 'DB_PASSWORD', '8Pp.8SG[e2' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '|(ooZ/V }Wp- lu44{2T,}>`aH9DrQ#p 5nS<_^~#cQ7C~RI#Q3<%v=wkOsTA#HV' );
define( 'SECURE_AUTH_KEY',  '0+76pv[uVh^kA?N}rB ;Z2@^)IyoewDSr_IbF)5y3J&$!gN(*`5umqoFO7SrIz$B' );
define( 'LOGGED_IN_KEY',    'IAU,2:bCN]N3dPm=lK6B_I#N@cv|*`FgIJp hC v$Q^KMP4BJ_Gr{/R%N8k*D?JT' );
define( 'NONCE_KEY',        '@O$-3fnm?Bc<Y-+:jb[t2@Y(rOm1w+;CvadH2<P]T+`hd}2L?&{$ptQU+:./4;}i' );
define( 'AUTH_SALT',        '/oR7=G}yD lavR~t6q4p/{}Q/[,db[h7p4$.(yp<UX*H.m^.[[JRqZ+yI(f@ysAd' );
define( 'SECURE_AUTH_SALT', 'g9h^G8CM6(0EL=$Vx2&~R:Nt*ekNW~U/I0*cF9q$+:-x}w3~j&:f)UTxdfv,#h]R' );
define( 'LOGGED_IN_SALT',   'NUc?ToynaSF rZ_$;palEhn1}no6vu&iMX5h8$sCg.y9wXQ8Y]|as$Nb@D?>fnsm' );
define( 'NONCE_SALT',       'l;LGJ6YTSowR@>#zwX8`GAmB..-Um|T2)au|f79$7%Iy|=6Qoy~@nXCozcn_tfj#' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
