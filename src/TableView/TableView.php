<?php
namespace CryCMS\TableView;

use CryCMS\CRUDHelper;
use CryCMS\DataProvider\DataProvider;
use CryCMS\TableView\Interfaces\TableViewInterface;
use RuntimeException;

class TableView
{
    protected TableViewInterface $class;
    protected DataProvider $dataProvider;

    protected array $columns = [];
    protected array $buttons = [];

    public const string DEFAULT_CELL_CLASS = 'p-2';

    public function __construct(TableViewInterface $tableViewClass, DataProvider $dataProvider)
    {
        $this->class = $tableViewClass;
        $this->dataProvider = $dataProvider;
        $this->columns = $tableViewClass::COLUMNS;
        $this->buttons = $tableViewClass::BUTTONS;
    }

    public function show(): void
    {
        $buttons = $this->buttons;
        if (method_exists($this->class, 'buttonPrepare')) {
            $buttons = $this->class->buttonPrepare($this->dataProvider, $buttons);
        }

        echo $this->template('list', [
            'class' => $this->class,
            'dataProvider' => $this->dataProvider,
            'columns' => $this->columns,
            'buttons' => $buttons,
        ]);
    }

    public function template(string $type, array $params = []): string
    {
        extract($params, EXTR_SKIP);

        $path = __DIR__ . '/Templates/' . $type . '.tpl.php';
        if (file_exists($path)) {
            ob_start();
            include $path;
            return ob_get_clean();
        }

        throw new RuntimeException('Template "' . $type . '" is not exists in Table component', 404);
    }

    public static function addClass(array &$properties, string $class): void
    {
        if (array_key_exists('class', $properties)) {
            $properties['class'] .= ' ' . $class;
            return;
        }

        $properties['class'] = $class;
    }

    public static function getOrderHref(array $properties, string $key): string
    {
        if (empty($properties['href'])) {
            return '#';
        }

        $url = parse_url($properties['href']);
        parse_str(urldecode($url['query']), $params);

        $params['order'] = $key
            . ':'
            . (
                (
                    array_key_exists('direction', $properties) &&
                    $properties['direction'] === 'ASC'
                )
                    ? 'desc' : 'asc'
            );

        if (array_key_exists('page', $params)) {
            unset($params['page']);
        }

        return $url['path'] . '?' . http_build_query($params);
    }

    public static function checkColumnVisible(DataProvider $dataProvider, array $column): bool
    {
        if (array_key_exists('visible', $column) === false) {
            return true;
        }

        if (is_bool($column['visible'])) {
            return $column['visible'];
        }

        if (mb_strpos($column['visible'], '::', 0, 'UTF-8') !== false) {
            [$listClass, $listMethod] = explode('::', $column['visible']);
            return $listClass::{$listMethod}($column, $dataProvider);
        }

        return false;
    }
}