<?php
/*
===============================================================================
TORNO SUBITO / FERRAMENTA VALENTI
Net Art experiment by EmaF205
Single-file site — PHP 5.6+ compatible

Internet non dovrebbe chiudere.
Questo sito lo fa.

Se stai leggendo il sorgente:
la ferramenta potrebbe essere chiusa.
Torna durante gli orari di apertura.

NON TOCCARE GLI ORARI:
il negozio deve chiudere davvero.
===============================================================================
*/

date_default_timezone_set('Europe/Rome');

/* PRODUZIONE
   Il sito usa esclusivamente data e ora reali (Europe/Rome).
   Gli stati dell'opera sono documentati separatamente in docs/STATES.md.
*/
$ALLOW_TEST_MODE = false;

$test = '';

$now = new DateTime('now', new DateTimeZone('Europe/Rome'));

function mins($dt) {
    return ((int)$dt->format('G')) * 60 + (int)$dt->format('i');
}

function shop_state($now, $test) {
    if ($test !== '') {
        if ($test === 'open')   return array('open', 'SIAMO APERTI', 'La ferramenta è aperta.', '');
        if ($test === 'lunch')  return array('lunch', 'PAUSA PRANZO', 'Riapriamo alle 15:00.', '15:00');
        if ($test === 'closed') return array('closed', 'LA FERRAMENTA È CHIUSA', 'Torneremo domani!', '08:00');
        if ($test === 'sunday') return array('sunday', 'RIPOSO SETTIMANALE', 'Ci rivediamo lunedì.', '08:00');
    }

    $dow = (int)$now->format('N'); // 1=lun, 7=dom
    $m = mins($now);

    if ($dow === 7) {
        return array('sunday', 'RIPOSO SETTIMANALE', 'Ci rivediamo lunedì alle 08:00.', '08:00');
    }

    if ($m >= 480 && $m < 750) {
        return array('open', 'SIAMO APERTI', 'La ferramenta è aperta.', '');
    }

    if ($m >= 750 && $m < 900) {
        return array('lunch', 'PAUSA PRANZO', 'Riapriamo alle 15:00.', '15:00');
    }

    if ($m >= 900 && $m < 1140) {
        return array('open', 'SIAMO APERTI', 'La ferramenta è aperta.', '');
    }

    return array('closed', 'LA FERRAMENTA È CHIUSA', 'Torneremo domani!', '08:00');
}

function next_transition($now) {
    $tz = new DateTimeZone('Europe/Rome');
    $dow = (int)$now->format('N');
    $today = $now->format('Y-m-d');

    if ($dow !== 7) {
        $times = array('08:00','12:30','15:00','19:00');
        foreach ($times as $t) {
            $candidate = new DateTime($today . ' ' . $t, $tz);
            if ($candidate > $now) return $candidate;
        }
    }

    $next = clone $now;
    $next->modify('+1 day');
    $next->setTime(8, 0, 0);

    while ((int)$next->format('N') === 7) {
        $next->modify('+1 day');
        $next->setTime(8, 0, 0);
    }
    return $next;
}

$data = shop_state($now, $test);
$state = $data[0];
$headline = $data[1];
$message = $data[2];
$reopen = $data[3];
$isOpen = ($state === 'open');

$next = next_transition($now);
$seconds_to_transition = max(2, $next->getTimestamp() - $now->getTimestamp());

