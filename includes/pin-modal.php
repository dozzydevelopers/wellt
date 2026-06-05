<?php
// Transaction PIN Modal
// Include this file in pages that require the transaction PIN confirmation step.
?>
<div class="pin-overlay" id="pinOverlay" style="display:none">
  <div class="pin-box">
    <div class="pin-box-hd">
      <div class="pin-lock-ico">🔐</div>
      <h3>Transaction PIN</h3>
      <p>Enter your 4-digit security PIN to continue</p>
    </div>
    <div class="pin-dots-row" id="pinDots">
      <span class="pd" id="pd0"></span>
      <span class="pd" id="pd1"></span>
      <span class="pd" id="pd2"></span>
      <span class="pd" id="pd3"></span>
    </div>
    <div class="pin-err-msg" id="pinErrMsg"></div>
    <div class="pin-keypad-grid">
      <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 'CLR', 0, '⌫'] as $k): ?>
        <button type="button" class="pk<?= $k === 'CLR' ? ' pk-clr' : ($k === '⌫' ? ' pk-bk' : '') ?>"
          onclick="pkPress('<?= $k ?>')"><?= $k ?></button>
      <?php endforeach; ?>
    </div>
    <button type="button" onclick="closePinModal()"
      style="margin-top:14px;background:none;border:none;color:#94A3B8;font-size:13px;cursor:pointer;">✕ Cancel</button>
  </div>
</div>

<style>
  .pin-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .75);
    backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn .2s;
  }

  .pin-box {
    background: #fff;
    border-radius: 24px;
    padding: 36px 32px 28px;
    width: 340px;
    max-width: 92vw;
    text-align: center;
    box-shadow: 0 24px 80px rgba(0, 0, 0, .3);
  }

  .pin-lock-ico {
    font-size: 44px;
    margin-bottom: 10px;
  }

  .pin-box-hd h3 {
    margin: 0 0 4px;
    color: #0F172A;
    font-size: 20px;
    font-weight: 800;
  }

  .pin-box-hd p {
    margin: 0 0 22px;
    color: #64748B;
    font-size: 13px;
  }

  .pin-dots-row {
    display: flex;
    gap: 18px;
    justify-content: center;
    margin-bottom: 8px;
  }

  .pd {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #CBD5E1;
    background: #F8FAFC;
    transition: all .2s;
  }

  .pd.filled {
    background: #F97316;
    border-color: #F97316;
    transform: scale(1.15);
  }

  .pin-err-msg {
    color: #EF4444;
    font-size: 12px;
    min-height: 20px;
    margin-bottom: 12px;
  }

  .pin-keypad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
  }

  .pk {
    height: 58px;
    border-radius: 14px;
    border: 2px solid #E2E8F0;
    background: #F8FAFC;
    font-size: 22px;
    font-weight: 700;
    color: #0F172A;
    cursor: pointer;
    transition: all .12s;
    font-family: inherit;
  }

  .pk:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
    transform: translateY(-1px);
  }

  .pk:active {
    transform: scale(.93);
    background: #E2E8F0;
  }

  .pk-clr {
    background: #FEF2F2;
    color: #EF4444;
    font-size: 13px;
    font-weight: 700;
  }

  .pk-bk {
    background: #FFF7ED;
    color: #F97316;
    font-size: 20px;
  }
</style>

<script>
  var _pv = '', _pf = null, _pfld = 'transaction_pin';
  function showPinModal(formId, field) {
    _pv = ''; _pf = document.getElementById(formId); _pfld = field || 'transaction_pin';
    _pdots(); document.getElementById('pinErrMsg').textContent = '';
    document.getElementById('pinOverlay').style.display = 'flex';
  }
  function closePinModal() { document.getElementById('pinOverlay').style.display = 'none'; _pv = ''; _pdots(); }
  function pkPress(k) {
    if (k === 'CLR') { _pv = ''; }
    else if (k === '⌫') { _pv = _pv.slice(0, -1); }
    else if (_pv.length < 4) { _pv += String(k); }
    _pdots();
    if (_pv.length === 4) setTimeout(_psubmit, 280);
  }
  function _pdots() { for (var i = 0; i < 4; i++) { var d = document.getElementById('pd' + i); if (d) d.className = 'pd' + (_pv.length > i ? ' filled' : ''); } }
  function _psubmit() {
    if (!_pf) return;
    var f = _pf.querySelector('[name=' + _pfld + ']');
    if (!f) { f = document.createElement('input'); f.type = 'hidden'; f.name = _pfld; _pf.appendChild(f); }
    f.value = _pv;
    document.getElementById('pinOverlay').style.display = 'none';
    _pf.submit();
  }
  document.addEventListener('keydown', function (e) {
    if (document.getElementById('pinOverlay').style.display === 'none') return;
    if (e.key >= '0' && e.key <= '9') pkPress(e.key);
    else if (e.key === 'Backspace') pkPress('⌫');
    else if (e.key === 'Escape') closePinModal();
  });
</script>