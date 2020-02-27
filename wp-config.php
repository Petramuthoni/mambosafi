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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mambosaf_wp' );

/** MySQL database username */
define( 'DB_USER', 'mambosaf_wp' );

/** MySQL database password */
define( 'DB_PASSWORD', 'QX28m83mxDzw' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'P6NLEGwNG_$Lv }7=yX#[}S9RkaFL *.!-~)1q[kUCb5Y&)bJJ3Yf;q1S3{yKrZ<' );
define( 'SECURE_AUTH_KEY',   '-Ydchv++pTc_j%[?@mVU/IfsRgW4zDbB nCD~-ilcAN3.({Rrhm5QqRuBtv$7]~{' );
define( 'LOGGED_IN_KEY',     'Qzs9]u7pHUdZUe_jObR6K_oqXkX$Fet9*x)s2jk~F+Ek#r4g|-Q^a82TsYCM1s5F' );
define( 'NONCE_KEY',         '8}QXm&|xf:@B`0_>xw7Wh$2a;uzbI!&Ynp[Kt@wx}5f~i16,xBt-FKr0uFK-W+8{' );
define( 'AUTH_SALT',         'h~HST8vpyxTS&5c43.S7|9E:ym79M5Dvg2=`/.-JsJ5D(]`P0s% yNS46;ba(xWY' );
define( 'SECURE_AUTH_SALT',  'gtI+VMm?1dGL}L {7.uTs0MaOb|O=1z{2;RhZ q^L;%+FM@BFL`Y!YkL<}2mX[X_' );
define( 'LOGGED_IN_SALT',    'Lm2;eul0]z*n`7u0PTu&k=:F4=&oGX!j`xsI[]&?D01;6JXAozF?h`~)H[z|{AeP' );
define( 'NONCE_SALT',        'hTN SaAa$,-G;d@MSdlm&+-LL ACKv[@JzAt={0[<5e*A8C+`#M>)G<`f)L^QrMb' );
define( 'WP_CACHE_KEY_SALT', '`:4$Zn(9]gR;^.;$2]2GGB1Wp3&A6,II|&C$%{m8vJ#U8?]q_P3c E:XY4{B,I^]' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
