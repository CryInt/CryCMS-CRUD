<?php
namespace CryCMS\TableView\Templates;

/**
 * @var TableViewInterface $class
 * @var DataProvider $dataProvider
 * @var array $columns
 * @var array $buttons
 */

use CryCMS\DataProvider\DataProvider;
use CryCMS\TableView\Interfaces\TableViewInterface;
use CryCMS\TableView\TableView;
use CryCMS\HTML;
?>
<div class="table-responsive">
    <form action="" method="get">
        <table class="table table-striped table-sm table-hover">
            <?php
            $filters = [];
            foreach ($columns as $field => $properties) {
                if (array_key_exists('filter', $properties)) {
                    $filters[$field] = $properties['filter'];
                    $filters[$field]['value'] = $dataProvider->filter[$field] ?? '';
                    unset($columns[$field]['filter']);
                }

                if (array_key_exists('order', $properties)) {
                    $order = [
                        'href' => $dataProvider->pageHref,
                    ];

                    if (array_key_exists($field, $dataProvider->order)) {
                        $order['direction'] = $dataProvider->order[$field];
                    }

                    $columns[$field]['order'] = $order;
                }
            }

            echo $this->template('list-header', [
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'filters' => $filters,
            ]);

            $list = $dataProvider->get();

            if (method_exists($class, 'preCache')) {
                $class->preCache($list);
            }

            if (!empty($list)) {
                $trs = [];
                foreach ($list as $once) {
                    $tds = [];
                    foreach ($columns as $field => $columnProperties) {
                        if (TableView::checkColumnVisible($dataProvider, $columnProperties) === false) {
                            continue;
                        }

                        $cellProperties = [
                            'class' => TableView::DEFAULT_CELL_CLASS,
                        ];

                        $cellValue = $class->cellPrepare($field, $once);

                        if (
                            array_key_exists('cell', $columnProperties)
                        ) {
                            $cellProperties = $columnProperties['cell'];
                            if (array_key_exists('value', $cellProperties)) {
                                unset($cellProperties['value']);
                            }
                        }

                        $tds[] = HTML::td($cellValue, $cellProperties);
                    }

                    $trProperties = [];

                    if (method_exists($class, 'rowPrepare')) {
                        $trProperties = $class->rowPrepare($once);
                    }

                    $trs[] = HTML::tr(implode(PHP_EOL, $tds), $trProperties);
                }

                echo HTML::tbody(implode(HTML::$afterTag, $trs));
            }
            ?>
        </table>
    </form>
</div>

<div class="container-fluid mt-3 p-0">
    <div class="row">
        <div class="col">
            <?php
            if (!empty($buttons)) {
                $html = [];
                foreach ($buttons as $properties) {
                    $html[] = HTML::a(
                        $properties['title'] ?? '+',
                        $properties['href'] ?? '?create',
                        [
                            'class' => $properties['class'] ?? '',
                            'id' => $properties['id'] ?? '',
                            'type' => 'button'
                        ]
                    );
                }

                echo '' .
                    HTML::div(
                          HTML::div(implode(PHP_EOL, $html), ['class' => 'col'])
                        , ['class' => 'row']
                    );
            }
            ?>
        </div>
        <div class="col">
            <?= $this->template('list-pager', [
                'dataProvider' => $dataProvider,
                'pagerPage' => $dataProvider->page,
                'pagerHref' => $dataProvider->pageHref,
                'withCreate' => true,
            ]) ?>
        </div>
    </div>
</div>
