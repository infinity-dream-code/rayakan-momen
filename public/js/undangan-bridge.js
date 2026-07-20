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
            var who = nama + (hadir ? ' \u00B7 ' + hadir : '');

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
            if (cfg.csrf) body.append('_token', cfg.csrf);
            body.append('nama', nama);
            body.append('ucapan', ucapan);
            body.append('kehadiran', kehadiran);

            var btn = form.querySelector('button[type="submit"], .submit-btn, button');
            if (btn) btn.disabled = true;

            var headers = {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json'
            };
            if (cfg.csrf) {
                headers['X-CSRF-TOKEN'] = cfg.csrf;
            }

            fetch(cfg.ucapanUrl, {
                method: 'POST',
                body: body,
                headers: headers,
                credentials: 'same-origin'
            })
                .then(function (res) {
                    if (res.status === 419) {
                        throw new Error('Sesi kedaluwarsa. Refresh halaman lalu coba lagi.');
                    }
                    if (res.status === 422) {
                        throw new Error('Data ucapan tidak valid. Cek nama & isi ucapan.');
                    }
                    if (!res.ok) throw new Error('Gagal mengirim ucapan (kode ' + res.status + ').');
                    return res.json().catch(function () { return {}; });
                })
                .then(function () {
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
                .catch(function (err) {
                    alert((err && err.message) ? err.message : 'Gagal mengirim ucapan. Coba lagi.');
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

    function buildBankCard(name, atasNama, nomor, id) {
        var el = document.createElement('div');
        el.className = 'bank-card';
        var label = name || 'Rekening';
        if (label && !/^bank\b/i.test(label)) label = 'Bank ' + label;
        el.innerHTML =
            '<p class="bank-name">' + escapeHtml(label) + '</p>' +
            (atasNama ? '<p class="acc-name">' + escapeHtml(atasNama) + '</p>' : '') +
            '<div class="acc-row"><span class="acc-num" id="' + id + '">' + escapeHtml(nomor || '') + '</span>' +
            '<button type="button" class="copy-btn" data-target="' + id + '">Salin</button></div>';
        return el;
    }

    var bankList = document.getElementById('bankList');
    if (bankList) {
        // Hapus kartu bank demo yang "bocor" di luar #bankList (bug HTML lama)
        var giftSection = bankList.closest('section') || document.getElementById('gift');
        if (giftSection) {
            giftSection.querySelectorAll('.bank-card').forEach(function (card) {
                if (!bankList.contains(card)) card.remove();
            });
        }

        // Buang kartu demo template, bangun ulang dari data admin saja
        bankList.innerHTML = '';

        (cfg.rekening || []).forEach(function (r, i) {
            if (!r || (!r.bank && !r.nomor)) return;
            bankList.appendChild(buildBankCard(r.bank, r.atas_nama, r.nomor, 'rek' + (i + 1)));
        });

        if (cfg.qris) {
            var qrisBox = document.createElement('div');
            qrisBox.className = 'bank-card';
            qrisBox.style.textAlign = 'center';
            qrisBox.innerHTML =
                '<p class="bank-name" style="margin-bottom:10px;">Scan QRIS</p>' +
                '<img src="' + cfg.qris + '" alt="QRIS" style="width:180px;height:180px;object-fit:contain;margin:0 auto;display:block;background:#fff;padding:8px;border-radius:12px;">';
            bankList.appendChild(qrisBox);
        }

        (cfg.ewallet || []).forEach(function (w, i) {
            if (!w || (!w.tipe && !w.nomor)) return;
            bankList.appendChild(buildBankCard(w.tipe, w.atas_nama, w.nomor, 'ew' + i));
        });

        // Re-bind tombol salin
        bankList.querySelectorAll('.copy-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(btn.getAttribute('data-target'));
                if (!target) return;
                var text = target.textContent.trim();
                if (!text) return;
                navigator.clipboard.writeText(text).then(function () {
                    var old = btn.textContent;
                    btn.textContent = 'Tersalin!';
                    setTimeout(function () { btn.textContent = old; }, 1500);
                });
            });
        });
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

    if (cfg.mapsUrl || cfg.mapsUrlResepsi) {
        var mapLinks = document.querySelectorAll('a.map-btn, a.event-map');
        mapLinks.forEach(function (a, i) {
            var u = i === 0
                ? (cfg.mapsUrl || cfg.mapsUrlResepsi)
                : (cfg.mapsUrlResepsi || cfg.mapsUrl);
            if (!u) return;
            a.href = u;
            a.target = '_blank';
            a.rel = 'noopener noreferrer';
        });

        // Kalau kartu event belum punya tombol maps, tambahkan
        document.querySelectorAll('.event-card').forEach(function (card, i) {
            if (card.querySelector('a.map-btn, a.event-map, a[data-maps]')) return;
            var u = i === 0
                ? (cfg.mapsUrl || cfg.mapsUrlResepsi)
                : (cfg.mapsUrlResepsi || cfg.mapsUrl);
            if (!u) return;
            var link = document.createElement('a');
            link.href = u;
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
