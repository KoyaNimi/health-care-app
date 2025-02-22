<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Record $record
 */
?>
<div class="records view content">
    <h3><?= h($record->disease_name) ?></h3>
    <div class="record-details">
        <table>
            <tr>
                <th><?= __('発症日') ?></th>
                <td><?= h($record->onset_date->format('Y-m-d')) ?></td>
            </tr>
            <tr>
                <th><?= __('重症度') ?></th>
                <td><?= $this->Number->format($record->severity) ?></td>
            </tr>
            <?php if ($record->recovery_date): ?>
                <tr>
                    <th><?= __('回復日') ?></th>
                    <td><?= h($record->recovery_date->format('Y-m-d')) ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- 操作ボタン -->
    <div class="actions">
        <?= $this->Html->link(__('編集'), ['action' => 'edit', $record->id], ['class' => 'button']) ?>
        <?= $this->Form->postLink(
            __('削除'),
            ['action' => 'delete', $record->id],
            [
                'confirm' => __('本当にこの記録を削除しますか？'),
                'class' => 'button delete'
            ]
        ) ?>
    </div>

    <div class="related">
        <h4><?= __('通院記録') ?></h4>
        <?php if (!empty($record->hospital_visits)): ?>
            <table>
                <tr>
                    <th><?= __('病院名') ?></th>
                    <th><?= __('通院日時') ?></th>
                    <th><?= __('治療内容') ?></th>
                </tr>
                <?php foreach ($record->hospital_visits as $visit): ?>
                    <tr>
                        <td><?= h($visit->hospital_name) ?></td>
                        <td><?= h($visit->visit_datetime->format('Y-m-d H:i')) ?></td>
                        <td><?= h($visit->treatment_details) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><?= __('通院記録はありません。') ?></p>
        <?php endif; ?>
    </div>
</div>