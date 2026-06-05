<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();

// Mark complete if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'complete') {
    query("UPDATE users SET biometric_enabled=1, face_verified=1, fingerprint_verified=1, security_level='biometric' WHERE id=?", [$user['id']]);
    $dest = $_SESSION['bio_redirect'] ?? '/public/dashboard.php';
    unset($_SESSION['bio_redirect']);
    redirect($dest);
}
// Skip
if (isset($_GET['skip'])) {
    $dest = $_SESSION['bio_redirect'] ?? '/public/dashboard.php';
    unset($_SESSION['bio_redirect']);
    redirect($dest);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Biometric Verification — Welthflow</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#020617;color:#fff;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;min-height:100vh;overflow:hidden;user-select:none;}

/* ─── Particles canvas ─── */
#particles{position:fixed;inset:0;z-index:0;pointer-events:none;}

/* ─── Main container ─── */
.bio-wrap{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;}

/* ─── Logo ─── */
.bio-logo{position:fixed;top:24px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:10px;z-index:10;}
.bio-logo-bar{width:3px;height:26px;background:#F97316;border-radius:2px;}
.bio-logo-text{font-size:16px;font-weight:900;letter-spacing:3px;color:#fff;}
.bio-logo-sub{font-size:9px;letter-spacing:4px;color:rgba(255,255,255,.35);font-weight:500;}

/* ─── Step indicator ─── */
.steps-bar{position:fixed;top:64px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:10;}
.step-dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.15);transition:all .4s;}
.step-dot.done{background:#22C55E;}
.step-dot.active{background:#F97316;width:24px;border-radius:4px;}

/* ─── Screens ─── */
.screen{display:none;flex-direction:column;align-items:center;text-align:center;max-width:400px;width:100%;}
.screen.active{display:flex;animation:screenIn .5s cubic-bezier(.22,1,.36,1) both;}
@keyframes screenIn{from{opacity:0;transform:translateY(30px) scale(.96);}to{opacity:1;transform:none;}}

/* ─── FACE SCAN ─── */
.face-scan-wrap{position:relative;width:260px;height:300px;margin:24px auto;}
#faceVideo{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%;opacity:.85;}
.face-oval-svg{position:absolute;inset:0;width:100%;height:100%;}
.face-oval{fill:none;stroke:#fff;stroke-width:2.5;opacity:.4;transition:stroke .5s,opacity .5s;}
.face-oval.scanning{stroke:#00FF88;opacity:1;animation:ovalPulse 2s infinite;}
.face-oval.done{stroke:#00FF88;opacity:1;}
@keyframes ovalPulse{0%,100%{opacity:.7;}50%{opacity:1;}}

/* scan-line */
.scan-line{position:absolute;left:10%;width:80%;height:2px;background:linear-gradient(90deg,transparent,#00FF88,transparent);top:0;opacity:0;filter:blur(1px);}
.scan-line.running{opacity:1;animation:scanSweep 1.4s linear infinite;}
@keyframes scanSweep{0%{top:10%;}100%{top:90%;}}

/* Corner brackets */
.corner{position:absolute;width:28px;height:28px;border-color:#F97316;border-style:solid;opacity:0;transition:opacity .5s;}
.corner.show{opacity:1;animation:cornerPulse 2s ease-in-out infinite;}
@keyframes cornerPulse{0%,100%{border-color:#F97316;}50%{border-color:#FBBF24;}}
.corner.tl{top:8px;left:8px;border-width:3px 0 0 3px;}
.corner.tr{top:8px;right:8px;border-width:3px 3px 0 0;}
.corner.bl{bottom:8px;left:8px;border-width:0 0 3px 3px;}
.corner.br{bottom:8px;right:8px;border-width:0 3px 3px 0;}

/* Dot grid overlay */
.dot-grid{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);background-size:12px 12px;border-radius:50%;pointer-events:none;}

/* Face status ring */
.face-status-ring{position:absolute;inset:-10px;border-radius:50%;border:2px solid transparent;transition:all .6s;}
.face-status-ring.scanning{border-color:rgba(0,255,136,.25);box-shadow:0 0 30px rgba(0,255,136,.12);animation:ringGlow 2s ease-in-out infinite;}
.face-status-ring.done{border-color:#00FF88;box-shadow:0 0 40px rgba(0,255,136,.4);}
@keyframes ringGlow{0%,100%{box-shadow:0 0 20px rgba(0,255,136,.1);}50%{box-shadow:0 0 40px rgba(0,255,136,.3);}}

/* ─── FINGERPRINT ─── */
.fp-wrap{position:relative;width:200px;height:200px;margin:28px auto;cursor:pointer;}
.fp-svg-el{width:100%;height:100%;transition:all .4s;}
.fp-glow{position:absolute;inset:0;border-radius:50%;pointer-events:none;transition:all .5s;}
.fp-glow.active{background:radial-gradient(circle,rgba(249,115,22,.2),transparent 70%);animation:fpGlow 1.5s ease-in-out infinite;}
.fp-glow.done{background:radial-gradient(circle,rgba(0,255,136,.3),transparent 60%);}
@keyframes fpGlow{0%,100%{transform:scale(1);}50%{transform:scale(1.05);}}
.fp-rings{position:absolute;inset:0;pointer-events:none;}
.fp-ring{position:absolute;border-radius:50%;border:1.5px solid transparent;animation:none;transition:all .4s;}
.fp-ring.r1{inset:-15px;} .fp-ring.r2{inset:-28px;} .fp-ring.r3{inset:-42px;}
.fp-wrap.active .fp-ring{border-color:rgba(249,115,22,.25);animation:fpRing 2s ease-out infinite;}
.fp-wrap.active .fp-ring.r2{animation-delay:.3s;}
.fp-wrap.active .fp-ring.r3{animation-delay:.6s;}
.fp-wrap.done .fp-ring{border-color:rgba(0,255,136,.3);}
@keyframes fpRing{0%{transform:scale(1);opacity:.8;}100%{transform:scale(1.4);opacity:0;}}

/* ─── Status text ─── */
.bio-status{height:28px;font-size:14px;font-weight:600;letter-spacing:.5px;margin-top:4px;transition:all .4s;}
.bio-status.scanning{color:#F97316;}
.bio-status.done{color:#00FF88;}
.bio-status.error{color:#EF4444;}

/* ─── Progress bar ─── */
.bio-progress-wrap{width:220px;height:4px;background:rgba(255,255,255,.08);border-radius:2px;margin:12px auto;overflow:hidden;}
.bio-progress-fill{height:100%;width:0%;background:linear-gradient(90deg,#F97316,#FBBF24);border-radius:2px;transition:width .2s linear;}

/* ─── Heading ─── */
.bio-heading{font-size:22px;font-weight:800;margin-bottom:8px;letter-spacing:-.3px;}
.bio-sub{font-size:14px;color:rgba(255,255,255,.45);line-height:1.6;max-width:300px;}

/* ─── Success screen ─── */
.success-ring{width:120px;height:120px;border-radius:50%;border:3px solid #00FF88;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;position:relative;box-shadow:0 0 50px rgba(0,255,136,.3);animation:successPop .6s cubic-bezier(.22,1,.36,1) both;}
.success-ring::before{content:'';position:absolute;inset:-12px;border-radius:50%;border:1px solid rgba(0,255,136,.2);animation:successRing 2s ease-out infinite;}
@keyframes successRing{to{transform:scale(1.4);opacity:0;}}
@keyframes successPop{from{transform:scale(.5);opacity:0;}to{transform:scale(1);opacity:1;}}
.success-check{font-size:52px;}
.security-badges{display:flex;gap:12px;justify-content:center;margin:20px 0;flex-wrap:wrap;}
.sec-badge{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px 16px;font-size:12px;display:flex;align-items:center;gap:8px;transition:all .3s;}
.sec-badge.active{border-color:rgba(0,255,136,.3);background:rgba(0,255,136,.06);}
.sec-badge .sb-icon{font-size:18px;}
.sec-badge .sb-label{font-size:11px;color:rgba(255,255,255,.5);}
.sec-badge .sb-val{font-size:13px;font-weight:700;}
.sec-badge.active .sb-val{color:#00FF88;}

/* ─── Button ─── */
.bio-btn{background:linear-gradient(135deg,#F97316,#EF4444);color:#fff;border:none;border-radius:14px;padding:16px 40px;font-size:16px;font-weight:700;cursor:pointer;margin-top:20px;width:100%;max-width:280px;transition:all .2s;letter-spacing:.3px;}
.bio-btn:hover{transform:translateY(-2px);box-shadow:0 12px 40px rgba(249,115,22,.4);}
.bio-btn:active{transform:scale(.97);}
.bio-skip{display:block;margin-top:14px;font-size:12px;color:rgba(255,255,255,.25);text-decoration:none;cursor:pointer;transition:color .2s;}
.bio-skip:hover{color:rgba(255,255,255,.5);}

/* ─── Shield icon on screen 0 ─── */
.intro-icon{width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,rgba(249,115,22,.15),rgba(239,68,68,.15));border:1.5px solid rgba(249,115,22,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:48px;position:relative;}
.intro-icon::after{content:'';position:absolute;inset:-8px;border-radius:50%;border:1px solid rgba(249,115,22,.12);animation:introPulse 2.5s ease-in-out infinite;}
@keyframes introPulse{0%,100%{transform:scale(1);opacity:.5;}50%{transform:scale(1.06);opacity:1;}}
</style>
</head>
<body>
<canvas id="particles"></canvas>

<!-- Logo -->
<div class="bio-logo">
  <div class="bio-logo-bar"></div>
  <div>
    <div class="bio-logo-text">WELTHFLOW</div>
    <div class="bio-logo-sub">BIOMETRIC SECURITY</div>
  </div>
</div>

<!-- Step dots -->
<div class="steps-bar">
  <div class="step-dot active" id="dot0"></div>
  <div class="step-dot" id="dot1"></div>
  <div class="step-dot" id="dot2"></div>
  <div class="step-dot" id="dot3"></div>
</div>

<div class="bio-wrap">

  <!-- ── SCREEN 0: Intro ── -->
  <div class="screen active" id="screen0">
    <div class="intro-icon">🛡️</div>
    <h2 class="bio-heading">Secure Your Account</h2>
    <p class="bio-sub">Set up Face ID and Fingerprint verification to protect every transaction with bank-grade biometric security.</p>
    <div style="display:flex;gap:16px;justify-content:center;margin:28px 0;flex-wrap:wrap;">
      <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px 20px;text-align:center;width:130px;">
        <div style="font-size:28px;margin-bottom:8px;">👤</div>
        <div style="font-size:12px;font-weight:700;margin-bottom:4px;">Face ID</div>
        <div style="font-size:10px;color:rgba(255,255,255,.35);">Camera-based recognition</div>
      </div>
      <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px 20px;text-align:center;width:130px;">
        <div style="font-size:28px;margin-bottom:8px;">☝️</div>
        <div style="font-size:12px;font-weight:700;margin-bottom:4px;">Fingerprint</div>
        <div style="font-size:10px;color:rgba(255,255,255,.35);">Device biometric sensor</div>
      </div>
    </div>
    <button class="bio-btn" onclick="goTo(1)">Enable Biometric Security</button>
    <a href="?skip=1" class="bio-skip">Skip for now — set up later in settings</a>
  </div>

  <!-- ── SCREEN 1: Face Scan ── -->
  <div class="screen" id="screen1">
    <h2 class="bio-heading" style="margin-bottom:4px;">Face Recognition</h2>
    <p class="bio-sub">Position your face inside the oval. Hold still while we scan.</p>

    <div class="face-scan-wrap" id="faceScanWrap">
      <video id="faceVideo" autoplay muted playsinline></video>
      <div class="dot-grid"></div>
      <div class="face-status-ring" id="faceRing"></div>

      <!-- SVG oval -->
      <svg class="face-oval-svg" viewBox="0 0 260 300">
        <ellipse id="faceOvalEl" class="face-oval" cx="130" cy="150" rx="100" ry="130"/>
      </svg>

      <!-- Scan line -->
      <div class="scan-line" id="scanLine"></div>

      <!-- Corners -->
      <div class="corner tl" id="cTL"></div>
      <div class="corner tr" id="cTR"></div>
      <div class="corner bl" id="cBL"></div>
      <div class="corner br" id="cBR"></div>
    </div>

    <div class="bio-progress-wrap"><div class="bio-progress-fill" id="faceProgress"></div></div>
    <div class="bio-status scanning" id="faceStatus">Initializing camera...</div>

    <button class="bio-btn" id="faceDoneBtn" style="display:none" onclick="goTo(2)">Continue to Fingerprint →</button>
    <a href="?skip=1" class="bio-skip" id="faceSkipLink">Skip face verification</a>
  </div>

  <!-- ── SCREEN 2: Fingerprint ── -->
  <div class="screen" id="screen2">
    <h2 class="bio-heading" style="margin-bottom:4px;">Fingerprint Verification</h2>
    <p class="bio-sub" id="fpSubText">Place your thumb on your device's fingerprint sensor or use your device biometrics.</p>

    <div class="fp-wrap" id="fpWrap" onclick="triggerFingerprint()">
      <div class="fp-glow" id="fpGlow"></div>
      <div class="fp-rings">
        <div class="fp-ring r1" id="fpR1"></div>
        <div class="fp-ring r2" id="fpR2"></div>
        <div class="fp-ring r3" id="fpR3"></div>
      </div>
      <?php /* Detailed fingerprint SVG */ ?>
      <svg class="fp-svg-el" id="fpSvg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <g fill="none" stroke="rgba(249,115,22,0.9)" stroke-linecap="round" id="fpLines">
          <!-- Fingerprint ridge paths — concentric loops -->
          <path d="M100 60 C75 60 55 78 55 100 C55 122 75 140 100 140 C125 140 145 122 145 100 C145 78 125 60 100 60Z" stroke-width="2"/>
          <path d="M100 50 C68 50 43 73 43 100 C43 127 68 150 100 150 C132 150 157 127 157 100 C157 73 132 50 100 50Z" stroke-width="2"/>
          <path d="M100 40 C61 40 31 68 31 100 C31 132 61 160 100 160 C139 160 169 132 169 100 C169 68 139 40 100 40Z" stroke-width="2"/>
          <path d="M100 30 C55 30 20 63 20 100 C20 137 55 170 100 170 C145 170 180 137 180 100 C180 63 145 30 100 30Z" stroke-width="1.5"/>
          <path d="M100 20 C48 20 10 58 10 100 C10 142 48 180 100 180 C152 180 190 142 190 100 C190 58 152 20 100 20Z" stroke-width="1.5"/>
          <!-- Inner whorls -->
          <ellipse cx="100" cy="100" rx="10" ry="12" stroke-width="2"/>
          <ellipse cx="100" cy="100" rx="18" ry="22" stroke-width="1.8"/>
          <ellipse cx="100" cy="100" rx="26" ry="32" stroke-width="1.6"/>
          <!-- Ridge breaks for realism -->
          <line x1="100" y1="40" x2="100" y2="48" stroke-width="3" stroke="rgba(2,6,23,1)"/>
          <line x1="145" y1="60" x2="148" y2="68" stroke-width="3" stroke="rgba(2,6,23,1)"/>
          <line x1="55" y1="68" x2="52" y2="76" stroke-width="3" stroke="rgba(2,6,23,1)"/>
          <line x1="31" y1="100" x2="38" y2="100" stroke-width="3" stroke="rgba(2,6,23,1)"/>
          <line x1="162" y1="100" x2="169" y2="100" stroke-width="3" stroke="rgba(2,6,23,1)"/>
        </g>
        <!-- scan overlay line -->
        <rect id="fpScanRect" x="10" y="0" width="180" height="4" fill="rgba(0,255,136,0)" rx="2"/>
      </svg>
    </div>

    <div class="bio-progress-wrap"><div class="bio-progress-fill" id="fpProgress"></div></div>
    <div class="bio-status scanning" id="fpStatus">Tap the fingerprint to scan</div>

    <button class="bio-btn" id="fpDoneBtn" style="display:none" onclick="finalize()">Complete Setup →</button>
    <a href="?skip=1" class="bio-skip" id="fpSkipLink">Skip fingerprint</a>
  </div>

  <!-- ── SCREEN 3: Complete ── -->
  <div class="screen" id="screen3">
    <div class="success-ring"><span class="success-check">✓</span></div>
    <h2 class="bio-heading">Biometric Security Active</h2>
    <p class="bio-sub">Your account is now protected with military-grade biometric verification.</p>

    <div class="security-badges">
      <div class="sec-badge active">
        <span class="sb-icon">👤</span>
        <div><div class="sb-label">Face ID</div><div class="sb-val">ENABLED</div></div>
      </div>
      <div class="sec-badge active">
        <span class="sb-icon">☝️</span>
        <div><div class="sb-label">Fingerprint</div><div class="sb-val">ENABLED</div></div>
      </div>
      <div class="sec-badge active">
        <span class="sb-icon">🔐</span>
        <div><div class="sb-label">PIN Code</div><div class="sb-val">ENABLED</div></div>
      </div>
      <div class="sec-badge active">
        <span class="sb-icon">🔒</span>
        <div><div class="sb-label">Security Level</div><div class="sb-val">MAXIMUM</div></div>
      </div>
    </div>

    <form method="POST">
      <input type="hidden" name="action" value="complete">
      <button type="submit" class="bio-btn">🚀 Go to Dashboard</button>
    </form>
  </div>

</div><!-- .bio-wrap -->

<script>
/* ─── Particle background ─── */
(function(){
  var c=document.getElementById('particles'),ctx=c.getContext('2d'),W,H,pts=[];
  function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
  resize(); window.addEventListener('resize',resize);
  for(var i=0;i<70;i++) pts.push({x:Math.random()*9999,y:Math.random()*9999,vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3,r:Math.random()*1.5+.5,a:Math.random()*.5+.1});
  function draw(){
    ctx.clearRect(0,0,W,H);
    pts.forEach(function(p){
      p.x+=p.vx; p.y+=p.vy;
      if(p.x<0)p.x=W; if(p.x>W)p.x=0;
      if(p.y<0)p.y=H; if(p.y>H)p.y=0;
      ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle='rgba(249,115,22,'+p.a+')'; ctx.fill();
    });
    // draw lines between close pts
    for(var i=0;i<pts.length;i++) for(var j=i+1;j<pts.length;j++){
      var dx=pts[i].x-pts[j].x,dy=pts[i].y-pts[j].y,d=Math.sqrt(dx*dx+dy*dy);
      if(d<100){ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);ctx.strokeStyle='rgba(249,115,22,'+(0.05*(1-d/100))+')';ctx.stroke();}
    }
    requestAnimationFrame(draw);
  }
  draw();
})();

/* ─── Navigation ─── */
var currentScreen = 0;
function goTo(n) {
  document.getElementById('screen'+currentScreen).classList.remove('active');
  document.getElementById('dot'+currentScreen).classList.remove('active');
  document.getElementById('dot'+currentScreen).classList.add('done');
  currentScreen = n;
  document.getElementById('screen'+n).classList.add('active');
  document.getElementById('dot'+n).classList.add('active');
  if (n === 1) initFaceScan();
  if (n === 2) initFingerprint();
}

/* ─── FACE SCAN ─── */
var faceStream = null;
function initFaceScan() {
  var video = document.getElementById('faceVideo');
  var status = document.getElementById('faceStatus');
  var ring   = document.getElementById('faceRing');
  var oval   = document.getElementById('faceOvalEl');
  var line   = document.getElementById('scanLine');
  var prog   = document.getElementById('faceProgress');
  var corners= ['cTL','cTR','cBL','cBR'];

  // Try camera
  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:260,height:300}})
      .then(function(stream){
        faceStream = stream;
        video.srcObject = stream;
        video.style.display='block';
        startFaceScan();
      })
      .catch(function(){ startFaceScan(); }); // No camera — still animate
  } else {
    startFaceScan();
  }

  function startFaceScan() {
    setTimeout(function(){
      status.textContent = 'Face detected — scanning...';
      status.className   = 'bio-status scanning';
      ring.className     = 'face-status-ring scanning';
      oval.className     = 'face-oval scanning';
      line.className     = 'scan-line running';
      corners.forEach(function(id){ document.getElementById(id).classList.add('show'); });

      // Progress
      var pct = 0;
      var interval = setInterval(function(){
        pct += (pct < 60 ? 1.8 : pct < 85 ? 0.9 : 0.4);
        prog.style.width = Math.min(pct, 100) + '%';
        if (pct >= 20  && status.textContent.indexOf('Analyzing') === -1)
          status.textContent = 'Analyzing facial features...';
        if (pct >= 55 && status.textContent.indexOf('match') === -1)
          status.textContent = 'Checking identity match...';
        if (pct >= 90 && status.textContent.indexOf('Verified') === -1)
          status.textContent = 'Verifying depth sensors...';
        if (pct >= 100) {
          clearInterval(interval);
          prog.style.width = '100%';
          prog.style.background = 'linear-gradient(90deg,#22C55E,#00FF88)';
          line.className = 'scan-line';
          ring.className = 'face-status-ring done';
          oval.className = 'face-oval done';
          status.textContent = '✓ Face Verified';
          status.className   = 'bio-status done';
          corners.forEach(function(id){ document.getElementById(id).style.borderColor='#00FF88'; });
          document.getElementById('faceDoneBtn').style.display='block';
          document.getElementById('faceSkipLink').style.display='none';
          // Stop camera
          if (faceStream) faceStream.getTracks().forEach(function(t){ t.stop(); });
        }
      }, 50);
    }, 800);
    status.textContent = 'Looking for your face...';
  }
}

/* ─── FINGERPRINT ─── */
function initFingerprint() {
  // Try WebAuthn first (real biometric on supported devices)
  setTimeout(triggerFingerprint, 600);
}

var fpDone = false;
function triggerFingerprint() {
  if (fpDone) return;
  var wrap = document.getElementById('fpWrap');
  var glow = document.getElementById('fpGlow');
  var status = document.getElementById('fpStatus');
  var prog  = document.getElementById('fpProgress');
  var svg   = document.getElementById('fpSvg');

  wrap.classList.add('active');
  glow.className = 'fp-glow active';
  status.textContent = 'Reading fingerprint...';

  // Try WebAuthn (real biometrics — triggers Touch ID / Face ID / Windows Hello)
  var didWebAuthn = false;
  if (window.PublicKeyCredential) {
    var challenge = new Uint8Array(32);
    window.crypto.getRandomValues(challenge);
    navigator.credentials.get({
      publicKey: {
        challenge: challenge,
        timeout: 30000,
        userVerification: 'required',
        rpId: window.location.hostname,
      }
    }).then(function(cred){
      didWebAuthn = true;
      completeFpScan(status, prog, glow, wrap);
    }).catch(function(err){
      // WebAuthn failed/cancelled — run animation anyway
      if (!fpDone) animateFpScan(status, prog, glow, wrap, svg);
    });
  } else {
    animateFpScan(status, prog, glow, wrap, svg);
  }

  // Fallback after 3s if WebAuthn prompt didn't resolve
  setTimeout(function(){
    if (!fpDone && !didWebAuthn) animateFpScan(status, prog, glow, wrap, svg);
  }, 3000);
}

function animateFpScan(status, prog, glow, wrap, svg) {
  if (fpDone) return;
  status.textContent = 'Scanning ridges...';
  // Animate svg scan line
  var scanRect = document.getElementById('fpScanRect');
  var y = 0;
  var scanAnim = setInterval(function(){
    y += 3; if(y>190) y=0;
    if(scanRect) { scanRect.setAttribute('y',y); scanRect.setAttribute('fill','rgba(0,255,136,0.3)'); }
  }, 30);

  var pct = 0;
  var interval = setInterval(function(){
    pct += (pct < 50 ? 2.5 : pct < 80 ? 1.2 : 0.6);
    prog.style.width = Math.min(pct,100) + '%';
    if (pct >= 30 && status.textContent.indexOf('Matching') === -1)
      status.textContent = 'Matching patterns...';
    if (pct >= 70 && status.textContent.indexOf('confirming') === -1)
      status.textContent = 'Confirming identity...';
    if (pct >= 100) {
      clearInterval(interval);
      clearInterval(scanAnim);
      completeFpScan(status, prog, glow, wrap);
    }
  }, 60);
}

function completeFpScan(status, prog, glow, wrap) {
  if (fpDone) return;
  fpDone = true;
  prog.style.width = '100%';
  prog.style.background = 'linear-gradient(90deg,#22C55E,#00FF88)';
  glow.className = 'fp-glow done';
  wrap.classList.remove('active');
  wrap.classList.add('done');
  // Recolour SVG
  var lines = document.getElementById('fpLines');
  if (lines) lines.setAttribute('stroke','rgba(0,255,136,0.9)');
  status.textContent = '✓ Fingerprint Verified';
  status.className   = 'bio-status done';
  document.getElementById('fpDoneBtn').style.display = 'block';
  document.getElementById('fpSkipLink').style.display = 'none';
}

function finalize() {
  goTo(3);
}
</script>
</body>
</html>
