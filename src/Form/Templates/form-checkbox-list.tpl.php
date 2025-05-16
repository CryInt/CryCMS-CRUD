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

echo HTML::label($title, ['class' => 'form-label']);
?>
<ul class="list-group">
    <?php
    if (!empty($addEmpty)) {
        echo HTML::input($field . '[]', '', ['type' => 'hidden']);
    }

    foreach ($list as $value => $name) {
        $tName = CRUDHelper::transliteration($name);

        $input = HTML::input($field . '[]', $value, [
            'class' => 'form-check-input me-1',
            'type' => 'checkbox',
            'id' => 'check-box-' . $field . '-' .$tName,
            'value' => $value,
            'checked' => array_key_exists($value, $checkedList),
        ]);

        $label = HTML::label($name, [
            'class' => 'form-check-label',
            'for' => 'check-box-' . $field . '-' .$tName,
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