/* Change password page – client-side helpers (no dependencies) */
(function () {
    'use strict';

    var form = document.getElementById('passwordForm');
    if (!form) { return; }

    var current = document.getElementById('current_password');
    var pw      = document.getElementById('new_password');
    var confirm = document.getElementById('confirm_password');
    var show    = document.getElementById('showPasswords');
    var meter   = document.getElementById('pwMeterBar');

    function strength(value) {
        var score = 0;
        if (value.length >= 8) { score++; }
        if (value.length >= 12) { score++; }
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) { score++; }
        if (/\d/.test(value)) { score++; }
        if (/[^A-Za-z0-9]/.test(value)) { score++; }
        return Math.min(score, 4); // 0..4
    }

    function updateMeter() {
        if (!meter) { return; }
        var s = pw.value ? strength(pw.value) : 0;
        meter.style.width = (s * 25) + '%';
        meter.className = 'level-' + s;
    }

    function checkMatch() {
        confirm.setCustomValidity(confirm.value !== '' && confirm.value !== pw.value ? 'mismatch' : '');
    }

    pw.addEventListener('input', function () { updateMeter(); checkMatch(); });
    confirm.addEventListener('input', checkMatch);

    if (show) {
        show.addEventListener('change', function () {
            var type = show.checked ? 'text' : 'password';
            [current, pw, confirm].forEach(function (el) { el.type = type; });
        });
    }

    form.addEventListener('submit', function (e) {
        checkMatch();
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            var firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) { firstInvalid.focus(); }
        }
    });
})();
