<?php
/**
 * HSL shim
 * Converts HSL values into hex code that works in all browsers
 * 
 * @before
 *     color: hsl( 100, 50%, 50% )
 * 
 * @after
 *    color: #6abf40
 */

csscrush_plugin::register( 'hsl-to-hex', array(
    'enable' => 'csscrush__enable_hsl_to_hex',
    'disable' => 'csscrush__disable_hsl_to_hex',
));

function csscrush__enable_hsl_to_hex () {
    csscrush_hook::add( 'rule_postalias', 'csscrush__hsl_to_hex' );
}

function csscrush__disable_hsl_to_hex () {
    csscrush_hook::remove( 'rule_postalias', 'csscrush__hsl_to_hex' );
}

function csscrush__hsl_to_hex ( csscrush_rule $rule ) {

    foreach ( $rule as &$declaration ) {
        if ( 
            ! $declaration->skip &&
            ( ! empty( $declaration->functions ) && in_array( 'hsl', $declaration->functions ) )
        ) {
            while ( preg_match( '!hsl(\?p\d+\?)!', $declaration->value, $m ) ) {
                $full_match = $m[0];
                $token = $m[1];
                $hsl = trim( csscrush::$process->tokens->p[ $token ], '()' );
                $hsl = array_map( 'trim', explode( ',', $hsl ) );
                $rgb = csscrush_color::cssHslToRgb( $hsl );
                $hex = csscrush_color::rgbToHex( $rgb );
                $declaration->value = str_replace( $full_match, $hex, $declaration->value );
            }
        }
    }
}
