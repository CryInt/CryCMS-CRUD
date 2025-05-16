<?php
/**
 * @var Thing $data
 * @var $title
 * @var $field
 * @var $readonly
 * @var $value
 * @var $rows
 */

use CryCMS\HTML;
use CryCMS\Thing;

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

echo HTML::textarea($value ?? $data->$field ?? '', [
    'name' => $field,
    'class' => 'form-control' . (!empty($data->getError($field)) ? ' is-invalid' : ''),
    'id' => $id ?? $field . '-field',
    'autocomplete' => 'off',
    'readonly' => $readonly ?? false,
    'rows' => $rows ?? 5,
]);

echo HTML::div($data->getError($field) ?? '', ['class' => 'invalid-feedback']);