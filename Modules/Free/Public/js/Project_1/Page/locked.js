(function() {
    'use strict';

    // ============================================================
    // ДАТА ЗАПУСКА — 14 августа 2026, 15:00 MSK
    // ============================================================
    var targetDate = new Date('2026-08-26T15:00:00+03:00').getTime();

    // Элементы таймера
    var daysEl = document.getElementById('days');
    var hoursEl = document.getElementById('hours');
    var minutesEl = document.getElementById('minutes');
    var secondsEl = document.getElementById('seconds');
    var progressBar = document.getElementById('progressBar');

    // Общая длительность кампании (сейчас до цели)
    var startDate = new Date('2026-08-01T00:00:00+03:00').getTime();
    var totalDuration = targetDate - startDate;

    // ============================================================
    // ФУНКЦИЯ ОБНОВЛЕНИЯ ТАЙМЕРА
    // ============================================================
    function updateTimer() {
        var now = Date.now();
        var diff = targetDate - now;

        // Если время вышло
        if (diff <= 0) {
            daysEl.textContent = '00';
            hoursEl.textContent = '00';
            minutesEl.textContent = '00';
            secondsEl.textContent = '00';
            if (progressBar) {
                progressBar.style.width = '100%';
            }

            var statusEl = document.querySelector('.coming-soon__status');
            if (statusEl) {
                statusEl.innerHTML =
                    '<span class="coming-soon__status-dot" style="background:#34d399;"></span> Готово! Запуск состоялся!';
            }
            return;
        }

        // Расчёт дней, часов, минут, секунд
        var days = Math.floor(diff / (1000 * 60 * 60 * 24));
        diff -= days * (1000 * 60 * 60 * 24);
        var hours = Math.floor(diff / (1000 * 60 * 60));
        diff -= hours * (1000 * 60 * 60);
        var minutes = Math.floor(diff / (1000 * 60));
        diff -= minutes * (1000 * 60);
        var seconds = Math.floor(diff / 1000);

        // Форматирование (две цифры)
        daysEl.textContent = String(days).padStart(2, '0');
        hoursEl.textContent = String(hours).padStart(2, '0');
        minutesEl.textContent = String(minutes).padStart(2, '0');
        secondsEl.textContent = String(seconds).padStart(2, '0');

        // Прогресс-бар
        if (progressBar) {
            var elapsed = now - startDate;
            var progress = (elapsed / totalDuration) * 100;
            if (progress > 100) progress = 100;
            if (progress < 0) progress = 0;
            progressBar.style.width = progress.toFixed(2) + '%';
        }
    }

    // ============================================================
    // ЗАПУСК ТАЙМЕРА
    // ============================================================
    updateTimer();
    setInterval(updateTimer, 1000);

    // ============================================================
    // ВЫВОД ДАТЫ ЗАПУСКА В ЧЕЛОВЕЧЕСКОМ ФОРМАТЕ
    // ============================================================
    var launchDateEl = document.getElementById('launchDate');
    if (launchDateEl) {
        var d = new Date(targetDate);
        var months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
            'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
        ];
        launchDateEl.textContent =
            d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() +
            ', ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + ' MSK';
    }
})();