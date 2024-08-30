<?php
defined( 'BLUDIT' ) || die( 'That did not work as expected.' );
/*
 * bs5docs theme for Bludit
 *
 * functions.inc.php (bs5docs)
 * Copyright 2024 Joaquim Homrighausen; all rights reserved.
 * Development sponsored by WebbPlatsen i Sverige AB, www.webbplatsen.se
 *
 * This file is part of bs5docs. bs5docs is free software.
 *
 * bs5docs is free software: you may redistribute it and/or modify it  under
 * the terms of the GNU AFFERO GENERAL PUBLIC LICENSE v3 as published by the
 * Free Software Foundation.
 *
 * bs5docs is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU AFFERO GENERAL PUBLIC LICENSE
 * v3 for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE v3
 * along with the bs5docs package. If not, write to:
 *  The Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor
 *  Boston, MA  02110-1301, USA.
 */

$bs5docs_locale = '';
$bs5docs_timezone = '';
$bs5docs_datefmt = null;

function bs5docs_getLocale() {
    global $site;

    if ( $site->locale() ) {
        $locales = explode( ',', $site->locale() );
        $longest_locale = '';
        foreach( $locales as $single_locale ) {
            if ( strlen( $single_locale ) > strlen( $longest_locale ) ) {
                $longest_locale = $single_locale;
            }
        }
        $our_locale = trim( $longest_locale );
        if ( empty( $our_locale ) ) {
            $our_locale = 'en_US';
        }
    } else {
        $our_locale = 'en_US';
    }
    return( $our_locale );
}

function bs5docs_getPostDate( $post_time, $date_format = false ) {
    global $site;
    global $bs5docs_locale;
    global $bs5docs_timezone;
    global $bs5docs_datefmt;
    global $themePlugin;

    if ( empty( $bs5docs_locale ) ) {
        $bs5docs_locale = bs5docs_getLocale();
    }
    if ( empty( $bs5docs_timezone ) ) {
        if ( $site->timezone() ) {
            $bs5docs_timezone = $site->timezone();
        } else {
            $bs5docs_timezone = 'Europe/Berlin';
        }
    }

    $return_date = '';
    if ( class_exists( 'Error' ) && class_exists( 'IntlDateFormatter' ) ) {
        if ( $bs5docs_datefmt === null ) {
            try {
                if ( $date_format === false ) {
                    $date_format = IntlDateFormatter::LONG;
                }
                if ( $themePlugin ) {
                    switch( $themePlugin->dateFormat() ) {
                        case 'long':
                            $date_format = IntlDateFormatter::LONG;
                            break;
                        case 'medium':
                            $date_format = IntlDateFormatter::MEDIUM;
                            break;
                        case 'short':
                            $date_format = IntlDateFormatter::SHORT;
                            break;
                        case 'full':
                            $date_format = IntlDateFormatter::FULL;
                            break;
                    }// switch
                }
                $bs5docs_datefmt = new IntlDateFormatter( $bs5docs_locale,
                                                          $date_format,
                                                          IntlDateFormatter::NONE,
                                                          $bs5docs_timezone );
                if ( ! is_object( $bs5docs_datefmt ) ) {
                    $bs5docs_datefmt = false;
                }
            } catch( \Error $e ) {
                $bs5docs_datefmt = false;
            }
        }
        if ( is_object( $bs5docs_datefmt ) ) {
            try {
                $return_date = $bs5docs_datefmt->format( $post_time );
                if ( $return_date === false ) {
                    $bs5docs_datefmt = false;
                }
            } catch( \Error $e ) {
                $bs5docs_datefmt = false;
                $return_date = '';
            }
        }
    }
    if ( empty( $return_date ) ) {
        $return_date = $post_time->format( $site->db['dateFormat'] );
    }
    return( $return_date );
}
