<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= esc($title ?? 'PPDB 2026 | Penerimaan Santri Baru Rumah Gemilang Indonesia'); ?></title>
<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png'); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png'); ?>">
<link rel="shortcut icon" href="<?= base_url('favicon.ico'); ?>">
<link rel="apple-touch-icon" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          blue: '#007DC9',
          blue2: '#0064A1',
          deep: '#00253D',
          sky: '#4FB0FF',
          pencil: '#FFD059',
          paper: '#FCFDFE',
          paper2: '#D3E3F1',
          ink: '#0C1F31',
          line: '#CAD8E5',
          muted: '#566B7E',
          err: '#B3261E'
        },
        fontFamily: {
          sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
          disp: ['"Bricolage Grotesque"', 'system-ui', 'sans-serif'],
          mono: ['"DM Mono"', 'ui-monospace', 'monospace']
        }
      }
    }
  };
</script>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style type="text/tailwindcss">
:root{
  --deep:#00253D;
  --blue:#007DC9;
  --blue2:#0064A1;
  --pencil:#FFD059;
  --paper:#FCFDFE;
  --paper2:#D3E3F1;
  --ink:#0C1F31;
  --muted:#566B7E;
  --rule:#CAD8E5;
  --err:#B3261E;
  --mx:0;
  --my:0;
  --sy:0;
  --disp:"Bricolage Grotesque",system-ui,sans-serif;
  --mono:"DM Mono",ui-monospace,monospace;
}
*{box-sizing:border-box}[hidden]{display:none!important}
html{scroll-behavior:smooth;scroll-padding-top:96px}
body{
  margin:0;
  min-height:100vh;
  background:linear-gradient(#0079C4,#0064A1);
  color:var(--ink);
  font:16px/1.6 "DM Sans",system-ui,sans-serif;
  -webkit-font-smoothing:antialiased;
  padding:env(safe-area-inset-top) 0 env(safe-area-inset-bottom);
}
#bg{position:fixed;inset:0;width:100%;height:100%;z-index:0;pointer-events:none}
.prop{position:fixed;z-index:1;pointer-events:none;opacity:0;transition:opacity .6s;transform:translate3d(calc(var(--mx)*var(--d)*1px),calc(var(--my)*var(--d)*1px - var(--sy)*var(--s)*1px),0) rotate(var(--r));will-change:transform}
.wrap{position:relative;z-index:2;max-width:900px;margin:0 auto;padding:24px 20px 80px}

/* =========================================================
   DYNAMIC ISLAND FIXED NAVBAR (ADAPTED FROM LANDING PAGE)
   ========================================================= */
#mainNavbarWrapper{
  position:fixed;
  top:0;
  left:0;
  right:0;
  z-index:50;
  padding-top:0px;
  transition:padding-top .35s cubic-bezier(0.16,1,0.3,1);
  pointer-events:none;
}
#mainNavbarWrapper.is-scrolled{
  padding-top:14px;
}
#navbarIsland{
  pointer-events:auto;
  width:100%;
  max-width:1180px;
  height:80px;
  margin-left:auto;
  margin-right:auto;
  padding-left:0;
  padding-right:0;
  border-radius:0;
  background:transparent;
  border:1px solid transparent;
  display:flex;
  justify-content:space-between;
  align-items:center;
  transition:all .35s cubic-bezier(0.16,1,0.3,1);
}
#mainNavbarWrapper.is-scrolled #navbarIsland{
  max-width:1150px;
  height:60px;
  padding-left:20px;
  padding-right:12px;
  border-radius:9999px;
  background:rgba(1,63,113,.35);
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  border:1px solid rgba(255,255,255,.2);
  box-shadow:0 18px 48px -8px rgba(4,36,63,.7),0 0 0 1px rgba(255,255,255,.1) inset;
}
.nav-brand{
  display:flex;
  align-items:center;
  gap:10px;
  color:#fff;
  text-decoration:none;
  font:700 20px/1.1 var(--disp);
  flex-shrink:0;
}
.nav-brand-badge{
  width:40px;
  height:40px;
  border-radius:50%;
  background:#fff;
  color:var(--blue);
  display:grid;
  place-items:center;
  font-size:17px;
  transition:transform .3s;
  box-shadow:0 2px 6px rgba(0,0,0,.15);
  flex-shrink:0;
}
#mainNavbarWrapper.is-scrolled .nav-brand-badge{
  width:34px;
  height:34px;
  font-size:15px;
}
.nav-brand:hover .nav-brand-badge{
  transform:rotate(-12deg) scale(1.05);
}
.nav-links{
  display:none;
  align-items:center;
  gap:16px;
  font-size:14px;
  font-weight:500;
  color:#fff;
}
@media(min-width:960px){.nav-links{display:flex}}
.nav-links a{
  color:rgba(255,255,255,.9);
  text-decoration:none;
  padding:8px 0;
  border-bottom:2px solid transparent;
  transition:all .2s;
}
.nav-links a:hover,.nav-links a.on{
  color:var(--pencil);
  border-color:var(--pencil);
}
.nav-acts{
  display:flex;
  align-items:center;
  gap:10px;
  flex-shrink:0;
}
.nav-btn-login{
  display:none;
  align-items:center;
  gap:6px;
  padding:7px 16px;
  border-radius:9999px;
  background:rgba(255,255,255,.12);
  color:#fff;
  text-decoration:none;
  font-size:13px;
  font-weight:600;
  border:1px solid rgba(255,255,255,.25);
  transition:all .2s;
}
@media(min-width:640px){.nav-btn-login{display:inline-flex}}
.nav-btn-login:hover{
  background:rgba(255,255,255,.25);
  color:#fff;
}
.nav-btn-cta{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:9px 18px;
  border-radius:9999px;
  background:var(--pencil);
  color:var(--deep);
  text-decoration:none;
  font-family:var(--disp);
  font-weight:800;
  font-size:13.5px;
  box-shadow:0 6px 16px rgba(0,0,0,.2);
  transition:all .2s;
  flex-shrink:0;
}
.nav-btn-cta:hover{
  background:#FFDC80;
  transform:translateY(-1px);
}
.nav-mobile-btn{
  display:flex;
  align-items:center;
  justify-content:center;
  width:38px;
  height:38px;
  border-radius:50%;
  background:rgba(255,255,255,.15);
  border:1px solid rgba(255,255,255,.25);
  color:#fff;
  cursor:pointer;
  font-size:16px;
  transition:background .2s;
}
@media(min-width:960px){.nav-mobile-btn{display:none}}
.nav-mobile-btn:hover{background:rgba(255,255,255,.25)}

/* Mobile Island Dropdown Menu */
#mobileIslandMenu{
  pointer-events:auto;
  max-width:92%;
  margin:10px auto 0;
  border-radius:18px;
  background:rgba(4,36,63,.98);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  border:1px solid rgba(255,255,255,.2);
  padding:14px;
  box-shadow:0 24px 50px rgba(0,0,0,.5);
  color:#fff;
  display:none;
}
#mobileIslandMenu.open{
  display:block;
  animation:fadeDown .2s cubic-bezier(0.16,1,0.3,1);
}
@keyframes fadeDown{
  from{opacity:0;transform:translateY(-8px)}
  to{opacity:1;transform:none}
}
#mobileIslandMenu a{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 12px;
  border-radius:10px;
  color:#fff;
  text-decoration:none;
  font-size:13.5px;
  font-weight:600;
  transition:background .15s;
}
#mobileIslandMenu a:hover{
  background:rgba(255,255,255,.1);
}
#mobileIslandMenu .mobile-actions{
  display:flex;
  gap:8px;
  padding-top:10px;
  margin-top:8px;
  border-top:1px solid rgba(255,255,255,.12);
}
#mobileIslandMenu .mobile-actions a{
  flex:1;
  text-align:center;
  justify-content:center;
  padding:10px;
}

/* =========================================================
   NOTE, TABS, SHEET AND FORM WIZARD
   ========================================================= */
.note{
  background:var(--pencil);
  border:0;
  color:var(--deep);
  padding:18px 18px 14px;
  margin:0 0 22px;
  box-shadow:0 14px 22px -10px rgba(0,37,61,.55);
  position:relative;
  transform:translate(var(--tx,0),var(--ty,0)) rotate(var(--rot,0deg));
  transition:transform .3s cubic-bezier(.3,1.4,.5,1);
  border-radius:4px;
}
.note::before{
  content:"";
  position:absolute;
  top:-11px;
  left:50%;
  width:74px;
  height:22px;
  margin-left:-37px;
  background:rgba(255,255,255,.45);
  transform:rotate(-3deg);
}
.note h2{margin:0 0 8px;font:800 16px/1.2 var(--disp)}
.note ul{margin:0;padding:0;list-style:none;font-size:14px;line-height:1.35;display:grid;gap:6px;grid-template-columns:1fr 1fr}
.note li{padding-left:16px;position:relative}
.note li::before{content:"";position:absolute;left:0;top:.5em;width:7px;height:7px;background:var(--deep);transform:rotate(45deg)}
@media(min-width:1280px){
  .note{
    position:absolute;
    top:96px;
    right:-175px;
    width:210px;
    --rot:3.5deg;
    cursor:grab;
    touch-action:none;
    user-select:none;
    margin:0;
  }
  .note ul{grid-template-columns:1fr}
  .note:hover,.note.lift{--rot:0deg;scale:1.03}
  .note.lift{transition:none;cursor:grabbing}
}

