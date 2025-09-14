<?php
/**
 * @var $data
 * @var $title
 * @var $field
 * @var $readonly
 * @var $rows
 */

use CryCMS\HTML;

$error = null;
if ($data !== null) {
    $error = $data->getError($field);
}

echo HTML::label($title, ['class' => 'form-label', 'for' => $field . '-field']);

echo HTML::textarea($data->$field ?? '', [
    'class' => 'form-control tinymce-editor' . ($error !== null ? ' is-invalid' : ''),
    'id' => $id ?? $field . '-field',
    'autocomplete' => 'off',
    'readonly' => $readonly ?? false,
    'rows' => $rows ?? 10,
    'name' => $field,
]);

echo HTML::div($error ?? '', ['class' => 'invalid-feedback']);