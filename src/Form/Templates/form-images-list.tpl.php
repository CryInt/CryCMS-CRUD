<?php
/**
 * @var $data
 * @var $title
 * @var $field
 * @var array $list [image_id, image_src]
 */

use CryCMS\HTML;

echo HTML::label($title, ['class' => 'form-label']);

$images = [];

if (!empty($list)) {
    foreach ($list as $image) {
        $imageHTML = HTML::img($image['image_src'] ?? '', ['class' => 'card-img-top ', 'alt' => '']);
        $inputHTML = HTML::input('imagesNew[]', $image['image_id'], ['type' => 'hidden']);
        $divCardBodyHTML = HTML::div($imageHTML . $inputHTML, ['class' => 'card-body p-0']);
        $divCardHTML = HTML::div($divCardBodyHTML, ['class' => 'card h-100 bg-light', 'role' => 'button']);
        $images[] = HTML::div($divCardHTML, ['class' => 'col']);
    }
}

$images = implode(PHP_EOL, $images);
?>
<div class="border rounded p-3">
    <input type="hidden" name="imagesNew[]" value="0">
    <div class="row row-cols-1 row-cols-md-3 g-3 images-list-box"><?= $images ?></div>

    <div id="images-list-upload-area" class="mt-3">
        <label for="images-list-upload" class="btn btn-success btn-sm"><i class="bi bi-plus"></i></label>
        <input type="file" id="images-list-upload" style="position: absolute; left: -9999px;">
    </div>
</div>