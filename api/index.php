<!doctype html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self' https://*.supabase.co wss://*.supabase.co; object-src 'none'; base-uri 'self'; form-action 'self'">
<meta name="referrer" content="no-referrer">
<meta name="robots" content="noindex,nofollow">
<meta name="description" content="Private inventory, profit and sales tracker.">
<title>Tindahan Inventory</title>
<style>
:root{--bg:#cfe3ec;--side:#2a5468;--card:#f6fafc;--ink:#173a4d;--mut:#44636f;--line:#b4cbd6;--acc:#3f6a80;--good:#1f7556;--bad:#a8481f;--warn:#8f5f00;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media(prefers-color-scheme:dark){:root{--bg:#10222b;--side:#0b181e;--card:#183241;--ink:#e3f0f5;--mut:#9fbdca;--line:#2a4a5a;--acc:#8cbad1;--good:#5fd0a5;--bad:#f09068;--warn:#e8b64c}}
*{box-sizing:border-box}html{scroll-padding-top:env(safe-area-inset-top,0px)}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.skip{position:absolute;left:8px;top:-60px;background:var(--card);color:var(--ink);padding:10px 14px;border-radius:8px;z-index:20;font-weight:700}.skip:focus{top:8px}
.app{display:flex;min-height:100vh}
nav{width:200px;background:var(--side);color:#e3f0f5;padding:22px 12px;display:flex;flex-direction:column;gap:4px;flex:none}
nav h1{font-size:17px;margin:0 8px 18px}
nav button{all:unset;cursor:pointer;padding:10px 14px;border-radius:10px 0 0 10px;color:#d3e6ef}
nav button.on{background:var(--card);color:#173a4d;font-weight:700}
nav button:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,.foot button:focus-visible{outline:2px solid var(--acc);outline-offset:2px}
nav button:focus-visible{outline-color:#fff}
main{flex:1;padding:18px;min-width:0}main:focus{outline:none}
h2{margin:0 0 14px;font-size:22px}h3{margin:0 0 10px;font-size:16px}
.grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));margin-bottom:14px}
.grid.w{grid-template-columns:repeat(auto-fit,minmax(min(320px,100%),1fr))}
.card{background:var(--card);border-radius:12px;padding:16px;min-width:0}
.k{border:1px solid var(--line)}.k b{display:block;font-size:26px;line-height:1.2}.k span{color:var(--mut);font-size:13px}
.good{color:var(--good)}.bad{color:var(--bad)}.warn{color:var(--warn)}.mut{color:var(--mut)}
table{width:100%;border-collapse:collapse}th{text-align:left;color:var(--mut);font-weight:600;font-size:13px}
td,th{padding:8px 6px;border-bottom:1px solid var(--line);vertical-align:middle}
.sc{overflow-x:auto}
input,select{font:inherit;font-size:16px;color:var(--ink);background:var(--bg);border:1px solid var(--line);border-radius:8px;padding:7px 9px;min-width:0}
.f{display:flex;flex-wrap:wrap;gap:8px;align-items:end}.f label{display:flex;flex-direction:column;font-size:13px;color:var(--mut);gap:3px}
button.b{font:inherit;cursor:pointer;border:0;border-radius:8px;padding:8px 13px;min-height:40px;background:var(--side);color:#fff;font-weight:600}
button.b.o{background:transparent;color:var(--ink);border:1px solid var(--line)}
button.b:disabled{opacity:.5;cursor:default}
.pill{display:inline-block;padding:2px 9px;border-radius:99px;font-size:12px;font-weight:700;background:var(--card);border:1px solid var(--line)}
.p-Pending{color:var(--warn)}.p-Out{color:var(--warn)}.p-Delivered{color:var(--good)}.p-Cancelled{color:var(--mut)}
.bar{display:flex;align-items:center;gap:8px;margin:7px 0;font-size:13px}.bar i{display:block;height:10px;border-radius:5px;background:var(--acc)}.bar span:first-child{width:120px;flex:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#toast{position:fixed;left:50%;bottom:calc(20px + env(safe-area-inset-bottom,0px));transform:translateX(-50%);background:var(--side);color:#fff;padding:10px 16px;border-radius:10px;display:none;max-width:90vw;z-index:30}
.note{padding:10px 12px;border-radius:8px;background:var(--bg);font-size:14px;margin-top:10px}
.foot{margin:18px 0 0;font-size:13px;color:var(--mut);display:flex;flex-wrap:wrap;gap:2px 8px;justify-content:center}
.foot button{all:unset;cursor:pointer;color:var(--acc);text-decoration:underline;padding:6px 4px}
mark{background:#ffe58a;color:#3a2a00;padding:0 3px;border-radius:3px}
dialog{width:min(680px,94vw);max-height:88vh;border:0;border-radius:14px;padding:0;background:var(--card);color:var(--ink)}
dialog::backdrop{background:rgba(8,30,42,.6)}
.dh{position:sticky;top:0;display:flex;justify-content:flex-end;padding:10px 14px;background:var(--card);border-bottom:1px solid var(--line)}
#lgb{padding:6px 22px 24px;line-height:1.6;max-width:70ch}#lgb h3{margin:18px 0 4px}#lgb p{margin:0 0 8px}
#edb{padding:6px 22px 22px}#edb input{width:100%}
.pk{display:flex;flex-wrap:wrap;gap:8px}
.pk button{font:inherit;cursor:pointer;border:1px solid var(--line);background:var(--bg);color:var(--ink);border-radius:10px;padding:8px 12px;text-align:left;min-height:44px}
.pk button small{display:block;color:var(--mut)}
.pk button.on{border-color:var(--acc);box-shadow:0 0 0 2px var(--acc)}
.pk button:disabled{opacity:.5;cursor:default}
.lk{all:unset;cursor:pointer;color:var(--acc);font-weight:700;text-decoration:underline}
.lk:focus-visible{outline:2px solid var(--acc);outline-offset:2px}
#cb{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom,0px));max-width:560px;margin:auto;background:var(--card);border:1px solid var(--line);border-radius:12px;padding:14px;z-index:25;box-shadow:0 10px 30px rgba(8,30,42,.3)}
#cb[hidden]{display:none}#cb p{margin:0 0 10px}#cb div{display:flex;gap:8px;flex-wrap:wrap}
@media(max-width:720px){.app{flex-direction:column}nav{width:auto;flex-direction:row;overflow-x:auto;padding:10px;position:sticky;top:0;z-index:10}nav h1{display:none}nav button{border-radius:10px;white-space:nowrap}}
main.lg{display:grid;place-items:center;min-height:100vh;padding:20px;background:linear-gradient(160deg,#1c4153,#3a6f88 58%,#7fb0c8)}
.lc{width:100%;max-width:380px;padding:32px 28px 22px;border-radius:18px;text-align:center;box-shadow:0 24px 60px rgba(8,30,42,.35)}
.lb{width:56px;height:56px;border-radius:16px;background:var(--side);color:#e3f0f5;display:grid;place-items:center;margin:0 auto 14px}
.lc h1{margin:0;font-size:27px;font-weight:800;letter-spacing:-.3px}
.ls{margin:4px 0 24px;color:var(--mut);font-size:14px}
.lf{display:flex;flex-direction:column;gap:14px;text-align:left}
.lf label{display:flex;flex-direction:column;gap:6px;font-size:13px;font-weight:700;color:var(--mut)}
.lf input{width:100%;padding:12px 13px;border-radius:10px}
.pw{position:relative;display:block}.pw input{padding-right:66px}
.pw button{position:absolute;right:6px;top:50%;transform:translateY(-50%);border:0;background:none;color:var(--acc);cursor:pointer;font:inherit;font-size:13px;font-weight:700;padding:8px 9px}
.lf .b{width:100%;padding:12px;font-size:15px;border-radius:10px;margin-top:4px}
#am{min-height:20px;font-size:13px;text-align:center}.cn{font-size:12px;color:var(--mut);text-align:center;margin:0}
</style></head><body>
<a class="skip" href="#main">Skip to content</a>
<div class="app"><nav id="nav" aria-label="Main"></nav><main id="main" tabindex="-1"></main></div>
<div id="toast" role="status" aria-live="polite"></div>
<dialog id="lg" aria-labelledby="lgh"><div class="dh"><button class="b o" onclick="this.closest('dialog').close()">Close</button></div><div id="lgb"></div></dialog>
<dialog id="ed" aria-labelledby="edh"><div class="dh"><button class="b o" onclick="this.closest('dialog').close()">Close</button></div><div id="edb"></div></dialog>
<div id="cb" role="region" aria-label="Analytics choice" hidden><p>May we count anonymous visits to improve the app? Sign-in storage is always on because the app needs it. See the Cookie &amp; Storage Policy.</p><div><button class="b" onclick="setC('no')">Essential only</button><button class="b o" onclick="setC('yes')">Allow analytics</button></div></div>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.45.4/dist/umd/supabase.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="config.js"></script>
<script>
/* Edit here, or put the same object in config.js. Leave nothing invented: empty fields show a yellow [ILAGAY] marker in the legal pages. */
window.APP_CONFIG=window.APP_CONFIG||{SUPABASE_URL:'https://fhcguxmehcuzkeqnnnnd.supabase.co',SUPABASE_ANON_KEY:'sb_publishable_FwOwTGVLHMks6nYAaOg13w_uwAnJfSj',
ANALYTICS_SRC:'', /* optional https:// script of a privacy-friendly tool; also add its host to script-src/connect-src in the CSP meta above */
BUSINESS:{name:'',owner:'',address:'',email:'',phone:'',dpo:'',refundDays:'',dtiReg:''}};
</script>
<script>
const DAY=864e5,C=window.APP_CONFIG||{};let S={products:[],orders:[],ledger:[]},tab='dash',wd='',user=null,db=null;
let cart=[],rsel=null,rq=10,osel=null,oqv=1,selling=false;
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const P=n=>'₱'+Math.round(n).toLocaleString('en-PH');
const pn=p=>p.name+(p.size?' '+p.size:'');const uname=e=>e.endsWith('@example.com')?e.slice(0,-12):e;
const sum=(a,f)=>a.reduce((s,x)=>s+f(x),0);
const cash=()=>sum(S.ledger,e=>e.t==='sale'||e.t==='capital'?e.a:-e.a);
const dels=()=>S.orders.filter(o=>o.st==='Delivered');
const pending=()=>S.orders.filter(o=>o.st==='Pending'||o.st==='Out');
const profit=()=>sum(dels(),o=>(o.price-o.cost)*o.qty);
const taken=()=>sum(S.ledger.filter(e=>e.t==='withdraw'),e=>e.a);
const safe=()=>Math.max(0,profit()-taken());
const low=()=>S.products.filter(p=>p.stock<=5);
const need=()=>sum(low(),p=>(10-p.stock)*p.cost);
function toast(m){const t=document.getElementById('toast');t.textContent=m;t.style.display='block';clearTimeout(toast.h);toast.h=setTimeout(()=>t.style.display='none',4500)}
function go(t){tab=t;wd='';draw();document.getElementById('main').focus({preventScroll:true})}
const num=id=>parseFloat(document.getElementById(id).value)||0;
const val=id=>document.getElementById(id).value.trim();
const run=async q=>{const{error}=await q;if(error){toast(error.message);return false}return true};

/* ---- Consent + optional analytics (off by default; honors Global Privacy Control) ---- */
const ck=()=>{try{return localStorage.getItem('tn_consent')}catch(e){return null}};
const gpc=()=>!!navigator.globalPrivacyControl;
function stats(){if(ck()!=='yes'||gpc()||window._an||!/^https:\/\//.test(C.ANALYTICS_SRC||''))return;window._an=1;const s=document.createElement('script');s.src=C.ANALYTICS_SRC;s.defer=true;s.referrerPolicy='no-referrer';document.head.appendChild(s)}
function setC(v){try{localStorage.setItem('tn_consent',v)}catch(e){}document.getElementById('cb').hidden=true;stats();toast(v==='yes'?'Analytics allowed.':'Essential only.')}
function consentUI(){document.getElementById('cb').hidden=false;document.querySelector('#cb button').focus()}

/* ---- Legal pages: templates, not legal advice. Business facts come only from APP_CONFIG.BUSINESS. ---- */
const LN={privacy:'Privacy Policy',terms:'Terms of Use',cookies:'Cookie & Storage Policy',refund:'Refund & Returns'};
function BZ(){const b=C.BUSINESS||{},f=(k,l)=>b[k]?esc(b[k]):'<mark>[ILAGAY: '+l+']</mark>';return{n:f('name','business name'),o:f('owner','owner / operator'),a:f('address','business address'),e:f('email','contact email'),p:f('phone','phone'),d:f('dpo','data protection contact'),r:f('refundDays','number of days'),t:f('dtiReg','DTI/SEC/BIR registration, if any')}}
function doc(k){const b=BZ(),h=x=>'<h3>'+x+'</h3>',up='<p class="mut">Last updated: 12 November 2026</p>',who='<p>Operated by '+b.n+' ('+b.o+'), '+b.a+'. Contact: '+b.e+', '+b.p+'. Registration: '+b.t+'.</p>';
if(k==='privacy')return'<h2 id="lgh">Privacy Policy</h2>'+up+who+'<p>This notice explains how we handle personal data in Tindahan, following the Data Privacy Act of 2012 (RA 10173).</p>'+
h('What we collect')+'<p>Your username or email and password (the password is stored only as a hash by our login provider, Supabase), and the business records you enter: products, prices, stock, orders and cash entries. We do not ask for customer names, addresses or payment card numbers. Please do not type them into any field. Servers also log technical data such as IP address and browser type.</p>'+
h('Why we use it')+'<p>To let you sign in and to run your inventory, orders and cash records. We do not sell your data and do not use it for advertising.</p>'+
h('Who handles it')+'<p>Supabase hosts the database and login on our behalf. Its servers may be outside the Philippines. We share data with others only when the law requires.</p>'+
h('How long we keep it')+'<p>Until you ask us to delete your account or records, or for as long as the law requires.</p>'+
h('Your rights')+'<p>You may ask to see, correct, block or delete your data, object to its processing, get a copy, and claim damages as provided by RA 10173. You may also complain to the National Privacy Commission (privacy.gov.ph). Contact: '+b.d+'.</p>';
if(k==='terms')return'<h2 id="lgh">Terms of Use</h2>'+up+who+
h('Using the app')+'<p>Tindahan is for recording your own shop inventory, sales and cash. Keep your password private; you are responsible for activity under your account. Do not use the app for anything unlawful or try to access other people’s data.</p>'+
h('Your records')+'<p>You own the records you enter. Figures such as profit and “safe to take” are calculated from what you enter. They are a tracking aid, not accounting, tax or legal advice.</p>'+
h('Availability')+'<p>We aim to keep the app running but do not promise it will always be available or error-free. Please keep your own backup of important records.</p>'+
h('Changes and law')+'<p>We may update these terms and will change the date above. These terms are governed by the laws of the Republic of the Philippines.</p>';
if(k==='cookies')return'<h2 id="lgh">Cookie &amp; Storage Policy</h2>'+up+
'<p>The app sets no cookies. It uses your browser’s local storage for two things: your sign-in session (needed so you stay logged in) and your choice about analytics. Both are essential to how the app works.</p>'+
'<p>Analytics are off unless you allow them. If no analytics tool is set up, none runs. If your browser sends a Global Privacy Control signal, we treat it as “essential only”.</p>'+
'<p>You can sign out or clear this site’s data in your browser at any time.'+(C.ANALYTICS_SRC?' <button class="b o" onclick="document.getElementById(\'lg\').close();consentUI()">Change analytics choice</button>':'')+'</p>';
return'<h2 id="lgh">Refund &amp; Returns Policy</h2>'+up+who+'<p>This applies to purchases made from '+b.n+' by our customers. Nothing here limits your rights under Philippine consumer law, including the Consumer Act and the Internet Transactions Act.</p>'+
h('When you can ask for a refund, replacement or repair')+'<p>If an item is wrong, damaged, defective or not as described.</p>'+
h('How')+'<p>Contact us at '+b.e+' within '+b.r+' days of purchase with your receipt or order details and a photo. We will tell you the remedy and refund eligible amounts through the same payment method.</p>'+
h('Complaints')+'<p>If we cannot resolve your concern, you may bring it to the DTI.</p>';}
function legal(k){document.getElementById('lgb').innerHTML=doc(k);const d=document.getElementById('lg');d.showModal();d.scrollTop=0;if(/ILAGAY/.test(d.innerHTML))console.warn('Tindahan: fill in APP_CONFIG.BUSINESS before publishing the legal pages.')}
const foot=()=>'<p class="foot">'+Object.keys(LN).map(k=>'<button onclick="legal(\''+k+'\')">'+LN[k]+'</button>').join('')+(C.ANALYTICS_SRC?'<button onclick="consentUI()">Analytics choice</button>':'')+'</p>';

async function load(){const[a,b,c]=await Promise.all([db.from('products').select('*').order('id'),db.from('orders').select('*').order('id'),db.from('ledger').select('*').order('id')]);
const err=a.error||b.error||c.error;if(err)return toast(err.message);S={products:a.data,orders:b.data,ledger:c.data};
cart=cart.filter(c=>S.products.some(p=>p.id==c.pid));draw()}

let mode='in',busy=false;
async function authGo(){if(busy)return;let em=mode==='reset'?'':val('em').toLowerCase();if(em&&!em.includes('@'))em+='@example.com';const pwe=document.getElementById('pw'),pw=pwe?pwe.value:'';
const msg=t=>{document.getElementById('am').innerHTML=t},bad=t=>msg('<span class="bad">'+esc(t)+'</span>'),ok=t=>msg('<span class="good">'+esc(t)+'</span>');
if(mode!=='reset'&&!/^\S+@\S+\.\S+$/.test(em))return bad('Enter your username.');
if(mode!=='forgot'&&pw.length<6)return bad('Password must be at least 6 characters.');
busy=true;msg('Please wait…');let r;
try{
if(mode==='in')r=await db.auth.signInWithPassword({email:em,password:pw});
else if(mode==='forgot')r=await db.auth.resetPasswordForEmail(em,{redirectTo:location.href.split('#')[0]});
else r=await db.auth.updateUser({password:pw});
}catch(e){r={error:e}}
busy=false;
if(r.error)return bad(r.error.message);
if(mode==='forgot')ok('Reset link sent. Check your email.');
else if(mode==='reset')mode='in';
}
async function signOut(){await db.auth.signOut();S={products:[],orders:[],ledger:[]};cart=[];rsel=null;osel=null}
function tp(){const e=document.getElementById('pw'),b=e.nextElementSibling,h=e.type==='password';e.type=h?'text':'password';b.textContent=h?'Hide':'Show';b.setAttribute('aria-pressed',h)}
function loginView(){const m=document.getElementById('main');document.getElementById('nav').style.display='none';m.className='lg';
const rs=mode==='reset',T=rs?'Save new password':'Sign in';
const uf=rs?'':'<label>Username<input id="em" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="Enter username"></label>';
const pf='<label>'+(rs?'New password':'Password')+'<span class="pw"><input id="pw" type="password" autocomplete="'+(rs?'new-password':'current-password')+'" placeholder="Enter password"><button type="button" aria-label="Show or hide password" aria-pressed="false" onclick="tp()">Show</button></span></label>';
m.innerHTML='<div class="card lc" onkeydown="if(event.key===\'Enter\')authGo()"><div class="lb"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg></div><h1>Tindahan</h1><p class="ls">Inventory, profit and sales</p>'+
(!db?'<p class="bad">Supabase is not connected. '+esc(why)+'</p>':'<div class="lf">'+uf+pf+'<button class="b" onclick="authGo()">'+T+'</button><div id="am" role="status" aria-live="polite"></div><p class="cn">By signing in you agree to the <button class="lk" style="font-weight:400" onclick="legal(\'terms\')">Terms</button> and have read the <button class="lk" style="font-weight:400" onclick="legal(\'privacy\')">Privacy Policy</button>.</p></div>')+foot()+'</div>'}

/* ---- Inventory: view + edit in pop-up only ---- */
function openEdit(id){const p=S.products.find(x=>x.id==id);if(!p)return;
document.getElementById('edb').innerHTML='<h3 id="edh">Edit '+esc(pn(p))+'</h3><div class="f" style="flex-direction:column;align-items:stretch"><label>Name<input id="en" value="'+esc(p.name)+'"></label><label>Size (e.g. 100 ml)<input id="ez" value="'+esc(p.size)+'"></label><label>Original price (puhunan)<input id="ec" type="number" min="0" value="'+p.cost+'"></label><label>Selling price<input id="ep" type="number" min="0" value="'+p.price+'"></label></div><p class="mut" style="font-size:13px">Stock: <b>'+p.stock+'</b>. To add stock, use the Restock tab.</p><div class="f"><button class="b" onclick="saveEdit('+id+')">Save</button><button class="b o" onclick="delP('+id+')">Delete product</button></div>';
document.getElementById('ed').showModal()}
async function saveEdit(id){const name=val('en'),size=val('ez'),cost=num('ec'),price=num('ep');
if(!name||cost<=0||price<=0)return toast('Fill in the name, original price and selling price.');
if(await run(db.from('products').update({name,size,cost,price}).eq('id',id))){document.getElementById('ed').close();toast('Saved.');load()}}
async function delP(id){const p=S.products.find(x=>x.id==id);if(!confirm('Delete '+(p?pn(p):'this product')+'? This cannot be undone.'))return;
if(await run(db.from('products').delete().eq('id',id))){document.getElementById('ed').close();if(rsel==id)rsel=null;if(osel==id)osel=null;load()}}

/* ---- Restock: existing product or new product ---- */
function pickR(id){rsel=id;draw();const e=document.getElementById('rqty');if(e)e.focus()}
function rtot(){const p=S.products.find(x=>x.id==rsel),el=document.getElementById('rt');if(!p||!el)return;const q=Math.floor(parseFloat(rq)||0),c=q*p.cost;
el.innerHTML=q>0?'Cost: <b>'+P(c)+'</b> · Cash on hand: '+P(cash())+(c>cash()?' <span class="bad">— short by '+P(c-cash())+'</span>':''):'Cash on hand: '+P(cash())}
async function restock(){const p=S.products.find(x=>x.id==rsel),q=Math.floor(parseFloat(rq)||0);
if(!p)return toast('Click a product first.');if(q<=0)return toast('Enter how many to buy.');const c=q*p.cost;
if(c>cash())return toast('Kulang ang pondo: need '+P(c)+', you have '+P(cash())+' (short by '+P(c-cash())+').');
if(await run(db.from('products').update({stock:p.stock+q}).eq('id',p.id))&&await run(db.from('ledger').insert({t:'restock',a:c,note:q+' × '+pn(p)+' @ '+P(p.cost)}))){await load();toast('Restocked '+q+' '+pn(p)+' for '+P(c))}}
async function newProduct(){const name=val('pn'),size=val('pz'),cost=num('pc'),price=num('pp'),stock=Math.floor(num('ps')),c=stock*cost;
if(!name||cost<=0||price<=0)return toast('Fill in the name, original price and selling price.');
if(c>cash())return toast('Kulang ang pondo: need '+P(c)+', you have '+P(cash())+' (short by '+P(c-cash())+').');
if(!await run(db.from('products').insert({name,size,cost,price,stock})))return;
if(stock>0)await run(db.from('ledger').insert({t:'restock',a:c,note:stock+' × '+name+(size?' '+size:'')+' @ '+P(cost)}));
await load();toast('Added '+name+(size?' '+size:'')+(stock>0?' with '+stock+' in stock ('+P(c)+')':''))}

/* ---- Sales (walk-in): cart, Buy, Cancel ---- */
function pickO(id){osel=id;draw();const e=document.getElementById('oq');if(e)e.focus()}
function addCart(){const p=S.products.find(x=>x.id==osel),q=Math.floor(num('oq'));
if(!p||q<=0)return toast('Click a product and enter the quantity.');
const l=cart.find(c=>c.pid==p.id),n=(l?l.q:0)+q;
if(n>p.stock)return toast('Only '+p.stock+' in stock.');
if(l)l.q=n;else cart.push({pid:p.id,q:n});osel=null;oqv=1;draw()}
function rmCart(pid){cart=cart.filter(c=>c.pid!=pid);draw()}
async function sell(){if(selling)return;let L=cart.map(c=>({pid:c.pid,q:c.q}));
if(!L.length){const q=Math.floor(num('oq'));if(!osel||q<=0)return toast('Click a product and enter the quantity.');L=[{pid:osel,q}]}
for(const l of L){const p=S.products.find(x=>x.id==l.pid);if(!p||l.q>p.stock)return toast('Not enough stock for '+(p?pn(p):'a product')+'.')}
selling=true;let ok=true;
for(const l of L){const p=S.products.find(x=>x.id==l.pid);
ok=await run(db.from('products').update({stock:p.stock-l.q}).eq('id',p.id))&&await run(db.from('orders').insert({pid:p.id,name:pn(p),qty:l.q,cost:p.cost,price:p.price}));if(!ok)break}
selling=false;cart=[];osel=null;oqv=1;await load();if(ok)toast('Sale created. Press Buy when paid.')}
async function adv(id){const o=S.orders.find(x=>x.id==id);if(!o)return;
if(await run(db.from('orders').update({st:'Delivered'}).eq('id',id))&&await run(db.from('ledger').insert({t:'sale',a:o.price*o.qty,note:o.qty+' × '+o.name}))){await load();toast('Bought: +'+P(o.price*o.qty))}}
async function buyAll(){const L=pending();if(!L.length)return;
if(!confirm('Mark all '+L.length+' pending sale(s) as bought? '+P(sum(L,o=>o.price*o.qty))+' goes into cash.'))return;
for(const o of L){if(!(await run(db.from('orders').update({st:'Delivered'}).eq('id',o.id))&&await run(db.from('ledger').insert({t:'sale',a:o.price*o.qty,note:o.qty+' × '+o.name}))))break}
await load();toast('All pending sales marked as bought.')}
async function cancel(id){const o=S.orders.find(x=>x.id==id),p=S.products.find(x=>x.id==o.pid);
if(!confirm('Cancel '+o.qty+' × '+o.name+'? The items go back to stock.'))return;
if(await run(db.from('orders').update({st:'Cancelled'}).eq('id',id))&&(!p||await run(db.from('products').update({stock:p.stock+o.qty}).eq('id',p.id))))load()}

/* ---- Cash ---- */
function preview(){const a=num('wa'),el=document.getElementById('wn');wd=a;
if(a<=0){el.innerHTML='';return}
if(a>cash())el.innerHTML='<span class="bad">Not enough cash. You only have '+P(cash())+'.</span>';
else if(a>safe()){const left=cash()-a;el.innerHTML='<span class="bad">This is more than your profit ('+P(safe())+'). It eats into your puhunan: cash left would be '+P(left)+', '+(left<need()?'short by '+P(need()-left)+' for restocking.':'still enough to restock.')+'</span>'}
else el.innerHTML='<span class="good">Safe — this comes from your profit.</span> Cash left: '+P(cash()-a)}
async function withdraw(){const a=num('wa');if(a<=0)return toast('Enter an amount.');if(a>cash())return toast('Not enough cash.');
wd='';if(await run(db.from('ledger').insert({t:'withdraw',a,note:val('wt')||'Personal use'}))){await load();toast('Withdrew '+P(a))}}
async function capital(){const a=num('ca');if(a<=0)return toast('Enter an amount.');if(await run(db.from('ledger').insert({t:'capital',a,note:'Added capital'})))load()}

function chart(){const m=[];const n=new Date();for(let i=5;i>=0;i--){const d=new Date(n.getFullYear(),n.getMonth()-i,1);m.push({k:d.getFullYear()+'-'+d.getMonth(),l:d.toLocaleString('en',{month:'short'}),v:0})}
dels().forEach(o=>{const d=new Date(o.d),x=m.find(y=>y.k==d.getFullYear()+'-'+d.getMonth());if(x)x.v+=(o.price-o.cost)*o.qty});
const mx=Math.max(1,...m.map(x=>x.v));
return '<svg viewBox="0 0 360 170" width="100%" role="img" aria-label="Profit per month: '+m.map(x=>x.l+' '+P(x.v)).join(', ')+'">'+m.map((x,i)=>{const h=x.v/mx*110,X=14+i*57;return '<rect x="'+X+'" y="'+(130-h)+'" width="40" height="'+h+'" rx="5" fill="var(--acc)"/><text x="'+(X+20)+'" y="'+(124-h)+'" text-anchor="middle" font-size="10" fill="var(--ink)">'+(x.v?P(x.v):'')+'</text><text x="'+(X+20)+'" y="150" text-anchor="middle" font-size="11" fill="var(--mut)">'+x.l+'</text>'}).join('')+'</svg>'}

function dash(){const L=low(),short=Math.max(0,need()-cash()),st=pending();
const top={};dels().forEach(o=>top[o.name]=(top[o.name]||0)+(o.price-o.cost)*o.qty);const t=Object.entries(top).sort((a,b)=>b[1]-a[1]).slice(0,6),mx=t[0]?t[0][1]:1;
return '<h2>Welcome back!</h2><div class="grid">'+
[[S.products.length,'Products'],[sum(S.products,p=>p.stock),'Units in stock'],[P(cash()),'Cash on hand'],['<span class="good">'+P(profit())+'</span>','Total profit'],[st.length,'Pending sales'],['<span class="'+(L.length?'bad':'')+'">'+S.products.filter(p=>p.stock==0).length+'</span>','Out of stock']].map(x=>'<div class="card k"><b>'+x[0]+'</b><span>'+x[1]+'</span></div>').join('')+'</div>'+
'<div class="grid w"><div class="card"><h3>Profit vs. restock money</h3><p style="margin:0 0 6px">Profit you can take: <b class="good">'+P(safe())+'</b><br>Already taken: <b>'+P(taken())+'</b></p>'+
(L.length?'<div class="note">'+L.length+' item(s) low or out. Restocking to 10 each costs <b>'+P(need())+'</b>. '+(short?'<span class="bad">Cash is short by '+P(short)+'.</span>':'<span class="good">You have enough cash.</span>')+'</div>':'<div class="note good">Stock levels look fine.</div>')+
'</div><div class="card"><h3>Profit per month</h3>'+chart()+'</div><div class="card"><h3>Top products by profit</h3>'+(t.length?t.map(([n,v])=>'<div class="bar"><span>'+esc(n)+'</span><i style="width:'+Math.max(6,v/mx*100)+'px"></i><span>'+P(v)+'</span></div>').join(''):'<p>No bought sales yet. Press Buy on a sale to see profit here.</p>')+'</div></div>'}

function inv(){return '<h2>Inventory</h2><div class="card sc"><table><caption class="sr">Products</caption><tr><th scope="col">Product</th><th scope="col">Original price</th><th scope="col">Selling price</th><th scope="col">Profit / unit</th><th scope="col">Stock</th><th scope="col"><span class="sr">Edit</span></th></tr>'+
(S.products.length?S.products.map(p=>'<tr><td><button class="lk" onclick="openEdit('+p.id+')" aria-label="Edit '+esc(pn(p))+'">'+esc(p.name)+'</button>'+(p.size?'<br><small class="mut">'+esc(p.size)+'</small>':'')+'</td><td>'+P(p.cost)+'</td><td>'+P(p.price)+'</td><td class="'+(p.price>p.cost?'good':'bad')+'">'+P(p.price-p.cost)+'</td><td class="'+(p.stock==0?'bad':p.stock<=5?'warn':'')+'"><b>'+p.stock+'</b>'+(p.stock==0?' <small>(out)</small>':p.stock<=5?' <small>(low)</small>':'')+'</td><td><button class="b o" onclick="openEdit('+p.id+')">Edit</button></td></tr>').join(''):'<tr><td colspan="6" class="mut">No products yet. Add one in Restock.</td></tr>')+
'</table></div><p class="mut" style="font-size:13px">Click a product to edit it. To add stock or a new product, go to the Restock tab.</p>'}

function rsv(){const p=S.products.find(x=>x.id==rsel);
return '<h2>Restock</h2><div class="grid w"><div class="card"><h3>Restock a product</h3><p class="mut" style="margin-top:0;font-size:13px">Click a product from your inventory, then enter how many to buy.</p><div class="pk">'+
(S.products.length?S.products.map(x=>'<button class="'+(x.id==rsel?'on':'')+'" aria-pressed="'+(x.id==rsel)+'" onclick="pickR('+x.id+')">'+esc(pn(x))+'<small>'+x.stock+' in stock · '+P(x.cost)+' each</small></button>').join(''):'<p class="mut">No products yet. Add a new one on the right.</p>')+'</div>'+
(p?'<div class="f" style="margin-top:12px"><label>Quantity to buy<input id="rqty" type="number" min="1" value="'+esc(rq)+'" oninput="rq=this.value;rtot()" style="width:110px"></label><button class="b" onclick="restock()">Restock '+esc(pn(p))+'</button></div><div id="rt" class="note" aria-live="polite"></div>':'')+'</div>'+
'<div class="card"><h3>New product</h3><div class="f"><label>Name<input id="pn"></label><label>Size (e.g. 100 ml)<input id="pz" placeholder="100 ml"></label><label>Original price (puhunan)<input id="pc" type="number" min="0"></label><label>Selling price<input id="pp" type="number" min="0"></label><label>Stock to buy<input id="ps" type="number" min="0" value="0"></label><button class="b" onclick="newProduct()">Add product</button></div></div></div>'+
'<p class="mut" style="font-size:13px">Restocking takes the cost out of your cash on hand ('+P(cash())+').</p>'}

function ord(){const lines=cart.map(c=>({p:S.products.find(x=>x.id==c.pid),q:c.q})).filter(l=>l.p),tot=sum(lines,l=>l.p.price*l.q),pd=pending(),
lab={Pending:'Pending',Out:'Pending',Delivered:'Bought',Cancelled:'Cancelled'};
return '<h2>Sales</h2><div class="grid w"><div class="card"><h3>New sale</h3><p class="mut" style="margin-top:0;font-size:13px">Click a product, enter the quantity, then press Sale. Use Add to cart to sell several products at once.</p><div class="pk">'+
(S.products.length?S.products.map(x=>'<button class="'+(x.id==osel?'on':'')+'" aria-pressed="'+(x.id==osel)+'" '+(x.stock<1?'disabled':'')+' onclick="pickO('+x.id+')">'+esc(pn(x))+'<small>'+P(x.price)+' · '+x.stock+' left</small></button>').join(''):'<p class="mut">No products yet.</p>')+'</div>'+
'<div class="f" style="margin-top:12px"><label>Qty<input id="oq" type="number" min="1" value="'+esc(oqv)+'" oninput="oqv=this.value" style="width:80px"></label><button class="b o" onclick="addCart()">Add to cart</button><button class="b" onclick="sell()">Sale</button></div></div>'+
'<div class="card sc"><h3>Cart ('+lines.length+')</h3>'+(lines.length?'<table><caption class="sr">Cart</caption><tr><th scope="col">Item</th><th scope="col">Qty</th><th scope="col">Subtotal</th><th scope="col"><span class="sr">Remove</span></th></tr>'+lines.map(l=>'<tr><td>'+esc(pn(l.p))+'</td><td>'+l.q+'</td><td>'+P(l.p.price*l.q)+'</td><td><button class="b o" onclick="rmCart('+l.p.id+')" aria-label="Remove '+esc(pn(l.p))+'">✕</button></td></tr>').join('')+'</table><p style="margin:10px 0 0"><b>Total: '+P(tot)+'</b></p><p class="mut" style="font-size:13px;margin:4px 0 0">Press Sale to create all items.</p>':'<p class="mut">Cart is empty.</p>')+'</div></div>'+
'<div class="card sc"><div class="f" style="justify-content:space-between;align-items:center;margin-bottom:8px"><h3 style="margin:0">Sales</h3>'+(pd.length>1?'<button class="b o" onclick="buyAll()">Buy all pending ('+pd.length+')</button>':'')+'</div><table><caption class="sr">Sales</caption><tr><th scope="col">Date</th><th scope="col">Item</th><th scope="col">Total</th><th scope="col">Profit</th><th scope="col">Status</th><th scope="col"><span class="sr">Actions</span></th></tr>'+
S.orders.slice().reverse().map(o=>'<tr><td>'+new Date(o.d).toLocaleDateString('en-PH',{month:'short',day:'numeric'})+'</td><td>'+o.qty+' × '+esc(o.name)+'</td><td>'+P(o.price*o.qty)+'</td><td class="good">'+P((o.price-o.cost)*o.qty)+'</td><td><span class="pill p-'+o.st+'">'+(lab[o.st]||esc(o.st))+'</span></td><td>'+((o.st==='Pending'||o.st==='Out')?'<button class="b" onclick="adv('+o.id+')">Buy</button> <button class="b o" onclick="cancel('+o.id+')">Cancel</button>':'')+'</td></tr>').join('')+'</table></div><p class="mut" style="font-size:13px">Money goes into your cash only after you press Buy. Cancel puts the items back in stock. Do not type customer names or addresses here.</p>'}

function cashv(){const L=S.ledger.slice().reverse();
return '<h2>Cash &amp; withdrawals</h2><div class="grid"><div class="card k"><b>'+P(cash())+'</b><span>Cash on hand</span></div><div class="card k"><b class="good">'+P(safe())+'</b><span>Profit you can take</span></div><div class="card k"><b>'+P(taken())+'</b><span>Taken so far</span></div><div class="card k"><b class="'+(need()>cash()?'bad':'')+'">'+P(need())+'</b><span>Needed to restock low items</span></div></div>'+
'<div class="grid w"><div class="card"><h3>Take money</h3><div class="f"><label>Amount<input id="wa" type="number" min="0" value="'+wd+'" oninput="preview()"></label><label>For<input id="wt" placeholder="Personal use"></label><button class="b" onclick="withdraw()">Take money</button></div><div id="wn" class="note" aria-live="polite"></div></div>'+
'<div class="card"><h3>Add capital</h3><div class="f"><label>Amount<input id="ca" type="number" min="0"></label><button class="b" onclick="capital()">Add</button></div></div></div>'+
'<div class="card sc"><h3>History</h3><table><caption class="sr">Cash history</caption><tr><th scope="col">Date</th><th scope="col">Type</th><th scope="col">Details</th><th scope="col">Amount</th></tr>'+L.map(e=>{const inn=e.t==='sale'||e.t==='capital';return '<tr><td>'+new Date(e.d).toLocaleDateString('en-PH',{month:'short',day:'numeric'})+'</td><td>'+({sale:'Sale',capital:'Capital',restock:'Restock',withdraw:'Withdrawal'})[e.t]+'</td><td>'+esc(e.note)+'</td><td class="'+(inn?'good':'bad')+'">'+(inn?'+':'−')+P(e.a)+'</td></tr>'}).join('')+'</table></div>'}

function setv(){return '<h2>Account settings</h2><p style="margin-top:-6px">Current username: <b>'+esc(uname(user.email))+'</b></p><div class="grid w"><div class="card"><h3>Change username</h3><div class="f"><label>New username<input id="nu" autocomplete="off"></label><label>Current password<input id="cu_pw" type="password" autocomplete="current-password"></label><button class="b" onclick="chgUser()">Save username</button></div></div><div class="card"><h3>Change password</h3><div class="f"><label>Current password<input id="cp_old" type="password" autocomplete="current-password"></label><label>New password<input id="cp_new" type="password" autocomplete="new-password"></label><label>Confirm new password<input id="cp_new2" type="password" autocomplete="new-password"></label><button class="b" onclick="chgPass()">Save password</button></div></div></div><p class="mut" style="font-size:13px">To access, correct or delete your data, see the Privacy Policy below.</p>'}
async function chgUser(){let n=val('nu').toLowerCase();const pw=document.getElementById('cu_pw').value;
if(!n)return toast('Enter a new username.');if(!n.includes('@'))n+='@example.com';
if(!/^\S+@\S+\.\S+$/.test(n))return toast('That username is not valid.');
if(!await run(db.auth.signInWithPassword({email:user.email,password:pw})))return;
const{error}=await db.auth.updateUser({email:n});if(error)return toast(error.message);
const r=await db.auth.getUser();if(r.data&&r.data.user)user=r.data.user;
toast(user.email===n?'Username changed.':'Saved, but not applied yet. Turn off "Confirm email" in Supabase (Authentication → Providers → Email).');draw()}
async function chgPass(){const o=document.getElementById('cp_old').value,a=document.getElementById('cp_new').value,b=document.getElementById('cp_new2').value;
if(a.length<6)return toast('New password must be at least 6 characters.');if(a!==b)return toast('The new passwords do not match.');
if(!await run(db.auth.signInWithPassword({email:user.email,password:o})))return;
if(await run(db.auth.updateUser({password:a}))){toast('Password changed.');draw()}}

function draw(){if(!user){loginView();return}document.getElementById('nav').style.display='';document.getElementById('main').className='';const T=[['dash','Dashboard'],['inv','Inventory'],['rs','Restock'],['ord','Sales'],['cash','Cash & withdrawals'],['set','Settings']];
document.getElementById('nav').innerHTML='<h1>Tindahan</h1>'+T.map(t=>'<button '+(tab==t[0]?'class="on" aria-current="page"':'')+' onclick="go(\''+t[0]+'\')">'+esc(t[1])+'</button>').join('');
document.getElementById('nav').insertAdjacentHTML('beforeend','<button onclick="signOut()" style="margin-top:auto">Sign out<br><small>'+esc(uname(user.email))+'</small></button>');
document.getElementById('main').innerHTML=({dash,inv,rs:rsv,ord,cash:cashv,set:setv})[tab]()+foot();if(tab=='cash'&&wd)preview();if(tab=='rs'&&rsel)rtot()}
let why='';
if(!window.APP_CONFIG)why='config.js was not found. Keep it in the same folder as index.html.';
else if(!C.SUPABASE_URL||!C.SUPABASE_ANON_KEY||C.SUPABASE_URL.includes('YOUR-PROJECT')||C.SUPABASE_ANON_KEY.includes('YOUR-ANON'))why='config.js still has the placeholder values.';
else if(typeof supabase==='undefined')why='The Supabase library did not load. Check your internet connection or ad-blocker.';
else try{db=supabase.createClient(C.SUPABASE_URL,C.SUPABASE_ANON_KEY)}catch(e){why=e.message}
stats();if(C.ANALYTICS_SRC&&ck()===null&&!gpc())document.getElementById('cb').hidden=false;
if(db){db.auth.onAuthStateChange((ev,s)=>{if(ev==='PASSWORD_RECOVERY'){mode='reset';user=null;loginView();return}user=s?s.user:null;if(user)load();else{mode='in';draw()}})}else draw();
</script></body></html>
