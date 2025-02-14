<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Record[] $records
 */
?>
<div class="records index content">
    <h3><?= __('Records') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('Disease Name') ?></th>
                    <th><?= __('Onset Date') ?></th>
                    <th><?= __('Recovery Date') ?></th>
                    <th><?= __('Severity') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= h($record->disease_name) ?></td>
                        <td><?= h($record->onset_date) ?></td>
                        <td><?= $record->recovery_date ? h($record->recovery_date) : '' ?></td>
                        <td><?= $this->Number->format($record->severity) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $record->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>