.tabs{display:flex;gap:4px;padding-left:28px;position:relative;z-index:3;margin-bottom:-1px}
.tab{
  appearance:none;
  border:0;
  cursor:pointer;
  font:500 12.5px var(--mono);
  letter-spacing:.06em;
  text-transform:uppercase;
  color:#3F6584;
  background:var(--paper2);
  padding:11px 16px 9px;
  min-height:44px;
  border-radius:10px 10px 0 0;
  transform:translateY(7px);
  transition:transform .3s cubic-bezier(.3,1.5,.5,1),background .2s;
  display:flex;
  gap:8px;
  align-items:center;
}
.tab b{
  font-weight:600;
  display:grid;
  place-items:center;
  width:20px;
  height:20px;
  border-radius:50%;
  border:1.5px solid currentColor;
  font-size:11px;
}
.tab:hover{transform:translateY(3px)}
.tab[aria-selected=true]{
  background:var(--paper);
  color:var(--deep);
  transform:none;
  padding-top:14px;
}
.tab.done b{background:var(--blue);border-color:var(--blue);color:#fff}

.sheetwrap{filter:drop-shadow(0 2px 2px rgba(0,37,61,.2)) drop-shadow(0 26px 40px rgba(0,37,61,.4))}
.sheet{
  position:relative;
  display:grid;
  grid-template-columns:96px 1fr;
  min-height:600px;
  background:var(--paper) repeating-linear-gradient(transparent 0 31px,rgba(0,122,206,.07) 31px 32px);
  clip-path:polygon(0 0,calc(100% - 46px) 0,100% 46px,100% 100%,0 100%);
  border-radius:4px 0 4px 4px;
}
.sheet::after{
  content:"";
  position:absolute;
  top:0;
  right:0;
  width:46px;
  height:46px;
  background:linear-gradient(225deg,transparent 50%,#BFD3E6 50%);
}
.rail{
  position:relative;
  border-right:1.5px solid rgba(4,36,63,.16);
  background:radial-gradient(circle at 22px 50%,var(--deep) 0 7px,rgba(0,0,0,.3) 8px,transparent 9px) 0 0/96px 130px repeat-y;
}
.ruler{
  position:absolute;
  right:10px;
  top:30px;
  bottom:30px;
  width:26px;
  background:repeating-linear-gradient(180deg,var(--ink) 0 1.5px,transparent 0 60px) right/20px 100% no-repeat,repeating-linear-gradient(180deg,var(--ink) 0 1px,transparent 0 12px) right/10px 100% no-repeat;
}
.ruler i{
  position:absolute;
  left:0;
  top:0;
  bottom:0;
  width:5px;
  background:var(--pencil);
  outline:1px solid var(--deep);
  transform-origin:top;
  transform:scaleY(var(--p,0));
  transition:transform .6s cubic-bezier(.3,.9,.3,1);
}
.pct{
  position:absolute;
  left:42px;
  top:30px;
  writing-mode:vertical-rl;
  font:500 12px var(--mono);
  letter-spacing:.1em;
  text-transform:uppercase;
  color:var(--muted);
}
.pct b{color:var(--deep);font-weight:700}
.body{position:relative;padding:30px 38px 28px 34px;perspective:1400px}
.head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:10px;padding-right:20px}
h1{margin:0;font:800 clamp(30px,5vw,46px)/1.02 var(--disp);letter-spacing:-.02em;color:var(--deep)}
h1 mark{background:linear-gradient(transparent 56%,rgba(255,208,89,.6) 56% 92%,transparent 92%);color:inherit;padding:0 4px;margin:0 -4px}
.sub{margin:8px 0 0;font:400 12.5px var(--mono);letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}
.stamp{
  flex:none;
  width:88px;
  height:88px;
  border:2px solid var(--blue);
  border-radius:50%;
  display:grid;
  place-items:center;
  text-align:center;
  font:500 10.5px/1.35 var(--mono);
  letter-spacing:.12em;
  color:var(--blue);
  transform:rotate(-14deg);
  box-shadow:inset 0 0 0 4px var(--paper),inset 0 0 0 5px var(--blue);
}

.pane{display:none;transform-origin:left center}
.pane.on{display:block;animation:turn .55s cubic-bezier(.2,.8,.2,1)}
@keyframes turn{from{opacity:0;transform:rotateY(calc(var(--dir,1)*-9deg)) translateX(calc(var(--dir,1)*28px))}}
.pane:focus{outline:0}
.sec{margin:20px 0 4px;display:flex;align-items:center;gap:12px;font:800 21px var(--disp);color:var(--deep)}
.sec::after{content:"";flex:1;border-top:1.5px dashed var(--rule)}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:4px 30px}
.span{grid-column:1/-1}
.fld,.grp{display:block;padding:10px 0 0;position:relative}
.fld>span,.lab{display:block;font:500 11.5px var(--mono);letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
[data-r]>span::after,[data-r]>.lab::after{content:" *";color:var(--err)}
.fld input,.fld textarea{
  display:block;
  width:100%;
  border:0;
  border-bottom:2px solid var(--rule);
  background:transparent;
  font:500 17px/1.4 "DM Sans",sans-serif;
  color:var(--ink);
  padding:8px 0;
  min-height:44px;
  border-radius:0;
  resize:vertical;
  transition:border-color .2s,background-color .2s;
}
.fld input::placeholder,.fld textarea::placeholder{color:#97a3ad}
.fld input:focus,.fld textarea:focus{outline:0;border-color:var(--blue);background:rgba(0,122,206,.07)}
.err{display:block;min-height:19px;font:500 12px var(--mono);color:var(--err);padding-top:3px}
.bad input,.bad textarea{border-color:var(--err)}
.shake{animation:shake .4s}
@keyframes shake{25%{transform:translateX(-6px)}60%{transform:translateX(5px)}}
.opts{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 2px}
.opt{position:relative}
.opt input{position:absolute;inset:0;width:100%;height:100%;opacity:0;margin:0;cursor:pointer}
.opt span{
  display:flex;
  align-items:center;
  min-height:44px;
  padding:0 16px;
  border:2px solid var(--rule);
  border-radius:999px;
  font:600 15px "DM Sans";
  background:rgba(255,255,255,.6);
  transition:transform .25s cubic-bezier(.3,1.5,.5,1),background .2s,border-color .2s,color .2s;
}
.sq .opt span{border-radius:8px}
.opt:hover span{border-color:var(--blue)}
.opt input:checked+span{background:var(--deep);color:#fff;border-color:var(--deep);transform:rotate(-1.4deg)}
.opt input:checked+span::before{content:"";width:8px;height:8px;border-radius:50%;background:var(--pencil);margin-right:9px}
.opt input:focus-visible+span{outline:3px solid var(--blue);outline-offset:2px}

.docs{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:10px}
.doc{
  position:relative;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:3px;
  min-height:122px;
  padding:14px 8px;
  border:2px dashed var(--rule);
  border-radius:8px;
  background:rgba(255,255,255,.55);
  text-align:center;
  cursor:pointer;
  transition:transform .3s cubic-bezier(.3,1.5,.5,1),border-color .2s,background .2s;
}
.doc:hover,.doc.drag{border-color:var(--blue);background:#fff;transform:translateY(-4px) rotate(-.7deg)}
.doc input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}
.doc svg{width:22px;height:22px;fill:none;stroke:var(--blue);stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.doc b{font:700 15px var(--disp);color:var(--deep)}
.doc small{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font:400 11.5px var(--mono);color:var(--muted)}
.doc.bad{border-color:var(--err)}.doc.bad small{color:var(--err)}
.doc.has{border:2px solid var(--deep);background:#fff;animation:clip .55s cubic-bezier(.3,1.6,.5,1)}
.doc.has svg{stroke:var(--deep);transform:rotate(-20deg)}
@keyframes clip{from{transform:translateY(-16px) rotate(4deg)}}
.doc .x{
  position:absolute;
  top:2px;
  right:2px;
  z-index:2;
  display:none;
  width:40px;
  height:40px;
  border:0;
  background:none;
  font:400 22px/1 "DM Sans";
  color:var(--muted);
  cursor:pointer;
}
.doc.has .x{display:block}.doc .x:hover{color:var(--err)}

.sum{display:grid;grid-template-columns:1fr 1fr;gap:0 30px;margin:6px 0 4px}
.sum div{padding:9px 0;border-bottom:1px dotted var(--rule);min-width:0}
.sum .w{grid-column:1/-1}
.sum dt{font:500 11px var(--mono);letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
.sum dd{margin:0;font-weight:700;color:var(--deep);overflow-wrap:anywhere}
.agree{display:flex;gap:12px;align-items:flex-start;margin:16px 0 0;cursor:pointer;font-size:15px}
.agree input{width:24px;height:24px;margin:0;accent-color:var(--deep);flex:none}
.nav{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:24px;padding-top:18px;border-top:2px dashed var(--rule)}
.btn{
  min-height:48px;
  padding:0 24px;
  border:2px solid var(--deep);
  background:transparent;
  color:var(--deep);
  font:700 15px/1 var(--disp);
  border-radius:10px;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  transition:all .2s;
}
.btn:hover{background:var(--deep);color:#fff}
.btn.go{background:var(--deep);color:#fff}
.btn.go:hover{background:#1A4170}
#send{background:var(--pencil);border-color:var(--pencil);color:var(--deep);font-weight:800}
#send:hover{background:#FFDC80}
.recv{
  position:absolute;
  left:50%;
  top:44%;
  z-index:5;
  pointer-events:none;
  border:6px solid var(--blue);
  color:var(--blue);
  padding:6px 26px;
  border-radius:12px;
  font:800 clamp(34px,8vw,58px)/1 var(--disp);
  letter-spacing:.06em;
  background:rgba(251,247,236,.92);
  transform:translate(-50%,-50%) rotate(-12deg);
  animation:slam .5s cubic-bezier(.2,1.6,.4,1) both;
}
@keyframes slam{from{opacity:0;transform:translate(-50%,-50%) rotate(-12deg) scale(2.6)}}

@media(max-width:720px){
  .sheet{grid-template-columns:1fr;min-height:0}
  .rail{height:34px;border-right:0;border-bottom:1.5px solid rgba(4,36,63,.16);background:none}
  .ruler{inset:auto 14px 8px 14px;width:auto;height:6px;background:rgba(4,36,63,.12)}
  .ruler i{width:auto;height:6px;right:0;transform:scaleX(var(--p,0));transform-origin:left;outline:0}
  .pct{writing-mode:horizontal-tb;left:14px;top:4px;font-size:11px}
  .body{padding:22px 18px}
  .grid,.sum{grid-template-columns:1fr}
  .docs{grid-template-columns:1fr 1fr}
  .stamp{width:64px;height:64px;font-size:8.5px}
  .tabs{padding-left:12px}
  .tab{padding:11px 12px 9px}
  .tab .t{display:none}
  .tab[aria-selected=true] .t{display:inline}
  .note ul{grid-template-columns:1fr}
  .prop{display:none}
}

.intro2{margin:10px 0 0;font-size:14.5px;color:#415366;max-width:52ch}
.priv{margin:14px 0 0;font-size:13px;color:var(--muted)}
body.inform .prop{opacity:.85}

/* =========================================================
   SECTIONS: HERO, KEUNGGULAN, ALUR, JURUSAN, SYARAT, FAQ
   ========================================================= */
main,footer{position:relative;z-index:2}
.c{max-width:1180px;margin:0 auto;padding:0 24px}
.b{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  min-height:44px;
  padding:0 18px;
  border:1.5px solid var(--deep);
  border-radius:12px;
  font:600 14.5px "DM Sans",sans-serif;
  color:var(--deep);
  text-decoration:none;
  transition:background .2s,color .2s,transform .2s;
}
.b:hover{transform:translateY(-1px)}
.b.p{background:var(--deep);color:#fff}
.b.p:hover{background:#1A4170}
.b.g:hover{background:var(--deep);color:#fff}
.b.lg{min-height:50px;padding:0 24px}
.kick{
  margin:0;
  font:500 12.5px var(--mono);
  letter-spacing:.1em;
  text-transform:uppercase;
  color:var(--pencil);
  display:flex;
  gap:10px;
  align-items:center;
}
.kick::before{content:"";width:26px;border-top:2px solid var(--pencil)}
.hero{padding:24px 0 72px;color:#fff}
.hero .c{display:grid;gap:40px;align-items:center}
@media(min-width:960px){.hero .c{grid-template-columns:7fr 5fr}}
.hh{
  margin:14px 0 18px;
  font:800 clamp(38px,5.6vw,72px)/1.04 var(--disp);
  letter-spacing:-.03em;
  color:#fff;
}
.hh mark{
  color:var(--deep);
  background:linear-gradient(transparent 70%,var(--pencil) 70% 95%,transparent 95%);
  padding:0 .1em;
  box-decoration-break:clone;
  -webkit-box-decoration-break:clone;
}
.lead{margin:0;font-size:18px;color:rgba(255,255,255,.92);max-width:56ch}
.acts{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px}
.hero .b.g{color:#fff;border-color:rgba(255,255,255,.35);background:rgba(255,255,255,.12)}
.hero .b.g:hover{background:rgba(255,255,255,.24);color:#fff}
.hero .b.p{background:var(--pencil);border-color:var(--pencil);color:var(--deep);font-weight:700}
.hero .b.p:hover{background:#FFDC80}
.feat{
  list-style:none;
  margin:30px 0 0;
  padding:20px 0 0;
  border-top:1px solid rgba(255,255,255,.22);
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:12px;
  font-size:14.5px;
  font-weight:500;
  color:#fff;
}
@media(min-width:640px){.feat{grid-template-columns:repeat(4,1fr)}}
.feat i{color:var(--pencil);margin-right:8px}

/* =========================================================
   HERO CARD (.info) - HIGH CONTRAST & VISIBLE TEXT FIX
   ========================================================= */
.info{
  position:relative;
  border:0;
  border-radius:26px;
  padding:0 0 24px;
  overflow:hidden;
  background:#fff !important;
  color:var(--ink) !important;
  transform:perspective(900px) rotateY(var(--ry,-4deg)) rotateX(var(--rx,2deg)) rotate(-2deg);
  transition:transform .2s ease-out;
  box-shadow:0 34px 50px -26px rgba(0,37,61,.75);
}
.info:hover{transform:perspective(900px) rotateY(var(--ry,0deg)) rotateX(var(--rx,0deg)) rotate(-1deg)}
.info::before{
  content:"";
  position:absolute;
  top:10px;
  left:50%;
  width:56px;
  height:8px;
  margin-left:-28px;
  border-radius:99px;
  background:rgba(255,255,255,.9);
  z-index:2;
}
.info .kick{
  background:var(--deep);
  color:#a9c8df;
  margin:0;
  padding:30px 24px 0;
}
.info .kick::before{display:none}
.info h3{
  background:var(--deep);
  color:#fff;
  margin:0;
  padding:6px 24px 22px;
  font:700 24px/1.2 var(--disp);
  display:flex;
  flex-wrap:wrap;
  gap:8px 14px;
  align-items:center;
}
.info .live{
  font:600 11px var(--mono);
  letter-spacing:.08em;
  text-transform:uppercase;
  color:var(--pencil);
  border:1.5px solid var(--pencil);
  border-radius:99px;
  padding:2px 10px;
}
.info dl,.info .acts{margin-left:24px;margin-right:24px}
.info dl{margin-top:8px}
.info dl div{padding:12px 0;border-bottom:1px dotted var(--rule)}
.info dt{
  font:600 11.5px var(--mono) !important;
  letter-spacing:.06em;
  text-transform:uppercase;
  color:var(--muted) !important;
}
.info dt i{color:var(--blue) !important;margin-right:6px}
.info dd{
  margin:4px 0 0 !important;
  font-weight:700 !important;
  font-size:15.5px !important;
  color:var(--deep) !important;
  line-height:1.35;
}
.info .acts{margin-top:20px}
.info .acts .b{flex:1}
.info .acts .b.g{
  color:var(--deep) !important;
  border-color:var(--deep) !important;
  background:rgba(0,37,61,.06) !important;
  font-weight:700 !important;
}
.info .acts .b.g:hover{background:var(--deep) !important;color:#fff !important}
.info .acts .b.p{
  background:var(--pencil) !important;
  border-color:var(--pencil) !important;
  color:var(--deep) !important;
  font-weight:800 !important;
}
.info .acts .b.p:hover{background:#FFDC80 !important}

/* =========================================================
   CONTENT SECTIONS STYLING
   ========================================================= */
.s{padding:76px 0;background:#fff}
.s.alt{background:#F2F7FB;border-block:1px solid #DDE6EE}
.s h2{
  margin:12px 0 10px;
  font:800 clamp(28px,3.6vw,42px)/1.15 var(--disp);
  color:var(--deep);
  letter-spacing:-.02em;
  max-width:24ch;
}
.s .intro{margin:0;max-width:62ch;color:var(--muted)}
.shead{display:flex;flex-wrap:wrap;gap:18px;justify-content:space-between;align-items:flex-end}
.grid4{display:grid;gap:20px;margin-top:36px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.grid2{display:grid;gap:16px;margin-top:26px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr))}
.cd{
  background:#fff;
  border:1px solid #DDE6EE;
  border-radius:16px;
  padding:22px;
  transition:transform .25s,border-color .25s,box-shadow .25s,opacity .6s;
}
.cd:hover{
  transform:translateY(-4px);
  border-color:var(--blue);
  box-shadow:0 14px 24px -16px rgba(15,42,71,.4);
}
.cd h3{margin:14px 0 6px;font:700 20px/1.25 var(--disp);color:var(--deep)}
.cd p{margin:0;font-size:14.5px;color:#415366}
.ico{
  width:44px;
  height:44px;
  display:grid;
  place-items:center;
  border-radius:10px;
  background:#E6F1FA;
  color:var(--blue);
  font-size:18px;
}
.flow{list-style:none;margin:36px 0 0;padding:0;display:grid;gap:22px;grid-template-columns:repeat(auto-fit,minmax(190px,1fr))}
.st{border-top:3px solid var(--rule);padding-top:16px;transition:border-color .3s,transform .3s,opacity .6s}
.st:hover{border-color:var(--pencil);transform:translateY(-3px)}
.st .n{display:block;font:800 44px/1 var(--disp);color:var(--blue)}
.st .w{font:600 11.5px var(--mono);letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
.st h4{margin:8px 0 6px;font:700 19px/1.25 var(--disp);color:var(--deep)}
.st p{margin:0 0 10px;font-size:14.5px;color:#415366}
.st em{font:600 12px var(--mono);font-style:normal;color:var(--blue)}
.st em i{margin-right:6px}

.jr .cd{display:flex;flex-direction:column}
.jr .cd p{flex:1}
.jm{display:flex;justify-content:space-between;gap:10px;align-items:center;font:600 12px var(--mono)}
.jm b{background:var(--deep);color:#fff;padding:3px 9px;border-radius:4px;font-weight:600;letter-spacing:.06em}
.jm span{color:var(--muted)}.jm i{margin-right:5px;color:var(--blue)}
.jr .cd h3{margin-top:12px}
.jr .cd>a{
  margin-top:16px;
  padding-top:12px;
  border-top:1px solid var(--rule);
  font-size:13.5px;
  font-weight:700;
  color:var(--blue);
  text-decoration:none;
  display:flex;
  justify-content:space-between;
  min-height:44px;
  align-items:center;
}
.jr .cd>a:hover{color:var(--deep)}
.jr .cd>a i{transition:transform .2s}
.jr .cd>a:hover i{transform:translateX(4px)}

.two{display:grid;gap:48px}
@media(min-width:1000px){.two{grid-template-columns:1fr 1.25fr}}
.ck{list-style:none;margin:22px 0 0;padding:0}
.ck li{display:flex;gap:14px;padding:13px 0;border-bottom:1px dotted var(--rule);font-weight:500}
.ck i{flex:none;margin-top:5px;color:var(--blue)}
.faq{max-width:820px}
.faq h2{max-width:none}
details{border-bottom:1px solid var(--rule)}
details:first-of-type{margin-top:28px;border-top:1px solid var(--rule)}
summary{
  list-style:none;
  cursor:pointer;
  display:flex;
  justify-content:space-between;
  gap:16px;
  align-items:center;
  min-height:56px;
  padding:12px 0;
  font:700 18px/1.3 var(--disp);
  color:var(--deep);
}
summary::-webkit-details-marker{display:none}
summary::after{content:"+";font:400 26px/1 "DM Sans";color:var(--blue);transition:transform .25s}
details[open] summary::after{transform:rotate(45deg)}
details p{margin:0 0 18px;max-width:68ch;color:#415366}
details a{color:var(--blue);font-weight:600}

#form-daftar{padding-top:84px;padding-bottom:96px}

footer{background:var(--deep);color:#c3d0dd;padding:48px 0 26px}
.fgrid{display:flex;flex-wrap:wrap;gap:24px;justify-content:space-between;align-items:center;padding-bottom:26px;border-bottom:1px solid rgba(255,255,255,.14)}
.fl{display:flex;flex-wrap:wrap;gap:6px 24px}
.fl a{color:#c3d0dd;text-decoration:none;min-height:44px;display:inline-flex;align-items:center;font-weight:500}
.fl a:hover{color:#fff;text-decoration:underline}
.fb{display:flex;flex-wrap:wrap;gap:8px 24px;justify-content:space-between;margin-top:20px;font-size:13px;color:#9fb0c1}

.js .rv{opacity:0;transform:translateY(18px)}
.js .rv.in{opacity:1;transform:none}
@media(prefers-reduced-motion:reduce){
  .js .rv{opacity:1;transform:none}
  html{scroll-behavior:auto}
  *,*::before{animation:none!important;transition:none!important}
  .prop{transform:none}
}
@media(max-width:640px){
  .hero{padding:20px 0 48px}
  .s{padding:56px 0}
  .c{padding:0 18px}
}
</style>
</head>
<body>

<!-- Canvas Latar Belakang Interaktif -->
<canvas id="bg" aria-hidden="true"></canvas>

<!-- Parallax Floating Geometry Props -->
<svg class="prop" aria-hidden="true" style="--d:-22;--s:.18;--r:-27deg;left:-120px;bottom:60px" width="470" height="44" viewBox="0 0 440 44"><rect x="60" width="300" height="44" fill="#FFD059"/><rect x="60" y="14" width="300" height="3" fill="#E3AE1F"/><rect x="60" y="27" width="300" height="3" fill="#E3AE1F"/><polygon points="360,0 420,22 360,44" fill="#F3DDB0"/><polygon points="404,16 440,22 404,28" fill="#0F3A5E"/><rect x="30" width="30" height="44" fill="#C9D8E6"/><rect width="34" height="44" rx="10" fill="#00253D"/></svg>
<svg class="prop" aria-hidden="true" style="--d:30;--s:.32;--r:12deg;right:-30px;top:60px" width="230" height="230" viewBox="0 0 220 220" fill="none"><path d="M10 10H210L10 210Z" fill="rgba(255,255,255,.07)" stroke="rgba(255,255,255,.45)" stroke-width="2"/><path d="M62 62H146L62 146Z" stroke="rgba(255,255,255,.3)" stroke-width="2"/><path d="M12 12H208" stroke="rgba(255,255,255,.5)" stroke-width="10" stroke-dasharray="1.5 10.5"/></svg>

<!-- =========================================================
     FIXED DYNAMIC ISLAND NAVBAR (PERSISTENT & FLOATING ON SCROLL)
     ========================================================= -->
<header id="mainNavbarWrapper">
  <div class="c">
    <div id="navbarIsland">
      <!-- Logo Brand -->
      <a class="nav-brand" href="<?= base_url('/'); ?>">
        <span class="nav-brand-badge" style="background:transparent;overflow:hidden;box-shadow:none;"><img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo Syiar Gemilang" style="width:100%;height:100%;object-fit:contain;border-radius:10px;"></span>
        <span>Syiar Gemilang</span>
      </a>

      <!-- Desktop Navigation Links -->
      <nav class="nav-links" aria-label="Navigasi PPDB">
        <a href="#keunggulan">Keunggulan</a>
        <a href="#alur">Alur Seleksi</a>
        <a href="#jurusan">Program Keahlian</a>
        <a href="#syarat">Persyaratan</a>
        <a href="#faq">FAQ</a>
        <a href="<?= base_url('ppdb/cek-status'); ?>">Cek Status</a>
      </nav>

      <!-- Action Buttons -->
      <div class="nav-acts hidden md:block">
        <a class="nav-btn-login" href="<?= base_url('login'); ?>">
          <i class="fa-solid fa-right-to-bracket text-xs"></i> Masuk
        </a>
        <!-- <a class="nav-btn-cta" href="#form-daftar">
          <i class="fa-solid fa-file-pen"></i>
          <span>Daftar Sekarang</span>
        </a> -->
        <button type="button" class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center border border-white/20 transition cursor-pointer md:hidden" onclick="toggleMobileIslandMenu()" aria-label="Buka Menu Navigasi">
          <i class="fa-solid fa-bars text-sm" id="mobileIslandIcon"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Island Menu -->
    <div id="mobileIslandMenu" class="hidden pointer-events-auto max-w-[92%] sm:max-w-md mx-auto mt-2 rounded-2xl bg-black/20 backdrop-blur-2xl border border-white/20 p-4 shadow-2xl text-white space-y-2 animate-in fade-in slide-in-from-top-2 duration-150">
      <a href="#keunggulan" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-star text-amber-400 w-5"></i> <span>Keunggulan Beasiswa</span>
      </a>
      <a href="#alur" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-route text-sky-400 w-5"></i> <span>Alur Seleksi Santri</span>
      </a>
      <a href="#jurusan" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-graduation-cap text-emerald-400 w-5"></i> <span>Program Keahlian</span>
      </a>
      <a href="#syarat" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-clipboard-check text-purple-400 w-5"></i> <span>Persyaratan Masuk</span>
      </a>
      <a href="#faq" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-circle-question text-teal-400 w-5"></i> <span>Tanya Jawab (FAQ)</span>
      </a>
      <a href="<?= base_url('ppdb/cek-status'); ?>" onclick="closeMobileIslandMenu()" class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold text-sky-200 rounded-xl hover:bg-white/10 transition">
        <i class="fa-solid fa-magnifying-glass text-blue-300 w-5"></i> <span>Cek Status Pendaftaran</span>
      </a>
      <div class="pt-2 border-t border-white/10 flex items-center gap-2">
        <a href="<?= base_url('login'); ?>" class="flex-1 py-2 text-center text-xs font-bold rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 transition">
          Portal Login
        </a>
        <a href="#form-daftar" onclick="closeMobileIslandMenu()" class="flex-1 py-2 text-center text-xs font-extrabold rounded-xl bg-pencil text-deep hover:bg-[#FFE066] shadow-sm transition">
          Daftar Sekarang
        </a>
      </div>
    </div>
  </div>
</header>

<main>
  <!-- Spacer Pengganti Navbar di Atas Hero -->
  <div style="height:84px" aria-hidden="true"></div>

  <!-- Hero Section -->
  <section class="hero">
    <div class="c">
      <div>
        <p class="kick">Penerimaan Santri Baru &middot; Angkatan 36 Tahun 2026</p>
        <h1 class="hh">Diklat Vokasi Unggulan <mark>100% Beasiswa</mark> &amp; Berasrama</h1>
        <p class="lead">Pendidikan vokasi kilat 6 bulan untuk pemuda dhuafa &amp; yatim berprestasi. Siap kerja, bersertifikasi BNSP, asrama &amp; konsumsi gratis, serta pembinaan akhlaq Qur’ani di Rumah Gemilang Indonesia.</p>
        <div class="acts">
          <a class="b p lg" href="#form-daftar"><i class="fa-solid fa-file-pen"></i> Daftar Santri Baru Sekarang</a>
          <a class="b g lg" href="#alur"><i class="fa-regular fa-compass"></i> Pelajari Alur Seleksi</a>
        </div>
        <ul class="feat">
          <li><i class="fa-solid fa-circle-check"></i> 100% Bebas Biaya</li>
          <li><i class="fa-solid fa-bed"></i> Fasilitas Asrama</li>
          <li><i class="fa-solid fa-certificate"></i> Sertifikasi BNSP</li>
          <li><i class="fa-solid fa-briefcase"></i> Penyaluran Kerja</li>
        </ul>
      </div>

      <!-- Hero Card: 3D Interactive Card with High Contrast Text -->
      <aside class="info" aria-label="Informasi gelombang">
        <p class="kick">Informasi Gelombang</p>
        <h3>PPDB RGI Angkatan 36 <span class="live"><i class="fa-solid fa-circle-dot"></i> Aktif Buka</span></h3>
        <dl>
          <div>
            <dt><i class="fa-solid fa-calendar-days"></i> Batas Pendaftaran Gelombang I</dt>
            <dd>30 November 2026</dd>
          </div>
          <div>
            <dt><i class="fa-solid fa-users"></i> Kuota Penerimaan</dt>
            <dd>200 Santri (Tersebar di 6 Kampus)</dd>
          </div>
          <div>
            <dt><i class="fa-solid fa-clock"></i> Masa Diklat Intensif</dt>
            <dd>6 Bulan Belajar + 2 Bulan Magang</dd>
          </div>
        </dl>
        <div class="acts">
          <a class="b g" href="<?= base_url('ppdb/cek-status'); ?>"><i class="fa-solid fa-magnifying-glass"></i> Cek Status</a>
          <a class="b p" href="https://wa.me/6281234567890?text=Halo%20Panitia%20PPDB%20Rumah%20Gemilang%20Indonesia" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> Panitia PPDB</a>
        </div>
      </aside>
    </div>
  </section>

  <!-- Section 1: Keunggulan -->
  <section id="keunggulan" class="s alt">
    <div class="c">
      <p class="kick" style="color:var(--blue)">Mengapa Rumah Gemilang Indonesia?</p>
      <h2>Pendidikan Vokasi Terpadu, Mengubah Mustahik Menjadi Muzakki</h2>
      <p class="intro">Program diklat pemberdayaan berbasis kejuruan dan adab islami untuk mencetak generasi mandiri dan profesional.</p>
      <div class="grid4">
        <article class="cd rv">
          <span class="ico"><i class="fa-solid fa-hand-holding-dollar"></i></span>
          <h3>Beasiswa 100% Penuh</h3>
          <p>Seluruh biaya pendidikan, asrama tempat tinggal, konsumsi 3x sehari, seragam, hingga modul belajar ditanggung sepenuhnya.</p>
        </article>
        <article class="cd rv">
          <span class="ico"><i class="fa-solid fa-certificate"></i></span>
          <h3>Sertifikasi BNSP</h3>
          <p>Lulusan dibekali sertifikat kompetensi profesi Badan Nasional Sertifikasi Profesi (BNSP) yang diakui dunia industri nasional.</p>
        </article>
        <article class="cd rv">
          <span class="ico"><i class="fa-solid fa-mosque"></i></span>
          <h3>Adab &amp; Karakter Qur’ani</h3>
          <p>Tidak hanya skill teknis, santri ditempa aqidah tauhid, fiqih ibadah, tahsin tahfidz Al-Qur’an, dan pembiasaan akhlaq karimah harian.</p>
        </article>
        <article class="cd rv">
          <span class="ico"><i class="fa-solid fa-briefcase"></i></span>
          <h3>Jejaring Kerja Luas</h3>
          <p>Bermitra dengan ratusan perusahaan, industri kreatif, dan jejaring wirausaha alumni untuk penyaluran magang serta karir kerja.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Section 2: Alur Seleksi -->
  <section id="alur" class="s">
    <div class="c">
      <div class="shead">
        <div>
          <p class="kick" style="color:var(--blue)">Tahapan Pendaftaran</p>
          <h2>Alur Seleksi Penerimaan Santri Baru</h2>
          <p class="intro">Proses seleksi transparan, objektif, dan memprioritaskan pemuda dhuafa yang bersungguh-sungguh ingin maju.</p>
        </div>
        <a class="b p" href="#form-daftar"><i class="fa-solid fa-file-pen"></i> Isi Formulir Online</a>
      </div>
      <ol class="flow">
        <li class="st rv">
          <span class="n">01</span>
          <span class="w">Tahap 1</span>
          <h4>Pendaftaran Online</h4>
          <p>Mengisi formulir biodata lengkap dan mengunggah dokumen administrasi persyaratan.</p>
          <em><i class="fa-regular fa-clock"></i> 1 Okt – 30 Nov 2026</em>
        </li>
        <li class="st rv">
          <span class="n">02</span>
          <span class="w">Tahap 2</span>
          <h4>Verifikasi Berkas</h4>
          <p>Pemeriksaan keaslian dan kelayakan berkas kriteria dhuafa/yatim oleh panitia PPDB.</p>
          <em><i class="fa-regular fa-clock"></i> 1 – 7 Des 2026</em>
        </li>
        <li class="st rv">
          <span class="n">03</span>
          <span class="w">Tahap 3</span>
          <h4>Tes &amp; Wawancara</h4>
          <p>Wawancara motivasi, tes potensi dasar kejuruan, dan baca Al-Qur’an (bisa daring / luring).</p>
          <em><i class="fa-regular fa-clock"></i> 10 – 18 Des 2026</em>
        </li>
        <li class="st rv">
          <span class="n">04</span>
          <span class="w">Tahap 4</span>
          <h4>Pengumuman &amp; Masuk Asrama</h4>
          <p>Pengumuman kelulusan, penandatanganan pakta integritas beasiswa, dan masuk asrama.</p>
          <em><i class="fa-regular fa-clock"></i> Awal Januari 2027</em>
        </li>
      </ol>
    </div>
  </section>

  <!-- Section 3: Jurusan Keahlian -->
  <section id="jurusan" class="s alt jr">
    <div class="c">
      <div class="shead">
        <div>
          <p class="kick" style="color:var(--blue)">Pilihan Kompetensi</p>
          <h2>8 Jurusan Kejuruan Siap Kerja</h2>
          <p class="intro">Kurikulum berbasis proyek riil didampingi instruktur praktisi berpengalaman di bidangnya.</p>
        </div>
        <span class="tag" style="background:var(--pencil);color:var(--deep);font-weight:700;padding:6px 14px;border-radius:6px;">6 Bulan Intensif Berasrama</span>
      </div>
      <div class="grid4">
        <article class="cd rv">
          <div class="jm"><b>TKJ</b><span><i class="fa-solid fa-location-dot"></i> Depok</span></div>
          <h3>Teknik Komputer &amp; Jaringan</h3>
          <p>Administrasi server Linux, konfigurasi router Mikrotik/Cisco, instalasi jaringan LAN/Fiber Optic, dan troubleshooting hardware.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Teknik Komputer dan Jaringan')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>TB</b><span><i class="fa-solid fa-location-dot"></i> Depok, Surabaya</span></div>
          <h3>Tata Busana &amp; Fashion</h3>
          <p>Konstruksi pola busana, teknik jahit mesin industri, grading ukuran, finishing garmen, dan dasar-dasar fashion design muslimah.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Tata Busana')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>FV</b><span><i class="fa-solid fa-location-dot"></i> Depok</span></div>
          <h3>Fotografi &amp; Videografi</h3>
          <p>Pengoperasian kamera profesional, tata cahaya studio, editing video Premiere &amp; After Effects, dan content creation.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Fotografi dan Videografi')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>DG</b><span><i class="fa-solid fa-location-dot"></i> Depok</span></div>
          <h3>Desain Grafis</h3>
          <p>Adobe Photoshop, Illustrator, branding identitas visual, layout publikasi, kemasan produk, dan digital asset.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Desain Grafis')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>OTO</b><span><i class="fa-solid fa-location-dot"></i> Depok, Surabaya</span></div>
          <h3>Teknik Otomotif</h3>
          <p>Mekanik sepeda motor injeksi &amp; konvensional, kelistrikan kendaraan, servis berkala, dan manajemen bengkel.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Otomotif')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>AP</b><span><i class="fa-solid fa-location-dot"></i> Depok, Magelang</span></div>
          <h3>Aplikasi Perkantoran</h3>
          <p>Administrasi perkantoran modern, Microsoft Office advance, korespondensi bisnis, kearsipan digital, dan customer service.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Aplikasi Perkantoran')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>KLN</b><span><i class="fa-solid fa-location-dot"></i> Yogyakarta</span></div>
          <h3>Kuliner Halal</h3>
          <p>Pengolahan hidangan nusantara &amp; kontinental, higienitas pangan halal, pastry &amp; bakery, serta perhitungan HPP kuliner.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Kuliner Halal')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
        <article class="cd rv">
          <div class="jm"><b>RPL</b><span><i class="fa-solid fa-location-dot"></i> Depok</span></div>
          <h3>Rekayasa Perangkat Lunak</h3>
          <p>Pemrograman web modern, HTML/CSS/JavaScript, database MySQL, backend PHP CodeIgniter / Node.js, dan Git versioning.</p>
          <a href="#form-daftar" onclick="pilihJurusan('Rekayasa Perangkat Lunak')">Daftar Jurusan Ini <i class="fa-solid fa-arrow-right"></i></a>
        </article>
      </div>
    </div>
  </section>

  <!-- Section 4: Syarat & Fasilitas -->
  <section id="syarat" class="s">
    <div class="c two">
      <div>
        <p class="kick" style="color:var(--blue)"><i class="fa-solid fa-clipboard-check"></i> Kualifikasi Calon Santri</p>
        <h2>Syarat Pendaftaran Santri Baru</h2>
        <ul class="ck">
          <li class="rv"><i class="fa-solid fa-check"></i> Pria atau Wanita berusia 17 - 30 tahun.</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Pendidikan minimal lulusan SMA / SMK / MA / Sederajat atau putus sekolah.</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Berasal dari keluarga dhuafa, yatim, atau kurang mampu (diprioritaskan).</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Belum menikah dan bersedia tidak menikah selama masa diklat.</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Bersedia tinggal di asrama penuh selama 6 bulan dan mematuhi tata tertib pesantren.</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Mendapatkan izin tertulis dari orang tua / wali.</li>
          <li class="rv"><i class="fa-solid fa-check"></i> Sehat jasmani dan rohani serta bebas narkoba.</li>
        </ul>
      </div>
      <div>
        <p class="kick" style="color:var(--blue)"><i class="fa-solid fa-gift"></i> Fasilitas 100% Gratis</p>
        <h2>Yang Didapatkan Santri</h2>
        <div class="grid2">
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-house-chimney"></i></span><h3>Asrama Santri</h3><p>Tempat tinggal representatif, kasur, lemari, dan ruang istirahat bersih.</p></div>
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-utensils"></i></span><h3>Makan 3x Sehari</h3><p>Konsumsi bergizi harian disiapkan lengkap untuk seluruh santri.</p></div>
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-laptop-code"></i></span><h3>Laboratorium &amp; Alat</h3><p>Praktik langsung menggunakan peralatan standar industri terkini.</p></div>
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-shirt"></i></span><h3>Seragam &amp; Modul</h3><p>Baju seragam diklat, modul ajar, dan perlengkapan ibadah santri.</p></div>
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-certificate"></i></span><h3>Uji Kompetensi BNSP</h3><p>Ujian sertifikasi profesi resmi negara tanpa dipungut biaya sepeserpun.</p></div>
          <div class="cd rv"><span class="ico"><i class="fa-solid fa-handshake"></i></span><h3>Inkubasi Kerja</h3><p>Pendampingan magang industri hingga penempatan kerja bagi lulusan.</p></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 5: FAQ -->
  <section id="faq" class="s alt">
    <div class="c faq">
      <p class="kick" style="color:var(--blue)">Pusat Bantuan</p>
      <h2>Pertanyaan yang Sering Diajukan (FAQ)</h2>
      <p class="intro">Jawaban seputar program beasiswa vokasi dan seleksi santri baru di Rumah Gemilang Indonesia.</p>
      <details name="faq">
        <summary>Apakah benar program ini gratis 100% tanpa biaya?</summary>
        <p>Ya, benar sekali. Rumah Gemilang Indonesia (RGI) adalah program pemberdayaan berbasis dana ZISWAF. Seluruh biaya pelatihan, asrama, makan 3x sehari, modul, hingga ujian sertifikasi BNSP ditanggung penuh oleh beasiswa.</p>
      </details>
      <details name="faq">
        <summary>Berapa lama durasi masa pelatihan di RGI?</summary>
        <p>Durasi diklat berlangsung selama 6 bulan intensif berasrama (kelas teori, praktik lab, dan pembinaan karakter), dilanjutkan dengan 2 bulan magang kerja / PKL di industri mitra.</p>
      </details>
      <details name="faq">
        <summary>Bagaimana jika calon santri berada di luar kota / luar pulau?</summary>
        <p>Calon santri dari seluruh Indonesia dapat mendaftar. Tahap seleksi awal berkas dan wawancara dapat dilakukan secara online melalui panggilan video/WhatsApp. Ketika dinyatakan lolos, santri difasilitasi asrama penuh di kampus pilihan.</p>
      </details>
      <details name="faq">
        <summary>Bagaimana cara mengecek apakah saya sudah terdaftar dan lolos seleksi?</summary>
        <p>Setelah mengisi formulir, Anda akan mendapatkan Nomor Registrasi (contoh: PPDB-36-001). Anda dapat mengecek status pendaftaran kapan saja melalui menu <a href="<?= base_url('ppdb/cek-status'); ?>">Cek Status PPDB</a>.</p>
      </details>
    </div>
  </section>

  <!-- =========================================================
       SECTION 6: FORM PENDAFTARAN WIZARD MULTI-STEP
       ========================================================= -->
  <section id="form-daftar" class="wrap">

    <!-- Flash Error Notification if redirected back -->
    <?php if (session()->getFlashdata('errors') || session()->getFlashdata('notif_error')) : ?>
      <div style="margin-bottom:20px;padding:16px 20px;border-radius:12px;background:#FEF2F2;border:1.5px solid #F87171;color:#991B1B;">
        <div style="font-weight:700;font-size:15px;margin-bottom:6px;"><i class="fa-solid fa-triangle-exclamation"></i> Mohon Periksa Kembali Isian Anda:</div>
        <?php if (session()->getFlashdata('errors')): ?>
          <ul style="margin:0;padding-left:20px;font-size:13.5px;">
            <?php foreach (session()->getFlashdata('errors') as $err) : ?>
              <li><?= esc($err); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p style="margin:0;font-size:13.5px;"><?= esc(session()->getFlashdata('notif_error')); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Sticky Note Kriteria -->
    <aside class="note" aria-label="Kriteria penerima manfaat RGI">
      <h2>Kriteria penerima manfaat RGI</h2>
      <ul>
        <li>Usia produktif 17-30 tahun</li>
        <li>Pendidikan maksimal SLTA/sederajat</li>
        <li>Bersedia tinggal di asrama</li>
        <li>Dari keluarga tidak mampu (dhuafa)</li>
        <li>Single / belum menikah</li>
        <li>Sehat jasmani dan rohani</li>
      </ul>
    </aside>

    <!-- Multi-step Tabs Header -->
    <div class="tabs" role="tablist" aria-label="Tahap pendaftaran">
      <button type="button" class="tab" role="tab" aria-selected="true" data-go="1"><b>1</b><span class="t">Biodata</span></button>
      <button type="button" class="tab" role="tab" aria-selected="false" data-go="2"><b>2</b><span class="t">Alamat</span></button>
      <button type="button" class="tab" role="tab" aria-selected="false" data-go="3"><b>3</b><span class="t">Berkas</span></button>
      <button type="button" class="tab" role="tab" aria-selected="false" data-go="4"><b>4</b><span class="t">Konfirmasi</span></button>
    </div>

    <div class="sheetwrap">
      <!-- Form Terintegrasi ke CodeIgniter 4 -->
      <form id="f" class="sheet" action="<?= base_url('ppdb/submit'); ?>" method="POST" enctype="multipart/form-data" novalidate>
        <?= csrf_field(); ?>

        <!-- Ruler Progress Bar Sidebar -->
        <div class="rail" aria-hidden="true">
          <div class="pct"><b id="pct">0</b>% lengkap</div>
          <div class="ruler"><i></i></div>
        </div>

        <div class="body">
          <div class="head">
            <div>
              <h1>Lembar <mark>Pendaftaran</mark></h1>
              <p class="sub">Formulir calon santri baru &middot; Gelombang I</p>
              <p class="intro2">Lengkapi dengan data asli dan valid. Pendaftaran tidak dipungut biaya apa pun (100% beasiswa penuh).</p>
            </div>
            <div class="stamp" aria-hidden="true">PPDB<br>2026<br>RGI</div>
          </div>

          <!-- Step 1: Biodata Pribadi -->
          <section class="pane on" data-step="1" tabindex="-1" aria-label="Biodata">
            <h2 class="sec">Biodata calon santri</h2>
            <div class="grid">
              <label class="fld span" data-r>
                <span>Nama lengkap</span>
                <input name="nama_lengkap" value="<?= old('nama_lengkap'); ?>" autocomplete="name" placeholder="Contoh: Muhammad Zaky Pratama">
                <em class="err"></em>
              </label>

              <label class="fld" data-r>
                <span>NIK (16 digit)</span>
                <input name="nik" value="<?= old('nik'); ?>" inputmode="numeric" maxlength="16" data-num data-re="^\d{16}$" data-m="NIK harus 16 digit" placeholder="Nomor NIK KTP">
                <em class="err"></em>
              </label>

              <div class="grp" data-r role="radiogroup" aria-label="Jenis kelamin">
                <span class="lab">Jenis kelamin</span>
                <div class="opts">
                  <label class="opt">
                    <input type="radio" name="jenis_kelamin" value="Laki-laki" <?= old('jenis_kelamin') === 'Laki-laki' || old('jenis_kelamin') === 'L' ? 'checked' : ''; ?>>
                    <span>Laki-laki</span>
                  </label>
                  <label class="opt">
                    <input type="radio" name="jenis_kelamin" value="Perempuan" <?= old('jenis_kelamin') === 'Perempuan' || old('jenis_kelamin') === 'P' ? 'checked' : ''; ?>>
                    <span>Perempuan</span>
                  </label>
                </div>
                <em class="err"></em>
              </div>

              <label class="fld">
                <span>Tempat lahir</span>
                <input name="tempat_lahir" value="<?= old('tempat_lahir'); ?>" placeholder="Kota tempat lahir">
                <em class="err"></em>
              </label>

              <label class="fld">
                <span>Tanggal lahir</span>
                <input type="date" name="tanggal_lahir" value="<?= old('tanggal_lahir'); ?>">
                <em class="err"></em>
              </label>

              <label class="fld" data-r>
                <span>WhatsApp aktif</span>
                <input type="tel" name="whatsapp" value="<?= old('whatsapp'); ?>" inputmode="numeric" autocomplete="tel" data-num data-re="^08\d{8,12}$" data-m="Format 08xxxxxxxxxx" placeholder="081234567890">
                <em class="err"></em>
              </label>

              <label class="fld">
                <span>Email</span>
                <input type="email" name="email" value="<?= old('email'); ?>" autocomplete="email" placeholder="nama@gmail.com">
                <em class="err"></em>
              </label>
            </div>
          </section>

          <!-- Step 2: Alamat & Wali -->
          <section class="pane" data-step="2" tabindex="-1" aria-label="Alamat">
            <h2 class="sec">Alamat dan wali</h2>
            <div class="grid">
              <label class="fld span" data-r>
                <span>Alamat domisili lengkap</span>
                <textarea name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"><?= old('alamat'); ?></textarea>
                <em class="err"></em>
              </label>

              <label class="fld">
                <span>Kota / kabupaten</span>
                <input name="kota" value="<?= old('kota'); ?>" placeholder="Contoh: Depok">
                <em class="err"></em>
              </label>
              <span></span>

              <label class="fld">
                <span>Nama wali (ayah / ibu / wali)</span>
                <input name="nama_wali" value="<?= old('nama_wali'); ?>" placeholder="Nama wali calon santri">
                <em class="err"></em>
              </label>

              <label class="fld">
                <span>WhatsApp wali</span>
                <input type="tel" name="whatsapp_wali" value="<?= old('whatsapp_wali'); ?>" inputmode="numeric" data-num placeholder="081298765432">
                <em class="err"></em>
              </label>

              <label class="fld span">
                <span>Motivasi singkat</span>
                <textarea name="motivasi" rows="3" placeholder="Cita-cita dan alasan ingin belajar di Rumah Gemilang Indonesia"><?= old('motivasi'); ?></textarea>
                <em class="err"></em>
              </label>
            </div>
          </section>

          <!-- Step 3: Cabang, Jurusan & Berkas -->
          <section class="pane" data-step="3" tabindex="-1" aria-label="Berkas">
            <h2 class="sec">Pilihan cabang dan jurusan</h2>
            
            <div class="grp" data-r role="radiogroup" aria-label="Cabang">
              <span class="lab">Cabang / lokasi kampus</span>
              <div class="opts">
                <label class="opt"><input type="radio" name="cabang_kampus" value="Depok" <?= old('cabang_kampus') === 'Depok' ? 'checked' : ''; ?>><span>Depok (Pusat)</span></label>
                <label class="opt"><input type="radio" name="cabang_kampus" value="Surabaya" <?= old('cabang_kampus') === 'Surabaya' ? 'checked' : ''; ?>><span>Surabaya</span></label>
                <label class="opt"><input type="radio" name="cabang_kampus" value="Magelang" <?= old('cabang_kampus') === 'Magelang' ? 'checked' : ''; ?>><span>Magelang</span></label>
                <label class="opt"><input type="radio" name="cabang_kampus" value="Yogyakarta" <?= old('cabang_kampus') === 'Yogyakarta' ? 'checked' : ''; ?>><span>Yogyakarta</span></label>
                <label class="opt"><input type="radio" name="cabang_kampus" value="Medan" <?= old('cabang_kampus') === 'Medan' ? 'checked' : ''; ?>><span>Medan</span></label>
                <label class="opt"><input type="radio" name="cabang_kampus" value="Aceh" <?= old('cabang_kampus') === 'Aceh' ? 'checked' : ''; ?>><span>Aceh</span></label>
              </div>
              <em class="err"></em>
            </div>

            <div class="grp sq" data-r role="radiogroup" aria-label="Jurusan">
              <span class="lab">Jurusan prioritas</span>
              <div class="opts" id="jur"></div>
              <em class="err"></em>
            </div>

            <div class="grp sq" role="radiogroup" aria-label="Jurusan cadangan">
              <span class="lab">Jurusan cadangan (opsional)</span>
              <div class="opts" id="jur2"></div>
            </div>

            <div class="grid">
              <div class="grp" data-r role="radiogroup" aria-label="Pendidikan terakhir">
                <span class="lab">Pendidikan terakhir</span>
                <div class="opts">
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="SMA" <?= old('pendidikan_terakhir') === 'SMA' ? 'checked' : ''; ?>><span>SMA</span></label>
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="SMK" <?= old('pendidikan_terakhir') === 'SMK' ? 'checked' : ''; ?>><span>SMK</span></label>
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="MA" <?= old('pendidikan_terakhir') === 'MA' ? 'checked' : ''; ?>><span>MA</span></label>
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="Paket C" <?= old('pendidikan_terakhir') === 'Paket C' ? 'checked' : ''; ?>><span>Paket C</span></label>
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="SMP / Sederajat" <?= old('pendidikan_terakhir') === 'SMP / Sederajat' ? 'checked' : ''; ?>><span>SMP</span></label>
                  <label class="opt"><input type="radio" name="pendidikan_terakhir" value="Lainnya" <?= old('pendidikan_terakhir') === 'Lainnya' ? 'checked' : ''; ?>><span>Lainnya</span></label>
                </div>
                <em class="err"></em>
              </div>

              <label class="fld">
                <span>Nama sekolah asal</span>
                <input name="asal_sekolah" value="<?= old('asal_sekolah'); ?>" placeholder="Contoh: SMKN 1 Depok">
                <em class="err"></em>
              </label>
            </div>

            <h2 class="sec">Upload berkas pendukung</h2>
            <div class="docs" id="docs"></div>
          </section>

          <!-- Step 4: Konfirmasi & Kirim -->
          <section class="pane" data-step="4" tabindex="-1" aria-label="Konfirmasi">
            <h2 class="sec">Periksa data lalu kirim</h2>
            <dl class="sum" id="sum"></dl>
            <div data-r class="fld">
              <label class="agree">
                <input type="checkbox" name="setuju" value="1">
                <span style="font:inherit;text-transform:none;letter-spacing:0;color:var(--ink);font-weight:500;">
                  Saya menyatakan bahwa data yang saya masukkan adalah benar, asli, dan siap mematuhi tata tertib serta ketentuan beasiswa diklat Rumah Gemilang Indonesia.
                </span>
              </label>
              <em class="err"></em>
            </div>
          </section>

          <!-- Navigation Controls -->
          <div class="nav">
            <button type="button" class="btn" id="prev"><span>&larr; Sebelumnya</span></button>
            <button type="button" class="btn go" id="next"><span>Lanjutkan &rarr;</span></button>
            <button type="button" class="btn go" id="send" hidden><span><i class="fa-solid fa-paper-plane mr-1"></i> Kirim Lembar Pendaftaran</span></button>
          </div>
          <p class="priv">Data Anda terjamin kerahasiaannya dan hanya digunakan untuk keperluan verifikasi seleksi PPDB Rumah Gemilang Indonesia.</p>
        </div>

        <!-- Indikator Terkirim -->
        <div class="recv" hidden>TERKIRIM</div>
      </form>
    </div>
  </section>
</main>

<footer>
  <div class="c">
    <div class="fgrid">
      <div>
        <a class="nav-brand" href="<?= base_url('/'); ?>" style="color:#fff">
          <span class="nav-brand-badge" style="background:transparent;overflow:hidden;box-shadow:none;"><img src="<?= base_url('assets/img/rgi.png'); ?>" alt="Logo Syiar Gemilang" style="width:100%;height:100%;object-fit:contain;border-radius:10px;"></span>
          <span>Syiar Gemilang<small style="display:block;font-size:12px;font-family:'DM Sans';font-weight:400;color:#9fb0c1">Penerimaan Peserta Didik Baru (PPDB) Angkatan 36</small></span>
        </a>
      </div>
      <nav class="fl">
        <a href="<?= base_url('/'); ?>">Beranda Utama</a>
        <a href="<?= base_url('ppdb/cek-status'); ?>">Cek Status PPDB</a>
        <a href="<?= base_url('login'); ?>">Portal Santri / Login</a>
      </nav>
    </div>
    <div class="fb">
      <span>&copy; <?= date('Y'); ?> Lembaga Pendidikan Rumah Gemilang Indonesia. Sistem Informasi Akademik Syiar Gemilang.</span>
      <span>Hotline Panitia PPDB: 0812-3456-7890</span>
    </div>
  </div>
</footer>

<script>
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const f = $('#f'), panes = $$('.pane'), tabs = $$('.tab'), root = document.documentElement;
const RM = matchMedia('(prefers-reduced-motion:reduce)').matches;
const LIVE = true;               // Aktif mengirim request ke backend CodeIgniter 4
const MAX = 5 * 1024 * 1024;     // Batas ukuran file 5 MB
let step = 1;

/* Inisialisasi daftar jurusan & upload berkas */
const jurusanList = [
  'Teknik Komputer dan Jaringan',
  'Tata Busana',
  'Fotografi dan Videografi',
  'Desain Grafis',
  'Otomotif',
  'Aplikasi Perkantoran',
  'Kuliner Halal',
  'Rekayasa Perangkat Lunak'
];

jurusanList.forEach(j => {
  [['#jur', 1], ['#jur2', 2]].forEach(([s, n]) => {
    $(s).insertAdjacentHTML('beforeend', `<label class="opt"><input type="radio" name="jurusan_pilihan${n}" value="${j}"><span>${j}</span></label>`);
  });
});

const IC = '<svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>';
const berkasList = [
  ['ijazah', 'Ijazah / SKL', 1],
  ['ktp', 'KTP Calon Santri', 1],
  ['kk', 'Kartu Keluarga', 1],
  ['sktm', 'Surat SKTM / Dhuafa', 1],
  ['sehat', 'Ket. Sehat Dokter', 1],
  ['vaksin', 'Bukti Vaksin', 0]
];

berkasList.forEach(([k, n, r]) => {
  $('#docs').insertAdjacentHTML('beforeend', `
    <label class="doc"${r ? ' data-r' : ''}>
      <input type="file" name="berkas_${k}" accept="image/*,.pdf">
      ${IC}
      <b>${n}</b>
      <small data-o="${r ? 'Wajib, pilih / seret' : 'Opsional'}">${r ? 'Wajib, pilih / seret' : 'Opsional'}</small>
      <button type="button" class="x" aria-label="Hapus ${n}">&times;</button>
    </label>
  `);
});

/* Cek preselected jurusan dari URL query jika ada */
<?php if (!empty($selectedJurusan)): ?>
window.addEventListener('DOMContentLoaded', () => {
  window.pilihJurusan(<?= json_encode($selectedJurusan); ?>);
});
<?php endif; ?>

/* Validasi form tiap tahap */
function check(c) {
  const i = $('input,textarea', c);
  if (c.classList.contains('doc')) return i.files.length ? '' : 'x';
  if (c.classList.contains('grp')) return $('input:checked', c) ? '' : 'Pilih salah satu opsi';
  if (i.type === 'checkbox') return i.checked ? '' : 'Centang persetujuan untuk mengirim';
  const v = i.value.trim();
  if (!v) return 'Wajib diisi';
  return i.dataset.re && !new RegExp(i.dataset.re).test(v) ? i.dataset.m : '';
}

function mark(c, m) {
  c.classList.toggle('bad', !!m);
  const e = $('.err', c);
  if (e) e.textContent = m && m !== 'x' ? m : '';
}

function validate(s) {
  let first = null;
  $$('[data-r]', panes[s - 1]).forEach(c => {
    const m = check(c);
    mark(c, m);
    if (m && !first) first = c;
  });
  return first;
}

function focusIn(c) {
  const i = $('input,textarea', c);
  i && i.focus({ preventScroll: false });
}

const shake = c => {
  c.classList.add('shake');
  setTimeout(() => c.classList.remove('shake'), 400);
};

/* Progres penggaris formulir */
function progress() {
  const all = $$('[data-r]');
  const n = all.filter(c => !check(c)).length;
  const p = n / (all.length || 1);
  f.style.setProperty('--p', p);
  $('#pct').textContent = Math.round(p * 100);
  tabs.forEach((t, i) => t.classList.toggle('done', $$('[data-r]', panes[i]).every(c => !check(c)) && i + 1 !== step || false));
}

f.addEventListener('input', e => {
  if (e.target.dataset.num) e.target.value = e.target.value.replace(/\D/g, '');
  const c = e.target.closest('[data-r]');
  if (c && c.classList.contains('bad') && !check(c)) mark(c, '');
  progress();
});

f.addEventListener('change', e => {
  const c = e.target.closest('[data-r]');
  if (c && c.classList.contains('bad') && !check(c)) mark(c, '');
  progress();
});

/* Navigasi ganti tab pane */
function show(n, dir, ev) {
  const same = n === step;
  step = n;
  panes.forEach((p, i) => {
    p.classList.toggle('on', i + 1 === n);
    if (!same && i + 1 === n) {
      p.style.setProperty('--dir', dir);
      p.style.animation = 'none';
      p.offsetWidth;
      p.style.animation = '';
    }
  });
  tabs.forEach((t, i) => t.setAttribute('aria-selected', i + 1 === n));
  $('#prev').style.visibility = n > 1 ? 'visible' : 'hidden';
  $('#next').hidden = n === 4;
  $('#send').hidden = n !== 4;
  if (n === 4) summary();
  progress();
  if (!same) {
    ripple(ev);
    panes[n - 1].focus({ preventScroll: true });
    if ($('.tabs').getBoundingClientRect().top < 0) {
      $('.tabs').scrollIntoView({ behavior: RM ? 'auto' : 'smooth' });
    }
  }
}

function go(n, ev) {
  let bad = null;
  if (n > step) {
    for (let s = step; s < n && !bad; s++) {
      const b = validate(s);
      if (b) { bad = b; n = s; }
    }
  }
  show(n, n < step ? -1 : 1, ev);
  if (bad) { shake(bad); focusIn(bad); }
}

tabs.forEach(t => t.addEventListener('click', e => go(+t.dataset.go, e)));
$('#prev').addEventListener('click', e => go(step - 1, e));
$('#next').addEventListener('click', e => go(step + 1, e));
f.addEventListener('submit', e => {
  e.preventDefault();
  step < 4 ? go(step + 1) : send(e);
});

/* Ringkasan Konfirmasi */
function summary() {
  const d = new FormData(f);
  const g = k => (d.get(k) || '').toString().trim() || '-';
  const s = $('#sum');
  s.textContent = '';
  const rawTl = g('tanggal_lahir');
  const tl = rawTl !== '-' ? rawTl.split('-').reverse().join('/') : '-';
  const nf = $$('.doc input').filter(i => i.files.length).length;

  [
    ['Nama Lengkap', g('nama_lengkap')],
    ['NIK', g('nik')],
    ['Jenis Kelamin', g('jenis_kelamin')],
    ['Tempat, Tanggal Lahir', g('tempat_lahir') + ', ' + tl],
    ['WhatsApp', g('whatsapp')],
    ['Email', g('email')],
    ['Alamat Domisili', g('alamat') + ', ' + g('kota'), 1],
    ['Nama & Kontak Wali', g('nama_wali') + ' (' + g('whatsapp_wali') + ')'],
    ['Kampus Pilihan', g('cabang_kampus')],
    ['Jurusan Prioritas', g('jurusan_pilihan1')],
    ['Jurusan Cadangan', g('jurusan_pilihan2')],
    ['Pendidikan Terakhir', g('pendidikan_terakhir') + ', ' + g('asal_sekolah')],
    ['Berkas Terunggah', nf + ' dari 6 berkas']
  ].forEach(([k, v, w]) => {
    const r = document.createElement('div'), a = document.createElement('dt'), b = document.createElement('dd');
    if (w) r.className = 'w';
    a.textContent = k;
    b.textContent = v;
    r.append(a, b);
    s.append(r);
  });
}

/* Upload berkas: drag & drop & visual status */
const fmt = b => b > 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
$$('.doc').forEach(d => {
  const i = $('input', d), n = $('small', d);
  const set = file => {
    if (!file) {
      i.value = '';
      d.classList.remove('has', 'bad');
      n.textContent = n.dataset.o;
      return;
    }
    if (file.size > MAX) {
      i.value = '';
      d.classList.remove('has');
      d.classList.add('bad');
      n.textContent = 'Maksimal ' + fmt(MAX);
      return;
    }
    d.classList.remove('bad');
    d.classList.add('has');
    n.textContent = file.name + ' (' + fmt(file.size) + ')';
  };

  i.addEventListener('change', () => { set(i.files[0]); progress(); });
  ['dragenter', 'dragover'].forEach(t => d.addEventListener(t, e => { e.preventDefault(); d.classList.add('drag'); }));
  ['dragleave', 'drop'].forEach(t => d.addEventListener(t, e => { e.preventDefault(); d.classList.remove('drag'); }));
  d.addEventListener('drop', e => {
    const fl = e.dataTransfer.files[0];
    if (!fl) return;
    const dt = new DataTransfer();
    dt.items.add(fl);
    i.files = dt.files;
    i.dispatchEvent(new Event('change', { bubbles: true }));
  });
  $('.x', d).addEventListener('click', e => {
    e.preventDefault();
    e.stopPropagation();
    set(null);
    progress();
  });
});

/* Kirim Formulir ke Server */
function send(ev) {
  const s = [1, 2, 3, 4].find(s => validate(s));
  if (s) {
    const b = validate(s);
    show(s, -1, ev);
    shake(b);
    focusIn(b);
    return;
  }
  $('.recv').hidden = false;
  $('#send').disabled = true;
  ripple(ev);
  if (LIVE) {
    setTimeout(() => f.submit(), 1000);
  }
}
$('#send').addEventListener('click', send);

/* Latar belakang titik buku gambar interaktif */
const cv = $('#bg'), cx = cv.getContext('2d'), G = 32, R = 150;
const m = { x: -999, y: -999, sx: -999, sy: -999 }, rip = { x: 0, y: 0, t: -1e9 };
let W, H;

function size() {
  const D = Math.min(devicePixelRatio || 1, 2);
  W = innerWidth;
  H = innerHeight;
  cv.width = W * D;
  cv.height = H * D;
  cx.setTransform(D, 0, 0, D, 0, 0);
  if (RM) draw(0);
}

function ripple(e) {
  const t = e && e.currentTarget && e.currentTarget.getBoundingClientRect ? e.currentTarget.getBoundingClientRect() : null;
  rip.x = t ? t.left + t.width / 2 : W / 2;
  rip.y = t ? t.top + t.height / 2 : H / 2;
  rip.t = performance.now();
}

function draw(now) {
  cx.clearRect(0, 0, W, H);
  m.sx += (m.x - m.sx) * .18;
  m.sy += (m.y - m.sy) * .18;
  const y0 = -(scrollY * .3) % G, rr = (now - rip.t) * .65, ra = rr < 1000 ? 1 - rr / 1000 : 0;
  cx.strokeStyle = 'rgba(255,255,255,.07)';
  cx.lineWidth = 1;
  cx.beginPath();
  for (let x = G / 2; x < W; x += G) { cx.moveTo(x + .5, 0); cx.lineTo(x + .5, H); }
  for (let y = y0; y < H; y += G) { cx.moveTo(0, y + .5); cx.lineTo(W, y + .5); }
  cx.stroke();

  for (let y = y0; y < H + G; y += G) {
    for (let x = G / 2; x < W + G; x += G) {
      let px = x, py = y, r = 1.2, f = 0;
      const dx = x - m.sx, dy = y - m.sy, d = Math.hypot(dx, dy);
      if (d < R) {
        f = 1 - d / R;
        const k = f * f * 18 / (d || 1);
        px += dx * k;
        py += dy * k;
        r += f * 2.6;
      }
      if (ra) {
        const ex = x - rip.x, ey = y - rip.y, ed = Math.hypot(ex, ey), w = 1 - Math.min(1, Math.abs(ed - rr) / 70);
        if (w > 0) {
          const q = w * ra, k = q * 16 / (ed || 1);
          px += ex * k;
          py += ey * k;
          r += q * 2;
          f = Math.max(f, q * .9);
        }
      }
      cx.fillStyle = f > .4 ? 'rgba(255,208,89,' + (.55 + f * .45) + ')' : 'rgba(255,255,255,' + (.16 + f * .4) + ')';
      cx.beginPath();
      cx.arc(px, py, r, 0, 6.283);
      cx.fill();
    }
  }
  if (!RM) requestAnimationFrame(draw);
}

size();
addEventListener('resize', size);

if (!RM) {
  addEventListener('pointermove', e => {
    m.x = e.clientX;
    m.y = e.clientY;
    root.style.setProperty('--mx', (e.clientX / W * 2 - 1).toFixed(3));
    root.style.setProperty('--my', (e.clientY / H * 2 - 1).toFixed(3));
  }, { passive: true });

  addEventListener('scroll', () => {
    root.style.setProperty('--sy', Math.max(0, Math.min(900, H - $('#form-daftar').getBoundingClientRect().top)));
  }, { passive: true });

  requestAnimationFrame(draw);
}

/* Catatan sticky note kriteria dapat digeser (layar desktop) */
const note = $('.note');
let dr = null, nx = 0, ny = 0;
if (note) {
  note.addEventListener('pointerdown', e => {
    if (innerWidth < 1280) return;
    dr = { x: e.clientX - nx, y: e.clientY - ny };
    note.setPointerCapture(e.pointerId);
    note.classList.add('lift');
  });
  note.addEventListener('pointermove', e => {
    if (!dr) return;
    nx = e.clientX - dr.x;
    ny = e.clientY - dr.y;
    note.style.setProperty('--tx', nx + 'px');
    note.style.setProperty('--ty', ny + 'px');
  });
  ['pointerup', 'pointercancel'].forEach(t => note.addEventListener(t, () => {
    dr = null;
    note.classList.remove('lift');
  }));
}

/* Reveal elements on scroll */
root.classList.add('js');
const rvo = new IntersectionObserver(es => es.forEach(e => {
  if (e.isIntersecting) {
    e.target.classList.add('in');
    rvo.unobserve(e.target);
  }
}), { threshold: .12 });
$$('.rv').forEach((el, i) => {
  el.style.transitionDelay = (i % 4) * 70 + 'ms';
  rvo.observe(el);
});

/* Menu scroll highlight */
const navs = $$('.nav-links a[href^="#"]');
const so = new IntersectionObserver(es => es.forEach(e => {
  if (e.isIntersecting) {
    navs.forEach(a => a.classList.toggle('on', a.getAttribute('href') === '#' + e.target.id));
  }
}), { rootMargin: '-40% 0px -55% 0px' });
$$('main section[id]').forEach(s => so.observe(s));

new IntersectionObserver(e => {
  document.body.classList.toggle('inform', e[0].isIntersecting);
}, { threshold: .05 }).observe($('#form-daftar'));

/* Shortcut pilih jurusan langsung membuka step 3 */
window.pilihJurusan = n => {
  const r = $$('input[name=jurusan_pilihan1]').find(i => i.value === n);
  if (r) {
    r.checked = true;
    r.dispatchEvent(new Event('change', { bubbles: true }));
  }
  show(3, 1);
};

/* =========================================================
   3D CARD TILT TRACKING ON HERO SECTION (SMOOTH PERSPECTIVE)
   ========================================================= */
const card = $('.info');
if (!RM && card) {
  $('.hero').addEventListener('pointermove', e => {
    const r = card.getBoundingClientRect();
    const ry = (((e.clientX - r.left - r.width / 2) / innerWidth) * 14).toFixed(2);
    const rx = (-((e.clientY - r.top - r.height / 2) / innerHeight) * 10).toFixed(2);
    card.style.setProperty('--ry', ry + 'deg');
    card.style.setProperty('--rx', rx + 'deg');
  });
  $('.hero').addEventListener('pointerleave', () => {
    card.style.setProperty('--ry', '-4deg');
    card.style.setProperty('--rx', '2deg');
  });
}

/* =========================================================
   DYNAMIC ISLAND NAVBAR SCROLL BEHAVIOR & MOBILE TOGGLE
   ========================================================= */
const islandWrapper = document.getElementById('mainNavbarWrapper');
function handleIslandScroll() {
  if (!islandWrapper) return;
  const scrollPos = window.scrollY || window.pageYOffset;
  if (scrollPos > 40) {
    islandWrapper.classList.add('is-scrolled');
  } else {
    islandWrapper.classList.remove('is-scrolled');
  }
}
window.addEventListener('scroll', handleIslandScroll, { passive: true });
handleIslandScroll();

window.toggleMobileIslandMenu = function() {
  const menu = document.getElementById('mobileIslandMenu');
  const icon = document.getElementById('mobileIslandIcon');
  if (menu) {
    menu.classList.toggle('hidden');
    if (icon) {
      if (menu.classList.contains('hidden')) {
        icon.classList.remove('fa-xmark');
        icon.classList.add('fa-bars');
      } else {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-xmark');
      }
    }
  }
};

window.closeMobileIslandMenu = function() {
  const menu = document.getElementById('mobileIslandMenu');
  const icon = document.getElementById('mobileIslandIcon');
  if (menu) menu.classList.add('hidden');
  if (icon) {
    icon.classList.remove('fa-xmark');
    icon.classList.add('fa-bars');
  }
};

// Mulai form di step 1
show(1, 1);
</script>
</body>
</html>
