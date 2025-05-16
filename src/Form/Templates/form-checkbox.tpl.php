<?php
/**
 * @var $title
 * @var $name
 * @var $value
 * @var $checked
 * @var $readonly
 * @var $tooltip
 */

use CryCMS\CRUDHelper;
use CryCMS\HTML;

$id = CRUDHelper::transliteration($name . '-' . $value . '-field');

$defaultInput = HTML::input($name, '0', ['type' => 'hidden']);

$input = HTML::input($name, $value, [
    'type' => 'checkbox',
    'class' => 'form-check-input filtering',
    'id' => $id,
    'readonly' => $readonly ?? false,
    'checked' => $checked ? 'checked' : false,
]);

$label = HTML::label($title, [
    'class' => 'form-label',
    'for' => $id,
    'title' => $tooltip ?? ''
]);

echo HTML::div($defaultInput . $input . $label, ['class' => 'form-check form-switch']);