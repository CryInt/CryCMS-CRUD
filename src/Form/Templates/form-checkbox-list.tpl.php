<?php

/**
 * @var $data
 * @var string $title
 * @var string $field
 * @var bool $addEmpty
 * @var array $list
 * @var string|array $checked
 */

use CryCMS\CRUDHelper;
use CryCMS\HTML;

$checkedList = [];

if (is_string($checked) && $data->$checked !== null) {
    $checkedList = $data->$checked;
}
elseif (is_array($checked)) {
    $checkedList = $checked;
}

echo HTML::label($title, ['class' => 'form-label']);
?>
<ul class="list-group">
    <?php
    if (!empty($addEmpty)) {
        echo HTML::input($field . '[]', '', ['type' => 'hidden']);
    }

    foreach ($list as $value => $name) {
        $fieldId = 'check-box-' . CRUDHelper::transliteration($field . '-' . $name . '-' . $value);

        $input = HTML::input($field . '[]', $value, [
            'class' => 'form-check-input me-1',
            'type' => 'checkbox',
            'id' => $fieldId,
            'value' => $value,
            'checked' => array_key_exists($value, $checkedList),
        ]);

        $label = HTML::label($name, [
            'class' => 'form-check-label',
            'for' => $fieldId,
        ]);
        ?>
        <li class="list-group-item">
            <?= $input ?>
            <?= $label ?>
        </li>
        <?php
    }
    ?>
</ul>