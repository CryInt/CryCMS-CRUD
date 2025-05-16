<?php
/**
 * @var DataProvider $dataProvider
 * @var $pagerPage int
 * @var $pagerHref string
 * @var bool $withCreate
 */

use CryCMS\DataProvider\DataProvider;

?>
<div class="<?= ($withCreate === true ? '' : 'container-fluid mt-3 p-0') ?>">
    <div class="row">
        <div class="col">
            <?= $this->template('pager', [
                'dataProvider' => $dataProvider,
                'pagerPage' => $pagerPage,
                'pagerHref' => $pagerHref,
            ]) ?>
        </div>
    </div>
</div>