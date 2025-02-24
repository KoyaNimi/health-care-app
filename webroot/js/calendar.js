document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const calendarEl = document.getElementById('calendar');
    console.log('Calendar element:', calendarEl);
    
    try {
        // カレンダーの初期化
        const calendar = new window.FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',  // 月表示
            headerToolbar: {
                left: 'prev,next today',  // 前月、次月、今日
                center: 'title',          // タイトル（年月）
                right: 'dayGridMonth'     // 表示切替ボタン
            },
            events: []  // イベントは空の配列で初期化
        });

        // カレンダーの描画
        calendar.render();
        console.log('Calendar rendered successfully');
    } catch (error) {
        console.error('Calendar initialization error:', error);
    }
}); 