$products = array(
    array('Martello da carpentiere', '€ 12,50', '&#128296;', 'NOVITÀ'),
    array('Cacciavite a stella', '€ 4,80', '&#128295;', ''),
    array('Metro 5 metri', '€ 6,20', '&#128207;', 'OFFERTA'),
    array('Scatola viti assortite', '€ 8,90', '&#9881;', ''),
    array('Pennello professionale', '€ 5,50', '&#9998;', ''),
    array('Guanti da lavoro', '€ 7,40', '&#9638;', 'NUOVO')
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>Ferramenta Valenti - Tutto per la casa</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Ferramenta Valenti - dal 1970 tutto per la casa">
<style>
:root{
  --daylight:1;
  --warmth:0;
}
*{box-sizing:border-box}
html,body{margin:0;padding:0;min-height:100%;font-family:Verdana,Arial,sans-serif;color:#202020}
body{
  background:#b8c3cf;
  transition:background 18s linear,filter 18s linear;
  filter:brightness(var(--daylight));
  overflow-x:auto;
}
body:before{
  content:"";
  position:fixed;inset:0;pointer-events:none;z-index:9998;
  background:
    radial-gradient(circle at 72% 8%,rgba(255,219,145,calc(.28 * var(--warmth))),transparent 34%),
    linear-gradient(rgba(9,19,43,calc((1 - var(--daylight)) * .42)),rgba(3,8,24,calc((1 - var(--daylight)) * .65)));
  transition:background 18s linear;
}
a{color:#003399;text-decoration:underline}
a:visited{color:#663399}

.skyline{
  width:960px;margin:8px auto 0;text-align:right;
  font-size:10px;color:#344;padding-right:6px;
}
.browser{
  width:960px;margin:8px auto 20px;background:#fff;border:2px solid #4b4b4b;
  box-shadow:3px 3px 0 #818181;
}
.winbar{
  height:28px;background:linear-gradient(#2f6bb5,#0a3c82);
  color:#fff;display:flex;align-items:center;padding:0 7px;
  font-size:12px;font-weight:bold;
}
.controls{margin-left:auto;font-family:Tahoma,sans-serif;letter-spacing:4px}
.menu,.toolbar,.address{
  background:#ece9d8;border-bottom:1px solid #9d9d9d;padding:5px 8px;font-size:11px;
}
.toolbar{padding:7px 8px}
.address span{
  display:inline-block;background:#fff;border:2px inset #dadada;padding:3px 7px;width:77%;
}
.page{background:#f2e7cc}
.header{
  position:relative;min-height:102px;padding:14px 18px 12px;
  border-bottom:3px double #725f38;
  background:
    linear-gradient(90deg,#f5e6bf 0,#ead5a0 60%,#d9c18a 100%);
}
.brand{
  font-family:Arial Black,Arial,sans-serif;color:#0c3d79;
  font-size:33px;letter-spacing:-1px;text-shadow:1px 1px #fff;
}
.sub{font-size:12px;font-style:italic;color:#31527e}
.cat{font-size:10px;color:#143f74;padding-top:8px;letter-spacing:.4px}
.sticky{
  position:absolute;right:18px;top:12px;width:220px;
  background:#fff1a6;border:1px solid #a88b36;padding:10px 12px;
  transform:rotate(-1.2deg);font-family:"Comic Sans MS",cursive;
  color:#81251d;text-align:center;box-shadow:2px 2px 0 rgba(0,0,0,.15);
}
.layout{display:table;width:100%;table-layout:fixed}
.sidebar,.content{display:table-cell;vertical-align:top}
.sidebar{
  width:170px;background:#efe5ce;border-right:1px solid #b1a387;padding:9px;
}
.sidebar.right{border-right:0;border-left:1px solid #b1a387}
.oldbtn{
  display:block;background:linear-gradient(#fff,#d8d8d8);
  border:2px outset #eee;padding:7px 8px;margin-bottom:5px;
  font-size:11px;color:#003399;text-decoration:none;
}
.oldbtn:hover{background:#ffffcc}
.section-title{
  font-family:Georgia,serif;color:#0b3f78;font-size:18px;margin:10px 0 7px;
}
.hours{
  background:#ffe99b;border:1px solid #a48a30;padding:9px;margin-top:10px;
  font-size:11px;line-height:1.5;box-shadow:1px 1px 0 #fff inset;
}
.status-open,.status-closed{
  padding:7px;text-align:center;font-weight:bold;margin-top:10px;font-size:10px;
}
.status-open{background:#dff0d8;border:1px solid #6a9d54;color:#2b5b1f}
.status-closed{background:#f2dede;border:1px solid #a94442;color:#8a1f11}

.content{
  position:relative;background:#fff;padding:13px;min-height:510px;overflow:hidden;
}
.marquee{
  background:#ffffcc;border:1px solid #c9bd66;padding:4px;color:#8b1717;font-size:10px;
}
.welcome{
  font-family:Georgia,serif;color:#0c3d76;font-size:24px;margin:10px 0 6px;
}
.intro{font-size:11px;line-height:1.55}
.hero{
  margin-top:10px;border:1px solid #a69c88;background:#f8f3e6;
  padding:10px;display:flex;align-items:center;gap:12px;
}
.hero-icon{font-size:52px}
.hero-copy{font-size:11px;line-height:1.45}
.products{margin-top:9px}
.card{
  position:relative;display:inline-block;vertical-align:top;width:29.7%;
  margin:1.15%;border:1px solid #b8ad98;background:#faf7ed;
  padding:8px;text-align:center;font-size:10px;min-height:120px;
  box-shadow:1px 1px 0 #ddd;
}
.badge{
  position:absolute;right:-5px;top:-5px;background:#d92222;color:#fff;
  font:bold 8px Verdana;padding:3px 4px;transform:rotate(5deg);
}
.ico{font-size:34px;display:block;margin:5px 0}
.price{color:#9e1717;font-weight:bold;margin:4px}
.buy{
  font-family:Verdana;font-size:9px;padding:4px 6px;border:2px outset #ddd;
  background:#ece9d8;cursor:pointer;
}
.buy[disabled]{color:#777;cursor:not-allowed}
.tip{
  font-size:9px;color:#555;margin-top:9px;border-top:1px dotted #999;padding-top:6px;
}
.footer{
  background:#e9dfc7;border-top:1px solid #b1a387;padding:8px 11px;
  font-size:9px;display:flex;justify-content:space-between;gap:12px;align-items:center;
}
.counter{
  display:inline-block;background:#111;color:#79ff79;font-family:"Courier New",monospace;
  padding:3px 6px;letter-spacing:2px;border:2px inset #666;
}
.testbar{
  width:960px;margin:10px auto 0;background:#202020;color:#fff;
  padding:7px 9px;font-size:10px;
}
.testbar a{color:#9fd4ff;margin-right:9px}
.clockbox{margin-top:12px;font:10px "Courier New",monospace;color:#333}

/* --- THE WORK: DIGITAL SHUTTER --- */
.shutter-wrap{
  position:absolute;inset:0;z-index:40;overflow:hidden;pointer-events:auto;
}
.shutter{
  position:absolute;left:0;right:0;top:-100%;height:100%;
  background:
    linear-gradient(90deg,rgba(255,255,255,.15),transparent 18%,rgba(255,255,255,.12) 52%,transparent 74%),
    repeating-linear-gradient(to bottom,#8f969a 0,#8f969a 14px,#646b6f 15px,#b8bdc0 17px,#72797d 20px);
  border-bottom:13px solid #4f5558;
  box-shadow:0 10px 18px rgba(0,0,0,.45);
  animation:closeFull 2.8s cubic-bezier(.18,.74,.25,1) forwards;
}
.shutter.lunch{animation:closeLunch 2.6s cubic-bezier(.18,.74,.25,1) forwards}
@keyframes closeFull{0%{top:-100%}72%{top:2%}84%{top:-1%}100%{top:0}}
@keyframes closeLunch{0%{top:-100%}72%{top:-31%}84%{top:-36%}100%{top:-34%}}
.shutter.vibrate{animation-duration:2.8s}
.shutter-sign{
  position:absolute;left:9%;right:9%;top:40%;text-align:center;
  color:#8b1111;font-family:Impact,"Arial Black",Arial,sans-serif;
  font-size:34px;letter-spacing:1.5px;text-shadow:0 1px #ddd;
}
.shutter-sign small{
  display:block;margin:17px auto 0;max-width:330px;
  font:18px "Comic Sans MS",cursive;color:#222;letter-spacing:0;
  background:#f6efce;border:1px solid #928363;padding:10px 12px;
  transform:rotate(-1.4deg);box-shadow:2px 2px 0 rgba(0,0,0,.17);
}
.downarrows{font-size:38px;color:#a71919;opacity:.55;margin-bottom:6px}
.floor-gap{
  position:absolute;left:0;right:0;bottom:0;height:35px;
  background:linear-gradient(#604a32,#2d241b);
  display:none;
}
.shutter.lunch .floor-gap{display:block}

/* closing warning, shown only if shop changes state while visitor is inside */
.closing-warning{
  position:fixed;left:50%;top:28px;transform:translateX(-50%);
  z-index:9999;background:#fff7be;border:2px solid #9b7a17;
  padding:8px 14px;font:11px Verdana;display:none;
  box-shadow:3px 3px 0 rgba(0,0,0,.25);
}
.closing-warning.show{display:block}

@media(max-width:990px){
  .browser,.testbar,.skyline{width:96%}
  .sidebar.right{display:none}
  .sidebar{width:150px}
  .card{width:44%}
  .sticky{display:none}
}
</style>
</head>
<body>

<div id="closingWarning" class="closing-warning">Stiamo per chiudere.</div>

<div class="skyline">
  Fuori: <span id="outsideLabel">giorno</span> &nbsp; | &nbsp;
  Ora della bottega: <span id="liveClock"><?php echo $now->format('H:i:s'); ?></span>
</div>

<div class="browser" id="browser">
  <div class="winbar">
    Ferramenta Valenti - Tutto per la casa - Microsoft Internet Explorer
    <span class="controls">_ □ ×</span>
  </div>
  <div class="menu">File &nbsp; Modifica &nbsp; Visualizza &nbsp; Preferiti &nbsp; Strumenti &nbsp; ?</div>
  <div class="toolbar">◀ Indietro &nbsp; ▶ Avanti &nbsp; ⟳ Aggiorna &nbsp; 🏠 Home &nbsp; 🔎 Cerca &nbsp; ★ Preferiti</div>
  <div class="address">Indirizzo: <span>http://www.ferramentavalenti.it/</span> &nbsp; Vai</div>

  <div class="page">
    <div class="header">
      <div class="brand">🔨 FERRAMENTA VALENTI</div>
      <div class="sub">dal 1970 tutto per la casa</div>
      <div class="cat">FERRAMENTA • UTENSILERIA • CASALINGHI • ELETTRICITÀ • GIARDINAGGIO • COLORI</div>
      <div class="sticky">Piccoli lavori,<br><b>grandi soddisfazioni!</b></div>
    </div>

    <div class="layout">
      <div class="sidebar">
        <a class="oldbtn" href="#">🏠 Home</a>
        <a class="oldbtn" href="#">🧰 Prodotti</a>
        <a class="oldbtn" href="#">⚙️ Elettroutensili</a>
        <a class="oldbtn" href="#">🌱 Giardinaggio</a>
        <a class="oldbtn" href="#">🎨 Vernici</a>
        <a class="oldbtn" href="#">% Offerte</a>
        <a class="oldbtn" href="#">🗺 Dove siamo</a>
        <a class="oldbtn" href="#">✉ Contatti</a>

        <div class="<?php echo $isOpen ? 'status-open' : 'status-closed'; ?>">
          <?php echo $isOpen ? 'SIAMO APERTI' : 'SIAMO CHIUSI'; ?>
        </div>

        <div class="hours">
          <b>ORARI</b><br>
          Lun–Sab<br>
          08:00–12:30<br>
          15:00–19:00<br><br>
          Domenica: <b>CHIUSO</b>
        </div>
      </div>

      <div class="content" id="shop">
        <div class="marquee">
          <marquee>*** BENVENUTI NEL NOSTRO NUOVO SITO INTERNET!!! *** SPEDIZIONI IN TUTTA ITALIA ***</marquee>
        </div>

        <div class="welcome">Benvenuti alla Ferramenta Valenti!</div>
        <div class="intro">
          Dal 1970 siamo al vostro servizio con utensili, ferramenta, colori e piccoli articoli per la casa.
          Da oggi potete vedere alcuni dei nostri prodotti anche su Internet.
        </div>

        <div class="hero">
          <div class="hero-icon">🔧</div>
          <div class="hero-copy">
            <b>Da oggi siamo anche su Internet!</b><br>
            Il sito è stato realizzato in famiglia. Se qualcosa non funziona, telefonateci durante gli orari di apertura.
          </div>
        </div>

        <div class="products">
          <?php
          foreach ($products as $p) {
              echo '<div class="card">';
              if ($p[3] !== '') echo '<div class="badge">'.$p[3].'</div>';
              echo '<span class="ico">'.$p[2].'</span>';
              echo '<b>'.htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8').'</b>';
              echo '<div class="price">'.htmlspecialchars($p[1], ENT_QUOTES, 'UTF-8').'</div>';
              if ($isOpen) {
                  echo '<button class="buy" onclick="alert(\'Prodotto aggiunto al carrello dimostrativo.\')">Aggiungi al carrello</button>';
              } else {
                  echo '<button class="buy" disabled>LA CASSA È CHIUSA</button>';
              }
              echo '</div>';
          }
          ?>
        </div>

        <div class="tip">
          Per ordini urgenti vi preghiamo di ripassare durante gli orari di apertura.
        </div>

        <?php if (!$isOpen) { ?>
        <div class="shutter-wrap" id="shutterWrap">
          <div class="shutter <?php echo ($state === 'lunch') ? 'lunch' : ''; ?>" id="shutter">
            <div class="shutter-sign">
              <div class="downarrows">↓ &nbsp;&nbsp;&nbsp; ↓</div>
              <?php echo htmlspecialchars($headline, ENT_QUOTES, 'UTF-8'); ?>
              <small><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div class="floor-gap"></div>
          </div>
        </div>
        <?php } ?>
      </div>

      <div class="sidebar right">
        <div class="section-title">Qualità.<br>Consigli.<br>Esperienza.</div>
        <div style="font-size:31px;margin:16px 0">🔧 &nbsp; 🔩 &nbsp; 📏</div>

        <div class="hours">
          <b>STATO ATTUALE</b><br><br>
          <?php echo htmlspecialchars($headline, ENT_QUOTES, 'UTF-8'); ?><br>
          <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
          <div class="clockbox"><?php echo $now->format('d/m/Y H:i'); ?></div>
        </div>

        <div style="font-size:9px;margin-top:12px;line-height:1.5">
          Sito realizzato da Matteo,<br>figlio del titolare.<br><br>
          Ultimo aggiornamento:<br><?php echo $now->format('d/m/Y'); ?>
        </div>
      </div>
    </div>

    <div class="footer">
      <div><span class="counter">005617</span> visitatori</div>
      <div><a href="#">Offerte</a> | <a href="#">Mappa</a> | <a href="#">Guestbook</a> | <a href="#">Scrivici</a></div>
      <div>🇮🇹 Best viewed at 1024×768</div>
    </div>
  </div>
</div>

<script>
(function(){
  /* Day/night cycle: the page follows a seasonal approximation of daylight
     in Italy. No external API: time itself becomes part of the work. */
  function seasonalTimes(d){
    var start = new Date(d.getFullYear(),0,0);
    var diff = d - start;
    var doy = Math.floor(diff/86400000);
    var seasonal = Math.sin((2*Math.PI*(doy-80))/365);
    return {
      sunrise: 7.0 - 1.45*seasonal,
      sunset: 18.3 + 2.25*seasonal
    };
  }

  function daylight(){
    var d = new Date();
    var t = d.getHours()+d.getMinutes()/60+d.getSeconds()/3600;
    var s = seasonalTimes(d), light, warmth=0, label='giorno';

    if(t < s.sunrise-1){ light=.58; label='notte'; }
    else if(t < s.sunrise){
      var x=(t-(s.sunrise-1));
      light=.58+x*.42; warmth=.45; label='alba';
    } else if(t < s.sunset-1){
      light=1; label='giorno';
      if(t > s.sunset-2.4) warmth=.25;
    } else if(t < s.sunset+.6){
      var y=(t-(s.sunset-1))/1.6;
      light=1-y*.40; warmth=.78; label='tramonto';
    } else {
      light=.58; label='notte';
    }

    document.documentElement.style.setProperty('--daylight', light.toFixed(2));
    document.documentElement.style.setProperty('--warmth', warmth.toFixed(2));
    var ol=document.getElementById('outsideLabel');
    if(ol) ol.innerHTML=label;
  }

  function clock(){
    var d=new Date();
    var p=function(n){return (n<10?'0':'')+n};
    var el=document.getElementById('liveClock');
    if(el) el.innerHTML=p(d.getHours())+':'+p(d.getMinutes())+':'+p(d.getSeconds());
  }

  daylight(); clock();
  setInterval(daylight,30000);
  setInterval(clock,1000);

  <?php if ($test === '') { ?>
  var seconds=<?php echo (int)$seconds_to_transition; ?>;
  if(seconds>7){
    setTimeout(function(){
      var w=document.getElementById('closingWarning');
      if(w) w.className+=' show';
    }, Math.max(0,(seconds-5)*1000));
  }
  setTimeout(function(){
    window.location.href=window.location.pathname;
  }, seconds*1000+700);
  <?php } ?>
})();
</script>

<!--
  TORNO SUBITO

  Il sito non è rotto.
  Il negozio è chiuso.

  Se Internet può restare aperto per sempre,
  perché abbiamo deciso che debba farlo?

  Concept: EmaF205
-->
</body>
</html>
