<?php
namespace CryCMS\TableView\Interfaces;

use CryCMS\Thing;

interface TableViewInterface
{
    public const array COLUMNS = [];
    public const array BUTTONS = [];

    public function cellPrepare(string $field, Thing $row): string;
}