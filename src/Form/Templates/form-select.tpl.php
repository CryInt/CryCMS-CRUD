<?php
/**
 * @var $data
 * @var $title
 * @var $field
 * @var $list
 * @var $empty
 * @var $disabled
 */

use CryCMS\HTML;

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

if (!empty($empty)) {
    if (empty($list)) {
        $list = [];
    }

    $list = $empty + $list;
}

$optionHTML = [];
if (!empty($list)) {
    $active = $data->$field ?? '';

    foreach ($list as $key => $title) {
        $properties = [];

        if (is_int($key) && is_string($active)) {
            $key = (string)$key;
        }

        if ($title === '{break}') {
            $properties['disabled'] = 'disabled';
            $title = '';
        }

        if ($key === $active) {
            $properties['selected'] = 'selected';
        }

        $optionHTML[] = HTML::option($title, $key, $properties);
    }
}
$optionHTML = implode(PHP_EOL, $optionHTML);

echo HTML::select($optionHTML, [
    'name' => $field,
    'class' => 'form-select' . (!empty($data->getError($field)) ? ' is-invalid' : ''),
    'id' => $field . '-field',
    'disabled' => $disabled ?? false,
]);

echo HTML::div($data->getError($field) ?? '', ['class' => 'invalid-feedback']);