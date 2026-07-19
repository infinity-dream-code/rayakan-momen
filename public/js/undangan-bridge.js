document.addEventListener('DOMContentLoaded', function () {
    var cfg = window.WEB_UNTAL;
    if (!cfg) return;

    var form = document.getElementById('rsvpForm');
    var list = document.getElementById('wishList');

    function renderWishes(items) {
        if (!list || !items) return;
        list.innerHTML = '';
        items.forEach(function (w) {
            var card = document.createElement('div');
            card.className = 'wish-card';
            card.innerHTML =
                '<div class="wish-head"><strong>' + escapeHtml(w.nama) + '</strong>' +
                '<span>' + escapeHtml(w.kehadiran || '') + '</span></div>' +
                '<p>' + escapeHtml(w.ucapan || '') + '</p>';
            list.appendChild(card);
        });
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    if (cfg.wishes && cfg.wishes.length) {
        renderWishes(cfg.wishes);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var nama = (document.getElementById('fname') || {}).value || '';
            var kehadiranRaw = (document.getElementById('fconfirm') || {}).value || 'Hadir';
            var ucapan = (document.getElementById('fmsg') || {}).value || '';
            var kehadiran = kehadiranRaw.toLowerCase().indexOf('tidak') >= 0 ? 'tidak_hadir' : 'hadir';

            var body = new FormData();
            body.append('_token', cfg.csrf);
            body.append('nama', nama);
            body.append('ucapan', ucapan);
            body.append('kehadiran', kehadiran);

            fetch(cfg.ucapanUrl, {
                method: 'POST',
                body: body,
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('Gagal kirim');
                    cfg.wishes = cfg.wishes || [];
                    cfg.wishes.unshift({
                        nama: nama,
                        ucapan: ucapan,
                        kehadiran: kehadiranRaw
                    });
                    renderWishes(cfg.wishes);
                    form.reset();
                    alert('Terima kasih! Ucapanmu sudah tersimpan.');
                })
                .catch(function () {
                    alert('Gagal mengirim ucapan. Coba lagi.');
                });
        }, true);
    }

    function syncBankListHeight() {
        var bankList = document.getElementById('bankList');
        if (!bankList) return;
        if (bankList.classList.contains('open')) {
            bankList.style.maxHeight = 'none';
            bankList.style.maxHeight = bankList.scrollHeight + 'px';
        } else {
            bankList.style.maxHeight = '';
        }
    }

    if (cfg.qris) {
        var bankList = document.getElementById('bankList');
        if (bankList) {
            var qrisBox = document.createElement('div');
            qrisBox.className = 'bank-card';
            qrisBox.style.textAlign = 'center';
            qrisBox.innerHTML =
                '<p class="bank-name" style="margin-bottom:10px;">Scan QRIS</p>' +
                '<img src="' + cfg.qris + '" alt="QRIS" style="width:180px;height:180px;object-fit:contain;margin:0 auto;display:block;background:#fff;padding:8px;border-radius:12px;">';
            bankList.insertBefore(qrisBox, bankList.firstChild);
        }
    }

    if (cfg.ewallet && cfg.ewallet.length) {
        var bankList2 = document.getElementById('bankList');
        if (bankList2) {
            cfg.ewallet.forEach(function (w, i) {
                if (!w.tipe && !w.nomor) return;
                var el = document.createElement('div');
                el.className = 'bank-card';
                el.innerHTML =
                    '<p class="bank-name">' + escapeHtml(w.tipe) + '</p>' +
                    '<p class="acc-name">a.n. ' + escapeHtml(w.atas_nama || '') + '</p>' +
                    '<div class="acc-row"><span class="acc-num" id="ew' + i + '">' + escapeHtml(w.nomor || '') + '</span>' +
                    '<button type="button" class="copy-btn" data-target="ew' + i + '">Salin</button></div>';
                bankList2.appendChild(el);
            });
        }
    }

    syncBankListHeight();
    var giftToggle = document.getElementById('giftToggle');
    if (giftToggle) {
        giftToggle.addEventListener('click', function () {
            setTimeout(syncBankListHeight, 30);
        });
    }

    if (cfg.mapsUrl) {
        document.querySelectorAll('a.map-btn').forEach(function (a) {
            a.href = cfg.mapsUrl;
            a.target = '_blank';
            a.rel = 'noopener noreferrer';
        });

        document.querySelectorAll('.event-card').forEach(function (card) {
            if (card.querySelector('a.map-btn, a[data-maps]')) return;
            var link = document.createElement('a');
            link.href = cfg.mapsUrl;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className = 'map-btn';
            link.setAttribute('data-maps', '1');
            link.textContent = 'Lihat Lokasi';
            link.style.display = 'inline-block';
            link.style.marginTop = '12px';
            card.appendChild(link);
        });
    }
});
