<?php
namespace CryCMS\TableView\Interfaces;

use CryCMS\Thing;

interface TableViewInterface
{
    public const COLUMNS = [];
    public const BUTTONS = [];

    public function cellPrepare(string $field, Thing $row): string;
}