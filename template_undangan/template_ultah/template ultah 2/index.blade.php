<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Undangan Ulang Tahun — Keiko</title>
<meta name="description" content="Undangan ulang tahun digital yang manis dan interaktif">

<!-- Tailwind -->
<script src="assets/js/tailwindcss_3259848.js"></script>

<!-- Fonts -->
<link rel="stylesheet" href="assets/fonts.css">
<link rel="stylesheet" href="assets/css/all.min_5924267.css">

<!-- Confetti fisik & animasi halus (open-source, MIT license) -->
<script src="assets/js/confetti.browser.min_5029321.js"></script>
<script src="assets/js/gsap.min_6404095.js"></script>
<script src="assets/js/ScrollTrigger.min_4920653.js"></script>

<style>
  :root{
    --cream:#F8F6FF;
    --cream-2:#F2EDFF;
    --pink-blush:#F4D8FF;
    --pink-mid:#EAB8FF;
    --pink-deep:#BB6BFF;
    --pink-deeper:#8E44EC;
    --lavender:#DCCBFF;
    --lavender-deep:#9F7CF8;
    --mint:#C9F7EF;
    --gold:#FFD889;
    --ink:#44335E;
    --ink-soft:#72608E;
    --frame-w:min(100vw, 480px);
    --frame-h:100vh;
    --frame-left:max(0px, calc(50% - min(240px, 50vw)));
    --frame-top:0px;
  }

  *{ box-sizing:border-box; }

  html{ scroll-behavior:smooth; }

  body{
    font-family:'Quicksand', sans-serif;
    color:var(--ink);
    background:var(--cream);
    overflow-x:hidden;
  }

  .font-display{ font-family:'Fredoka', sans-serif; }
  .font-script{ font-family:'Caveat', cursive; }

  /* ---------- Scroll container acts as the "phone frame" on desktop ---------- */
  .stage{
    width:var(--frame-w);
    margin:var(--frame-top) auto 0;
    position:relative;
    background:linear-gradient(180deg,var(--cream) 0%,var(--cream-2) 100%);
    min-height:var(--frame-h);
    box-shadow:0 0 80px rgba(232,85,133,0.15);
    overflow:hidden;
  }

  @media(min-width:481px){
    :root{
      --frame-h:min(calc(100vh - 48px), 920px);
      --frame-top:calc((100vh - var(--frame-h)) / 2);
    }
    body{
      background:linear-gradient(135deg,#1e1833,#30224f);
      min-height:100vh;
    }
    .stage{
      margin:var(--frame-top) auto;
      border-radius:32px;
    }
  }

  /* ---------- Cover ---------- */
  #cover{
    position:fixed; z-index:50;
    top:var(--frame-top);
    left:var(--frame-left);
    width:var(--frame-w);
    height:var(--frame-h);
    background:#E8D8FF;
    background-image:radial-gradient(circle at 50% 15%, #F6EEFF 0%, #E8D8FF 42%, #C8A6FF 100%);
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    text-align:center; padding:28px 28px 16vh;
    overflow:hidden;
    isolation:isolate;
  }
  #cover::before{
    content:''; position:absolute; inset:0; z-index:0; pointer-events:none;
    background:url('assets/images/night_sky_alt.jpg') center/cover;
    opacity:.12;
  }
  #cover::after{
    content:''; position:absolute; inset:0; z-index:0; pointer-events:none;
    background:linear-gradient(180deg, rgba(255,255,255,.08) 0%, rgba(142,68,236,.12) 55%, rgba(90,45,166,.18) 100%);
  }
  #cover > *:not(#coverFlash){ position:relative; z-index:1; }
  @media(min-width:481px){ #cover{ border-radius:32px; } }


  .cover-deco{
    position:absolute; opacity:.75; color:#fff;
    animation:float-sparkle 4s ease-in-out infinite;
    text-shadow:0 2px 8px rgba(232,85,133,.35);
    pointer-events:none; z-index:1;
  }
  .cover-ribbon{
    position:absolute; top:0; left:0; right:0; height:72px; pointer-events:none; z-index:1;
    background:url('assets/images/Party_Popper_8592339.png') center top/56px no-repeat;
    opacity:.9;
  }
  .cover-mascot-img{
    width:150px; height:150px; object-fit:contain;
    filter:drop-shadow(0 8px 18px rgba(92,59,77,.18));
  }
  .aurora-card{
    background:linear-gradient(135deg, rgba(187,107,255,.12), rgba(255,216,137,.18));
    backdrop-filter:blur(2px);
    border:1px solid rgba(255,255,255,.45);
  }
  #cover:not(.cover-ready) .cover-line,
  #cover:not(.cover-ready) #coverMascot,
  #cover:not(.cover-ready) #openBtn,
  #cover:not(.cover-ready) .cover-sparkle,
  #cover:not(.cover-ready) .cover-deco,
  #cover:not(.cover-ready) .cover-ribbon{ opacity:0; }

  #cover.opened{ transform:translateY(-100%); opacity:0.4; pointer-events:none; transition:transform 0.9s cubic-bezier(.76,0,.24,1), opacity 0.9s ease; }

  .cover-sparkle{
    position:absolute; opacity:.8; animation:float-sparkle 4s ease-in-out infinite;
  }

  @keyframes float-sparkle{
    0%,100%{ transform:translateY(0) rotate(0deg); }
    50%{ transform:translateY(-14px) rotate(15deg); }
  }

  .open-btn{
    background:linear-gradient(135deg,#C56CFF,#8E44EC);
    border:2px solid rgba(255,255,255,.42);
    box-shadow:0 10px 0 #B33764, 0 16px 26px rgba(142,68,236,.42);
    transition:transform .15s ease, box-shadow .15s ease, filter .15s ease;
    font-weight:700;
    min-width:240px;
  }
  .open-btn:hover{ filter:brightness(1.04); }
  .open-btn:active{ transform:translateY(7px); box-shadow:0 3px 0 #B33764, 0 7px 14px rgba(142,68,236,.35); }

  .cover-tagline{ display:none; }
  .cover-name{
    color:#ffffff;
    text-shadow:0 6px 24px rgba(90,45,166,.55), 0 2px 4px rgba(0,0,0,.22);
    letter-spacing:.04em;
  }
  .cover-age{
    color:#4C1D95;
    background:rgba(255,255,255,.72);
    border:1px solid rgba(255,255,255,.82);
    padding:7px 18px;
    border-radius:999px;
    display:inline-block;
    box-shadow:0 6px 18px rgba(90,45,166,.16);
  }
  .cover-guest-card{
    background:rgba(255,255,255,.6);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.85);
    border-radius:22px;
    padding:14px 28px 12px;
    margin-bottom:28px;
    box-shadow:0 10px 30px rgba(90,45,166,.14);
  }
  .cover-guest-label{
    color:#7C5CD6;
    font-size:.78rem;
    letter-spacing:.14em;
    text-transform:uppercase;
    font-weight:600;
  }
  .cover-guest-name{
    color:#2E1A47;
    font-weight:600;
    margin-top:2px;
  }
  .cover-cta-note{ display:none; }
  .open-btn{
    animation:cover-cta-glow 2.2s ease-in-out infinite;
  }
  @keyframes cover-cta-glow{
    0%,100%{ box-shadow:0 10px 0 #B33764, 0 16px 26px rgba(142,68,236,.42); }
    50%{ box-shadow:0 10px 0 #B33764, 0 20px 32px rgba(142,68,236,.5); }
  }

  /* ---------- Section reveal ---------- */
  .reveal{ opacity:0; transform:translateY(28px); transition:opacity .8s ease, transform .8s ease; }
  .reveal.in{ opacity:1; transform:translateY(0); }

  /* ---------- Floating balloons / confetti backdrop ---------- */
  .balloon{ position:absolute; z-index:1; animation:balloon-float linear infinite; opacity:.9; }
  @keyframes balloon-float{
    0%{ transform:translateY(0) translateX(0) rotate(-4deg); opacity:0; }
    12%{ opacity:.95; }
    50%{ transform:translateY(-28vh) translateX(12px) rotate(4deg); opacity:.95; }
    100%{ transform:translateY(-55vh) translateX(-6px) rotate(-4deg); opacity:0; }
  }

  .butterfly{ position:absolute; animation:butterfly-fly linear infinite; }
  .butterfly .wing-l, .butterfly .wing-r{ transform-origin:50% 50%; animation:wing-flap 0.35s ease-in-out infinite alternate; }
  .butterfly .wing-r{ animation-delay:0.02s; }
  @keyframes wing-flap{
    0%{ transform:scaleX(1); }
    100%{ transform:scaleX(0.35); }
  }
  @keyframes butterfly-fly{
    0%{ transform:translate(0,0) rotate(0deg); }
    20%{ transform:translate(-24px,-30px) rotate(-8deg); }
    40%{ transform:translate(-40px,-70px) rotate(6deg); }
    60%{ transform:translate(-10px,-110px) rotate(-6deg); }
    80%{ transform:translate(20px,-150px) rotate(8deg); }
    100%{ transform:translate(0,-190px) rotate(0deg); opacity:0; }
  }

  .confetti-piece{ position:absolute; top:-10px; will-change:transform; animation:confetti-fall linear forwards; }
  @keyframes confetti-fall{
    to{ transform:translateY(120vh) rotate(720deg); opacity:0; }
  }

  /* ---------- Blob decorative shapes ---------- */
  .blob{ position:absolute; border-radius:50%; filter:blur(0px); }

  /* ---------- Dividers ---------- */
  .scallop{
    height:22px;
    background-image:radial-gradient(circle at 10px -6px, transparent 12px, var(--cream) 13px);
    background-size:24px 24px;
    background-repeat:repeat-x;
    position:relative;
    z-index:5;
  }
  .section-front{
    position:relative;
    z-index:5;
  }

  /* ---------- Countdown box ---------- */
  .time-box{
    background:#fff;
    border:2px solid var(--pink-blush);
    box-shadow:0 6px 0 var(--pink-blush);
    transition:transform .3s ease;
  }
  .time-box.pop{ transform:scale(1.08); }
  .countdown-icon{ font-size:1.1rem; color:var(--pink-deep); margin-bottom:2px; }

  /* ---------- Star cake / blow hold ---------- */
  .star-cake{
    position:relative; width:240px; height:260px; margin:0 auto;
    filter:drop-shadow(0 14px 24px rgba(68,51,94,.22));
  }
  .star-cake svg{ display:block; margin:0 auto; }
  .candle-stick{
    width:5px; height:22px; border-radius:2px;
    background:linear-gradient(180deg,#fff6d0,#ffd889);
    box-shadow:0 0 0 1px rgba(255,255,255,.25);
  }
  .star-flame{
    width:14px; height:18px; border-radius:50% 50% 50% 50% / 60% 60% 40% 40%;
    background:radial-gradient(circle at 50% 20%, #fff6b0, #ff9f43 70%, #ff6b6b);
    animation:flicker 1s ease-in-out infinite alternate;
    box-shadow:0 0 12px rgba(255,180,80,.8);
  }
  @keyframes flicker{
    0%{ transform:scaleY(1) rotate(-2deg); }
    100%{ transform:scaleY(1.12) rotate(3deg); }
  }
  .star-flame.off{ opacity:0; transform:scale(.4); transition:opacity .35s ease, transform .35s ease; animation:none; }
  .blow-btn{
    background:linear-gradient(135deg,#7dd3fc,#60a5fa);
    box-shadow:0 6px 0 #3b82c6, 0 10px 20px rgba(59,130,246,.28);
    transition:transform .12s ease, box-shadow .12s ease;
  }
  .blow-btn:active, .blow-btn.holding{
    transform:translateY(4px);
    box-shadow:0 2px 0 #3b82c6, 0 4px 10px rgba(59,130,246,.28);
  }
  .blow-meter{
    height:10px; border-radius:999px; background:rgba(255,255,255,.25);
    overflow:hidden; max-width:240px; margin:12px auto 0;
  }
  .blow-meter-fill{
    height:100%; width:0%; border-radius:999px;
    background:linear-gradient(90deg,#7dd3fc,#bb6bff);
    transition:width .08s linear;
  }

  /* ---------- Avatar dress-up ---------- */
  .avatar-stage{
    width:min(260px, 72vw); margin:0 auto 18px; padding:18px;
    border-radius:28px; background:rgba(255,255,255,.72);
    box-shadow:0 12px 30px rgba(68,51,94,.12);
  }
  .outfit-btn{
    width:58px; height:58px; border-radius:18px; border:3px solid #fff;
    box-shadow:0 6px 14px rgba(68,51,94,.14);
    display:flex; align-items:center; justify-content:center;
    transition:transform .15s ease, box-shadow .15s ease;
    cursor:pointer;
  }
  .outfit-btn i{ color:#fff; font-size:1.2rem; }
  .outfit-btn.active{ transform:translateY(-4px) scale(1.06); box-shadow:0 10px 20px rgba(68,51,94,.2); outline:3px solid rgba(187,107,255,.35); }
  #avatarDress{ transition:fill .35s ease; }
  #avatarBow{ transition:fill .35s ease; }

  /* ---------- Capsule gacha ---------- */
  .capsule-machine{
    width:min(300px, 86vw); margin:0 auto; position:relative;
    background:linear-gradient(180deg,#c084fc 0%,#9333ea 45%,#7e22ce 100%);
    border-radius:26px 26px 22px 22px;
    border:4px solid #c4b5fd;
    box-shadow:0 22px 44px rgba(126,34,206,.32), inset 0 2px 0 rgba(255,255,255,.35), inset 0 -5px 0 rgba(0,0,0,.12);
    overflow:visible;
  }
  .capsule-machine.shake{ animation:capsule-shake .55s ease; }
  @keyframes capsule-shake{
    0%,100%{ transform:translateX(0); }
    15%{ transform:translateX(-5px) rotate(-.6deg); }
    30%{ transform:translateX(5px) rotate(.6deg); }
    45%{ transform:translateX(-4px); }
    60%{ transform:translateX(4px); }
    75%{ transform:translateX(-2px); }
  }
  .capsule-header{
    background:linear-gradient(180deg,#f3e8ff,#e9d5ff);
    border-radius:22px 22px 0 0; padding:10px 14px 8px;
    border-bottom:3px solid rgba(255,255,255,.45); text-align:center;
  }
  .capsule-header span{
    font-family:'Fredoka',sans-serif; font-weight:700; font-size:.92rem;
    letter-spacing:.12em; color:#6d28d9;
    text-shadow:0 0 12px rgba(187,107,255,.45), 0 1px 0 rgba(255,255,255,.85);
  }
  .capsule-body{ display:flex; gap:10px; align-items:flex-end; padding:16px 16px 12px; }
  .capsule-window{
    flex:1; height:148px; border-radius:18px; position:relative; overflow:hidden;
    background:linear-gradient(180deg,rgba(255,255,255,.22) 0%,rgba(255,255,255,.06) 100%);
    border:3px solid rgba(255,255,255,.55);
    box-shadow:inset 0 6px 24px rgba(0,0,0,.14), inset 0 -3px 10px rgba(255,255,255,.18);
  }
  .capsule-window::before{
    content:''; position:absolute; top:6px; left:10px; right:12px; height:38%;
    background:linear-gradient(180deg,rgba(255,255,255,.38),transparent);
    border-radius:14px; pointer-events:none; z-index:2;
  }
  .capsule-orbs{ position:absolute; inset:12px 10px 36px; z-index:1; }
  .capsule-orb{
    position:absolute; border-radius:50%;
    box-shadow:inset -3px -5px 8px rgba(0,0,0,.14), 0 3px 8px rgba(0,0,0,.1);
    animation:orb-bob 2.8s ease-in-out infinite;
  }
  @keyframes orb-bob{
    0%,100%{ transform:translateY(0); }
    50%{ transform:translateY(-5px); }
  }
  .capsule-chute-label{
    position:absolute; bottom:6px; left:50%; transform:translateX(-50%);
    font-size:.6rem; font-weight:600; letter-spacing:.06em; color:rgba(255,255,255,.55);
    z-index:2;
  }
  .capsule-lever-box{
    width:58px; flex-shrink:0; display:flex; flex-direction:column;
    align-items:center; padding-bottom:4px;
  }
  .capsule-lever-arm{
    display:flex; flex-direction:column; align-items:center;
    transform-origin:bottom center;
    transition:transform .35s cubic-bezier(.34,1.56,.64,1);
  }
  .capsule-lever-box.pulling .capsule-lever-arm{ transform:rotate(-32deg); }
  .capsule-lever-stick{
    width:14px; height:76px; border-radius:7px;
    background:linear-gradient(90deg,#64748b,#e2e8f0 45%,#94a3b8);
    box-shadow:2px 0 6px rgba(0,0,0,.22);
  }
  .capsule-lever-knob{
    width:32px; height:32px; border-radius:50%; margin-top:-6px; cursor:pointer;
    background:radial-gradient(circle at 35% 28%,#ffc4d6,#e85585 55%,#be185d);
    border:3px solid rgba(255,255,255,.65);
    box-shadow:0 5px 12px rgba(232,85,133,.45), inset 0 -3px 5px rgba(0,0,0,.12);
    transition:transform .15s ease;
  }
  .capsule-lever-knob:active{ transform:scale(.94); }
  .capsule-lever-knob:disabled{ opacity:.6; cursor:not-allowed; }
  .capsule-lever-label{
    margin-top:6px; font-size:.65rem; font-weight:600; color:rgba(255,255,255,.85);
    letter-spacing:.04em;
  }
  .capsule-slot{
    height:52px; margin:0 18px 16px; position:relative;
    background:linear-gradient(180deg,#1a1430,#2a2145);
    border-radius:0 0 14px 14px;
    border:3px solid rgba(255,255,255,.28); border-top:2px solid rgba(0,0,0,.25);
    overflow:hidden;
  }
  .capsule-slot::before{
    content:''; position:absolute; top:0; left:50%; transform:translateX(-50%);
    width:54px; height:6px; border-radius:0 0 8px 8px;
    background:rgba(0,0,0,.35);
  }
  .capsule-ball{
    width:46px; height:46px; border-radius:50%; position:absolute;
    left:50%; top:-60px; transform:translateX(-50%);
    opacity:0; pointer-events:none;
    box-shadow:inset -5px -8px 12px rgba(0,0,0,.15), 0 6px 14px rgba(0,0,0,.2);
  }
  .capsule-ball.half{
    background:linear-gradient(180deg, var(--cap-top,#fff) 48%, var(--cap-bot,#bb6bff) 52%);
  }
  .capsule-ball.half::after{
    content:''; position:absolute; left:4px; right:4px; top:50%; height:3px;
    background:rgba(0,0,0,.12); border-radius:2px;
  }
  .capsule-ball.drop{
    opacity:1;
    animation:capsule-drop .85s cubic-bezier(.34,1.35,.64,1) forwards;
  }
  @keyframes capsule-drop{
    0%{ top:-60px; transform:translateX(-50%) rotate(0deg); }
    55%{ top:6px; transform:translateX(-50%) rotate(200deg); }
    72%{ top:-3px; transform:translateX(-50%) rotate(230deg); }
    100%{ top:4px; transform:translateX(-50%) rotate(210deg); }
  }
  .capsule-stars{
    position:absolute; inset:0; pointer-events:none; overflow:hidden; border-radius:inherit;
  }
  .capsule-star{
    position:absolute; color:rgba(255,255,255,.35); font-size:.7rem;
    animation:star-twinkle 2s ease-in-out infinite;
  }
  @keyframes star-twinkle{
    0%,100%{ opacity:.25; transform:scale(1); }
    50%{ opacity:.8; transform:scale(1.15); }
  }
  .capsule-result-pop{
    animation:result-pop .45s cubic-bezier(.34,1.4,.64,1);
  }
  @keyframes result-pop{
    0%{ opacity:0; transform:translateY(8px) scale(.92); }
    100%{ opacity:1; transform:translateY(0) scale(1); }
  }

  /* ---------- Film strip gallery ---------- */
  .film-wrap{
    overflow:hidden; padding:10px 0 18px;
    mask-image:linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
    -webkit-mask-image:linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
  }
  .film-marquee{
    display:flex; width:max-content;
    animation:film-scroll 28s linear infinite;
  }
  .film-marquee:hover{ animation-play-state:paused; }
  @keyframes film-scroll{
    0%{ transform:translateX(0); }
    100%{ transform:translateX(-50%); }
  }
  .film-strip{
    display:flex; gap:14px; flex:0 0 auto;
    background:
      repeating-linear-gradient(90deg, #2f2548 0 10px, transparent 10px 18px),
      linear-gradient(180deg,#f8f6ff,#efe8ff);
    border-radius:14px; padding:16px 12px;
    box-shadow:0 12px 28px rgba(68,51,94,.12);
  }
  .film-frame{
    flex:0 0 auto;
    width:150px; background:#fff; padding:8px 8px 22px;
    box-shadow:0 8px 18px rgba(68,51,94,.12);
    transform:rotate(var(--tilt, -2deg));
    transition:transform .25s ease;
    cursor:pointer;
  }
  .film-frame:nth-child(even){ --tilt:2deg; }
  .film-frame:hover{ transform:rotate(0deg) scale(1.04); z-index:2; }
  .film-frame img{ width:100%; aspect-ratio:1; object-fit:cover; border-radius:4px; display:block; }
  .film-frame .cap{ font-family:'Caveat',cursive; text-align:center; font-size:1.1rem; color:var(--ink-soft); margin-top:6px; }

  /* ---------- Photo grid (2 kolom) ---------- */
  .photo-grid-card{
    background:#fff; border-radius:16px; overflow:hidden;
    box-shadow:0 8px 20px rgba(68,51,94,.1);
    transition:transform .25s ease, box-shadow .25s ease;
    cursor:pointer;
  }
  .photo-grid-card:hover{ transform:translateY(-4px); box-shadow:0 14px 28px rgba(68,51,94,.16); }
  .photo-grid-card img{ width:100%; aspect-ratio:1; object-fit:cover; display:block; }
  .photo-grid-card .cap{
    padding:8px 10px 12px; font-family:'Caveat',cursive;
    font-size:1.05rem; color:var(--ink-soft); text-align:center;
  }

  /* ---------- RSVP ---------- */
  .input-cute{
    background:#fff;
    border:2px solid var(--pink-blush);
    transition:border-color .2s ease, box-shadow .2s ease;
  }
  .input-cute:focus{ outline:none; border-color:var(--pink-deep); box-shadow:0 0 0 4px rgba(255,111,156,.15); }

  .chip{
    border:2px solid var(--pink-blush); transition:all .15s ease;
  }
  .chip.selected{ background:var(--pink-deep); border-color:var(--pink-deep); color:#fff; }

  /* ---------- RSVP ---------- */
  #musicToggle{
    position:fixed;
    left:calc(var(--frame-left) + var(--frame-w) - 68px);
    bottom:max(24px, calc(var(--frame-top) + 16px + env(safe-area-inset-bottom, 0px)));
    z-index:40;
    width:52px; height:52px;
    border-radius:50%;
    background:#fff;
    border:2px solid var(--pink-mid);
    display:none;
    align-items:center; justify-content:center;
    box-shadow:0 4px 14px rgba(92,59,77,.22);
  }
  #musicToggle.show{ display:flex; }
  #musicToggle i{ color:var(--pink-deep); font-size:1.15rem; }

  /* Reduced motion */
  @media (prefers-reduced-motion: reduce){
    *{ animation-duration:0.01ms !important; animation-iteration-count:1 !important; transition-duration:0.01ms !important; }
  }

  ::selection{ background:var(--pink-deep); color:#fff; }

  /* ---------- Scroll progress bar ---------- */
  #scrollBar{
    position:fixed;
    top:var(--frame-top);
    left:var(--frame-left);
    width:var(--frame-w);
    height:4px; z-index:45; pointer-events:none;
    opacity:0; visibility:hidden; transition:opacity .3s ease;
  }
  #scrollBar.show{ opacity:1; visibility:visible; }
  @media(min-width:481px){
    #scrollBar{ border-radius:32px 32px 0 0; overflow:hidden; }
  }
  #scrollBarFill{
    height:100%; width:0%;
    background:linear-gradient(90deg, var(--pink-deep), var(--lavender-deep), var(--gold));
    transition:width .08s linear;
  }

  /* ---------- Subtle dot texture ---------- */
  .dot-texture{
    background-image:radial-gradient(rgba(255,111,156,0.12) 1.5px, transparent 1.5px);
    background-size:20px 20px;
  }

  /* ---------- Button micro-interaction ---------- */
  .btn-pop{ transition:transform .12s ease; }
  .btn-pop:hover{ transform:translateY(-2px) scale(1.02); }
  .btn-pop:active{ transform:translateY(1px) scale(0.98); }

  .ripple{
    position:absolute; border-radius:50%; background:rgba(255,255,255,.55);
    transform:scale(0); animation:ripple-anim .6s ease-out forwards; pointer-events:none;
  }
  @keyframes ripple-anim{ to{ transform:scale(3.2); opacity:0; } }

  /* ---------- Letter stagger heading ---------- */
  .split-char{ display:inline-block; opacity:0; transform:translateY(14px); }

  /* ---------- Card corner sparkle ---------- */
  .corner-sparkle{ position:absolute; top:-6px; right:-6px; opacity:.9; }

  /* ---------- Timeline ---------- */
  .timeline-line{ position:absolute; left:50%; top:0; bottom:0; width:3px; background:var(--pink-blush); transform:translateX(-50%); }
  .timeline-line-fill{ position:absolute; left:0; top:0; width:100%; height:0%; background:linear-gradient(180deg,var(--pink-deep),var(--lavender-deep)); }
  .timeline-item{ opacity:0; transform:translateY(24px); transition:opacity .7s ease, transform .7s ease; }
  .timeline-item.in{ opacity:1; transform:translateY(0); }
  .timeline-dot{ width:16px; height:16px; border-radius:50%; background:var(--pink-deep); border:3px solid #fff; box-shadow:0 0 0 3px var(--pink-blush); }

  /* ---------- Prize wheel (removed - replaced by capsule) ---------- */

  /* ---------- Lightbox ---------- */
  #lightbox{
    position:fixed; inset:0; z-index:90; background:rgba(45,20,32,.88);
    display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none;
    transition:opacity .3s ease; padding:24px;
  }
  #lightbox.show{ opacity:1; pointer-events:auto; }
  #lightbox .box{ background:#fff; border-radius:20px; padding:14px 14px 30px; max-width:340px; width:100%; transform:scale(.85); transition:transform .3s ease; }
  #lightbox.show .box{ transform:scale(1); }

  /* ---------- Guestbook wall ---------- */
  .note-card{ transition:transform .2s ease; }
  .note-card:hover{ transform:translateY(-4px) rotate(0deg) !important; }

  /* ---------- Cursor heart trail ---------- */
  .heart-trail{ position:fixed; pointer-events:none; z-index:80; will-change:transform,opacity; }
</style>
</head>
<body class="dot-texture">

<div id="scrollBar"><div id="scrollBarFill"></div></div>

<!-- ============ COVER ============ -->
<div id="cover">
  <div id="coverFlash" class="pointer-events-none absolute inset-0 z-20" style="background:#fff; opacity:0;"></div>
  <div class="cover-ribbon" aria-hidden="true"></div>

  <i class="cover-deco fa-solid fa-star" style="top:10%; left:10%; font-size:1.1rem; animation-delay:0s;"></i>
  <i class="cover-deco fa-solid fa-heart" style="top:16%; right:12%; font-size:1rem; animation-delay:.8s;"></i>
  <i class="cover-deco fa-solid fa-gift" style="bottom:18%; left:14%; font-size:1.2rem; animation-delay:1.4s;"></i>
  <i class="cover-deco fa-solid fa-cake-candles" style="bottom:22%; right:10%; font-size:1.1rem; animation-delay:.4s;"></i>
  <i class="cover-deco fa-solid fa-wand-magic-sparkles" style="top:28%; left:8%; font-size:.95rem; animation-delay:1.8s;"></i>

  <svg class="cover-sparkle" style="top:8%; left:12%; width:26px;" viewBox="0 0 24 24" fill="#fff"><path d="M12 0l2.2 8.8L23 12l-8.8 2.2L12 23l-2.2-8.8L1 12l8.8-2.2z"/></svg>
  <svg class="cover-sparkle" style="top:18%; right:10%; width:18px; animation-delay:1s;" viewBox="0 0 24 24" fill="#fff"><path d="M12 0l2.2 8.8L23 12l-8.8 2.2L12 23l-2.2-8.8L1 12l8.8-2.2z"/></svg>
  <svg class="cover-sparkle" style="bottom:16%; left:16%; width:20px; animation-delay:2s;" viewBox="0 0 24 24" fill="#fff"><path d="M12 0l2.2 8.8L23 12l-8.8 2.2L12 23l-2.2-8.8L1 12l8.8-2.2z"/></svg>

  <img id="coverMascot" class="cover-mascot-img mb-2" src="assets/images/Unicorn_4477257.png" alt="Maskot Keiko" width="150" height="150">

  <h1 class="cover-line cover-name font-display text-5xl sm:text-6xl font-700 mt-3 mb-2">KEIKO</h1>
  <p class="cover-line cover-age font-semibold tracking-wide mb-7"><i class="fa-solid fa-cake-candles mr-1"></i> Ulang tahun ke-7 <i class="fa-solid fa-star ml-1"></i></p>

  <div class="cover-line cover-guest-card">
    <p class="cover-guest-label" id="guestLabel">Kepada Yth.</p>
    <p class="cover-guest-name font-display text-xl" id="guestName">Teman-teman terkasih</p>
  </div>

  <button id="openBtn" class="open-btn cover-line font-display text-white text-lg px-10 py-[15px] rounded-full flex items-center justify-center gap-2">
    <span>Buka Surat Undangan</span>
    <i class="fa-solid fa-envelope-open-text"></i>
  </button>
  <p class="cover-line cover-cta-note">Ketuk tombol untuk mulai ✨</p>
</div>

<!-- ============ STAGE ============ -->
<div class="stage" id="stage">

  <!-- floating decorative balloons (ambient) -->
  <!-- ===== HERO ===== -->
  <section class="relative pt-16 pb-10 px-6 text-center" id="heroSection" style="z-index:1;">
    <div class="pointer-events-none absolute inset-0 overflow-visible" id="ambientBalloons" style="z-index:1;"></div>
    <canvas id="sparkleCanvas" class="pointer-events-none absolute inset-0" style="z-index:0;"></canvas>

    <div class="blob parallax-slow" style="width:220px;height:220px; background:var(--pink-blush); top:-60px; left:-60px; opacity:.6;"></div>
    <div class="blob parallax-fast" style="width:160px;height:160px; background:var(--lavender); top:40px; right:-50px; opacity:.5;"></div>
    <svg class="parallax-fast absolute" style="top:6%; left:8%; width:20px;" viewBox="0 0 24 24" fill="var(--gold)"><path d="M12 0l2.2 8.8L23 12l-8.8 2.2L12 23l-2.2-8.8L1 12l8.8-2.2z"/></svg>
    <svg class="parallax-slow absolute" style="top:20%; right:12%; width:16px;" viewBox="0 0 24 24" fill="var(--pink-deep)"><path d="M12 0l2.2 8.8L23 12l-8.8 2.2L12 23l-2.2-8.8L1 12l8.8-2.2z"/></svg>

    <div class="relative reveal in" id="heroContent" style="z-index:2;">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Selamat datang di Taman Bintang Keiko ✨</p>

      <img src="assets/images/photo-1717205964281-ab2bd111dcb2_8989027.jpg" alt="Keiko" class="mx-auto my-3 w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg" style="box-shadow:0 10px 24px rgba(232,85,133,.25);">

      <h2 class="font-display text-4xl font-700 text-[var(--pink-deeper)] leading-tight" id="heroTitle">Pesta Bintang<br>Keiko ke-7</h2>
      <p class="mt-3 text-[var(--ink)] leading-relaxed font-medium">Malam penuh lampu, tawa, dan kejutan kecil menanti. Yuk datang dan rayakan bersama Keiko — bikin kenangan manis yang nggak akan terlupakan 💜</p>
    </div>
  </section>

  <div class="scallop section-front" style="background-image:radial-gradient(circle at 10px -6px, transparent 12px, var(--pink-blush) 13px);"></div>

  <!-- ===== COUNTDOWN ===== -->
  <section class="reveal section-front aurora-card px-6 py-10 text-center" style="background:linear-gradient(180deg,var(--pink-blush) 0%, var(--pink-mid) 100%);">
    <p class="font-script text-2xl text-white mb-1"><i class="fa-solid fa-hourglass-half mr-1"></i> Menuju Hari Spesialnya</p>
    <h3 class="font-display text-2xl font-600 text-white mb-6"><i class="fa-solid fa-calendar-days mr-1"></i> Sabtu, 15 Agustus 2026</h3>

    <div class="grid grid-cols-4 gap-2 max-w-sm mx-auto" id="countdown">
      <div class="time-box rounded-2xl py-3" id="box-days">
        <div class="countdown-icon"><i class="fa-solid fa-sun"></i></div>
        <div class="font-display text-2xl font-700 text-[var(--pink-deeper)]" id="cd-days">00</div>
        <div class="text-[10px] uppercase tracking-wide text-[var(--ink-soft)] font-semibold">Hari</div>
      </div>
      <div class="time-box rounded-2xl py-3" id="box-hours">
        <div class="countdown-icon"><i class="fa-solid fa-clock"></i></div>
        <div class="font-display text-2xl font-700 text-[var(--pink-deeper)]" id="cd-hours">00</div>
        <div class="text-[10px] uppercase tracking-wide text-[var(--ink-soft)] font-semibold">Jam</div>
      </div>
      <div class="time-box rounded-2xl py-3" id="box-min">
        <div class="countdown-icon"><i class="fa-solid fa-stopwatch"></i></div>
        <div class="font-display text-2xl font-700 text-[var(--pink-deeper)]" id="cd-min">00</div>
        <div class="text-[10px] uppercase tracking-wide text-[var(--ink-soft)] font-semibold">Menit</div>
      </div>
      <div class="time-box rounded-2xl py-3" id="box-sec">
        <div class="countdown-icon"><i class="fa-solid fa-bolt"></i></div>
        <div class="font-display text-2xl font-700 text-[var(--pink-deeper)]" id="cd-sec">00</div>
        <div class="text-[10px] uppercase tracking-wide text-[var(--ink-soft)] font-semibold">Detik</div>
      </div>
    </div>
  </section>

  <div class="scallop" style="transform:rotate(180deg); background-image:radial-gradient(circle at 10px 28px, transparent 12px, var(--cream) 13px);"></div>

  <!-- ===== TIMELINE PERJALANAN ===== -->
  <section class="reveal px-6 py-10 relative">
    <div class="text-center mb-8">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Tumbuh Bersama Tiap Tahun</p>
      <h3 class="font-display text-2xl font-700">Perjalanan Bintang Keiko</h3>
    </div>

    <div class="relative" id="timelineWrap">
      <div class="timeline-line"><div class="timeline-line-fill" id="timelineFill"></div></div>
      <div class="space-y-8" id="timelineItems"></div>
    </div>
  </section>

  <div class="scallop"></div>

  <!-- ===== QUOTE ===== -->
  <section class="reveal aurora-card px-8 py-10 text-center relative">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="var(--pink-blush)" class="mx-auto mb-3"><path d="M7 7c-2.2 0-4 1.8-4 4v6h6v-6H6c0-1.1.9-2 2-2V7zm10 0c-2.2 0-4 1.8-4 4v6h6v-6h-3c0-1.1.9-2 2-2V7z"/></svg>
    <p class="font-script text-2xl text-[var(--pink-deeper)] leading-relaxed">"Setiap doa baikmu di bawah langit malam ini, akan jadi cahaya kecil yang menemani langkah Keiko ke depan."</p>
    <p class="mt-3 text-sm text-[var(--ink-soft)]">— Mama & Papa</p>
  </section>

  <!-- ===== ACARA / DETAIL ===== -->
  <section class="reveal px-6 py-8">
    <div class="text-center mb-6">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Info Pesta</p>
      <h3 class="font-display text-2xl font-700">Kapan & Di Mana?</h3>
    </div>

    <div class="space-y-4">
      <div class="bg-white rounded-3xl p-5 flex items-start gap-4" style="box-shadow:0 8px 20px rgba(92,59,77,.08);">
        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0" style="background:var(--pink-blush)">
          <i class="fa-solid fa-calendar-days text-xl" style="color:var(--pink-deeper)"></i>
        </div>
        <div>
          <p class="font-display font-600 text-lg">Sabtu, 15 Agustus 2026</p>
          <p class="text-sm text-[var(--ink-soft)]">Pukul 10.00 — 13.00 WIB</p>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 flex items-start gap-4" style="box-shadow:0 8px 20px rgba(92,59,77,.08);">
        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0" style="background:var(--lavender)">
          <i class="fa-solid fa-location-dot text-xl" style="color:#7C5CD6"></i>
        </div>
        <div class="flex-1">
          <p class="font-display font-600 text-lg">Moonbeam Party Hall</p>
          <p class="text-sm text-[var(--ink-soft)]">Jl. Bintang Selatan No. 7, Jakarta Selatan</p>
          <div class="mt-3 rounded-2xl overflow-hidden" style="box-shadow:0 4px 14px rgba(92,59,77,.12);">
            <iframe src="https://maps.google.com/maps?q=Jakarta%20Selatan&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="140" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi acara"></iframe>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 flex items-start gap-4" style="box-shadow:0 8px 20px rgba(92,59,77,.08);">
        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0" style="background:var(--mint)">
          <i class="fa-solid fa-wand-magic-sparkles text-xl" style="color:#2FAE73"></i>
        </div>
        <div>
          <p class="font-display font-600 text-lg">Dress code</p>
          <p class="text-sm text-[var(--ink)]">Warna-warna cerah & bintang — scroll ke bawah untuk cobain ganti outfit Keiko! 👗</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== DRESS UP KEIKO ===== -->
  <section class="reveal px-6 py-10 text-center" style="background:linear-gradient(180deg, var(--cream) 0%, var(--lavender) 130%);">
    <p class="font-script text-2xl text-[var(--pink-deeper)] mb-1">Ganti Outfit Keiko</p>
    <h3 class="font-display text-2xl font-700 mb-1">Pilih Warna Baju Favoritmu</h3>
    <p class="text-sm text-[var(--ink)] mb-5">Ketuk warna di bawah — Keiko langsung ganti baju sesuai pilihanmu!</p>

    <div class="avatar-stage">
      <svg id="keikoAvatar" viewBox="0 0 200 260" width="200" height="260" class="mx-auto">
        <ellipse cx="100" cy="248" rx="52" ry="7" fill="#00000012"/>
        <path d="M72 78 Q100 48 128 78 L124 96 Q100 72 76 96 Z" fill="#5a3f72"/>
        <circle cx="100" cy="92" r="34" fill="#ffe8f2"/>
        <circle cx="88" cy="90" r="4" fill="#44335e"/>
        <circle cx="112" cy="90" r="4" fill="#44335e"/>
        <path d="M94 100 Q100 106 106 100" stroke="#44335e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        <ellipse cx="78" cy="98" rx="7" ry="4" fill="#ffb8d2"/>
        <ellipse cx="122" cy="98" rx="7" ry="4" fill="#ffb8d2"/>
        <path id="avatarDress" d="M68 132 Q100 118 132 132 L142 210 Q100 224 58 210 Z" fill="#E85585"/>
        <path id="avatarBow" d="M100 128 L88 118 L88 138 Z M100 128 L112 118 L112 138 Z" fill="#FFD889"/>
        <path d="M58 150 Q42 170 48 196" stroke="#ffe8f2" stroke-width="14" stroke-linecap="round" fill="none"/>
        <path d="M142 150 Q158 170 152 196" stroke="#ffe8f2" stroke-width="14" stroke-linecap="round" fill="none"/>
        <path d="M82 210 L74 248 M118 210 L126 248" stroke="#ffe8f2" stroke-width="12" stroke-linecap="round"/>
      </svg>
    </div>

    <div class="flex justify-center gap-3 flex-wrap max-w-sm mx-auto mb-4" id="outfitPicker">
      <button type="button" class="outfit-btn active" data-color="#E85585" data-name="Merah Glow" style="background:#E85585" aria-label="Baju merah"><i class="fa-solid fa-shirt"></i></button>
      <button type="button" class="outfit-btn" data-color="#BB6BFF" data-name="Lilac Star" style="background:#BB6BFF" aria-label="Baju lilac"><i class="fa-solid fa-shirt"></i></button>
      <button type="button" class="outfit-btn" data-color="#FFD889" data-name="Golden Moon" style="background:#FFD889" aria-label="Baju emas"><i class="fa-solid fa-shirt"></i></button>
      <button type="button" class="outfit-btn" data-color="#5EDFCF" data-name="Mint Dream" style="background:#5EDFCF" aria-label="Baju mint"><i class="fa-solid fa-shirt"></i></button>
      <button type="button" class="outfit-btn" data-color="#A8B4D8" data-name="Silver Night" style="background:#A8B4D8" aria-label="Baju silver"><i class="fa-solid fa-shirt"></i></button>
    </div>
    <p id="outfitMsg" class="font-script text-2xl text-[var(--pink-deeper)]">Keiko pakai baju Merah Glow — cantik banget! ✨</p>
  </section>

  <!-- ===== TIUP BINTANG LILIN ===== -->
  <section class="reveal px-6 py-10 text-center" style="background:linear-gradient(180deg, #2a2145 0%, #1e1833 100%); color:#fff;">
    <p class="font-script text-2xl mb-1" style="color:#ffd889">Ritual Lilin Bintang</p>
    <h3 class="font-display text-2xl font-700 mb-1 text-white">Tiup 7 Lilin Keiko</h3>
    <p class="text-sm mb-6 font-medium" style="color:#e8d8ff">Tahan tombol tiup sampai meter penuh — lalu semua lilin padam serentak! 🌬️</p>

    <div class="star-cake" id="starCake">
      <svg viewBox="0 0 240 260" width="240" height="260" aria-hidden="true">
        <defs>
          <linearGradient id="cakeTopGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#F8F0FF"/>
            <stop offset="100%" stop-color="#E8D8FF"/>
          </linearGradient>
          <linearGradient id="cakeBotGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#BB6BFF"/>
            <stop offset="100%" stop-color="#8E44EC"/>
          </linearGradient>
          <linearGradient id="icingGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#fff"/>
            <stop offset="100%" stop-color="#F4D8FF"/>
          </linearGradient>
        </defs>
        <!-- shadow -->
        <ellipse cx="120" cy="242" rx="88" ry="10" fill="#00000030"/>
        <!-- bottom tier -->
        <rect x="28" y="150" width="184" height="70" rx="14" fill="url(#cakeBotGrad)"/>
        <rect x="28" y="150" width="184" height="18" rx="10" fill="url(#icingGrad)"/>
        <path d="M28 168 Q42 182 56 168 Q70 182 84 168 Q98 182 112 168 Q126 182 140 168 Q154 182 168 168 Q182 182 196 168 Q210 182 212 168" fill="none" stroke="#F4D8FF" stroke-width="6" stroke-linecap="round"/>
        <!-- cherries / sprinkles -->
        <circle cx="52" cy="190" r="5" fill="#FFD889"/>
        <circle cx="88" cy="198" r="5" fill="#fff"/>
        <circle cx="120" cy="188" r="5" fill="#FF9FC0"/>
        <circle cx="152" cy="198" r="5" fill="#fff"/>
        <circle cx="188" cy="190" r="5" fill="#FFD889"/>
        <!-- top tier -->
        <rect x="58" y="98" width="124" height="54" rx="12" fill="url(#cakeTopGrad)" stroke="#D8CBFF" stroke-width="3"/>
        <rect x="58" y="98" width="124" height="14" rx="8" fill="#fff"/>
        <path d="M58 112 Q70 124 82 112 Q94 124 106 112 Q118 124 130 112 Q142 124 154 112 Q166 124 178 112 Q182 120 182 112" fill="none" stroke="#BB6BFF" stroke-width="5" stroke-linecap="round"/>
        <text x="120" y="140" text-anchor="middle" fill="#8E44EC" font-family="Fredoka,sans-serif" font-size="26" font-weight="700">7</text>
      </svg>
      <div id="starCandles" class="absolute inset-0 pointer-events-none"></div>
    </div>

    <button id="blowBtn" type="button" class="blow-btn font-display text-white px-8 py-3 rounded-full mt-5">Tahan & Tiup 🌬️</button>
    <div class="blow-meter"><div class="blow-meter-fill" id="blowMeterFill"></div></div>
    <p class="text-sm mt-3 font-semibold" style="color:#ffd889" id="blowHint">Tahan tombolnya dulu ya~</p>

    <div id="wishBox" class="mt-4 opacity-0 transition-opacity duration-500">
      <p class="font-script text-2xl" style="color:#ffd889">Wish-mu sudah terbang ke galaksi Keiko! 🌟</p>
    </div>
  </section>

  <!-- ===== STAR CAPSULE ===== -->
  <section class="reveal px-6 py-10 text-center">
    <p class="font-script text-2xl text-[var(--pink-deeper)] mb-1">Kejutan dari Semesta</p>
    <h3 class="font-display text-2xl font-700 mb-1">Mesin Kapsul Bintang</h3>
    <p class="text-sm text-[var(--ink)] mb-6">Tarik tuas pink-nya — dapat hadiah kejutan spesial buat tamu! 🎁</p>

    <div class="capsule-machine" id="capsuleMachine">
      <div class="capsule-header"><span>✦ STAR GACHA ✦</span></div>
      <div class="capsule-stars" aria-hidden="true">
        <span class="capsule-star" style="top:12%;left:8%">✦</span>
        <span class="capsule-star" style="top:18%;right:10%;animation-delay:.6s">✧</span>
        <span class="capsule-star" style="bottom:22%;left:12%;animation-delay:1.1s">✦</span>
        <span class="capsule-star" style="bottom:16%;right:8%;animation-delay:.3s">✧</span>
      </div>
      <div class="capsule-body">
        <div class="capsule-window">
          <div class="capsule-orbs" id="capsuleOrbs"></div>
          <span class="capsule-chute-label">DISPENSER</span>
        </div>
        <div class="capsule-lever-box" id="capsuleLeverBox">
          <div class="capsule-lever-arm">
            <button id="capsuleBtn" type="button" class="capsule-lever-knob" aria-label="Tarik tuas"></button>
            <div class="capsule-lever-stick"></div>
          </div>
          <span class="capsule-lever-label">TARIK</span>
        </div>
      </div>
      <div class="capsule-slot">
        <div class="capsule-ball half" id="capsuleBall"></div>
      </div>
    </div>
    <p id="capsuleResult" class="font-script text-2xl text-[var(--pink-deeper)] mt-4 min-h-[32px]"></p>
  </section>

  <!-- ===== GALERI FILM STRIP ===== -->
  <section class="reveal px-6 py-10" style="background:linear-gradient(180deg,var(--cream-2),var(--cream));">
    <div class="text-center mb-6">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Gulungan Kenangan</p>
      <h3 class="font-display text-2xl font-700">Momen Keiko Berputar</h3>
      <p class="text-xs text-[var(--ink)] mt-1 font-medium">Film bergulir otomatis — sentuh untuk jeda sebentar</p>
    </div>
    <div class="film-wrap">
      <div class="film-marquee" id="filmMarquee">
        <div class="film-strip" id="filmStrip"></div>
        <div class="film-strip" id="filmStripClone" aria-hidden="true"></div>
      </div>
    </div>

    <div class="mt-8">
      <p class="font-script text-xl text-[var(--pink-deeper)] text-center mb-4">Potret Keiko</p>
      <div class="grid grid-cols-2 gap-3 max-w-sm mx-auto" id="photoGrid"></div>
    </div>
  </section>

  <!-- ===== RSVP ===== -->
  <section class="reveal aurora-card px-6 py-10" style="background:linear-gradient(180deg, var(--pink-blush) 0%, var(--cream) 100%);">
    <div class="text-center mb-6">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Konfirmasi Kehadiran</p>
      <h3 class="font-display text-2xl font-700">Kamu Datang, Kan?</h3>
    </div>

    <form id="rsvpForm" class="space-y-4 max-w-sm mx-auto">
      <div>
        <label class="text-sm font-semibold block mb-1">Nama</label>
        <input type="text" id="rsvpName" name="nama" required placeholder="Nama kamu" class="input-cute w-full rounded-xl px-4 py-3" autocomplete="name">
      </div>
      <div>
        <label class="text-sm font-semibold block mb-1">Apakah kamu bisa hadir?</label>
        <div class="flex gap-2">
          <button type="button" data-val="Hadir" class="chip-btn chip flex-1 rounded-xl py-2 font-semibold text-sm">🎉 Hadir</button>
          <button type="button" data-val="Mungkin" class="chip-btn chip flex-1 rounded-xl py-2 font-semibold text-sm">🤔 Mungkin</button>
          <button type="button" data-val="Tidak" class="chip-btn chip flex-1 rounded-xl py-2 font-semibold text-sm">😢 Tidak</button>
        </div>
      </div>
      <div>
        <label class="text-sm font-semibold block mb-1">Ucapan untuk Keiko</label>
        <textarea id="rsvpMsg" name="pesan" rows="3" maxlength="60" placeholder="Tulis doa & ucapan manis..." class="input-cute w-full rounded-xl px-4 py-3 resize-none"></textarea>
      </div>
      <button type="submit" class="open-btn font-display text-white w-full py-3 rounded-full">Kirim Konfirmasi</button>
    </form>

    <div id="rsvpList" class="max-w-sm mx-auto mt-8 space-y-3"></div>
  </section>

  <!-- ===== GUESTBOOK WALL ===== -->
  <section class="reveal aurora-card px-6 py-10" style="background:var(--cream-2);">
    <div class="text-center mb-6">
      <p class="font-script text-2xl text-[var(--pink-deeper)]">Dinding Ucapan</p>
      <h3 class="font-display text-2xl font-700">Pesan Manis untuk Keiko</h3>
    </div>
    <div class="grid grid-cols-2 gap-3" id="guestbookWall"></div>
  </section>

  <!-- ===== FOOTER ===== -->
  <footer class="relative px-6 py-12 text-center overflow-hidden" style="background:linear-gradient(135deg,var(--pink-deeper),#5A2DA6);">
    <img src="assets/images/Glowing_Star_6154983.png" alt="" class="mx-auto mb-3 w-20 h-20 object-contain drop-shadow-lg" aria-hidden="true">
    <p class="font-script text-2xl text-white">Terima kasih sudah jadi bagian ceritanya~</p>
    <p class="text-white/90 text-sm mt-2 max-w-xs mx-auto">Sampai jumpa di Pesta Bintang Keiko. Jangan lupa bawa senyum terbaikmu! 💫</p>
    <p class="text-white/50 text-[11px] mt-8 tracking-wide">Dibuat dengan 💗 — Undangan Digital</p>
  </footer>

  <!-- Lightbox modal -->
  <div id="lightbox">
    <div class="box relative">
      <button id="lightboxClose" class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-white flex items-center justify-center" style="box-shadow:0 4px 10px rgba(0,0,0,.2);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="#5C3B4D" stroke-width="2.5" stroke-linecap="round"/></svg>
      </button>
      <div id="lightboxContent"></div>
    </div>
  </div>

</div>

<!-- Music toggle (di luar stage agar tidak terpotong overflow) -->
<button id="musicToggle" type="button" aria-label="Putar musik" title="Musik">
  <i class="fa-solid fa-music"></i>
</button>

<audio id="bgMusic" loop preload="none" src="assets/audio/1784180726263-39e40e91-d924-4aaf-8267-b8c08a11a0c4_8624006.mp3">Your browser does not support audio</audio>

<script>
(function(){
  // ---------- Guest name from URL ?to=Nama ----------
  const params = new URLSearchParams(window.location.search);
  const guest = params.get('to');
  if(guest){ document.getElementById('guestName').textContent = decodeURIComponent(guest); }

  // ---------- Open invitation ----------
  const cover = document.getElementById('cover');
  const openBtn = document.getElementById('openBtn');
  openBtn.addEventListener('click', () => {
    const mascotSvg = document.getElementById('coverMascot');
    const flash = document.getElementById('coverFlash');
    const revealChrome = ()=>{
      document.getElementById('scrollBar').classList.add('show');
      document.getElementById('musicToggle').classList.add('show');
      setMusic(true);
      initScrollReveal();
      animateHeroTitle();
    };
    if(typeof gsap !== 'undefined'){
      const tl = gsap.timeline();

      // 1. tombol ditekan, sedikit "menghirup" sebelum aksi
      tl.to(openBtn, { scale:0.9, duration:0.12, ease:'power2.out' })
        .to(openBtn, { scale:1.05, duration:0.15, ease:'power2.out' })

        // 2. maskot melompat riang dengan wobble alami, lalu berputar & memudar lembut
        .to(mascotSvg, { y:-18, scale:1.1, rotate:-4, duration:0.28, ease:'power2.out' }, 0.1)
        .to(mascotSvg, { y:0, scale:1, rotate:3, duration:0.32, ease:'elastic.out(1,0.55)' }, 0.38)
        .to(mascotSvg, { scale:0.3, rotate:20, opacity:0, filter:'blur(6px)', duration:0.55, ease:'power3.in' }, 0.55)

        // 3. teks & tombol melayang naik memudar dengan jeda kecil antar elemen, kurva lembut
        .to(cover.querySelectorAll('.cover-line'), {
            y:-22, opacity:0, filter:'blur(3px)', duration:0.5, stagger:0.06, ease:'power3.inOut'
          }, 0.35)
        .to(openBtn, { y:-14, opacity:0, duration:0.4, ease:'power3.inOut' }, 0.5)

        // 4. kilau-kilau meledak keluar perlahan membesar
        .to(cover.querySelectorAll('.cover-sparkle'), {
            scale:2.8, opacity:0, duration:0.6, stagger:0.06, ease:'power1.out'
          }, 0.3)

        // 5. kilatan cahaya lembut menyapu layar
        .to(flash, { opacity:0.85, duration:0.22, ease:'power1.in' }, 0.78)
        .to(flash, { opacity:0, duration:0.5, ease:'power2.out' }, 1.0)

        // 6. seluruh cover membesar sedikit sambil menghilang naik — transisi menyatu, bukan slide kaku
        .to(cover, { scale:1.12, opacity:0.4, duration:0.55, ease:'power2.in' }, 0.85)
        .to(cover, { yPercent:-100, duration:0.6, ease:'power3.inOut' }, 1.05)
        .set(cover, { display:'none' })
        .add(() => { launchConfetti(90); revealChrome(); });
    } else {
      cover.classList.add('opened');
      launchConfetti(80);
      setTimeout(()=>{ cover.style.display='none'; revealChrome(); }, 950);
    }
    document.body.style.overflow = 'auto';
  });
  document.body.style.overflow = 'hidden';

  // ---------- Cover entrance animation ----------
  function playCoverIntro(){
    const coverEl = document.getElementById('cover');
    if(typeof gsap === 'undefined'){
      coverEl.classList.add('cover-ready');
      return;
    }
    gsap.set('#coverMascot', { scale:0.4, y:30 });
    gsap.set('.cover-line', { y:24 });
    const tl = gsap.timeline({
      delay:0.2,
      onComplete:()=> coverEl.classList.add('cover-ready')
    });
    tl.to('.cover-ribbon', { opacity:0.9, duration:0.5, ease:'power2.out' })
      .to('#coverMascot', { opacity:1, scale:1, y:0, duration:0.85, ease:'back.out(1.8)' }, '-=0.2')
      .to('.cover-line', { opacity:1, y:0, duration:0.55, stagger:0.1, ease:'power3.out' }, '-=0.45')
      .from('.cover-sparkle, .cover-deco', { scale:0, opacity:0, duration:0.45, stagger:0.06, ease:'back.out(2)' }, '-=0.5')
      .to('#openBtn', { opacity:1, scale:1, duration:0.4, ease:'back.out(2)' }, '-=0.2')
      .to('#openBtn', { y:-4, duration:0.9, repeat:-1, yoyo:true, ease:'sine.inOut' });
  }
  playCoverIntro();

  // ---------- Scroll reveal (hanya setelah cover dibuka) ----------
  let scrollRevealReady = false;
  function initScrollReveal(){
    if(scrollRevealReady) return;
    scrollRevealReady = true;
    const revealEls = document.querySelectorAll('.reveal');
    const revealNow = ()=>{
      revealEls.forEach(el=>{
        const rect = el.getBoundingClientRect();
        if(rect.top < window.innerHeight * 0.92 && rect.bottom > 40){
          el.classList.add('in');
        }
      });
    };
    revealNow();
    requestAnimationFrame(revealNow);
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); } });
    }, {threshold:0.12, rootMargin:'0px 0px -8% 0px'});
    revealEls.forEach(el=>io.observe(el));
  }

  // ---------- Countdown ----------
  const target = new Date('2026-08-15T10:00:00+07:00').getTime();
  let prevSec = null;
  function popBox(id){
    const box = document.getElementById(id);
    if(!box) return;
    box.classList.add('pop');
    setTimeout(()=>box.classList.remove('pop'), 300);
  }
  function updateCountdown(){
    const now = Date.now();
    let diff = Math.max(0, target - now);
    const d = Math.floor(diff/(1000*60*60*24));
    const h = Math.floor((diff/(1000*60*60))%24);
    const m = Math.floor((diff/(1000*60))%60);
    const s = Math.floor((diff/1000)%60);
    document.getElementById('cd-days').textContent = String(d).padStart(2,'0');
    document.getElementById('cd-hours').textContent = String(h).padStart(2,'0');
    document.getElementById('cd-min').textContent = String(m).padStart(2,'0');
    document.getElementById('cd-sec').textContent = String(s).padStart(2,'0');
    if(prevSec !== null && s !== prevSec) popBox('box-sec');
    prevSec = s;
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);

  // ---------- Outfit picker (dress-up Keiko) ----------
  const avatarDress = document.getElementById('avatarDress');
  const avatarBow = document.getElementById('avatarBow');
  const outfitMsg = document.getElementById('outfitMsg');
  const bowMap = {
    '#E85585':'#FFD889',
    '#BB6BFF':'#E8D8FF',
    '#FFD889':'#FF9F43',
    '#5EDFCF':'#BB6BFF',
    '#A8B4D8':'#F8F6FF'
  };
  document.querySelectorAll('.outfit-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      document.querySelectorAll('.outfit-btn').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const color = btn.dataset.color;
      const name = btn.dataset.name;
      if(avatarDress) avatarDress.setAttribute('fill', color);
      if(avatarBow) avatarBow.setAttribute('fill', bowMap[color] || '#FFD889');
      if(outfitMsg) outfitMsg.textContent = `Keiko pakai baju ${name} — cantik banget! ✨`;
      if(typeof gsap !== 'undefined'){
        gsap.fromTo('#keikoAvatar', {scale:0.96}, {scale:1, duration:0.35, ease:'back.out(2)'});
      }
    });
  });

  // ---------- Star cake: tahan tombol untuk tiup semua lilin ----------
  const starCandles = document.getElementById('starCandles');
  const candlePositions = [
    {left:'32%', top:'27%'}, {left:'38%', top:'25%'}, {left:'44%', top:'24%'},
    {left:'50%', top:'23%'}, {left:'56%', top:'24%'}, {left:'62%', top:'25%'}, {left:'68%', top:'27%'}
  ];
  candlePositions.forEach((pos,i)=>{
    const wrap = document.createElement('div');
    wrap.className = 'absolute flex flex-col items-center';
    wrap.style.left = pos.left;
    wrap.style.top = pos.top;
    wrap.style.transform = 'translateX(-50%)';
    wrap.innerHTML = `<div class="star-flame" data-i="${i}"></div><div class="candle-stick"></div>`;
    starCandles.appendChild(wrap);
  });
  const flames = starCandles.querySelectorAll('.star-flame');
  const blowBtn = document.getElementById('blowBtn');
  const blowMeterFill = document.getElementById('blowMeterFill');
  const blowHint = document.getElementById('blowHint');
  const wishBox = document.getElementById('wishBox');
  let blowAmount = 0;
  let blowTimer = null;
  let cakeDone = false;

  function finishBlow(){
    if(cakeDone) return;
    cakeDone = true;
    flames.forEach(f=>f.classList.add('off'));
    blowMeterFill.style.width = '100%';
    blowHint.textContent = 'Semua lilin padam! Wish-mu terkirim ✨';
    wishBox.style.opacity = '1';
    launchConfetti(70);
    blowBtn.disabled = true;
  }
  function startBlow(){
    if(cakeDone) return;
    blowBtn.classList.add('holding');
    blowTimer = setInterval(()=>{
      blowAmount = Math.min(100, blowAmount + 4);
      blowMeterFill.style.width = blowAmount + '%';
      if(blowAmount >= 100) finishBlow();
    }, 60);
  }
  function stopBlow(){
    blowBtn.classList.remove('holding');
    if(blowTimer){ clearInterval(blowTimer); blowTimer = null; }
    if(!cakeDone){
      blowAmount = Math.max(0, blowAmount - 8);
      blowMeterFill.style.width = blowAmount + '%';
    }
  }
  blowBtn.addEventListener('mousedown', startBlow);
  blowBtn.addEventListener('mouseup', stopBlow);
  blowBtn.addEventListener('mouseleave', stopBlow);
  blowBtn.addEventListener('touchstart', (e)=>{ e.preventDefault(); startBlow(); }, {passive:false});
  blowBtn.addEventListener('touchend', stopBlow);

  // ---------- RSVP chips ----------
  const chips = document.querySelectorAll('.chip-btn');
  let selected = null;
  chips.forEach(c=>{
    c.addEventListener('click', ()=>{
      chips.forEach(x=>x.classList.remove('selected'));
      c.classList.add('selected');
      selected = c.dataset.val;
    });
  });

  // ---------- RSVP submit (local demo only) ----------
  const form = document.getElementById('rsvpForm');
  const rsvpList = document.getElementById('rsvpList');
  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    const name = form.querySelector('input[type=text]').value.trim();
    const msg = form.querySelector('textarea').value.trim();
    if(!name) return;
    const card = document.createElement('div');
    card.className = 'bg-white rounded-2xl p-4';
    card.style.boxShadow = '0 6px 16px rgba(92,59,77,.08)';
    card.innerHTML = `<p class="font-display font-600">${escapeHtml(name)} <span class="text-xs font-normal text-[var(--ink-soft)]">— ${selected || 'Belum konfirmasi'}</span></p>` +
                      (msg ? `<p class="text-sm text-[var(--ink-soft)] mt-1">${escapeHtml(msg)}</p>` : '');
    rsvpList.prepend(card);
    form.reset();
    chips.forEach(x=>x.classList.remove('selected'));
    selected = null;
    launchConfetti(30);
  });
  function escapeHtml(str){
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ---------- Film strip gallery (auto infinite scroll) ----------
  const filmStrip = document.getElementById('filmStrip');
  const filmStripClone = document.getElementById('filmStripClone');
  const photoGrid = document.getElementById('photoGrid');
  const filmPhotos = [
    { src:'assets/images/photo-1717205964281-ab2bd111dcb2_934917.jpg', cap:'Foto bareng kue' },
    { src:'assets/images/photo-1503454537195-1dcabb73ffb9_7175134.jpg', cap:'Anak-anak ceria' },
    { src:'assets/images/photo-1558636508-e0db3814bd1d_3301971.jpg', cap:'Pesta ulang tahun' },
    { src:'assets/images/photo-1464349095431-e9a21285b5f3_8258714.jpg', cap:'Tiup lilin' },
    { src:'assets/images/photo-1530103862676-de8c9debad1d_8658287.jpg', cap:'Balon warna-warni' },
    { src:'assets/images/pexels-photo-8613329_7047846.jpeg', cap:'Party time' }
  ];
  const gridPhotos = [
    { src:'assets/images/photo-1476703993599-0035a21b17a9_7282642.jpg', cap:'Senyum manis' },
    { src:'assets/images/photo-1544776193-352d25ca82cd_4818127.jpg', cap:'Teman-teman' },
    { src:'assets/images/pexels-photo-8613089_8587889.jpeg', cap:'Momen bahagia' },
    { src:'assets/images/pexels-photo-3661397_6503839.jpeg', cap:'Rayakan bersama' },
    { src:'assets/images/pexels-photo-7879620_9138059.jpeg', cap:'Dekorasi pesta' },
    { src:'assets/images/pexels-photo-8613379_4609687.jpeg', cap:'Kenangan manis' }
  ];
  function openPhotoLightbox(src, cap){
    document.getElementById('lightboxContent').innerHTML = `<img src="${src}" alt="${cap}" class="w-full rounded-xl">`;
    document.getElementById('lightbox').classList.add('show');
  }
  function buildFilmFrame(photo, parent){
    const frame = document.createElement('div');
    frame.className = 'film-frame';
    frame.innerHTML = `<img src="${photo.src}" alt="${photo.cap}" loading="lazy" referrerpolicy="no-referrer"><p class="cap">${photo.cap}</p>`;
    frame.addEventListener('click', ()=> openPhotoLightbox(photo.src, photo.cap));
    parent.appendChild(frame);
  }
  filmPhotos.forEach(photo=> buildFilmFrame(photo, filmStrip));
  filmPhotos.forEach(photo=> buildFilmFrame(photo, filmStripClone));
  gridPhotos.forEach(photo=>{
    const card = document.createElement('div');
    card.className = 'photo-grid-card';
    card.innerHTML = `<img src="${photo.src}" alt="${photo.cap}" loading="lazy" referrerpolicy="no-referrer"><p class="cap">${photo.cap}</p>`;
    card.addEventListener('click', ()=> openPhotoLightbox(photo.src, photo.cap));
    photoGrid.appendChild(card);
  });

  // ---------- Ambient balloons (realistic w/ gradient shine) ----------
  const ambient = document.getElementById('ambientBalloons');
  const balloonPalettes = [
    {main:'#FF6F9C', dark:'#E8477E'},
    {main:'#B7A2F5', dark:'#9376E8'},
    {main:'#FFCF6B', dark:'#F5AE2E'},
    {main:'#7CE0B0', dark:'#4FC98F'}
  ];
  let balloonUid = 0;
  function spawnBalloon(){
    const b = document.createElement('div');
    b.className = 'balloon';
    const pal = balloonPalettes[Math.floor(Math.random()*balloonPalettes.length)];
    const w = 26 + Math.random()*16;
    const h = w*1.3;
    const gid = 'bg' + (balloonUid++);
    b.style.left = (Math.random()*88)+'%';
    b.style.bottom = (-90 - Math.random()*80) + 'px';
    b.style.animationDuration = (10+Math.random()*6)+'s';
    b.innerHTML = `
      <svg width="${w}" height="${h+26}" viewBox="0 0 40 66">
        <defs>
          <radialGradient id="${gid}" cx="34%" cy="28%" r="75%">
            <stop offset="0%" stop-color="#ffffff"/>
            <stop offset="35%" stop-color="${pal.main}"/>
            <stop offset="100%" stop-color="${pal.dark}"/>
          </radialGradient>
        </defs>
        <path d="M20 2 Q37 2 37 24 Q37 44 22 50 L18 50 Q3 44 3 24 Q3 2 20 2Z" fill="url(#${gid})"/>
        <ellipse cx="14" cy="16" rx="4.5" ry="7" fill="#ffffff" opacity=".55"/>
        <path d="M20 50 L20 54" stroke="${pal.dark}" stroke-width="2" stroke-linecap="round"/>
        <path d="M20 54 Q24 58 20 62 Q16 66 20 66" stroke="#C9A9A9" stroke-width="1.2" fill="none" stroke-linecap="round" opacity=".7"/>
      </svg>`;
    ambient.appendChild(b);
    setTimeout(()=>b.remove(), 15000);
  }
  setInterval(spawnBalloon, 2400);
  for(let i=0;i<3;i++) setTimeout(spawnBalloon, i*700);

  // ---------- Ambient butterflies (detailed, flapping wings) ----------
  function spawnButterfly(){
    const bf = document.createElement('div');
    bf.className = 'butterfly';
    const palettes = [
      {a:'#FF9FC0', b:'#FF6F9C'},
      {a:'#C9B6FF', b:'#9376E8'},
      {a:'#FFE29A', b:'#FFB84D'}
    ];
    const pal = palettes[Math.floor(Math.random()*palettes.length)];
    const size = 20 + Math.random()*10;
    bf.style.left = (Math.random()*80)+'%';
    bf.style.top = (30 + Math.random()*40)+'%';
    bf.style.animationDuration = (3.5+Math.random()*2)+'s';
    bf.style.animationIterationCount = '1';
    bf.style.opacity = '0.95';
    bf.innerHTML = `
      <svg width="${size}" height="${size*0.8}" viewBox="0 0 40 32">
        <g class="wing-l">
          <path d="M20 16 C10 2 0 4 2 14 C3 22 12 22 20 16Z" fill="${pal.a}"/>
          <path d="M20 16 C13 20 6 26 10 30 C15 32 20 24 20 16Z" fill="${pal.b}"/>
        </g>
        <g class="wing-r">
          <path d="M20 16 C30 2 40 4 38 14 C37 22 28 22 20 16Z" fill="${pal.a}"/>
          <path d="M20 16 C27 20 34 26 30 30 C25 32 20 24 20 16Z" fill="${pal.b}"/>
        </g>
        <rect x="19" y="10" width="2" height="14" rx="1" fill="#5C3B4D"/>
        <circle cx="20" cy="10" r="1.6" fill="#5C3B4D"/>
      </svg>`;
    ambient.appendChild(bf);
    setTimeout(()=>bf.remove(), 6000);
  }
  setInterval(spawnButterfly, 2600);
  setTimeout(spawnButterfly, 500);

  // ---------- Parallax on scroll ----------
  const stageEl = document.getElementById('stage');
  const slowEls = document.querySelectorAll('.parallax-slow');
  const fastEls = document.querySelectorAll('.parallax-fast');
  let ticking = false;
  function onScroll(){
    if(!ticking){
      requestAnimationFrame(()=>{
        const y = stageEl.scrollTop || window.scrollY;
        slowEls.forEach(el=> el.style.transform = `translateY(${y*0.08}px)`);
        fastEls.forEach(el=> el.style.transform = `translateY(${y*0.18}px)`);
        ticking = false;
      });
      ticking = true;
    }
  }
  window.addEventListener('scroll', onScroll, {passive:true});

  // ---------- Scroll progress bar ----------
  const scrollBarFill = document.getElementById('scrollBarFill');
  function updateScrollBar(){
    const h = document.documentElement.scrollHeight - window.innerHeight;
    const pct = h > 0 ? (window.scrollY / h) * 100 : 0;
    scrollBarFill.style.width = pct + '%';
  }
  window.addEventListener('scroll', updateScrollBar, {passive:true});

  // ---------- Ripple effect on buttons ----------
  document.querySelectorAll('.open-btn, .chip-btn').forEach(btn=>{
    btn.classList.add('btn-pop');
    btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.addEventListener('click', function(e){
      const rect = this.getBoundingClientRect();
      const rip = document.createElement('span');
      rip.className = 'ripple';
      const size = Math.max(rect.width, rect.height);
      rip.style.width = rip.style.height = size + 'px';
      rip.style.left = (e.clientX - rect.left - size/2) + 'px';
      rip.style.top = (e.clientY - rect.top - size/2) + 'px';
      this.appendChild(rip);
      setTimeout(()=>rip.remove(), 650);
    });
  });

  // ---------- Letter-by-letter heading (setelah undangan dibuka) ----------
  let heroTitleReady = false;
  function animateHeroTitle(){
    const heroTitle = document.getElementById('heroTitle');
    if(!heroTitle || heroTitleReady) return;
    heroTitleReady = true;
    const original = heroTitle.innerHTML;
    const lines = original.split('<br>');
    heroTitle.innerHTML = lines.map(line =>
      line.split('').map(ch => `<span class="split-char">${ch === ' ' ? '&nbsp;' : ch}</span>`).join('')
    ).join('<br>');
    const chars = heroTitle.querySelectorAll('.split-char');
    if(typeof gsap !== 'undefined'){
      gsap.to(chars, { opacity:1, y:0, duration:0.5, stagger:0.03, ease:'back.out(1.7)', delay:0.15 });
    } else {
      chars.forEach(c=>{ c.style.opacity='1'; c.style.transform='translateY(0)'; });
    }
  }
  // ---------- Timeline perjalanan ----------
  const timelineData = [
    {age:1, text:'Belajar berjalan sambil ketawa paling keras di rumah.', icon:'👣'},
    {age:2, text:'Sering nyanyi sendiri — liriknya campur aduk tapi lucu.', icon:'🎤'},
    {age:3, text:'Mulai koleksi boneka unicorn dan benda-benda bintang.', icon:'🦄'},
    {age:4, text:'Pertama kali tampil percaya diri di depan kelas.', icon:'🎒'},
    {age:5, text:'Hobi menari makin kuat, plus bikin koreografi sendiri.', icon:'💃'},
    {age:6, text:'Suka membuat kartu ucapan handmade untuk keluarga.', icon:'🎨'},
    {age:7, text:'Tumbuh jadi anak yang hangat, cerdas, dan penuh cahaya.', icon:'✨'}
  ];
  const timelineItems = document.getElementById('timelineItems');
  timelineData.forEach((t,i)=>{
    const row = document.createElement('div');
    row.className = 'timeline-item relative flex items-center gap-4 ' + (i%2===0 ? 'flex-row' : 'flex-row-reverse text-right');
    row.innerHTML = `
      <div class="flex-1 bg-white rounded-2xl p-4" style="box-shadow:0 8px 18px rgba(92,59,77,.08);">
        <p class="font-display font-700 text-[var(--pink-deeper)]">${t.age} Tahun ${t.icon}</p>
        <p class="text-sm text-[var(--ink-soft)] mt-1">${t.text}</p>
      </div>
      <div class="timeline-dot shrink-0" style="margin:0 -8px; z-index:1;"></div>
      <div class="flex-1"></div>`;
    timelineItems.appendChild(row);
  });
  const timelineIo = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('in'); });
  }, {threshold:0.3});
  document.querySelectorAll('.timeline-item').forEach(el=>timelineIo.observe(el));

  if(typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined'){
    gsap.registerPlugin(ScrollTrigger);
    gsap.to('#timelineFill', {
      height: '100%',
      ease: 'none',
      scrollTrigger: {
        trigger: '#timelineWrap',
        start: 'top 70%',
        end: 'bottom 60%',
        scrub: 0.6
      }
    });
  }

  // ---------- Star capsule gacha ----------
  const capsuleBtn = document.getElementById('capsuleBtn');
  const capsuleBall = document.getElementById('capsuleBall');
  const capsuleResult = document.getElementById('capsuleResult');
  const capsuleMachine = document.getElementById('capsuleMachine');
  const capsuleLeverBox = document.getElementById('capsuleLeverBox');
  const capsuleOrbs = document.getElementById('capsuleOrbs');
  const capsuleColors = [
    { top:'#fff', bot:'#BB6BFF' },
    { top:'#fff', bot:'#FF9FC0' },
    { top:'#fff', bot:'#7DD3FC' },
    { top:'#fff', bot:'#FFD889' },
    { top:'#fff', bot:'#BEF2DB' },
    { top:'#fff', bot:'#F472B6' }
  ];
  const capsulePrizes = [
    'Pass Foto Neon Booth 📸',
    'Voucher Milkshake Galaksi 🥤',
    'Sticker Unicorn Limited ✨',
    'Extra Glow Bracelet 💫',
    'Mystery Gift dari Keiko 🎁',
    'VIP Dance Floor Access 🕺'
  ];
  const orbLayout = [
    { left:'8%', top:'18%', size:28, delay:0 },
    { left:'28%', top:'8%', size:24, delay:.4 },
    { left:'50%', top:'22%', size:30, delay:.8 },
    { left:'72%', top:'10%', size:22, delay:1.2 },
    { left:'18%', top:'48%', size:26, delay:.2 },
    { left:'42%', top:'52%', size:32, delay:.6 },
    { left:'65%', top:'46%', size:24, delay:1 },
    { left:'82%', top:'55%', size:20, delay:1.4 }
  ];
  orbLayout.forEach((o, i)=>{
    const orb = document.createElement('div');
    orb.className = 'capsule-orb';
    const c = capsuleColors[i % capsuleColors.length];
    orb.style.cssText = `left:${o.left};top:${o.top};width:${o.size}px;height:${o.size}px;background:linear-gradient(180deg,${c.top} 48%,${c.bot} 52%);animation-delay:${o.delay}s;`;
    capsuleOrbs.appendChild(orb);
  });
  let capsuleBusy = false;
  capsuleBtn.addEventListener('click', ()=>{
    if(capsuleBusy) return;
    capsuleBusy = true;
    capsuleBtn.disabled = true;
    capsuleLeverBox.classList.add('pulling');
    capsuleMachine.classList.add('shake');
    capsuleResult.textContent = 'Mesin berputar... siap-siap ya!';
    capsuleResult.classList.remove('capsule-result-pop');
    capsuleBall.classList.remove('drop');
    void capsuleBall.offsetWidth;
    const color = capsuleColors[Math.floor(Math.random()*capsuleColors.length)];
    capsuleBall.style.setProperty('--cap-top', color.top);
    capsuleBall.style.setProperty('--cap-bot', color.bot);
    const prize = capsulePrizes[Math.floor(Math.random()*capsulePrizes.length)];
    setTimeout(()=>{
      capsuleBall.classList.add('drop');
    }, 320);
    setTimeout(()=>{
      capsuleResult.textContent = `🎉 Selamat! Kamu dapat: ${prize}`;
      capsuleResult.classList.add('capsule-result-pop');
      launchConfetti(55);
    }, 900);
    setTimeout(()=>{
      capsuleLeverBox.classList.remove('pulling');
      capsuleMachine.classList.remove('shake');
      capsuleBtn.disabled = false;
      capsuleBusy = false;
    }, 1400);
  });

  // ---------- Guestbook wall ----------
  const guestbookData = [
    {name:'Tante Rani', msg:'Selamat ulang tahun Keiko sayang, sehat selalu ya!'},
    {name:'Kak Bima', msg:'Makin pintar dan makin cantik, semangat sekolahnya!'},
    {name:'Oma', msg:'Cucu kesayangan Oma, panjang umur nak.'},
    {name:'Sahabat Dea', msg:'Happy birthday bestie! Main boneka lagi yuk!'}
  ];
  const wallColors = ['#FFD9E6','#D8CBFF','#BEF2DB','#FFE3A3'];
  const guestbookWall = document.getElementById('guestbookWall');
  guestbookData.forEach((g,i)=>{
    const card = document.createElement('div');
    card.className = 'note-card rounded-2xl p-4';
    card.style.background = wallColors[i % wallColors.length];
    card.style.transform = `rotate(${i%2===0 ? -2 : 2}deg)`;
    card.innerHTML = `<p class="text-sm text-[var(--ink)] leading-snug">"${g.msg}"</p><p class="font-script text-lg text-[var(--pink-deeper)] mt-2">— ${g.name}</p>`;
    guestbookWall.appendChild(card);
  });

  // ---------- Lightbox for gallery ----------
  const lightbox = document.getElementById('lightbox');
  const lightboxContent = document.getElementById('lightboxContent');
  document.getElementById('lightboxClose').addEventListener('click', ()=> lightbox.classList.remove('show'));
  lightbox.addEventListener('click', (e)=>{ if(e.target === lightbox) lightbox.classList.remove('show'); });

  // ---------- Cursor heart trail (desktop only) ----------
  if(window.matchMedia('(hover:hover)').matches){
    let lastTrail = 0;
    window.addEventListener('mousemove', (e)=>{
      const now = Date.now();
      if(now - lastTrail < 90) return;
      lastTrail = now;
      const heart = document.createElement('div');
      heart.className = 'heart-trail';
      heart.style.left = e.clientX + 'px';
      heart.style.top = e.clientY + 'px';
      heart.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="#FF6F9C"><path d="M12 21s-7-4.6-9.5-9C.5 8 2 4 6 4c2 0 3.5 1.2 4 2.4C10.5 5.2 12 4 14 4c4 0 5.5 4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>`;
      document.body.appendChild(heart);
      if(typeof gsap !== 'undefined'){
        gsap.to(heart, { y:-24, opacity:0, duration:0.8, ease:'power1.out', onComplete:()=>heart.remove() });
      } else {
        setTimeout(()=>heart.remove(), 700);
      }
    });
  }

  const confettiColors = ['#FF6F9C','#B7A2F5','#FFCF6B','#7CE0B0','#FFD9E6'];
  function launchConfetti(count){
    if(typeof confetti === 'function'){
      confetti({
        particleCount: count,
        spread: 70,
        startVelocity: 32,
        gravity: 0.9,
        ticks: 200,
        origin: { y: 0.4 },
        colors: confettiColors,
        scalar: 0.9,
        zIndex: 200
      });
    }
  }

  // ---------- Ambient sparkle particles (canvas) ----------
  const sparkleCanvas = document.getElementById('sparkleCanvas');
  const sctx = sparkleCanvas.getContext('2d');
  function resizeSparkle(){
    sparkleCanvas.width = sparkleCanvas.offsetWidth;
    sparkleCanvas.height = sparkleCanvas.offsetHeight;
  }
  resizeSparkle();
  window.addEventListener('resize', resizeSparkle);
  const sparkles = Array.from({length:26}, () => ({
    x: Math.random(),
    y: Math.random(),
    r: 0.6 + Math.random()*1.4,
    speed: 0.04 + Math.random()*0.08,
    phase: Math.random()*Math.PI*2
  }));
  function drawSparkles(t){
    sctx.clearRect(0,0,sparkleCanvas.width,sparkleCanvas.height);
    sparkles.forEach(s=>{
      const twinkle = 0.4 + 0.6*Math.abs(Math.sin(t/900 + s.phase));
      const px = s.x*sparkleCanvas.width;
      const py = ((s.y*sparkleCanvas.height) + t*s.speed) % sparkleCanvas.height;
      sctx.beginPath();
      sctx.arc(px, py, s.r, 0, Math.PI*2);
      sctx.fillStyle = `rgba(255,255,255,${twinkle*0.8})`;
      sctx.fill();
    });
    requestAnimationFrame(drawSparkles);
  }
  requestAnimationFrame(drawSparkles);

  // ---------- Tilt 3D on cards (desktop hover) ----------
  function addTilt(el, max=8){
    el.style.transformStyle = 'preserve-3d';
    el.addEventListener('mousemove', (e)=>{
      const r = el.getBoundingClientRect();
      const px = (e.clientX - r.left)/r.width - 0.5;
      const py = (e.clientY - r.top)/r.height - 0.5;
      el.style.transform = `perspective(600px) rotateY(${px*max}deg) rotateX(${-py*max}deg) scale(1.03)`;
    });
    el.addEventListener('mouseleave', ()=>{ el.style.transform = ''; });
  }
  document.querySelectorAll('.film-frame').forEach(el=>addTilt(el, 8));
  document.querySelectorAll('.photo-grid-card').forEach(el=>addTilt(el, 6));

  // ---------- Music (sama seperti index.html) ----------
  const bgMusic = document.getElementById('bgMusic');
  const musicToggle = document.getElementById('musicToggle');
  let playing = false;
  if(bgMusic) bgMusic.volume = 0.55;

  function setMusic(on){
    if(!bgMusic) return;
    try{
      if(on){
        const p = bgMusic.play();
        if(p && typeof p.then === 'function'){
          p.then(()=>{
            playing = true;
            if(musicToggle){
              musicToggle.classList.add('show');
              musicToggle.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
            }
          }).catch(()=>{
            playing = false;
            if(musicToggle){
              musicToggle.classList.add('show');
              musicToggle.innerHTML = '<i class="fa-solid fa-music"></i>';
            }
          });
        }else{
          playing = true;
          if(musicToggle) musicToggle.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
        }
      }else{
        bgMusic.pause();
        playing = false;
        if(musicToggle) musicToggle.innerHTML = '<i class="fa-solid fa-music"></i>';
      }
    }catch(err){}
  }

  if(musicToggle){
    musicToggle.addEventListener('click', ()=> setMusic(!playing));
  }
  if(bgMusic){
    bgMusic.addEventListener('error', ()=>{
      console.warn('Gagal load musik:', bgMusic.currentSrc || bgMusic.src);
    });
  }
})();
</script>
</body>
</html>