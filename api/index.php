<!doctype html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Tindahan Inventory</title>
<style>
:root{--bg:#cfe3ec;--side:#2a5468;--card:#f6fafc;--ink:#173a4d;--mut:#5d7f90;--line:#c5d9e2;--acc:#4f7a90;--good:#2f8f6b;--bad:#c2603a;--warn:#b57a10;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media(prefers-color-scheme:dark){:root{--bg:#10222b;--side:#0b181e;--card:#183241;--ink:#e3f0f5;--mut:#92b3c2;--line:#2a4a5a;--acc:#7fb0c8;--good:#5fd0a5;--bad:#f09068;--warn:#e8b64c}}
*{box-sizing:border-box}html{scroll-padding-top:env(safe-area-inset-top,0px)}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.45 "Nunito",system-ui,sans-serif}
.app{display:flex;min-height:100vh}
nav{width:200px;background:var(--side);color:#e3f0f5;padding:22px 12px;display:flex;flex-direction:column;gap:4px;flex:none}
nav h1{font-size:17px;margin:0 8px 18px}
nav button{all:unset;cursor:pointer;padding:10px 14px;border-radius:10px 0 0 10px;color:#b9d3df}
nav button.on{background:var(--card);color:var(--ink);font-weight:700}
nav button:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible{outline:2px solid var(--acc);outline-offset:2px}
main{flex:1;padding:18px;min-width:0}
h2{margin:0 0 14px;font-size:22px}h3{margin:0 0 10px;font-size:16px}
.grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));margin-bottom:14px}
.grid.w{grid-template-columns:repeat(auto-fit,minmax(320px,1fr))}
.card{background:var(--card);border-radius:12px;padding:16px;min-width:0}
.k{border:1px solid var(--line)}.k b{display:block;font-size:26px;line-height:1.2}.k span{color:var(--mut);font-size:13px}
.good{color:var(--good)}.bad{color:var(--bad)}.warn{color:var(--warn)}
table{width:100%;border-collapse:collapse}th{text-align:left;color:var(--mut);font-weight:600;font-size:13px}
td,th{padding:8px 6px;border-bottom:1px solid var(--line);vertical-align:middle}
.sc{overflow-x:auto}
input,select{font:inherit;color:var(--ink);background:var(--bg);border:1px solid var(--line);border-radius:8px;padding:7px 9px;min-width:0}
td input{width:84px}td input.q{width:56px}
.f{display:flex;flex-wrap:wrap;gap:8px;align-items:end}.f label{display:flex;flex-direction:column;font-size:12px;color:var(--mut);gap:3px}
button.b{font:inherit;cursor:pointer;border:0;border-radius:8px;padding:8px 13px;background:var(--side);color:#fff;font-weight:600}
button.b.o{background:transparent;color:var(--ink);border:1px solid var(--line)}
.pill{display:inline-block;padding:2px 9px;border-radius:99px;font-size:12px;font-weight:700;background:var(--bg)}
.p-Pending{color:var(--warn)}.p-Out{color:var(--acc)}.p-Delivered{color:var(--good)}.p-Cancelled{color:var(--mut)}
.bar{display:flex;align-items:center;gap:8px;margin:7px 0;font-size:13px}.bar i{display:block;height:10px;border-radius:5px;background:var(--acc)}.bar span:first-child{width:120px;flex:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#toast{position:fixed;left:50%;bottom:calc(20px + env(safe-area-inset-bottom,0px));transform:translateX(-50%);background:var(--side);color:#fff;padding:10px 16px;border-radius:10px;display:none;max-width:90vw;z-index:9}
.note{padding:10px 12px;border-radius:8px;background:var(--bg);font-size:14px;margin-top:10px}
@media(max-width:720px){.app{flex-direction:column}nav{width:auto;flex-direction:row;overflow-x:auto;padding:10px;position:sticky;top:0}nav h1{display:none}nav button{border-radius:10px;white-space:nowrap}}
</style></head><body>
<div class="app"><nav id="nav"></nav><main id="main"></main></div><div id="toast" role="status"></div>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.45.4/dist/umd/supabase.js"></script>
<script>if(typeof supabase==='undefined')document.write('<script src="https://unpkg.com/@supabase/supabase-js@2.45.4/dist/umd/supabase.js"><\/script>')</script>
<script src="config.js"></script>
<script>
/* Backup config: used only if config.js is missing or not found next to this file. */
window.APP_CONFIG=window.APP_CONFIG||{SUPABASE_URL:'https://qejpamcqqemisvvkgiq.supabase.co',SUPABASE_ANON_KEY:'sb_publishable_lm5YQ4cXV9A2sDvUNFdHyg_t_wkfvrK'};
</script>
<script>
const DAY=864e5,C=window.APP_CONFIG||{};let S={products:[],orders:[],ledger:[]},tab='dash',wd='',user=null,db=null;
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const P=n=>'₱'+Math.round(n).toLocaleString('en-PH');
const sum=(a,f)=>a.reduce((s,x)=>s+f(x),0);
const cash=()=>sum(S.ledger,e=>e.t==='sale'||e.t==='capital'?e.a:-e.a);
const dels=()=>S.orders.filter(o=>o.st==='Delivered');
const profit=()=>sum(dels(),o=>(o.price-o.cost)*o.qty);
const taken=()=>sum(S.ledger.filter(e=>e.t==='withdraw'),e=>e.a);
const safe=()=>Math.max(0,profit()-taken());
const low=()=>S.products.filter(p=>p.stock<=5);
const need=()=>sum(low(),p=>(10-p.stock)*p.cost);
function toast(m){const t=document.getElementById('toast');t.textContent=m;t.style.display='block';clearTimeout(toast.h);toast.h=setTimeout(()=>t.style.display='none',3600)}
function go(t){tab=t;wd='';draw()}
const num=id=>parseFloat(document.getElementById(id).value)||0;
const val=id=>document.getElementById(id).value.trim();
const run=async q=>{const{error}=await q;if(error){toast(error.message);return false}return true};

async function load(){const[a,b,c]=await Promise.all([db.from('products').select('*').order('id'),db.from('orders').select('*').order('id'),db.from('ledger').select('*').order('id')]);
const err=a.error||b.error||c.error;if(err)return toast(err.message);S={products:a.data,orders:b.data,ledger:c.data};draw()}

let mode='in',busy=false;
function setMode(m){mode=m;loginView()}
async function authGo(){if(busy)return;const em=mode==='reset'?'':val('em'),pwe=document.getElementById('pw'),pw=pwe?pwe.value:'';
const msg=t=>{document.getElementById('am').innerHTML=t},bad=t=>msg('<span class="bad">'+esc(t)+'</span>'),ok=t=>msg('<span class="good">'+esc(t)+'</span>');
if(mode!=='reset'&&!/^\S+@\S+\.\S+$/.test(em))return bad('Enter a valid email.');
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
async function signOut(){await db.auth.signOut();S={products:[],orders:[],ledger:[]}}
function loginView(){document.getElementById('nav').innerHTML='';
const T={in:'Sign in',up:'Create account',forgot:'Reset password',reset:'Set a new password'}[mode];
const f=(id,l,ac)=>'<label>'+l+'<input id="'+id+'" type="'+(id==='em'?'email':'password')+'" autocomplete="'+ac+'"></label>';
const lk=(m,t)=>'<button class="b o" onclick="setMode(\''+m+'\')">'+t+'</button>';
document.getElementById('main').innerHTML='<div class="card" style="max-width:380px;margin:8vh auto" onkeydown="if(event.key===\'Enter\')authGo()"><h2>Tindahan</h2><h3>'+T+'</h3>'+
(!db?'<p class="bad">Supabase is not connected. '+esc(why)+'</p>':
'<div class="f" style="flex-direction:column;align-items:stretch">'+(mode!=='reset'?f('em','Email','email'):'')+(mode!=='forgot'?f('pw',mode==='in'?'Password':'New password',mode==='in'?'current-password':'new-password'):'')+
'<button class="b" onclick="authGo()">'+T+'</button>'+(mode==='in'?lk('forgot','Forgot password?'):mode==='reset'?'':lk('in','Back to sign in'))+'<div id="am" role="status" aria-live="polite"></div></div>')+'</div>'}

async function addProduct(){const name=val('pn'),cost=num('pc'),price=num('pp'),stock=Math.floor(num('ps'));
if(!name||cost<=0||price<=0)return toast('Fill in the name, original price and selling price.');
if(await run(db.from('products').insert({name,cost,price,stock})))load()}
async function setP(id,f,v){if(await run(db.from('products').update({[f]:Math.max(0,parseFloat(v)||0)}).eq('id',id)))load()}
async function delP(id){if(await run(db.from('products').delete().eq('id',id)))load()}
async function restock(id){const p=S.products.find(x=>x.id==id),q=Math.floor(parseFloat(document.getElementById('r'+id).value)||0);
if(q<=0)return toast('Enter how many to buy.');const c=q*p.cost;
if(c>cash())return toast('Kulang ang pondo: need '+P(c)+', you have '+P(cash())+' (short by '+P(c-cash())+').');
if(await run(db.from('products').update({stock:p.stock+q}).eq('id',id))&&await run(db.from('ledger').insert({t:'restock',a:c,note:q+' × '+p.name+' @ '+P(p.cost)}))){await load();toast('Restocked '+q+' '+p.name+' for '+P(c))}}
async function addOrder(){const p=S.products.find(x=>x.id==val('op')),q=Math.floor(num('oq'));
if(!p||q<=0)return toast('Pick a product and quantity.');if(q>p.stock)return toast('Only '+p.stock+' in stock.');
if(await run(db.from('products').update({stock:p.stock-q}).eq('id',p.id))&&await run(db.from('orders').insert({pid:p.id,name:p.name,qty:q,cost:p.cost,price:p.price,cust:val('oc')||'Walk-in',addr:val('oa')||'-'})))load()}
async function adv(id){const o=S.orders.find(x=>x.id==id),n=o.st==='Pending'?'Out':'Delivered';
if(await run(db.from('orders').update({st:n}).eq('id',id))&&(n!=='Delivered'||await run(db.from('ledger').insert({t:'sale',a:o.price*o.qty,note:o.qty+' × '+o.name}))))load()}
async function cancel(id){const o=S.orders.find(x=>x.id==id),p=S.products.find(x=>x.id==o.pid);
if(await run(db.from('orders').update({st:'Cancelled'}).eq('id',id))&&(!p||await run(db.from('products').update({stock:p.stock+o.qty}).eq('id',p.id))))load()}
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
return '<svg viewBox="0 0 360 170" width="100%" role="img" aria-label="Profit per month">'+m.map((x,i)=>{const h=x.v/mx*110,X=14+i*57;return '<rect x="'+X+'" y="'+(130-h)+'" width="40" height="'+h+'" rx="5" fill="var(--acc)"/><text x="'+(X+20)+'" y="'+(124-h)+'" text-anchor="middle" font-size="10" fill="var(--ink)">'+(x.v?P(x.v):'')+'</text><text x="'+(X+20)+'" y="150" text-anchor="middle" font-size="11" fill="var(--mut)">'+x.l+'</text>'}).join('')+'</svg>'}

function dash(){const L=low(),short=Math.max(0,need()-cash()),st=S.orders.filter(o=>o.st==='Pending'||o.st==='Out');
const top={};dels().forEach(o=>top[o.name]=(top[o.name]||0)+(o.price-o.cost)*o.qty);const t=Object.entries(top).sort((a,b)=>b[1]-a[1]).slice(0,6),mx=t[0]?t[0][1]:1;
return '<h2>Welcome back!</h2><div class="grid">'+
[[S.products.length,'Products'],[sum(S.products,p=>p.stock),'Units in stock'],[P(cash()),'Cash on hand'],['<span class="good">'+P(profit())+'</span>','Total profit'],[st.length,'Orders to deliver'],['<span class="'+(L.length?'bad':'')+'">'+S.products.filter(p=>p.stock==0).length+'</span>','Out of stock']].map(x=>'<div class="card k"><b>'+x[0]+'</b><span>'+x[1]+'</span></div>').join('')+'</div>'+
'<div class="grid w"><div class="card"><h3>Profit vs. restock money</h3><p style="margin:0 0 6px">Profit you can take: <b class="good">'+P(safe())+'</b><br>Already taken: <b>'+P(taken())+'</b></p>'+
(L.length?'<div class="note">'+L.length+' item(s) low or out. Restocking to 10 each costs <b>'+P(need())+'</b>. '+(short?'<span class="bad">Cash is short by '+P(short)+'.</span>':'<span class="good">You have enough cash.</span>')+'</div>':'<div class="note good">Stock levels look fine.</div>')+
'</div><div class="card"><h3>Profit per month</h3>'+chart()+'</div><div class="card"><h3>Top products by profit</h3>'+(t.length?t.map(([n,v])=>'<div class="bar"><span>'+esc(n)+'</span><i style="width:'+Math.max(6,v/mx*100)+'px"></i><span>'+P(v)+'</span></div>').join(''):'<p>No delivered orders yet.</p>')+'</div></div>'}

function inv(){return '<h2>Inventory</h2><div class="card" style="margin-bottom:14px"><h3>Add product</h3><div class="f"><label>Name<input id="pn"></label><label>Original price (puhunan)<input id="pc" type="number" min="0"></label><label>Selling price<input id="pp" type="number" min="0"></label><label>Stock<input id="ps" type="number" min="0" value="0"></label><button class="b" onclick="addProduct()">Add product</button></div></div>'+
'<div class="card sc"><table><tr><th>Product</th><th>Original price</th><th>Selling price</th><th>Profit / unit</th><th>Stock</th><th>Buy more</th><th></th></tr>'+
S.products.map(p=>'<tr><td>'+esc(p.name)+'</td><td><input type="number" value="'+p.cost+'" onchange="setP('+p.id+',\'cost\',this.value)" aria-label="Original price"></td><td><input type="number" value="'+p.price+'" onchange="setP('+p.id+',\'price\',this.value)" aria-label="Selling price"></td><td class="'+(p.price>p.cost?'good':'bad')+'">'+P(p.price-p.cost)+'</td><td class="'+(p.stock==0?'bad':p.stock<=5?'warn':'')+'"><b>'+p.stock+'</b></td><td><input class="q" id="r'+p.id+'" type="number" min="1" value="10" aria-label="Quantity to buy"> <button class="b o" onclick="restock('+p.id+')">Restock</button></td><td><button class="b o" onclick="delP('+p.id+')" aria-label="Delete">✕</button></td></tr>').join('')+'</table></div><p style="color:var(--mut);font-size:13px">Restock takes the cost out of your cash on hand ('+P(cash())+').</p>'}

function ord(){const lab={Pending:'Pending',Out:'Out for delivery',Delivered:'Delivered',Cancelled:'Cancelled'},nx={Pending:'Send out',Out:'Mark delivered'};
return '<h2>Orders & delivery</h2><div class="card" style="margin-bottom:14px"><h3>New order</h3><div class="f"><label>Product<select id="op">'+S.products.filter(p=>p.stock>0).map(p=>'<option value="'+p.id+'">'+esc(p.name)+' ('+p.stock+' left)</option>').join('')+'</select></label><label>Qty<input id="oq" type="number" min="1" value="1" style="width:70px"></label><label>Customer<input id="oc"></label><label>Address<input id="oa"></label><button class="b" onclick="addOrder()">Create order</button></div></div>'+
'<div class="card sc"><table><tr><th>Order</th><th>Customer</th><th>Total</th><th>Profit</th><th>Status</th><th></th></tr>'+
S.orders.slice().reverse().map(o=>'<tr><td>'+o.qty+' × '+esc(o.name)+'</td><td>'+esc(o.cust)+'<br><small style="color:var(--mut)">'+esc(o.addr)+'</small></td><td>'+P(o.price*o.qty)+'</td><td class="good">'+P((o.price-o.cost)*o.qty)+'</td><td><span class="pill p-'+o.st+'">'+lab[o.st]+'</span></td><td>'+(nx[o.st]?'<button class="b" onclick="adv('+o.id+')">'+nx[o.st]+'</button> <button class="b o" onclick="cancel('+o.id+')">Cancel</button>':'')+'</td></tr>').join('')+'</table></div><p style="color:var(--mut);font-size:13px">Money from an order goes into your cash only after it is marked delivered.</p>'}

function cashv(){const L=S.ledger.slice().reverse();
return '<h2>Cash & withdrawals</h2><div class="grid"><div class="card k"><b>'+P(cash())+'</b><span>Cash on hand</span></div><div class="card k"><b class="good">'+P(safe())+'</b><span>Profit you can take</span></div><div class="card k"><b>'+P(taken())+'</b><span>Taken so far</span></div><div class="card k"><b class="'+(need()>cash()?'bad':'')+'">'+P(need())+'</b><span>Needed to restock low items</span></div></div>'+
'<div class="grid w"><div class="card"><h3>Take money</h3><div class="f"><label>Amount<input id="wa" type="number" min="0" value="'+wd+'" oninput="preview()"></label><label>For<input id="wt" placeholder="Personal use"></label><button class="b" onclick="withdraw()">Take money</button></div><div id="wn" class="note" aria-live="polite"></div></div>'+
'<div class="card"><h3>Add capital</h3><div class="f"><label>Amount<input id="ca" type="number" min="0"></label><button class="b" onclick="capital()">Add</button></div></div></div>'+
'<div class="card sc"><h3>History</h3><table><tr><th>Date</th><th>Type</th><th>Details</th><th>Amount</th></tr>'+L.map(e=>{const inn=e.t==='sale'||e.t==='capital';return '<tr><td>'+new Date(e.d).toLocaleDateString('en-PH',{month:'short',day:'numeric'})+'</td><td>'+({sale:'Sale',capital:'Capital',restock:'Restock',withdraw:'Withdrawal'})[e.t]+'</td><td>'+esc(e.note)+'</td><td class="'+(inn?'good':'bad')+'">'+(inn?'+':'−')+P(e.a)+'</td></tr>'}).join('')+'</table></div>'}

function draw(){if(!user){loginView();return}const T=[['dash','Dashboard'],['inv','Inventory'],['ord','Orders & delivery'],['cash','Cash & withdrawals']];
document.getElementById('nav').innerHTML='<h1>Tindahan</h1>'+T.map(t=>'<button class="'+(tab==t[0]?'on':'')+'" onclick="go(\''+t[0]+'\')">'+t[1]+'</button>').join('');
document.getElementById('nav').insertAdjacentHTML('beforeend','<button onclick="signOut()" style="margin-top:auto">Sign out<br><small>'+esc(user.email)+'</small></button>');
document.getElementById('main').innerHTML=({dash,inv,ord,cash:cashv})[tab]();if(tab=='cash'&&wd)preview()}
let why='';
if(!window.APP_CONFIG)why='config.js was not found. Keep it in the same folder as index.html.';
else if(!C.SUPABASE_URL||!C.SUPABASE_ANON_KEY||C.SUPABASE_URL.includes('YOUR-PROJECT')||C.SUPABASE_ANON_KEY.includes('YOUR-ANON'))why='config.js still has the placeholder values.';
else if(typeof supabase==='undefined')why='The Supabase library did not load. Check your internet connection or ad-blocker.';
else try{db=supabase.createClient(C.SUPABASE_URL,C.SUPABASE_ANON_KEY)}catch(e){why=e.message}
if(db){db.auth.onAuthStateChange((ev,s)=>{if(ev==='PASSWORD_RECOVERY'){mode='reset';user=null;loginView();return}user=s?s.user:null;if(user)load();else{mode='in';draw()}})}else draw();
</script></body></html>
