<?php
/**
 * @var Thing $data
 * @var $title
 * @var $field
 */

use CryCMS\HTML;
use CryCMS\Thing;

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

echo HTML::input($field, $value ?? $data->$field ?? '', [
    'class' => 'form-control' . (!empty($data->getError($field)) ? ' is-invalid' : ''),
    'type' => 'file',
    'id' => $id ?? $field . '-field',
    'autocomplete' => 'off',
]);

echo HTML::div($data->getError($field) ?? '', ['class' => 'invalid-feedback']);