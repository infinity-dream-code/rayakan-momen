document.addEventListener('DOMContentLoaded', function () {
    var cfg = window.RAYAKAN_MOMEN || window.WEB_UNTAL;
    if (!cfg) return;

    var form = document.getElementById('rsvpForm');
    var list = document.getElementById('wishList')
        || document.getElementById('guestWall')
        || document.getElementById('guestbookWall')
        || document.getElementById('rsvpList');

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

    function fieldByIds(ids) {
        for (var i = 0; i < ids.length; i++) {
            var el = document.getElementById(ids[i]);
            if (el) return el;
        }
        return null;
    }

    function isIslamWishList() {
        return !!(list && (list.classList.contains('space-y-4') || document.getElementById('kado')));
    }

    function isUltahWall() {
        return !!(list && (list.id === 'guestWall' || list.id === 'guestbookWall' || list.id === 'rsvpList'));
    }

    /** Struktur HTML mengikuti CSS tiap template */
    function renderWishes(items) {
        if (!list || !items) return;
        list.innerHTML = '';
        var islamStyle = isIslamWishList();
        var ultahStyle = isUltahWall();
        var wallColors = ['#FFE8F0', '#E8F4FF', '#FFF3D6', '#E8FFE8', '#F3E8FF', '#FFE8E8'];

        items.forEach(function (w, idx) {
            var nama = (w.nama || w.name || '').trim();
            var msg = (w.ucapan || w.pesan || w.msg || '').trim();
            var hadir = formatHadir(w.kehadiran || w.hadir || w.confirm || '');
            var item = document.createElement('div');

            if (islamStyle) {
                item.className = 'event-card rounded-xl p-4 text-left';
                var top = document.createElement('div');
                top.className = 'flex justify-between mb-1';
                var nameEl = document.createElement('p');
                nameEl.className = 'font-display text-emerald-900';
                nameEl.textContent = nama;
                var badge = document.createElement('span');
                badge.className = 'font-kufi text-[0.6rem] tracking-widest text-gold-600';
                badge.textContent = (hadir || '').toUpperCase();
                top.appendChild(nameEl);
                top.appendChild(badge);
                var msgEl = document.createElement('p');
                msgEl.className = 'text-sm text-ink/70';
                msgEl.textContent = msg;
                item.appendChild(top);
                item.appendChild(msgEl);
            } else if (ultahStyle && list.id === 'guestWall') {
                item.className = 'sticky-note rounded-2xl p-5';
                item.style.background = wallColors[idx % wallColors.length];
                var quote = document.createElement('p');
                quote.className = 'font-hand text-xl leading-snug';
                quote.textContent = '"' + msg + '"';
                var by = document.createElement('p');
                by.className = 'font-display font-bold text-sm mt-3';
                by.textContent = '— ' + nama + (hadir ? ' · ' + hadir : '');
                item.appendChild(quote);
                item.appendChild(by);
            } else if (ultahStyle) {
                item.className = 'bg-white rounded-2xl p-4';
                item.style.boxShadow = '0 6px 16px rgba(92,59,77,.08)';
                var title = document.createElement('p');
                title.className = 'font-display font-600';
                title.textContent = nama + (hadir ? ' — ' + hadir : '');
                item.appendChild(title);
                if (msg) {
                    var body = document.createElement('p');
                    body.className = 'text-sm text-[var(--ink-soft)] mt-1';
                    body.textContent = msg;
                    item.appendChild(body);
                }
            } else {
                var who = nama + (hadir ? ' \u00B7 ' + hadir : '');
                item.className = 'wish-item';
                item.innerHTML = '<div class="who"></div><div class="msg"></div>';
                item.querySelector('.who').textContent = who;
                item.querySelector('.msg').textContent = msg;
            }
            list.appendChild(item);
        });
    }

    // Timpa render localStorage bawaan template dengan data DB
    if (cfg.wishes && cfg.wishes.length) {
        renderWishes(cfg.wishes);
    } else if (list && list.id === 'wishList') {
        list.innerHTML = '';
    }

    var FORBIDDEN_CHARS = /[<>'"]/;
    var UCAPAN_MAX = 60;

    var fmsgEl = fieldByIds(['fmsg', 'fPesan', 'rsvpMsg']);
    if (fmsgEl) {
        fmsgEl.setAttribute('maxlength', String(UCAPAN_MAX));
    }

    function readKehadiran() {
        var confirmEl = fieldByIds(['fconfirm', 'fHadir']);
        if (confirmEl && confirmEl.value) return confirmEl.value;

        if (form) {
            var radio = form.querySelector('input[name="hadir"]:checked');
            if (radio && radio.value) return radio.value;

            var chip = form.querySelector('.chip-btn.selected, .chip.selected');
            if (chip) return chip.getAttribute('data-val') || chip.textContent.trim() || 'Hadir';
        }
        return 'Hadir';
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var namaEl = fieldByIds(['fname', 'fNama', 'rsvpName'])
                || form.querySelector('input[type="text"], input:not([type]), input[type="search"]');
            var msgEl = fieldByIds(['fmsg', 'fPesan', 'rsvpMsg'])
                || form.querySelector('textarea');

            var nama = ((namaEl || {}).value || '').trim();
            var kehadiranRaw = readKehadiran();
            var ucapan = ((msgEl || {}).value || '').trim();

            // Ultah: ucapan boleh kosong → isi default singkat agar lolos validasi API
            if (!ucapan && (fieldByIds(['rsvpName', 'rsvpMsg']) || document.getElementById('guestWall') || document.getElementById('rsvpList'))) {
                ucapan = 'Semoga harinya menyenangkan!';
            }

            if (!nama || !ucapan) {
                alert('Nama dan ucapan wajib diisi.');
                return;
            }
            if (ucapan.length > UCAPAN_MAX) {
                alert('Ucapan maksimal ' + UCAPAN_MAX + ' karakter.');
                return;
            }
            if (FORBIDDEN_CHARS.test(nama) || FORBIDDEN_CHARS.test(ucapan)) {
                alert('Nama/ucapan tidak boleh berisi karakter < > \' "');
                return;
            }

            var kehadiran = kehadiranRaw.toLowerCase().indexOf('tidak') >= 0 ? 'tidak_hadir' : 'hadir';

            var body = new FormData();
            if (cfg.csrf) body.append('_token', cfg.csrf);
            body.append('nama', nama);
            body.append('ucapan', ucapan);
            body.append('kehadiran', kehadiran);

            var btn = form.querySelector('button[type="submit"], .submit-btn, button.open-btn');
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
                        return res.json().then(function (payload) {
                            var msg = 'Data ucapan tidak valid. Cek nama & isi ucapan.';
                            if (payload && payload.errors) {
                                var first = Object.values(payload.errors)[0];
                                if (first && first[0]) msg = first[0];
                            } else if (payload && payload.message) {
                                msg = payload.message;
                            }
                            throw new Error(msg);
                        });
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
                    var jumlah = document.getElementById('fJumlah') || document.getElementById('rsvpCount');
                    if (jumlah) jumlah.value = '1';
                    form.querySelectorAll('.chip-btn.selected, .chip.selected').forEach(function (c) {
                        c.classList.remove('selected');
                    });
                    if (typeof window.showAppToast === 'function') {
                        window.showAppToast('Ucapan terkirim');
                    } else if (typeof window.toast === 'function') {
                        window.toast('Terima kasih atas doanya');
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
        var mapSel = 'a.map-btn, a.event-map, a[data-maps], a[href*="google.com/maps"], a[href*="maps.google"], a[href*="maps.app.goo.gl"], a[href*="goo.gl/maps"]';
        var mapLinks = document.querySelectorAll(mapSel);
        mapLinks.forEach(function (a, i) {
            var u = i === 0
                ? (cfg.mapsUrl || cfg.mapsUrlResepsi)
                : (cfg.mapsUrlResepsi || cfg.mapsUrl);
            if (!u) return;
            a.href = u;
            a.target = '_blank';
            a.rel = 'noopener noreferrer';
        });

        // Kalau kartu event belum punya tombol maps (mis. LIHAT PETA), baru tambahkan
        document.querySelectorAll('.event-card').forEach(function (card, i) {
            if (card.querySelector(mapSel)) return;
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
