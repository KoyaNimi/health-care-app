<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Record $record
 */
?>
<div class="records form content">
    <?= $this->Form->create($record) ?>
    <fieldset>
        <legend><?= __('新規記録作成') ?></legend>
        <?php
        echo $this->Form->control('onset_date', ['label' => '発症日']);
        echo $this->Form->control('disease_name', ['label' => '病名']);
        echo $this->Form->control('severity', ['label' => '重症度']);
        echo $this->Form->control('complications', ['label' => '合併症']);
        echo $this->Form->control('medications', ['label' => '処方薬']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('保存')) ?>
    <?= $this->Form->end() ?>
</div>