document.addEventListener('DOMContentLoaded', function () {
    var cfg = window.RAYAKAN_MOMEN || window.WEB_UNTAL;
    if (!cfg) return;

    var form = document.getElementById('rsvpForm');
    var list = document.getElementById('wishList');

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatHadir(raw) {
        var v = String(raw || '').toLowerCase();
        if (v.indexOf('tidak') >= 0 || v === 'tidak_hadir') return 'Tidak Hadir';
        if (!v) return '';
        return 'Hadir';
    }

    /** Struktur HTML mengikuti CSS template (.wish-item / .who / .msg) */
    function renderWishes(items) {
        if (!list || !items) return;
        list.innerHTML = '';
        items.forEach(function (w) {
            var nama = (w.nama || w.name || '').trim();
            var msg = (w.ucapan || w.msg || '').trim();
            var hadir = formatHadir(w.kehadiran || w.confirm || '');
            var who = nama + (hadir ? ' · ' + hadir : '');

            var item = document.createElement('div');
            item.className = 'wish-item';
            item.innerHTML = '<div class="who"></div><div class="msg"></div>';
            item.querySelector('.who').textContent = who;
            item.querySelector('.msg').textContent = msg;
            list.appendChild(item);
        });
    }

    // Timpa render localStorage bawaan template dengan data DB
    if (cfg.wishes && cfg.wishes.length) {
        renderWishes(cfg.wishes);
    } else if (list) {
        list.innerHTML = '';
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var nama = ((document.getElementById('fname') || {}).value || '').trim();
            var kehadiranRaw = (document.getElementById('fconfirm') || {}).value || 'Hadir';
            var ucapan = ((document.getElementById('fmsg') || {}).value || '').trim();
            if (!nama || !ucapan) {
                alert('Nama dan ucapan wajib diisi.');
                return;
            }

            var kehadiran = kehadiranRaw.toLowerCase().indexOf('tidak') >= 0 ? 'tidak_hadir' : 'hadir';

            var body = new FormData();
            body.append('_token', cfg.csrf);
            body.append('nama', nama);
            body.append('ucapan', ucapan);
            body.append('kehadiran', kehadiran);

            var btn = form.querySelector('button[type="submit"], .submit-btn, button');
            if (btn) btn.disabled = true;

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
                    if (typeof window.showAppToast === 'function') {
                        window.showAppToast('Ucapan terkirim');
                    } else {
                        alert('Terima kasih! Ucapanmu sudah tersimpan.');
                    }
                })
                .catch(function () {
                    alert('Gagal mengirim ucapan. Coba lagi.');
                })
                .finally(function () {
                    if (btn) btn.disabled = false;
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

    function hideBrokenImage(img) {
        if (!img || img.dataset.brokenHidden === '1') return;
        img.dataset.brokenHidden = '1';
        img.removeAttribute('alt');
        img.style.display = 'none';
        var box = img.closest('figure.gal-item, figure.gal-formal, button.gal-item, .gal-item, .arch-frame');
        if (box) box.style.display = 'none';
    }

    document.querySelectorAll('img').forEach(function (img) {
        img.addEventListener('error', function () {
            hideBrokenImage(img);
        });
        if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) {
            hideBrokenImage(img);
        }
    });

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
