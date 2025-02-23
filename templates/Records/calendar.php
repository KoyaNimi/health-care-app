<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Record[] $records
 * @var array $yearOptions
 * @var array $monthOptions
 */

// CSSとJavaScriptの読み込み
$this->Html->css('calendar', ['block' => true]);
$this->Html->script('calendar', ['block' => true]);
?>

<div class="records calendar">
    <h3><?= __('症状カレンダー') ?></h3>
    <div id="calendar"></div>
</div>

<!-- 年月選択モーダル -->
<div id="month-picker-modal" class="modal">
    <div class="modal-content">
        <?= $this->Form->select('year', $yearOptions, [
            'id' => 'year-select',
            'value' => date('Y')
        ]) ?>
        <?= $this->Form->select('month', $monthOptions, [
            'id' => 'month-select',
            'value' => date('n')
        ]) ?>
        <button id="jump-button">移動</button>
        <button id="close-modal">閉じる</button>
    </div>
</div>

<!-- FullCalendarのCSSとJS -->
<?= $this->Html->css('https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.css') ?>
<?= $this->Html->css('https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.css') ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.js') ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.js') ?>
<?= $this->Html->script('calendar-constants') ?>
<?= $this->Html->script('calendar') ?>

<!-- 症状データをJavaScriptに渡す -->
<script>
    const calendarEvents = <?= json_encode(array_map(function ($record) {
                                return [
                                    'title' => h($record->disease_name),
                                    'start' => $record->onset_date->format('Y-m-d'),
                                    'severity' => $record->severity,
                                    'hasHospitalVisit' => !empty($record->hospital_visits),
                                    'url' => '/records/view/' . $record->id
                                ];
                            }, $records->toArray())) ?>;
</script>