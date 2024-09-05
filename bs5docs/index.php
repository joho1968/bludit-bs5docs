<?php
defined( 'BLUDIT' ) || die( 'That did not work as expected.' );
/*
 * bs5docs theme for Bludit
 *
 * index.php (bs5docs)
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
echo '<!doctype html>' . "\n";
if ( $themePlugin ) {
    switch( $themePlugin->colorMode() ) {
        case 'light': $colorMode = 'light'; $bsThemeAuto = false; break;
        case 'dark': $colorMode = 'dark'; $bsThemeAuto = false; break;
        default: $colorMode = 'auto'; $bsThemeAuto = true; break;
    }
    echo '<html lang="' . $language->currentLanguageShortVersion() . '" data-bs-theme="' . $colorMode . '">' . "\n";
} else {
    echo '<html lang="' . $language->currentLanguageShortVersion() . '" data-bs-theme="auto">' . "\n";
    $bsThemeAuto = true;
}
?>
<head>
<meta charset="<?php if ( defined('CHARSET') && ! empty( CHARSET ) ) { echo CHARSET; } else { echo 'UTF-8'; } ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    if ( $WHERE_AM_I == 'page' ) {
        $page_title = $page->title();
        if ( empty( $page_title ) ) {
            $page_title = $site->title();
        } else {
            $page_title .= ' | ' . $site->title();
        }
    } else {
        $page_title = $site->title();
    }
    echo '<title>' . $page_title . '</title>' . "\n";
    if ( $WHERE_AM_I == 'page' ) {
        if ( $page && $page->description() && ! empty( $page->description() ) ) {
            $page_description = $page->description();
        } else {
            $page_description = $site->description();
        }
    } else {
        $page_description = '';
    }
    echo '<meta name="description" content="' . $page_description . '">' . "\n";
    echo Theme::favicon( 'res/img/favicon.png' );
    echo '<link rel="stylesheet" type="text/css" href="' . DOMAIN_THEME . 'res/css/bootstrap.min.css">' . "\n";
    echo '<link rel="stylesheet" type="text/css" href="' . DOMAIN_THEME . 'css/bs5docs.css">' . "\n";
    Theme::plugins( 'siteHead' );
?>
<?php
    if ( $bsThemeAuto ) {
?>
    <script>
        (() => {
            'use strict';
            const colorMode = window.matchMedia("(prefers-color-scheme: dark)").matches ?
                "dark" :
                "light";
            document.querySelector("html").setAttribute("data-bs-theme", colorMode);
        })();
    </script>
<?php
    }
?>
</head>

<?php
require_once( THEME_DIR_PHP . 'functions.inc.php' );

// Try to sort strings properly if we can
if ( class_exists( 'Collator' ) ) {
    $site_locale = bs5docs_getLocale();
    $collator = new Collator( $site_locale );
    if ( is_object( $collator ) ) {
        $locale = $collator->getLocale( Locale::VALID_LOCALE );
        if ( ! empty( $locale ) && $locale != 'root' && $locale != $site_locale ) {
            if ( function_exists( 'locale_set_default' ) ) {
                locale_set_default( $locale );
            }
        }
    }
} else {
    $collator = false;
}
function sortMenuChildren( $a, $b ) {
    global $collator;
    if ( $a->position() === $b->position() ) {
        if ( is_object( $collator) ) {
            $cmp = $collator->compare( $a->title(), $b->title() );
            if ( $cmp === false ) {
                $cmp = strcmp( $a->title(), $b->title() );
            }
            return( $cmp );
        }
        return( strcmp( $a->title(), $b->title() ) );
    }
    return ( $a->position() - $b->position() );
}

// Build navigation
$menu_html = '';
$is_first = true;
$page_not_found = $site->pageNotFound();
$doc_pages = buildStaticPages();
$bs5docs_navigation = array();
$bs5docs_navcount = 0;
$bs5docs_nav_active = -1;
if ( ! empty( $doc_pages ) ) {
    foreach( $doc_pages as $sp ) {
        if ( $sp->key() == $page_not_found ) {
            continue;
        }
        if ( ! $sp->hasChildren() ) {
            continue;
        }
        if ( ! $sp->isChild() ) {
            if ( $is_first ) {
                $menu_html .= '<nav>';
                $is_first = false;
            }
            $item_title = $sp->title();
            if ( empty( $item_title ) ) {
                $item_title = $L->get( 'no-title' );
            }
            $menu_html .= '<div class="mt-3 ms-2 text-uppercase bs5docs-menu-header">' . $item_title . '</div>';
            $is_first_child = true;

            // Sort children based on position and title (if position is same)
            $children = array();
            foreach( $sp->children() as $child ) {
                $children[] = $child;
            }
            usort( $children, 'sortMenuChildren' );
            $child_count = 0;
            foreach( $children as $child ) {
                if ( $is_first_child ) {
                    $menu_html .= '<ul class="nav flex-column">';
                    $is_first_child = false;
                }
                if ( $child->key() == $url->slug() ) {
                    $is_active = ' active-nav';
                    $is_active_aria = ' aria-current="page"';
                    $bs5docs_navigation[$bs5docs_navcount] = array( 'url' => $child->permaLink(), 'active' => true );
                    $bs5docs_nav_active = $bs5docs_navcount;
                } else {
                    $is_active = '';
                    $is_active_aria = '';
                    $bs5docs_navigation[$bs5docs_navcount] = array( 'url' => $child->permaLink() );
                }
                $menu_html .= '<li class="nav-item ms-2 bs5docs-menu-item">';
                $menu_html .= '<a' . $is_active_aria . ' class="nav-link' . $is_active . '" title="' . $child->title() . '" href="' . $child->permalink() . '">' . $child->title() . '</a>';
                $menu_html .= '</li>'. "\n";
                $bs5docs_navcount++;
                //$bs5docs_navigation['previous'] = array( 'title' => $child->title(), 'permalink' => $child->permalink() );
            }// foreach
            if ( ! $is_first_child ) {
                $menu_html .= '</ul>';
            }
        }
    }// foreach
    if ( ! $is_first) {
        $menu_html .= '</nav>' . "\n";
    }
    // Figure out navigation
    $bs5docs_nav_prev = -1;
    $bs5docs_nav_next = -1;
    $nav_html = '';
    if ( $bs5docs_navcount > 0 && $bs5docs_nav_active >= 0 ) {
        $nav_html =
            '<nav aria-label="' . $L->get( 'page-navigation') . '">' .
            '<ul class="pagination pagination-sm">';
        if ( $bs5docs_nav_active > 0 ) {
            $nav_html .=
                '<li class="page-item mr-2">' .
                '<a class="page-link" href="' . $bs5docs_navigation[ $bs5docs_nav_active - 1 ]['url'] . '" tabindex="-1" title="' . $L->get( 'previous' ) . '" aria-label="' . $L->get( 'previous' ) . '" . >' .
                '<span aria-hidden="true">&laquo;</span>' .
                '</a>'.
                '</li>';
            $bs5docs_nav_prev = ( $bs5docs_nav_active - 1 );
        } else {
            $nav_html .=
                '<li class="page-item mr-2 disabled">' .
                '<a class="page-link" href="#" tabindex="-1" title="' . $L->get( 'previous' ) . '" aria-label="' . $L->get( 'previous' ) . '" . >' .
                '<span aria-hidden="true">&laquo;</span>' .
                '</a>'.
                '</li>';
        }
        $nav_html .=
            '<li class="page-item mr-2' . ( $bs5docs_nav_active === 0 ? ' disabled':'' ) . '">' .
            '<a class="page-link" href="' . $bs5docs_navigation[0]['url'] . '" title="' . $L->get( 'home') . '" aria-label="' . $L->get( 'home' ) . '"><span aria-hidden="true">&#x1F3E0;</span></a>' .
            '</li>';
        if ( $bs5docs_nav_active < ( $bs5docs_navcount - 1 ) ) {
            $nav_html .=
                '<li class="page-item mr-2">' .
                '<a class="page-link" href="' . $bs5docs_navigation[ $bs5docs_nav_active + 1 ]['url'] . '" tabindex="-1" title="' . $L->get( 'next' ) . '" aria-label="' . $L->get( 'next' ) . '">' .
                '<span aria-hidden="true">&raquo;</span>' .
                '</a>' .
                '</li>';
            $bs5docs_nav_next = ( $bs5docs_nav_active + 1 );
        } else {
            $nav_html .=
                '<li class="page-item mr-2 disabled">' .
                '<a class="page-link" href="#" tabindex="-1" title="' . $L->get( 'next' ) . '" aria-label="' . $L->get( 'next' ) . '">' .
                '<span aria-hidden="true">&raquo;</span>' .
                '</a>' .
                '</li>';
        }
        $nav_html .= '</ul></nav>';
    }
    // Site footer
    $footer = $site->footer();
    if ( ! empty( $footer ) ) {
        $menu_html .= '<div class="p-2 text-center text-secondary bs5docs-footer">' . $footer . '</div>';
    }
    $hideCredits = false;
    if ( $themePlugin && $themePlugin->hideCredits() ) {
        $hideCredits = true;
    }
    if ( ! $hideCredits ) {
        $menu_html .= '<div class="p-2 mt-5 text-center text-secondary opacity-50" style="font-size: 0.75em !important" title="Powered by BS5Docs and Bludit">' .
                      'Powered by BS5Docs and Bludit' .
                      '</div>';
    }
}
?>


<body class="bg-body">
    <script>
        (() => {
            'use strict';

            var scrollTimer = null;
            var backToTop = null;


            // Set theme to the user's preferred color scheme
            function updateBootstrapTheme() {
                const colorMode = window.matchMedia("(prefers-color-scheme: dark)").matches ?
                    "dark" :
                    "light";
                document.querySelector("html").setAttribute("data-bs-theme", colorMode);
            }
            function documentSetup() {
                let navScrollMode = 'smooth';
                const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
                if (motionQuery.matches) {
                    navScrollMode = 'instant';
                }
                let activeNav = document.getElementsByClassName("active-nav");
                if (activeNav && activeNav[0]) {
                    let navTimer = setTimeout(() => {
                      activeNav[0].scrollIntoView({ behavior: navScrollMode, block: "center", inline: "nearest" });
                    }, 5);
                }
                // Update theme when the preferred scheme changes
                <?php
                if ( $bsThemeAuto ) {
                ?>
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateBootstrapTheme);
                <?php
                }
                ?>
                // Figure out light/dark mode
                let colorMode = window.matchMedia("(prefers-color-scheme: dark)").matches ?
                    "dark" :
                    "light";
                const offCanvas = document.getElementById('offcanvasMenu');
                offCanvas.addEventListener('shown.bs.offcanvas', event => {
                    document.getElementsByClassName("offcanvas-scroll")[0].scrollIntoView({ behavior: "instant", block: "center", inline: "nearest" });
                });
                const contentDiv = document.getElementById('content-area-inner');
                backToTop = document.getElementById('showbacktotop');
                <?php
                if ( $themePlugin && $themePlugin->useHighlightJS() ) {
                ?>
                if (contentDiv) {
                  hljs.highlightAll();
                  const blocks = contentDiv.querySelectorAll('pre code.hljs');
                  Array.prototype.forEach.call(blocks, function(block) {
                    var language = block.result.language;
                    block.insertAdjacentHTML('afterbegin', `<label>${language}</label>`);
                  });
                }
                <?php
                }
                $showTOC = true;
                if ( $themePlugin && ! $themePlugin->showTOC() ) {
                    $showTOC = false;
                }
                if ( $WHERE_AM_I == 'page' && $showTOC ) {
                ?>
                if (contentDiv) {
                  const headings = contentDiv.querySelectorAll('h2');
                  if (headings && headings.length > 1) {
                    let hData = '';
                    let tocID = 1;
                    headings.forEach((heading) => {
                      let theId = heading.getAttribute('id');
                        if (!theId) {
                          theId = tocID++;
                          heading.setAttribute('id', theId);
                        }
                        if (hData.length == 0) {
                            hData = '<h6 class="fw-bold"><?php echo $L->get('on-this-page'); ?></h6>';
                        }
                        hData += '<li><a class="bs5toc-link" title="' + heading.innerText + '" href="#' +theId + '">' + heading.innerText + '</a></li>';
                    });
                    if (hData.length>0) {
                      let e = document.getElementById('bs5docs-toc');
                      if (e) {
                        e.innerHTML = '<ul>' + hData + '</ul>';
                        e.classList.remove('d-none');
                        }
                    }
                  }
                  let btotop = document.getElementById('backtotop');
                  if (btotop) {
                    btotop.addEventListener('click', function(ev) {
                      ev.preventDefault();
                      let a = document.getElementById('content-area');
                      if (a) {
                          a.scrollTop = 0;
                      }
                    });
                  }

                  let a = document.getElementById('content-area');
                  a.addEventListener('scroll', function(e) {
                    if (scrollTimer != null) {
                      window.clearTimeout(scrollTimer);
                    }
                    scrollTimer = setTimeout(() => {
                      if (this.scrollTop>0) {
                          if (backToTop) {
                              backToTop.classList.remove('d-none');
                          }
                      } else {
                          if (backToTop) {
                              backToTop.classList.add('d-none');
                          }
                      }
                    }, 1500);
                  });
                  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                      e.preventDefault();
                      this.scrollIntoView({ behavior: "smooth", inline: "nearest" });
                    });
                  });
                }
                <?php
                }// page
                ?>
                // Some more BS initialization
                const popoverTriggerList = document.querySelectorAll('[data-bs-toggle=\"popover\"]');
                const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }
            if (document.readyState === 'complete' ||
                    (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
                documentSetup();
            } else {
                document.addEventListener('DOMContentLoaded', documentSetup);
            }
        })();
    </script>

    <nav class="navbar navbar-expand bg-body-secondary fixed-top">
        <div class="container-fluid d-flex justify-content-end">
                <button class="navbar-toggler d-block d-md-none me-2"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                        aria-controls="offcanvasMenu" aria-label="<?php echo $L->get( 'Navigation' ); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <a class="navbar-brand text-truncate flex-grow-1" href="<?php echo $site->url(); ?>">
                    <span><?php echo $site->title(); ?></span>
                </a>

                <div class="navbar-collapse flex-grow-0 flex-shrink-1 me-0">
                    <ul class="navbar-nav">
                      <li class="nav-item dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                          <?php echo $L->get( 'more' ); ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 me-3 shadow bs5docs-more-menu">
                            <?php
                            echo '<div class="bs5offcanvas-section">';
                            Theme::plugins('siteSidebar');
                            echo '</div>';
                            ?>

                            <?php
                            // RSS
                            if ( Theme::rssUrl() ) {
                                echo '<div class="bs5offcanvas-section"><div class="h5">RSS</div>';
                                echo '<a class="text-decoration-none ms-2" href="' .
                                     Theme::rssUrl() . '" target="_blank" role="button" title="' . Theme::rssUrl() . '">' .
                                     '<img class="img-thumbnail bs5socialmedia-icon" loading="lazy" src="' . DOMAIN_THEME . 'res/img/rss.png' . '" alt="RSS" />' .
                                     '</a>';
                                echo '</div>';
                            }
                            ?>

                            <?php
                            // Social networks
                            if ( ! empty( Theme::socialNetworks() ) ) {
                            ?>
                            <div class=" bs5offcanvas-section">
                              <div class="h5"><?php echo $L->get( 'Social media' ); ?></div>
                              <div class="d-inline-flex flex-row ms-2 flex-wrap">
                              <?php
                              foreach( Theme::socialNetworks() as $key => $label ) {
                              ?>
                                  <div class="p-1">
                                      <a class="text-decoration-none" href="<?php echo $site->{$key}(); ?>" target="_blank">
                                          <img class="img-thumbnail bg-light bs5socialmedia-icon" loading="lazy" src="<?php echo DOMAIN_THEME . 'res/img/' . $key . '.png' ?>" alt="<?php echo $label ?>" title="<?php echo $site->{$key}(); ?>" />
                                      </a>
                                  </div>
                              <?php
                              }
                              ?>
                              </div>
                            </div>
                          <?php
                          }
                          ?>
                        </div>
                      </li>
                    </ul>
                </div>

        </div>
    </nav>

    <div class="container-fluid h-100" style="padding-top: 56px;" id="mainbox">
        <div class="row h-100">
            <!-- Menu Area -->
            <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-3 d-none d-md-block menu-area p-2 border-end border-secondary-subtle border-2 bg-body-tertiary">
                <?php
                echo $menu_html;
                ?>
            </div>

            <div class="offcanvas offcanvas-start d-md-none bg-body bs5docs-notransition" tabindex="-1" id="offcanvasMenu">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasMenuLabel"><?php echo $L->get('navigation'); ?></h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="offcanvas"
                            aria-label="<?php echo $L->get('close'); ?>"></button>
                </div>
                <div class="offcanvas-body menu-area p-1">
                    <?php
                    echo preg_replace( '/' . ' active-nav' . '/', ' active-nav offcanvas-scroll', $menu_html );
                    ?>
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-8 col-xl-8 col-xxl-9 content-area bg-body" id="content-area">
                <div class="container-fluid container-md mt-3 p-1 ms-md-0 page-content" id="content-area-inner">
                    <?php
                    if ( $WHERE_AM_I == 'page' ) {
                        require_once( THEME_DIR_PHP . 'page.php' );
                    } else {
                        require_once( THEME_DIR_PHP . 'home.php' );
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <?php
    echo '<script src="' . DOMAIN_THEME . 'res/js/bootstrap.bundle.min.js" defer></script>';
    if ( $themePlugin && $themePlugin->useHighlightJS() ) {
        echo '<script src="' . DOMAIN_THEME . 'res/js/highlight.min.js" defer></script>';
    }
    Theme::plugins( 'siteBodyEnd' );
    ?>
</body>
</html>
