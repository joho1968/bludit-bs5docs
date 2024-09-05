<?php
defined( 'BLUDIT' ) || die( 'That did not work as expected.' );
/*
 * bs5docs theme for Bludit
 *
 * home.php (bs5docs)
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

// define( 'BS5DOCS_DEBUG_HOME', true );

if ( defined( 'BS5DOCS_DEBUG_HOME' ) && BS5DOCS_DEBUG_HOME ) {
    define( 'BS5DOCS_DEBUG_EOL', "\n" );
} else {
    define( 'BS5DOCS_DEBUG_EOL', '' );
}
?>

<?php
    $site_logo = $site->logo();
    $site_desc = $site->description();
    $site_slogan = $site->slogan();
    if ( $WHERE_AM_I !== 'search' && ( $site_logo || $site_desc || $site_slogan ) ) {
        echo '<header>';
        echo '<div class="row my-2">';
        echo '<div class="col-12 col-lg-12 mx-auto mt-5 mt-md-4 mt-lg-0 ms-0 ps-0 ps-lg-3 mb-4">';
        if ( ! empty( $site_logo ) && empty( $site_desc ) && empty( $site_slogan ) ) {
            echo '<div class="mx-auto">';
            echo '<img class="img-thumbnail rounded-circle mx-auto d-block bs5docs-logo-img" src="' . $site_logo . '" alt="" />';
            echo '</div>';
        } elseif ( ! empty( $site_desc ) && empty ( $site_logo ) && empty( $site_slogan ) ) {
            echo '<h2 class="h1 ms-5 me-4 text-center mt-0 p-0">';
            echo $site_desc;
            echo '</h2>';
        } elseif ( ! empty( $site_slogan ) && empty( $site_logo ) && empty( $site_desc ) ) {
            echo '<h2 class="h1 ms-5 me-4 text-center mt-0 p-0">';
            echo $site_slogan;
            echo '</h2>';
        } elseif ( ! empty( $site_slogan ) && empty( $site_logo ) && ! empty( $site_desc ) ) {
            echo '<h2 class="ms-5 me-4 text-center mt-0 p-0">';
            echo $site_desc;
            echo '</h2>';
            echo '<h4 class="ms-5 me-4 text-center mt-0 p-0 text-body">';
            echo $site_slogan;
            echo '</h2>';
        } else {
            echo '<div class="d-flex flex-row justify-content-center ms-0 ms-md-2 mx-auto">';
            if ( ! empty( $site_logo ) ) {
                echo '<div class="align-self-center">';
                echo '<img class="img-thumbnail rounded-circle bs5docs-logo-img" src="' . $site_logo . '" alt="" />';
                echo '</div>';
            }
            echo '<div class="align-self-center ms-2">';
            if ( ! empty( $site_desc ) ) {
                echo '<div class="h2 ms-0 ms-lg-4 me-4 text-center align-self-center mt-0 p-0">' . $site_desc . '</div>';
            }
            if ( ! empty( $site_slogan ) ) {
                if ( empty( $site_desc ) ) {
                    echo '<div class="h3 ms-0 ms-lg-4 mt-2 text-center align-self-center mt-0 p-0">' . $site_slogan . '</div>';
                } else {
                    echo '<div class="ms-0 ms-lg-4 mt-2 text-center align-self-center mt-0 p-0">' . $site_slogan . '</div>';
                }
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';// col
        echo '</div>';// row
        echo '</header>';
    }

?>

<section>
    <div class="row">
        <div class="col-12 ms-0 ps-0 ps-lg-3">
            <?php
            // Make sure there's content
            if ( empty( $content ) ) {
                echo '<div class="bs5docs-error-box"><h3>' .
                     $L->get( 'no-pages-found' ) .
                     '</h3></div>';
            }
            ?>

            <?php
            $pageNotFound = $site->pageNotFound();
            // Show pages
            echo '<div>';
            $time_now = date_create_immutable();
            $time_now_string = $time_now->format( 'Ymd' );

            foreach( $content as $post ) {
                if ( $post->isChild() ) {
                    if ( ! empty( $WHERE_AM_I ) && $WHERE_AM_I == 'home' ) {
                        // Skip sub pages on home
                        continue;
                    }
                } elseif ( $post->key() == $pageNotFound ) {
                    // Skip our "Page not found page" in this context
                    continue;
                }
                Theme::plugins('pageBegin');
                // item start
                // echo '<div class="mb-5 border border-secondary p-3 rounded-2">';
                echo '<div class="shadow mb-5 bg-body p-3 rounded-2">' . BS5DOCS_DEBUG_EOL;
                echo '<div class="h5 text-truncate">' . BS5DOCS_DEBUG_EOL .
                     '<a class="link-opacity-50-hover text-decoration-none" href="' . $post->permalink() . '" title="' . $post->title() . '">' .
                     '&raquo;&nbsp;' . $post->title() . '</a>' .
                     '</div>' . BS5DOCS_DEBUG_EOL;
                // Content
                echo '<div class="border-bottom mt-3 mb-3">' . BS5DOCS_DEBUG_EOL;
                if ( ! empty( $WHERE_AM_I ) ) {
                    switch( $WHERE_AM_I ) {
                        case 'page':
                        case 'home':
                            // Only show "full post" on 'page' and 'home'
                            echo $post->contentBreak();
                            if ( $post->readMore() ) {
                                echo '<a class="btn btn-outline-success btn-sm text-decoration-none ms-0" href="' .
                                     $post->permalink() . '" role="button">' . $L->get( 'read-more' ) .
                                     '</a>';
                            }
                            break;
                        case 'search':
                            break;
                        case 'tag':
                            break;
                        case 'category':
                            break;
                    }
                }// ! empty( $WHERE_AM_I )
                echo '</div>' . BS5DOCS_DEBUG_EOL;
                // Post time
                // - fetch raw date(s) from DB instead of current $page object
                $db_page = $pages->getPageDB( $post->key() );
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
                    $date_modified = $post->dateModified();
                    $date_raw = $post->dateRaw();
                }
                if ( ! empty( $date_modified ) ) {
                    $post_time = date_create_immutable( $date_modified );
                } else {
                    $post_time = false;
                }
                if ( ! $post_time ) {
                    $post_time = date_create_immutable( $date_raw );
                }
                if ( $post_time !== false ) {
                    $time_now = date_create_immutable();
                    if ( $time_now_string == $post_time->format( 'Ymd' ) ) {
                        $date_color = 'text-success';
                    } else {
                        $date_color = 'text-body-secondary';
                    }
                    echo '<div class="mb-2 small ' . $date_color . '" title="' . $post_time->format( 'Y-m-d, H:i' ) . '">' . BS5DOCS_DEBUG_EOL;
                    echo '<span class="me-2">' . '&#x1F4C5;' . '</span>' .
                         '<span class="font-monospace small">' .
                         bs5docs_getPostDate( $post_time ) .
                         '</span>';
                    echo '</div>' . BS5DOCS_DEBUG_EOL;
                }
                // Check tags
                $post_tags = $post->tags( true );
                if ( ! empty( $post_tags ) ) {
                    echo '<div class="small">' . BS5DOCS_DEBUG_EOL;
                    foreach( $post_tags as $tag_key => $tag_name ) {
                        echo '<a class="badge text-bg-primary text-decoration-none me-2" href="' .
                             DOMAIN_TAGS . $tag_key . '">' .
                             $tag_name .
                             '</a>';
                    }
                    echo '</div>' . BS5DOCS_DEBUG_EOL;
                }
                // item end
                echo '</div>' . BS5DOCS_DEBUG_EOL;
                Theme::plugins('pageEnd');
            }// foreach

            echo '</div>' . BS5DOCS_DEBUG_EOL;

            //Pagination
            if ( Paginator::numberOfPages() > 1 ) {
                echo '<nav aria-label="Page navigation">';
                echo '<ul class="pagination">';
                if ( Paginator::showPrev() ) {
                    echo '<li class="page-item mr-2">' .
                         '<a class="page-link" href="' . Paginator::previousPageUrl() . '" tabindex="-1" title="' . $L->get( 'previous' ) . '" aria-label="' . $L->get( 'previous' ) . '" . >' .
                         '<span aria-hidden="true">&laquo;</span>' .
                         '</a>'.
                         '</li>';
                }
                echo '<li class="page-item mr-2' . ( Paginator::currentPage() == 1 ? ' disabled':'' ) . '">' .
                     '<a class="page-link" href="' . Theme::siteUrl() . '" title="' . $L->get( 'home') . '" aria-label="' . $L->get( 'home' ) . '"><span aria-hidden="true">&#x1F3E0;</span></a>' .
                     '</li>';
                if ( Paginator::showNext() ) {
                    echo '<li class="page-item mr-2">' .
                         '<a class="page-link" href="' . Paginator::nextPageUrl() . '" tabindex="-1" title="' . $L->get( 'next' ) . '" aria-label="' . $L->get( 'next' ) . '">' .
                         '<span aria-hidden="true">&raquo;</span>' .
                         '</a>' .
                         '</li>';
                }
                echo '</ul></nav>';
            }
            ?>
        </div>
    </div>
</section>
