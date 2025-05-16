<?php
/**
 * @var $data
 * @var $title
 * @var $field
 * @var $readonly
 * @var $min
 * @var $max
 * @var $step
 */

use CryCMS\HTML;

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

echo HTML::input($field, $data->$field ?? '', [
    'class' => 'form-control' . (!empty($data->getError($field)) ? ' is-invalid' : ''),
    'id' => $field . '-field',
    'autocomplete' => 'off',
    'readonly' => $readonly ?? false,
    'type' => 'number',
    'min' => $min,
    'max' => $max,
    'step' => $step ?? 1,
]);

echo HTML::div($data->getError($field) ?? '', ['class' => 'invalid-feedback']);