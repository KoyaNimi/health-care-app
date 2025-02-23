document.addEventListener('DOMContentLoaded', function () {
    // カレンダー要素の取得
    const calendarEl = document.getElementById('calendar');

    // モーダル関連の要素
    const modal = document.getElementById('month-picker-modal');
    const yearSelect = document.getElementById('year-select');
    const monthSelect = document.getElementById('month-select');
    const jumpButton = document.getElementById('jump-button');
    const closeButton = document.getElementById('close-modal');

    // カレンダーの初期化
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        titleFormat: {
            year: 'numeric',
            month: 'long'
        },
        // タイトルクリックで年月選択モーダルを表示
        titleRender: function (arg) {
            arg.el.addEventListener('click', function () {
                // 現在表示中の年月をモーダルの初期値に設定
                const date = calendar.getDate();
                yearSelect.value = date.getFullYear();
                monthSelect.value = date.getMonth() + 1;
                modal.style.display = 'block';
            });
        },
        // 症状データの表示設定
        events: calendarEvents,
        eventContent: function (arg) {
            const event = arg.event;
            const severity = event.extendedProps.severity;
            const hasHospitalVisit = event.extendedProps.hasHospitalVisit;

            // 重症度に応じた色を設定
            let backgroundColor;
            switch (severity) {
                case 1:
                    backgroundColor = SEVERITY_COLORS.LIGHT;
                    break;
                case 2:
                    backgroundColor = SEVERITY_COLORS.MEDIUM;
                    break;
                case 3:
                    backgroundColor = SEVERITY_COLORS.SEVERE;
                    break;
                default:
                    backgroundColor = SEVERITY_COLORS.DEFAULT;
            }

            // イベントの表示内容を作成
            const content = document.createElement('div');
            content.style.backgroundColor = backgroundColor;
            Object.assign(content.style, EVENT_STYLES);
            content.textContent = event.title;

            // 通院記録がある場合は🏥アイコンを追加
            if (hasHospitalVisit) {
                const icon = document.createElement('span');
                icon.textContent = ' 🏥';
                content.appendChild(icon);
            }

            return { domNodes: [content] };
        }
    });

    // モーダルの「移動」ボタンクリック時
    jumpButton.addEventListener('click', function () {
        const year = parseInt(yearSelect.value);
        const month = parseInt(monthSelect.value) - 1; // JavaScriptの月は0始まり
        calendar.gotoDate(new Date(year, month));
        modal.style.display = 'none';
    });

    // モーダルの「閉じる」ボタンクリック時
    closeButton.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // カレンダーの描画
    calendar.render();
}); 