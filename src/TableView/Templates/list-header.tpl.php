<?php
/**
 * @var $dataProvider
 * @var $columns
 * @var $filters
 */

use CryCMS\TableView\TableView;
use CryCMS\HTML;

$ths = $tds = [];

$filterIsset = false;

if (!empty($columns)) {
    $lastColumn = array_key_last($columns);

    foreach ($columns as $key => $once) {
        if (TableView::checkColumnVisible($dataProvider, $once) === false) {
            continue;
        }

        $properties = $once;
        unset($properties['title']);

        if (array_key_exists('order', $properties)) {
            if (is_array($properties['order']) && array_key_exists('direction', $properties['order'])) {
                $once['title'] .= HTML::i(
                    '',
                    ['class' => 'bi ' . ($properties['order']['direction'] === 'ASC' ? 'bi-caret-up-fill' : 'bi-caret-down-fill')]
                );
            }

            $href = TableView::getOrderHref($properties['order'], $key);

            $once['title'] = HTML::a($once['title'], $href, ['class' => 'text-decoration-none text-dark']);
            TableView::addClass($properties, 'text-nowrap');
            unset($properties['order']);
        }

        $ths[] = HTML::th($once['title'], $properties);

        $filterElement = '';
        if (!empty($filters[$key])) {
            $filterData = $filters[$key];
            $filterElement = HTML::input('filter[' . $key . ']', $filterData['value'] ?? '', [
                'type' => 'text',
                'class' => 'form-control font-size-12 p-1' . ($key === $lastColumn ? ' w-75' : ''),
            ]);

            $filterIsset = true;
        }

        if ($key === $lastColumn) {
            $filterElement .= HTML::input('', 'FIND', [
                'type' => 'submit',
                'class' => 'btn btn-primary font-size-12 position-absolute',
                'style' => 'top: 5px; right: 5px; height: 27px; padding: 0px 10px;',
            ]);
        }

        $tds[] = HTML::td($filterElement, ['class' => 'position-relative']);
    }
}

echo HTML::thead(
    HTML::tr(
        implode(PHP_EOL, $ths)
    )
);

if ($filterIsset === true) {
    echo HTML::tbody(
        implode(PHP_EOL, $tds), ['class' => 'table-light']
    );
}
