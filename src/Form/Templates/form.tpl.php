<?php
/**
 * @var Form $form
 */

use CryCMS\Form\Form;
use CryCMS\HTML;
?>
<div class="container-sm col-md-8">
    <form action="" method="post">
        <?php
        $form->generateFields();
        ?>

        <div class="container-fluid mt-4 p-0 pt-4 pb-4 border-top">
            <div class="row">
                <div class="col">
                    <?= HTML::a(HTML::i('', ['class' => 'bi bi-arrow-left']) . ' back' , $form->pageBase, [
                        'class' => 'text-muted align-middle text-decoration-none'
                    ]) ?>
                </div>
                <div class="col text-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>