<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Record[] $records
 * @var array $yearOptions
 * @var array $monthOptions
 */

// CSSとJavaScriptの読み込み
$this->Html->css('calendar', ['block' => true]);
$this->Html->script('calendar-constants', ['block' => true]);
$this->Html->script('calendar', ['block' => true]);

// FullCalendarの必要なファイルを読み込み
$scriptsExist = [
    'fullcalendar/index.global.min.js' => file_exists(WWW_ROOT . 'js/fullcalendar/index.global.min.js'),
    'fullcalendar/daygrid/index.global.min.js' => file_exists(WWW_ROOT . 'js/fullcalendar/daygrid/index.global.min.js'),
    'fullcalendar/timegrid/index.global.min.js' => file_exists(WWW_ROOT . 'js/fullcalendar/timegrid/index.global.min.js'),
];

// デバッグ情報を出力
debug($scriptsExist);

// スクリプトを読み込み
$this->Html->script([
    'fullcalendar/index.global.min',
    'fullcalendar/daygrid/index.global.min',
    'fullcalendar/timegrid/index.global.min'
], ['block' => true]);

// 必要なスクリプトの読み込み
$this->Html->script([
    'fullcalendar/index.global.min',
    'calendar'
], ['block' => true]);
?>

<!-- FullCalendarのCSSとJS -->
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/main.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.11/main.css' rel='stylesheet' />

<!-- カスタムCSS -->
<?= $this->Html->css('calendar') ?>

<style>
    /* 基本的なカレンダーのスタイル */
    #calendar {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }
</style>

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

<?php $this->start('script'); ?>
<script>
    console.log('Script block started');
</script>

<script>
    // スクリプトの読み込み状態を確認
    function checkScriptsLoaded() {
        const scripts = [
            '/js/fullcalendar/index.global.min.js',
            '/js/fullcalendar/daygrid/index.global.min.js',
            '/js/fullcalendar/timegrid/index.global.min.js'
        ];

        return Promise.all(scripts.map(src => {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.async = false; // 順序を保持
                script.onload = () => resolve(src);
                script.onerror = () => reject(new Error(`Failed to load ${src}`));
                document.head.appendChild(script);
            });
        }));
    }

    // カレンダーの初期化
    async function initializeCalendar() {
        try {
            console.log('Loading FullCalendar scripts...');
            await checkScriptsLoaded();

            console.log('Scripts loaded, checking FullCalendar object:', window.FullCalendar);

            if (typeof window.FullCalendar === 'undefined') {
                throw new Error('FullCalendar not loaded properly');
            }

            const calendarEl = document.getElementById('calendar');
            console.log('Calendar element:', calendarEl);

            const calendar = new window.FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                events: []
            });

            calendar.render();
            console.log('Calendar rendered successfully');

        } catch (error) {
            console.error('Calendar initialization error:', error);
            console.log('FullCalendar availability:', {
                core: window.FullCalendar,
                version: window.FullCalendar?.version
            });
        }
    }

    // DOMContentLoadedイベントで初期化を開始
    document.addEventListener('DOMContentLoaded', initializeCalendar);
</script>

<?= $this->Html->script('calendar-constants', ['block' => true]) ?>
<?= $this->Html->script('calendar', ['block' => true]) ?>

<script>
    console.log('All scripts loaded');
</script>
<?php $this->end(); ?>