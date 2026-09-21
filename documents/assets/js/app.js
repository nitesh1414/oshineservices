/* Oshin Services · Document Generator – form behaviour (no dependencies) */
(function () {
    'use strict';

    var form = document.getElementById('documentForm');
    if (!form) { return; }

    var body       = document.getElementById('itemsBody');
    var template   = document.getElementById('itemRowTemplate');
    var addBtn     = document.getElementById('addRowBtn');
    var taxType    = document.getElementById('tax_type');
    var itemCount  = document.getElementById('itemCount');
    var currency   = form.getAttribute('data-currency') || '₹';

    /* ---------- number helpers (Indian grouping) ---------- */
    function round2(n) { return Math.round((n + Number.EPSILON) * 100) / 100; }

    function formatINR(n) {
        n = round2(Number(n) || 0);
        var neg = n < 0; n = Math.abs(n);
        var parts = n.toFixed(2).split('.');
        var int = parts[0];
        if (int.length > 3) {
            var last3 = int.slice(-3), rest = int.slice(0, -3);
            rest = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
            int = rest + ',' + last3;
        }
        return (neg ? '-' : '') + int + '.' + parts[1];
    }

    /* ---------- rows ---------- */
    function rows() { return Array.prototype.slice.call(body.querySelectorAll('tr.item-row')); }

    function renumber() {
        rows().forEach(function (tr, i) {
            var no = tr.querySelector('.row-no');
            if (no) { no.textContent = String(i + 1); }
        });
        if (itemCount) { itemCount.textContent = String(rows().length); }
    }

    function addRow(focus) {
        var fragment = template.content.cloneNode(true);
        var tr = fragment.querySelector('tr');
        body.appendChild(tr);
        renumber();
        autosize(tr.querySelector('.f-desc'));
        recalc();
        if (focus !== false) {
            var d = tr.querySelector('.f-desc');
            if (d) { d.focus(); tr.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
        }
        return tr;
    }

    function removeRow(tr) {
        if (rows().length <= 1) {
            // keep one row – just clear it
            tr.querySelectorAll('input, textarea').forEach(function (el) {
                if (el.classList.contains('f-qty')) { el.value = '1'; } else { el.value = ''; }
            });
        } else {
            tr.parentNode.removeChild(tr);
        }
        renumber();
        recalc();
    }

    /* ---------- calculation ---------- */
    function recalc() {
        var igst = taxType && taxType.value === 'igst';
        var subtotal = 0, tax = 0;

        rows().forEach(function (tr) {
            var qty  = parseFloat(tr.querySelector('.f-qty').value) || 0;
            var rate = parseFloat(tr.querySelector('.f-rate').value) || 0;
            var gst  = parseFloat(tr.querySelector('.f-gst').value) || 0;

            var taxable   = round2(qty * rate);
            var gstAmount = round2(taxable * gst / 100);
            var total     = round2(taxable + gstAmount);

            tr.querySelector('.c-taxable').textContent = formatINR(taxable);
            tr.querySelector('.c-gst').textContent     = formatINR(gstAmount);
            tr.querySelector('.c-total').textContent   = formatINR(total);

            subtotal += taxable;
            tax      += gstAmount;
        });

        subtotal = round2(subtotal);
        tax      = round2(tax);
        var grand   = round2(subtotal + tax);
        var payable = Math.round(grand);
        var roundOff = round2(payable - grand);
        var cgst = round2(tax / 2);
        var sgst = round2(tax - cgst);

        setText('sumSubtotal', formatINR(subtotal));
        setText('sumCgst', formatINR(igst ? 0 : cgst));
        setText('sumSgst', formatINR(igst ? 0 : sgst));
        setText('sumIgst', formatINR(igst ? tax : 0));
        setText('sumTotal', formatINR(grand));
        setText('sumRound', (roundOff > 0 ? '+' : (roundOff < 0 ? '-' : '')) + formatINR(Math.abs(roundOff)));
        setText('sumPayable', currency + ' ' + formatINR(payable));

        document.querySelectorAll('.tax-split').forEach(function (el) { el.classList.toggle('d-none', igst); });
        document.querySelectorAll('.tax-igst').forEach(function (el) { el.classList.toggle('d-none', !igst); });
    }

    function setText(id, text) {
        var el = document.getElementById(id);
        if (el) { el.textContent = text; }
    }

    /* ---------- description textarea autosize ---------- */
    function autosize(el) {
        if (!el) { return; }
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight + 2, 160) + 'px';
    }

    /* ---------- events ---------- */
    addBtn.addEventListener('click', function () { addRow(true); });

    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove');
        if (btn) { removeRow(btn.closest('tr')); }
    });

    body.addEventListener('input', function (e) {
        if (e.target.classList.contains('f-desc')) { autosize(e.target); }
        if (e.target.classList.contains('f-hsn')) { e.target.value = e.target.value.replace(/\D/g, ''); }
        recalc();
    });
    body.addEventListener('change', recalc);

    // Enter inside a row moves to the next field (never submits accidentally); on the last field it adds a row.
    body.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' || e.target.tagName === 'TEXTAREA' && e.shiftKey) { return; }
        if (e.target.matches('input, select, textarea')) {
            e.preventDefault();
            var tr = e.target.closest('tr');
            var fields = Array.prototype.slice.call(tr.querySelectorAll('input, select, textarea'));
            var idx = fields.indexOf(e.target);
            if (idx >= 0 && idx < fields.length - 1) {
                fields[idx + 1].focus();
            } else if (tr === rows()[rows().length - 1]) {
                addRow(true);
            }
        }
    });

    if (taxType) { taxType.addEventListener('change', recalc); }

    var gstin = document.getElementById('customer_gstin');
    if (gstin) {
        gstin.addEventListener('input', function () { gstin.value = gstin.value.toUpperCase().replace(/\s+/g, ''); });
    }

    /* ---------- submit ---------- */
    var submitting = false;
    form.addEventListener('submit', function (e) {
        var hasItem = rows().some(function (tr) { return tr.querySelector('.f-desc').value.trim() !== ''; });

        if (!form.checkValidity() || !hasItem) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            var firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ block: 'center', behavior: 'smooth' });
            } else if (!hasItem) {
                alert('Please add at least one line item.');
            }
            return;
        }

        // Which button was used? (preview opens a new tab, download saves the file)
        var button = e.submitter || document.activeElement;
        var isPreview = button && button.value === 'preview';
        if (!isPreview) {
            if (submitting) { e.preventDefault(); return; }
            submitting = true;
            var dl = document.getElementById('downloadBtn');
            var original = dl.innerHTML;
            // Defer so the button's name/value is still part of the submitted form data.
            setTimeout(function () { dl.innerHTML = 'Generating PDF…'; dl.disabled = true; }, 0);
            setTimeout(function () { dl.innerHTML = original; dl.disabled = false; submitting = false; }, 4000);
        }
    });

    /* ---------- init ---------- */
    body.querySelectorAll('.f-desc').forEach(autosize);
    renumber();
    recalc();
})();
