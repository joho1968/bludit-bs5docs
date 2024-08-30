<?php
defined( 'BLUDIT' ) || die( 'That did not work as expected.' );
/*
 * bs5docs theme for Bludit
 *
 * page.php (bs5docs)
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

Theme::plugins('pageBegin');

if ( ! $page->isStatic() && ! $url->notFound() && $page->type() !== 'published' && $page->type() !== 'sticky' ) {
    echo '<div class="fs-3 my-5 my-5 p-5 w-100">' .
         $L->get( 'this-post-is-not-available-at-the-moment' ) .
         '</div>';
    Theme::plugins('pageEnd');
    exit;
}
?>
<section>
    <?php
    // Title
    if ( ! $url->notFound() ) {
        if ( $themePlugin ) {
            $show_nav = $themePlugin->showNavigation();
            if ( $show_nav != 'small' && $show_nav != 'always' && $show_nav != 'never' ) {
                $show_nav = 'small';
            } elseif ( $show_nav == 'never' ) {
                $show_nav = false;
            }
        } else {
            $show_nav = 'small';
        }
        if ( $show_nav !== false ) {
            echo '<div class="mt-2 mb-4' . ( $show_nav === 'small' ? ' d-md-none':'' ) . '">';
            echo $nav_html;
            echo '</div>';
        }
        echo '<h1>' . $page->title() . '</h1>';
        echo '<div class="d-none d-lg-block">';
        echo '<div id="bs5docs-toc" class="d-none text-truncate text-nowrap"></div>';
        echo '</div>';
    }
    // Cover image
    if ( $page->coverImage() ) {
        echo '<div class="text-center">';
        echo '<img class="img-thumbnail bs5docs-cover-img p-2" src="' . $page->coverImage() . '" />';
        echo '</div>';
    }
    // Content
    echo '<div class="bs5docs-page-content mt-3 mb-5">' .
         $page->content() .
         '</div>';
    // Check tags
    $post_tags = $page->tags( true );
    if ( ! empty( $post_tags ) ) {
        echo '<div class="bs5docs-page-content-tags small mb-3">';
        foreach( $post_tags as $tag_key => $tag_name ) {
            echo '<a class="badge text-bg-secondary text-decoration-none me-2" href="' .
                 DOMAIN_TAGS . $tag_key . '">' .
                 $tag_name .
                 '</a>';
        }
        echo '</div>';
    }
    // Time
    if ( ! $url->notFound() ) {
        $showDate = true;
        if ( $page->type() === 'static' ) {
            if ( $themePlugin && $themePlugin->dateFormat() == 'hidden' ) {
                $showDate = false;
            }
        }
        if ( $showDate ) {
            // Fetch raw date(s) from DB instead of current @page object
            $db_page = $pages->getPageDB( $page->key() );
            if ( is_array( $db_page ) ) {
                if ( ! empty( $db_page['dateModified'] ) ) {
                    $date_modified = $db_page['dateModified'];
                } else {
                    $date_modified = '';
                }
                if ( ! empty( $db_page['date'] ) ) {
                    $date_raw = $db_page['date'];
                } else {
                    $date_raw = '';
                }
            } else {
                $date_modified = $page->dateModified();
                $date_raw = $page->dateRaw();
            }
            if ( ! empty( $date_modified ) ) {
                $post_time = date_create_immutable( $date_modified );
            } else {
                $post_time = false;
            }
            if ( ! $post_time ) {
                $post_time = date_create_immutable( $date_raw );
            }
            if ( $post_time ) {
                $time_now = date_create_immutable();
                if ( $time_now->format( 'Ymd' ) == $post_time->format( 'Ymd' ) ) {
                    $date_color = 'text-success';
                } else {
                    $date_color = 'text-secondary';
                }
                /*$fmt = datefmt_create( $site->db['locale'], IntlDateFormatter::FULL, IntlDateFormatter::FULL, )*/
                echo '<div class="py-2 small ' . $date_color . ' text-end" title="' . $post_time->format( 'Y-m-d, H:i' ) . '">' .
                     '<span class="me-2">' . '&#x1F4C5;' . '</span>' .
                     '<span class="font-monospace">' .
                     bs5docs_getPostDate( $post_time ) .
                     /*$post_time->format( $site->db['dateFormat'] ) .*/
                     '</span></div>';
            }
        }// $showDate
    }
    ?>
    <div id="showbacktotop" class="text-end d-none">
        <button id="backtotop" role="button"
                class="btn btn-outline-secondary"
                title="<?php echo $L->get( 'back-to-top' ); ?>">&#9650;</button>
    </div>
</section>

<?php Theme::plugins('pageEnd'); ?>
