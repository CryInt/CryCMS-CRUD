<?php
/**
 * @var DataProvider $dataProvider
 * @var $pagerPage
 * @var $pagerHref
 */

use CryCMS\DataProvider\DataProvider;
use CryCMS\HTML;

$count = $dataProvider->dataCount;
$onPage = $dataProvider->limit;

if (empty($onPage)) {
    return;
}

$pages = ceil($count / $onPage);
?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <?php
        $i = 1;
        $end = $pages;
        $n = 2;

        if ($pages > 1) {
            if ( $pagerPage - ( $n - 1 ) > 0 ) {
                $i = $pagerPage - ($n - 1);
            }

            if ( $pagerPage + ( $n + 1 ) <= $pages ) {
                $end = $pagerPage + ($n + 1);
            }

            if ($pagerPage > 0) {
                echo
                HTML::li(
                    HTML::a(
                        HTML::span('&laquo;', ['area-hidden' => 'true']),
                        $pagerHref . ($pagerPage - 1),
                        [
                            'class' => 'page-link text-dark',
                            'aria-label' => 'Previous'
                        ]
                    ), [
                        'class' => 'page-item'
                    ]
                );
            }

            if ($i > 1) {
                echo
                HTML::li(
                    HTML::a('1', $pagerHref . '0', ['class' => 'page-link text-dark']),
                    ['class' => 'page-item']
                );
            }

            if ($pagerPage > $n + 2  && $i >= $n) {
                echo
                HTML::li(
                    HTML::a(($i === $n) ? '2' : "&hellip;", $pagerHref . ($i - 2), ['class' => 'page-link text-dark']),
                    ['class' => 'page-item']
                );
            }

            if ($pagerPage === $n + 2) {
                $i--;
            }

            for ( ; $i <= $end ; $i++ ) {
                if ($i !== $pagerPage + 1) {
                    echo
                    HTML::li(
                        HTML::a($i, $pagerHref . ($i - 1), ['class' => 'page-link text-dark']),
                        ['class' => 'page-item']
                    );
                }
                else {
                    echo HTML::li(
                        HTML::span($i, ['class' => 'page-link text-dark']),
                        ['class' => 'page-item fw-bold']
                    );
                }
            }

            if ($pagerPage < $pages - ($n + 2) && $end <= $pages - ( $n - 1 )) {
                echo
                HTML::li(
                    HTML::a(($end === $pages - 2) ? $pages - 1 : '&hellip;', $pagerHref . ($i - 1), ['class' => 'page-link text-dark']),
                    ['class' => 'page-item']
                );
            }

            if ($end < $pages) {
                echo
                HTML::li(
                    HTML::a($pages, $pagerHref . ($pages - 1), ['class' => 'page-link text-dark']),
                    ['class' => 'page-item']
                );
            }

            if ($pagerPage < ($pages - 1)) {
                echo
                HTML::li(
                    HTML::a(
                        HTML::span('&raquo;', ['area-hidden' => 'true']),
                        $pagerHref . ($pagerPage + 1),
                        [
                            'class' => 'page-link text-dark',
                            'aria-label' => 'Next'
                        ]
                    ), [
                        'class' => 'page-item'
                    ]
                );
            }
        }
        ?>
    </ul>
</nav>