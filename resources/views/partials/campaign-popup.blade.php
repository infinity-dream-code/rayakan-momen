@if (!empty($campaign['image_url']))
    <div id="campaignPopup" class="campaign-popup is-hidden" role="dialog" aria-modal="true" aria-label="Campaign">
        <div class="campaign-popup__backdrop" data-campaign-close></div>
        <div class="campaign-popup__box">
            <button type="button" class="campaign-popup__close" data-campaign-close aria-label="Tutup">&times;</button>
            <img src="{{ $campaign['image_url'] }}" alt="Campaign Rayakan Momen" class="campaign-popup__img"
                width="640" height="640" decoding="async">
        </div>
    </div>
    <style>
        .campaign-popup {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 1;
            visibility: visible;
            transition: opacity .35s ease, visibility .35s ease;
        }

        .campaign-popup.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .campaign-popup__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(14, 19, 32, .72);
            backdrop-filter: blur(2px);
        }

        .campaign-popup__box {
            position: relative;
            z-index: 1;
            max-width: min(92vw, 520px);
            width: 100%;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .35);
            background: #fff;
        }

        .campaign-popup__img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
            background: #faf7f2;
        }

        .campaign-popup__close {
            position: absolute;
            top: .5rem;
            right: .5rem;
            z-index: 2;
            width: 2.25rem;
            height: 2.25rem;
            border: 0;
            border-radius: 9999px;
            background: rgba(14, 19, 32, .72);
            color: #fff;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .campaign-popup__close:hover {
            background: rgba(14, 19, 32, .9);
        }
    </style>
    <script>
        (function() {
            var popup = document.getElementById('campaignPopup');
            if (!popup) return;

            var showDelay = 1500;
            var visibleMs = 2500;
            var showTimer;
            var closeTimer;

            function closePopup() {
                popup.classList.add('is-hidden');
                if (showTimer) clearTimeout(showTimer);
                if (closeTimer) clearTimeout(closeTimer);
            }

            function showPopup() {
                popup.classList.remove('is-hidden');
                closeTimer = setTimeout(closePopup, visibleMs);
            }

            popup.querySelectorAll('[data-campaign-close]').forEach(function(el) {
                el.addEventListener('click', closePopup);
            });

            showTimer = setTimeout(showPopup, showDelay);
        })();
    </script>
@endif
