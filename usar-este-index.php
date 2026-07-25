<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XAVIER — a compilar algo grande</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --bg: #0A0C11;
    --bg-panel: #10131A;
    --line: #1E232D;
    --text: #E7EAF1;
    --dim: #5B6473;
    --amber: #F5A623;
    --amber-dim: #8A6423;
    --green: #3ED98B;
  }

  *{ box-sizing:border-box; }

  html,body{
    margin:0;
    padding:0;
    width:100%;
    min-height:100vh;
    background:var(--bg);
    color:var(--text);
    font-family:'JetBrains Mono', monospace;
    overflow-x:hidden;
  }

  body{
    background-image:
      radial-gradient(ellipse at 20% -10%, rgba(245,166,35,0.06), transparent 55%),
      radial-gradient(ellipse at 100% 110%, rgba(62,217,139,0.05), transparent 50%);
  }

  /* scanline / CRT texture */
  .scanlines{
    position:fixed;
    inset:0;
    pointer-events:none;
    z-index:50;
    background:repeating-linear-gradient(
      to bottom,
      rgba(255,255,255,0.025) 0px,
      rgba(255,255,255,0.025) 1px,
      transparent 1px,
      transparent 3px
    );
    mix-blend-mode:overlay;
  }

  .vignette{
    position:fixed;
    inset:0;
    pointer-events:none;
    z-index:49;
    box-shadow: inset 0 0 180px rgba(0,0,0,0.65);
  }

  .wrap{
    position:relative;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px 20px;
  }

  .terminal{
    width:100%;
    max-width:760px;
    background:var(--bg-panel);
    border:1px solid var(--line);
    border-radius:10px;
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.02),
      0 40px 80px -30px rgba(0,0,0,0.8),
      0 0 60px -20px rgba(245,166,35,0.08);
    overflow:hidden;
  }

  .titlebar{
    display:flex;
    align-items:center;
    gap:8px;
    padding:12px 16px;
    border-bottom:1px solid var(--line);
    background:linear-gradient(180deg, #14171F, #10131A);
  }

  .dot{
    width:10px;
    height:10px;
    border-radius:50%;
    background:#3A4150;
  }
  .dot.red{ background:#E5555A; opacity:.55; }
  .dot.yellow{ background:#E6B450; opacity:.55; }
  .dot.green{ background:#3ED98B; opacity:.55; }

  .titlebar span.path{
    margin-left:10px;
    font-size:12px;
    color:var(--dim);
    letter-spacing:0.02em;
  }

  .screen{
    padding:28px 26px 34px;
    min-height:420px;
  }

  .log-line{
    font-size:13px;
    line-height:1.9;
    color:var(--dim);
    white-space:pre-wrap;
    word-break:break-word;
  }
  .log-line .ok{ color:var(--green); }
  .log-line .warn{ color:var(--amber); }
  .log-line .path-hi{ color:#8FA0BF; }

  .prompt-caret{
    display:inline-block;
    width:8px;
    height:14px;
    background:var(--amber);
    vertical-align:-2px;
    margin-left:2px;
    animation:blink 1s steps(1) infinite;
  }
  @keyframes blink{ 50%{ opacity:0; } }

  .reveal{
    margin-top:26px;
    opacity:0;
    transform:translateY(6px);
    transition:opacity .5s ease, transform .5s ease;
  }
  .reveal.show{ opacity:1; transform:translateY(0); }

  .brand{
    font-family:'Space Grotesk', sans-serif;
    font-weight:700;
    font-size:clamp(48px, 11vw, 84px);
    letter-spacing:0.01em;
    line-height:1;
    color:var(--text);
    margin:0;
    position:relative;
    display:inline-block;
  }

  .brand::after{
    content:"DEVELOPER";
    position:absolute;
    left:2px;
    bottom:-16px;
    font-family:'JetBrains Mono', monospace;
    font-weight:500;
    font-size:12px;
    letter-spacing:0.42em;
    color:var(--amber);
  }

  .glitch{
    position:relative;
    display:inline-block;
  }
  .glitch::before, .glitch::after{
    content:"XAVIER";
    position:absolute;
    left:0; top:0;
    width:100%;
    overflow:hidden;
    color:var(--text);
  }
  .glitch::before{
    color:var(--amber);
    clip-path: inset(0 0 55% 0);
    animation: glitchTop 3.6s infinite linear;
    opacity:0.7;
  }
  .glitch::after{
    color:var(--green);
    clip-path: inset(60% 0 0 0);
    animation: glitchBottom 3.9s infinite linear;
    opacity:0.55;
  }
  @keyframes glitchTop{
    0%, 92%, 100%{ transform:translate(0,0); }
    93%{ transform:translate(-2px,-1px); }
    94%{ transform:translate(2px,1px); }
    95%{ transform:translate(-1px,0); }
    96%{ transform:translate(0,0); }
  }
  @keyframes glitchBottom{
    0%, 90%, 100%{ transform:translate(0,0); }
    91%{ transform:translate(2px,1px); }
    92%{ transform:translate(-2px,0); }
    93%{ transform:translate(1px,-1px); }
    94%{ transform:translate(0,0); }
  }

  .tagline{
    margin:34px 0 0;
    font-family:'JetBrains Mono', monospace;
    font-size:14px;
    color:var(--dim);
    letter-spacing:0.01em;
  }
  .tagline b{ color:var(--text); font-weight:500; }

  .statusrow{
    display:flex;
    align-items:center;
    gap:14px;
    margin-top:26px;
    font-size:12px;
    color:var(--dim);
  }

  .barwrap{
    flex:1;
    height:6px;
    border-radius:4px;
    background:#1A1E27;
    overflow:hidden;
    position:relative;
  }
  .bar{
    position:absolute;
    inset:0;
    width:0%;
    background:linear-gradient(90deg, var(--amber-dim), var(--amber));
    border-radius:4px;
    transition:width 1.6s cubic-bezier(.2,.7,.2,1);
  }

  .pct{
    font-variant-numeric:tabular-nums;
    color:var(--amber);
    min-width:38px;
    text-align:right;
  }

  .footer-note{
    margin-top:30px;
    padding-top:18px;
    border-top:1px dashed var(--line);
    font-size:12px;
    color:var(--dim);
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
  }

  .footer-note .signal{
    display:inline-flex;
    align-items:center;
    gap:7px;
  }
  .pulse{
    width:7px;
    height:7px;
    border-radius:50%;
    background:var(--green);
    box-shadow:0 0 0 rgba(62,217,139,.6);
    animation:pulse 1.8s infinite;
  }
  @keyframes pulse{
    0%{ box-shadow:0 0 0 0 rgba(62,217,139,.5); }
    70%{ box-shadow:0 0 0 8px rgba(62,217,139,0); }
    100%{ box-shadow:0 0 0 0 rgba(62,217,139,0); }
  }

  @media (max-width:520px){
    .screen{ padding:20px 18px 26px; }
    .brand::after{ letter-spacing:0.28em; bottom:-14px; font-size:10px; }
    .footer-note{ flex-direction:column; }
  }

  @media (prefers-reduced-motion: reduce){
    .glitch::before, .glitch::after, .pulse, .prompt-caret{ animation:none; }
  }
</style>
</head>
<body>

<div class="scanlines"></div>
<div class="vignette"></div>

<div class="wrap">
  <div class="terminal">
    <div class="titlebar">
      <span class="dot red"></span>
      <span class="dot yellow"></span>
      <span class="dot green"></span>
      <span class="path">~/xavier — build.log</span>
    </div>

    <div class="screen">
      <div class="log-line" id="log"></div>

      <div class="reveal" id="revealBlock">
        <h1 class="brand"><span class="glitch">XAVIER</span></h1>

        <p class="tagline">Este espaço está reservado para <b>algo grande</b>.<br>O deploy já começou — falta pouco.</p>

        <div class="statusrow">
          <span>build</span>
          <div class="barwrap"><div class="bar" id="bar"></div></div>
          <span class="pct" id="pct">0%</span>
        </div>

        <div class="footer-note">
          <span class="signal"><span class="pulse"></span> servidor online</span>
          <span id="clock">a preparar lançamento…</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const lines = [
    { text: "$ whoami", cls: "" },
    { text: "> xavier · developer", cls: "path-hi" },
    { text: "$ git status", cls: "" },
    { text: "> branch: proximo-projeto", cls: "" },
    { text: "> 1 ideia grande, pronta para deploy", cls: "warn" },
    { text: "$ npm run build", cls: "" },
    { text: "> a compilar algo grande", cls: "ok" },
  ];

  const logEl = document.getElementById('log');
  const revealBlock = document.getElementById('revealBlock');
  const bar = document.getElementById('bar');
  const pct = document.getElementById('pct');
  const clockEl = document.getElementById('clock');

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function typeLine(lineObj, container, onDone){
    const span = document.createElement('div');
    if(lineObj.cls) span.className = lineObj.cls;
    container.appendChild(span);
    let i = 0;
    if(reduced){
      span.textContent = lineObj.text;
      onDone();
      return;
    }
    const step = () => {
      span.textContent = lineObj.text.slice(0, i);
      i++;
      if(i <= lineObj.text.length){
        setTimeout(step, 16 + Math.random()*22);
      } else {
        onDone();
      }
    };
    step();
  }

  function runLog(index){
    if(index >= lines.length){
      const caret = document.createElement('span');
      caret.className = 'prompt-caret';
      logEl.appendChild(caret);
      setTimeout(showReveal, 350);
      return;
    }
    typeLine(lines[index], logEl, () => {
      setTimeout(() => runLog(index + 1), 160);
    });
  }

  function showReveal(){
    revealBlock.classList.add('show');
    animateBar();
  }

  function animateBar(){
    const target = 92;
    requestAnimationFrame(() => {
      bar.style.width = target + '%';
    });
    let current = 0;
    const iv = setInterval(() => {
      current += Math.ceil(Math.random()*4)+1;
      if(current >= target){ current = target; clearInterval(iv); }
      pct.textContent = current + '%';
    }, 55);
  }

  if(reduced){
    lines.forEach(l => typeLine(l, logEl, ()=>{}));
    showReveal();
  } else {
    runLog(0);
  }
</script>

</body>
</html>