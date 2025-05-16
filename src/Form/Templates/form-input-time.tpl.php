<?php
/**
 * @var Thing $data
 * @var $title
 * @var $field
 * @var $readonly
 * @var $value
 */

use CryCMS\HTML;
use CryCMS\Thing;

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

echo HTML::input($field, $value ?? $data->$field ?? '', [
    'type' => 'time',
    'class' => 'form-control' . (!empty($data->getError($field)) ? ' is-invalid' : ''),
    'id' => $id ?? $field . '-field',
    'autocomplete' => 'off',
    'readonly' => $readonly ?? false,
]);

echo HTML::div($data->getError($field) ?? '', ['class' => 'invalid-feedback']);