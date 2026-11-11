<!doctype html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pharmacy Store</title>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.45.4/dist/umd/supabase.min.js"></script>
<style>
:root{--pine:#12372a;--bone:#fafbf8;--mist:#eaf0eb;--ink:#17211c;--mute:#5d6b63;--amber:#b7791f;--red:#b3261e;--ok:#2d7a4f;--line:#d5ded7}
*{box-sizing:border-box}body{margin:0;font:15px/1.5 "Public Sans",system-ui,sans-serif;color:var(--ink);background:var(--bone)}
#app{display:grid;grid-template-columns:200px 1fr;min-height:100vh}
nav{background:var(--pine);color:#fff;padding:20px 12px;display:flex;flex-direction:column;gap:4px}
nav h1{font-size:17px;margin:0 8px 16px}nav button{all:unset;cursor:pointer;padding:8px 12px;border-radius:6px;color:#cfe0d6}
nav button:hover,nav button.on{background:#1f5240;color:#fff}nav button:focus-visible{outline:2px solid #fff}
nav .out{margin-top:auto}
main{padding:24px;max-width:1100px;width:100%}h2{margin:0 0 16px;font-size:22px}h3{font-size:16px;margin:24px 0 8px}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px}
.kpi{background:var(--mist);border-radius:8px;padding:14px}.kpi b{display:block;font-size:26px}.kpi span{color:var(--mute)}
.kpi.warn b{color:var(--amber)}.kpi.bad b{color:var(--red)}
table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--line)}
th,td{padding:8px 10px;text-align:left;border-bottom:1px solid var(--line)}th{background:var(--mist);font-weight:500}
.r{text-align:right}.mute{color:var(--mute)}.low{color:var(--red);font-weight:700}.soon{color:var(--amber);font-weight:700}
.bar{height:6px;background:var(--mist);border-radius:3px;min-width:80px}.bar i{display:block;height:100%;border-radius:3px;background:var(--ok)}.bar i.l{background:var(--red)}
.chart{display:flex;align-items:flex-end;gap:8px;height:120px;border-bottom:1px solid var(--line);padding-bottom:4px}
.chart div{flex:1;background:var(--pine);border-radius:3px 3px 0 0;min-height:2px;position:relative}.chart small{position:absolute;bottom:-20px;left:0;font-size:11px;color:var(--mute)}
form,.row{display:flex;flex-wrap:wrap;gap:8px;align-items:end;margin:8px 0}
label{display:flex;flex-direction:column;font-size:13px;color:var(--mute);gap:2px}
input,select,button.b{font:inherit;padding:7px 9px;border:1px solid var(--line);border-radius:6px;background:#fff}
button.b{background:var(--pine);color:#fff;border-color:var(--pine);cursor:pointer}button.b.alt{background:#fff;color:var(--pine)}
.shop{display:grid;grid-template-columns:1fr 320px;gap:20px}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:12px;display:flex;flex-direction:column;gap:4px}.card .price{font-size:18px;font-weight:700;color:var(--pine)}
aside{background:var(--mist);border-radius:8px;padding:14px;align-self:start;position:sticky;top:12px}aside form{flex-direction:column;align-items:stretch}
.good{color:var(--ok);font-weight:700}button.b:disabled{opacity:.4;cursor:default}
@media(max-width:900px){.shop{grid-template-columns:1fr}aside{position:static}}
@media print{nav,form,button,details,.noprint{display:none!important}#app{display:block}}
#msg{position:fixed;right:16px;bottom:16px;background:var(--ink);color:#fff;padding:10px 14px;border-radius:6px;display:none}#msg.err{background:var(--red)}
#login,#setup{max-width:360px;margin:15vh auto;padding:0 16px}#login form,#setup form{flex-direction:column;align-items:stretch}
details{margin:8px 0}summary{cursor:pointer;color:var(--pine);font-weight:500}
@media(max-width:700px){#app{grid-template-columns:1fr}nav{flex-direction:row;flex-wrap:wrap}nav .out{margin:0}main{padding:16px}table{display:block;overflow-x:auto}}
</style></head><body>
<div id="setup" hidden><h2>Connect to Supabase</h2><form id="cf"><label>Project URL<input name="u" placeholder="https://xxxx.supabase.co" required></label><label>Anon public key<input name="k" required></label><button class="b">Connect</button></form><p class="mute">Find both in Supabase, Project Settings, API. They are saved in this browser only.</p></div>
<div id="login" hidden><h2>Pharmacy Stock</h2><form id="lf"><label>Email<input name="e" type="email" required></label><label>Password<input name="p" type="password" required></label><button class="b">Sign in</button></form><p class="mute">Create staff users in Supabase → Authentication → Users.</p></div>
<div id="app" hidden><nav><h1>Pharmacy Store</h1>
<button data-v="dashboard">Dashboard</button><button data-v="inventory">Inventory</button><button data-v="store">Store</button><button data-v="deliveries">Deliveries</button><button data-v="profit">Profit</button><button data-v="restock">Restock</button><button data-v="reports">Reports</button><button data-v="settings">Settings</button><button class="out" id="so">Sign out</button></nav><main id="main"></main></div>
<div id="msg" role="status"></div>
<script>
// Settings live in the app (Settings tab). You can also hard-code your Supabase details here.
const DEFAULT_URL='YOUR_PROJECT_URL', DEFAULT_KEY='YOUR_ANON_KEY';
let C={url:DEFAULT_URL,key:DEFAULT_KEY,store:'Pharmacy Store',cur:'$',fee:0,expiry:90};
try{Object.assign(C,JSON.parse(localStorage.getItem('pharmacyCfg')||'{}'))}catch(e){}
let CUR=C.cur,db=null;
if(/^https?:\/\//.test(C.url)&&C.key&&C.key!==DEFAULT_KEY)db=supabase.createClient(C.url,C.key);
const saveCfg=()=>{try{localStorage.setItem('pharmacyCfg',JSON.stringify(C))}catch(e){toast('Could not save settings in this browser',1)}};
const $=s=>document.querySelector(s), esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const money=n=>CUR+Number(n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
const iso=d=>d.toISOString().slice(0,10), main=$('#main');
function toast(t,err){const m=$('#msg');m.textContent=t;m.className=err?'err':'';m.style.display='block';setTimeout(()=>m.style.display='none',3500)}
async function run(p,ok){const {data,error}=await p;if(error){toast(error.message,1);return null}if(ok)toast(ok);return data??true}
const tbl=(cols,rows)=>`<table><tr>${cols.map(c=>`<th class="${c[2]||''}">${c[0]}</th>`).join('')}</tr>${rows.length?rows.map(r=>`<tr>${cols.map(c=>`<td class="${c[2]||''}">${c[1](r)}</td>`).join('')}</tr>`).join(''):`<tr><td colspan="${cols.length}" class="mute">Nothing here yet.</td></tr>`}</table>`;
const opts=(a,f)=>a.map(x=>`<option value="${x.id}">${esc(f(x))}</option>`).join('');

function showSetup(){$('#login').hidden=true;$('#app').hidden=true;$('#setup').hidden=false}
async function boot(){document.title=C.store;document.querySelector('nav h1').textContent=C.store;
 if(!db)return showSetup();$('#setup').hidden=true;const {data:{session}}=await db.auth.getSession();
 $('#login').hidden=!!session;$('#app').hidden=!session;if(session)go(location.hash.slice(1)||'dashboard')}
$('#lf').onsubmit=async e=>{e.preventDefault();const f=e.target;
 const {error}=await db.auth.signInWithPassword({email:f.e.value,password:f.p.value});error?toast(error.message,1):boot()};
const connect=(u,k)=>{u=u.trim();if(!/^https?:\/\//.test(u))return toast('URL must start with https://',1);C.url=u;C.key=k.trim();saveCfg();location.reload()};
$('#cf').onsubmit=e=>{e.preventDefault();connect(e.target.u.value,e.target.k.value)};
$('#so').onclick=async()=>{await db.auth.signOut();boot()};
document.querySelectorAll('nav [data-v]').forEach(b=>b.onclick=()=>go(b.dataset.v));
function go(v){location.hash=v;document.querySelectorAll('nav [data-v]').forEach(b=>b.classList.toggle('on',b.dataset.v===v));(views[v]||views.dashboard)()}

const views={
async dashboard(){
 const [s,low,ex,dl]=await Promise.all([
  db.from('v_daily_sales').select('*').order('day',{ascending:false}).limit(7),
  db.from('v_stock').select('*').eq('is_low',true).order('on_hand'),
  db.from('v_expiring').select('*').order('expiry_date').limit(15),
  db.from('deliveries').select('id',{count:'exact',head:true}).in('status',['pending','packed','out_for_delivery'])]);
 const days=(s.data||[]).slice().reverse(), t=(s.data||[]).find(r=>r.day===iso(new Date())), mx=Math.max(1,...days.map(d=>d.revenue));
 const exp=(ex.data||[]).filter(r=>r.days_left<=C.expiry);
 main.innerHTML=`<h2>Dashboard</h2><div class="kpis">
 <div class="kpi"><b>${money(t?.revenue)}</b><span>Sales today</span></div>
 <div class="kpi"><b>${money(t?.profit)}</b><span>Profit today</span></div>
 <div class="kpi ${low.data?.length?'bad':''}"><b>${low.data?.length||0}</b><span>Low or out of stock</span></div>
 <div class="kpi ${exp.length?'warn':''}"><b>${exp.length}</b><span>Batches expiring in ${C.expiry} days</span></div>
 <div class="kpi"><b>${dl.count||0}</b><span>Open deliveries</span></div></div>
 <h3>Revenue, last 7 days</h3><div class="chart">${days.map(d=>`<div style="height:${d.revenue/mx*100}%" title="${money(d.revenue)}"><small>${d.day.slice(5)}</small></div>`).join('')||'<span class="mute">No sales yet.</span>'}</div>
 <h3>Needs reorder</h3>${tbl([['Product',r=>esc(r.name)],['On hand',r=>`<span class="low">${r.on_hand}</span>`,'r'],['Reorder at',r=>r.reorder_level,'r']],low.data||[])}
 <h3>Expiring soon</h3>${tbl([['Product',r=>esc(r.name)],['Batch',r=>esc(r.batch_no)],['Qty',r=>r.qty,'r'],['Expires',r=>`<span class="${r.days_left<=30?'low':'soon'}">${r.expiry_date}${r.days_left<0?' (expired)':''}</span>`]],exp)}`},

async inventory(){
 const {data}=await db.from('v_stock').select('*').order('name');const p=data||[];
 main.innerHTML=`<h2>Inventory</h2>
 <details><summary>Add product</summary><form id="pf"><label>SKU<input name="sku" required></label><label>Name<input name="name" required></label><label>Category<input name="category"></label><label>Price<input name="price" type="number" step="0.01" required></label><label>Cost<input name="cost" type="number" step="0.01" value="0"></label><label>Reorder at<input name="rl" type="number" value="10"></label><label>Prescription<select name="rx"><option value="false">No</option><option value="true">Yes</option></select></label><button class="b">Save product</button></form></details>
 <details><summary>Receive stock</summary><form id="rf"><label>Product<select name="p">${opts(p,x=>x.name)}</select></label><label>Batch no.<input name="b" required></label><label>Expiry<input name="e" type="date" required></label><label>Quantity<input name="q" type="number" min="1" required></label><label>Supplier<input name="s"></label><button class="b">Receive stock</button></form></details>
 <div class="row"><label>Search<input id="q" placeholder="Name or SKU"></label></div><div id="it"></div>`;
 const draw=()=>{const k=$('#q').value.toLowerCase();$('#it').innerHTML=tbl([['SKU',r=>esc(r.sku)],['Product',r=>esc(r.name)+(r.requires_rx?' <span class="mute">(Rx)</span>':'')],['Category',r=>esc(r.category)],['Price',r=>money(r.price),'r'],['Stock',r=>`<span class="${r.is_low?'low':''}">${r.on_hand}</span><div class="bar"><i class="${r.is_low?'l':''}" style="width:${Math.min(100,r.on_hand/Math.max(1,r.reorder_level*3)*100)}%"></i></div>`,'r'],['Next expiry',r=>r.next_expiry||'–']],p.filter(r=>(r.name+r.sku).toLowerCase().includes(k)))};
 $('#q').oninput=draw;draw();
 $('#pf').onsubmit=async e=>{e.preventDefault();const f=e.target;
  if(await run(db.from('products').insert({sku:f.sku.value,name:f.name.value,category:f.category.value||null,price:+f.price.value,cost:+f.cost.value,reorder_level:+f.rl.value,requires_rx:f.rx.value==='true'}),'Product saved'))views.inventory()};
 $('#rf').onsubmit=async e=>{e.preventDefault();const f=e.target;
  if(await run(db.rpc('receive_stock',{p_product:f.p.value,p_batch:f.b.value,p_expiry:f.e.value,p_qty:+f.q.value,p_supplier:f.s.value||null}),'Stock received'))views.inventory()}},

async store(){
 const [st,rc]=await Promise.all([db.from('v_stock').select('*').order('name'),db.from('sales').select('*, deliveries(status)').order('created_at',{ascending:false}).limit(10)]);
 const p=st.data||[];let cart=[];const cats=[...new Set(p.map(x=>x.category).filter(Boolean))];
 main.innerHTML=`<h2>Store</h2><div class="shop"><section><div class="row"><label>Search<input id="q" placeholder="Search medicine"></label><label>Category<select id="cat"><option value="">All</option>${cats.map(c=>`<option>${esc(c)}</option>`).join('')}</select></label></div><div class="grid" id="grid"></div></section>
 <aside><h3 style="margin-top:0">Order</h3><div id="cart"></div><form id="sf"><label>Customer<input name="c"></label><label>Payment<select name="pay"><option>cash</option><option>card</option><option>e-wallet</option></select></label><label>Order type<select name="d"><option value="">Pick-up at counter</option><option value="1">Delivery</option></select></label><label>Address<input name="a"></label><label>Phone<input name="ph"></label><label>Delivery fee<input name="fee" type="number" step="0.01" value="${C.fee}"></label><button class="b">Complete order</button></form></aside></div>
 <h3>Recent orders</h3>${tbl([['#',r=>r.sale_no],['Time',r=>new Date(r.created_at).toLocaleString()],['Customer',r=>esc(r.customer_name)||'Walk-in'],['Payment',r=>r.payment_method],['Delivery',r=>r.deliveries?.status||'–'],['Total',r=>money(r.total),'r']],rc.data||[])}`;
 const draw=()=>{$('#cart').innerHTML=cart.length?tbl([['Item',r=>esc(r.name)],['Qty',r=>r.qty,'r'],['',r=>`<button class="b alt" data-m="${r.id}">−</button>`]],cart)+`<p class="r"><b>Total ${money(cart.reduce((a,r)=>a+r.qty*r.price,0))}</b></p>`:'<p class="mute">Cart is empty. Add items from the shelf.</p>';
  document.querySelectorAll('[data-m]').forEach(b=>b.onclick=()=>{const r=cart.find(x=>x.id===b.dataset.m);if(--r.qty<=0)cart=cart.filter(x=>x!==r);draw()})};
 const shelf=()=>{const k=$('#q').value.toLowerCase(),c=$('#cat').value;
  $('#grid').innerHTML=p.filter(x=>(x.name+x.sku).toLowerCase().includes(k)&&(!c||x.category===c)).map(x=>`<div class="card"><b>${esc(x.name)}</b><span class="mute">${esc(x.category)}${x.requires_rx?' (Rx)':''}</span><span class="price">${money(x.price)}</span><small class="${x.on_hand?'':'low'}">${x.on_hand?x.on_hand+' in stock':'Out of stock'}</small><button class="b" data-a="${x.id}" ${x.on_hand?'':'disabled'}>Add</button></div>`).join('')||'<p class="mute">No matching products.</p>';
  document.querySelectorAll('[data-a]').forEach(b=>b.onclick=()=>{const x=p.find(r=>r.id===b.dataset.a),ex=cart.find(r=>r.id===x.id);
   if((ex?ex.qty:0)+1>x.on_hand)return toast('Only '+x.on_hand+' in stock',1);ex?ex.qty++:cart.push({id:x.id,name:x.name,price:x.price,qty:1});draw()})};
 $('#q').oninput=shelf;$('#cat').onchange=shelf;shelf();draw();
 $('#sf').onsubmit=async e=>{e.preventDefault();const f=e.target;if(!cart.length)return toast('Add at least one item',1);
  if(f.d.value&&!f.a.value)return toast('Enter a delivery address',1);
  if(await run(db.rpc('create_sale',{p_items:cart.map(r=>({product_id:r.id,qty:r.qty})),p_customer:f.c.value||null,p_payment:f.pay.value,p_address:f.d.value?f.a.value:null,p_phone:f.ph.value||null,p_fee:+f.fee.value||0}),'Order completed'))views.store()}},

async deliveries(){
 const {data}=await db.from('deliveries').select('*, sales(sale_no,customer_name,total)').order('created_at',{ascending:false}).limit(100);
 const S=['pending','packed','out_for_delivery','delivered','failed','cancelled'];
 main.innerHTML=`<h2>Deliveries</h2>`+tbl([['Sale',r=>'#'+r.sales.sale_no],['Customer',r=>esc(r.sales.customer_name)||'–'],['Address',r=>esc(r.address)+(r.phone?`<br><span class="mute">${esc(r.phone)}</span>`:'')],['Rider',r=>`<input data-rider="${r.id}" value="${esc(r.rider)}" placeholder="Assign rider" size="12">`],['Status',r=>`<select data-st="${r.id}">${S.map(s=>`<option ${s===r.status?'selected':''} value="${s}">${s.replace(/_/g,' ')}</option>`).join('')}</select>`],['Total',r=>money(+r.sales.total+ +r.fee),'r']],data||[]);
 document.querySelectorAll('[data-st]').forEach(s=>s.onchange=()=>run(db.from('deliveries').update({status:s.value,delivered_at:s.value==='delivered'?new Date().toISOString():null}).eq('id',s.dataset.st),'Status updated'));
 document.querySelectorAll('[data-rider]').forEach(i=>i.onchange=()=>run(db.from('deliveries').update({rider:i.value||null}).eq('id',i.dataset.rider),'Rider saved'))},

async profit(){
 const to=new Date(),from=new Date(Date.now()-29*864e5);
 main.innerHTML=`<h2>Profit</h2><form id="pf2"><label>From<input name="f" type="date" value="${iso(from)}"></label><label>To<input name="t" type="date" value="${iso(to)}"></label><button class="b">Show profit</button><button class="b alt" type="button" id="rs">Go to restock</button></form><div id="out"></div>`;
 $('#rs').onclick=()=>go('restock');
 const show=async()=>{const f=$('#pf2').f.value,t=$('#pf2').t.value;
  const {data}=await db.from('v_profit').select('*').gte('day',f).lte('day',t),by={};
  (data||[]).forEach(r=>{const o=by[r.name]||(by[r.name]={n:r.name,u:0,rev:0,cogs:0,pr:0});o.u+=r.units;o.rev+=+r.revenue;o.cogs+=+r.cogs;o.pr+=+r.profit});
  const L=Object.values(by).sort((a,b)=>b.pr-a.pr),T=L.reduce((a,r)=>({rev:a.rev+r.rev,cogs:a.cogs+r.cogs,pr:a.pr+r.pr}),{rev:0,cogs:0,pr:0}),m=(x,y)=>y?(x/y*100).toFixed(1)+'%':'–';
  $('#out').innerHTML=`<div class="kpis"><div class="kpi"><b>${money(T.rev)}</b><span>Sales</span></div><div class="kpi"><b>${money(T.cogs)}</b><span>Cost of goods</span></div><div class="kpi"><b class="good">${money(T.pr)}</b><span>Profit (kita)</span></div><div class="kpi"><b>${m(T.pr,T.rev)}</b><span>Profit margin</span></div></div>
  <h3>Profit by product</h3>${tbl([['Product',r=>esc(r.n)],['Units sold',r=>r.u,'r'],['Sales',r=>money(r.rev),'r'],['Cost',r=>money(r.cogs),'r'],['Profit',r=>money(r.pr),'r'],['Margin',r=>`<span class="${r.pr/r.rev<.15?'soon':''}">${m(r.pr,r.rev)}</span>`,'r']],L)}`};
 $('#pf2').onsubmit=e=>{e.preventDefault();show()};show()},

async restock(){
 const [r,po]=await Promise.all([db.from('v_reorder').select('*').order('on_hand'),db.from('purchase_orders').select('*, purchase_items(id,qty,unit_cost,products(name))').order('created_at',{ascending:false}).limit(10)]);
 const L=(r.data||[]).map((x,i)=>({...x,i}));
 main.innerHTML=`<h2>Restock</h2><p class="mute">Products at or below their reorder level. Adjust quantities, then create the purchase order.</p><div id="rl"></div>
 <form id="rf2"><label>Supplier<input name="s" required></label><button class="b">Create purchase order</button><button class="b alt" type="button" id="pr">Print list</button><button class="b alt" type="button" id="cv">Download CSV</button></form>
 <h3 class="noprint">Purchase orders</h3><div id="pos" class="noprint"></div>`;
 $('#rl').innerHTML=tbl([['Buy',r=>`<input type="checkbox" data-c="${r.i}" checked>`],['Product',r=>esc(r.name)],['On hand',r=>`<span class="low">${r.on_hand}</span>`,'r'],['Sold, 30 days',r=>r.sold_30d,'r'],['Order qty',r=>`<input data-q="${r.i}" type="number" min="1" value="${r.suggested_qty}" style="width:80px">`,'r'],['Unit cost',r=>money(r.cost),'r']],L);
 const picks=()=>L.filter(x=>$(`[data-c="${x.i}"]`).checked).map(x=>({product_id:x.product_id,name:x.name,qty:+$(`[data-q="${x.i}"]`).value,unit_cost:x.cost})).filter(x=>x.qty>0);
 $('#rf2').onsubmit=async e=>{e.preventDefault();const k=picks();if(!k.length)return toast('Select at least one product',1);
  if(await run(db.rpc('create_po',{p_supplier:e.target.s.value,p_items:k.map(({product_id,qty,unit_cost})=>({product_id,qty,unit_cost}))}),'Purchase order created'))views.restock()};
 $('#pr').onclick=()=>window.print();
 $('#cv').onclick=()=>{const c='product,quantity,unit_cost,estimated_cost\n'+picks().map(x=>[`"${x.name}"`,x.qty,x.unit_cost,(x.qty*x.unit_cost).toFixed(2)].join(',')).join('\n');
  const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([c],{type:'text/csv'}));a.download='restock-list.csv';a.click()};
 const P=po.data||[];
 $('#pos').innerHTML=tbl([['PO',r=>'#'+r.po_no],['Supplier',r=>esc(r.supplier)],['Items',r=>r.purchase_items.map(i=>esc(i.products.name)+' x'+i.qty).join(', ')],['Cost',r=>money(r.purchase_items.reduce((a,i)=>a+i.qty*i.unit_cost,0)),'r'],['Status',r=>r.status],['',r=>r.status==='ordered'?`<button class="b" data-rc="${r.id}">Receive stock</button>`:'']],P);
 document.querySelectorAll('[data-rc]').forEach(b=>b.onclick=async()=>{const o=P.find(x=>x.id===b.dataset.rc),bt=[];
  for(const it of o.purchase_items){const e=prompt('Expiry date for '+it.products.name+' (YYYY-MM-DD)');if(!e)return;bt.push({item_id:it.id,batch_no:'PO'+o.po_no,expiry:e})}
  if(await run(db.rpc('receive_po',{p_po:o.id,p_batches:bt}),'Stock received'))views.restock()})},

async settings(){
 main.innerHTML=`<h2>Settings</h2><form id="st"><label>Store name<input name="s" value="${esc(C.store)}"></label><label>Currency symbol<input name="c" value="${esc(C.cur)}" size="4"></label><label>Default delivery fee<input name="f" type="number" step="0.01" min="0" value="${C.fee}"></label><label>Expiry alert (days, max 90)<input name="x" type="number" min="1" max="90" value="${C.expiry}"></label><button class="b">Save settings</button></form>
 <h3>Supabase connection</h3><form id="cn"><label>Project URL<input name="u" value="${esc(C.url)}" size="34"></label><label>Anon public key<input name="k" value="${esc(C.key)}" size="34"></label><button class="b alt">Update connection</button></form>
 <p class="mute">Settings are saved in this browser. Reorder levels are set per product in Inventory.</p>`;
 $('#st').onsubmit=e=>{e.preventDefault();const f=e.target;C.store=f.s.value.trim()||'Pharmacy Store';C.cur=f.c.value||'$';C.fee=+f.f.value||0;C.expiry=Math.min(90,Math.max(1,+f.x.value||90));saveCfg();location.reload()};
 $('#cn').onsubmit=e=>{e.preventDefault();connect(e.target.u.value,e.target.k.value)}},

async reports(){
 const to=new Date(),from=new Date(Date.now()-29*864e5);
 main.innerHTML=`<h2>Reports</h2><form id="rp"><label>From<input name="f" type="date" value="${iso(from)}"></label><label>To<input name="t" type="date" value="${iso(to)}"></label><button class="b">Run report</button><button class="b alt" type="button" id="csv">Download CSV</button></form><div id="out"></div>`;
 let rows=[];
 const go2=async()=>{const f=$('#rp').f.value,t=$('#rp').t.value;
  const [d,i]=await Promise.all([db.from('v_daily_sales').select('*').gte('day',f).lte('day',t).order('day',{ascending:false}),
   db.from('sale_items').select('qty,unit_price,unit_cost,products(name),sales!inner(created_at,status)').gte('sales.created_at',f).lt('sales.created_at',iso(new Date(new Date(t).getTime()+864e5)))]);
  rows=d.data||[];const sum=k=>rows.reduce((a,r)=>a+ +r[k],0),top={};
  (i.data||[]).filter(x=>x.sales.status==='completed').forEach(x=>{const n=x.products.name,o=top[n]||(top[n]={n,q:0,rev:0,pr:0});o.q+=x.qty;o.rev+=x.qty*x.unit_price;o.pr+=x.qty*(x.unit_price-x.unit_cost)});
  $('#out').innerHTML=`<div class="kpis"><div class="kpi"><b>${money(sum('revenue'))}</b><span>Revenue</span></div><div class="kpi"><b>${money(sum('profit'))}</b><span>Profit</span></div><div class="kpi"><b>${sum('sales')}</b><span>Sales</span></div></div>
  <h3>Best sellers</h3>${tbl([['Product',r=>esc(r.n)],['Units',r=>r.q,'r'],['Revenue',r=>money(r.rev),'r'],['Profit',r=>money(r.pr),'r']],Object.values(top).sort((a,b)=>b.rev-a.rev).slice(0,10))}
  <h3>Daily sales</h3>${tbl([['Date',r=>r.day],['Sales',r=>r.sales,'r'],['Revenue',r=>money(r.revenue),'r'],['Profit',r=>money(r.profit),'r']],rows)}`};
 $('#rp').onsubmit=e=>{e.preventDefault();go2()};
 $('#csv').onclick=()=>{const c='date,sales,revenue,profit\n'+rows.map(r=>[r.day,r.sales,r.revenue,r.profit].join(',')).join('\n');
  const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([c],{type:'text/csv'}));a.download='daily-sales.csv';a.click()};
 go2()}
};
boot();
</script></body></html>
