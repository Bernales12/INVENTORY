<!doctype html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mini Store</title>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.45.4/dist/umd/supabase.min.js"></script>
<script src="config.js"></script>
<style>
:root{--pine:#12372a;--bone:#fafbf8;--mist:#eaf0eb;--ink:#17211c;--mute:#5d6b63;--amber:#b7791f;--red:#b3261e;--ok:#2d7a4f;--line:#d5ded7}
*{box-sizing:border-box}
body{margin:0;font:15px/1.5 "Public Sans",system-ui,sans-serif;color:var(--ink);background:var(--bone)}
[hidden]{display:none!important}
#app{display:grid;grid-template-columns:200px 1fr;min-height:100vh}
nav{background:var(--pine);color:#fff;padding:20px 12px;display:flex;flex-direction:column;gap:4px}
nav h1{font-size:17px;margin:0 8px 16px}
nav button{all:unset;cursor:pointer;display:block;padding:8px 12px;border-radius:6px;color:#cfe0d6}
nav button:hover,nav button.on{background:#1f5240;color:#fff}
nav button:focus-visible{outline:2px solid #fff}
nav .out{margin-top:auto}
main{padding:24px;max-width:1100px;width:100%}
h2{margin:0 0 16px;font-size:22px}h3{font-size:16px;margin:24px 0 8px}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin:12px 0}
.kpi{background:var(--mist);border-radius:8px;padding:14px}.kpi b{display:block;font-size:26px}.kpi span,.mute{color:var(--mute)}
table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--line)}
th,td{padding:8px 10px;text-align:left;border-bottom:1px solid var(--line)}th{background:var(--mist);font-weight:500}
.r{text-align:right}.low{color:var(--red);font-weight:700}.soon{color:var(--amber);font-weight:700}.good{color:var(--ok);font-weight:700}
form,.row{display:flex;flex-wrap:wrap;gap:8px;align-items:end;margin:8px 0}.grow{flex:1;min-width:180px}
label{display:flex;flex-direction:column;font-size:13px;color:var(--mute);gap:2px}
input,select,.b{font:inherit;padding:7px 9px;border:1px solid var(--line);border-radius:6px;background:#fff}
input:focus,select:focus{outline:2px solid var(--pine);border-color:var(--pine)}
.b{background:var(--pine);color:#fff;border-color:var(--pine);cursor:pointer}.b.alt{background:#fff;color:var(--pine)}.b:disabled{opacity:.4;cursor:default}
.shop{display:grid;grid-template-columns:1fr 320px;gap:20px}
.chips{display:flex;gap:6px;flex-wrap:wrap;margin:6px 0 12px}
.chips button{all:unset;cursor:pointer;padding:4px 14px;border-radius:99px;background:#fff;border:1px solid var(--line)}
.chips button.on{background:var(--pine);color:#fff;border-color:var(--pine)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:12px;display:flex;flex-direction:column;gap:3px}
.av{width:40px;height:40px;border-radius:8px;background:var(--mist);color:var(--pine);display:grid;place-items:center;font-weight:700;font-size:18px;margin-bottom:4px}
.price{font-size:18px;font-weight:700;color:var(--pine)}.card .b{margin-top:6px}
aside{background:var(--mist);border-radius:8px;padding:14px;align-self:start;position:sticky;top:12px}
aside form{flex-direction:column;align-items:stretch}aside h3:first-child{margin-top:0}
.bar{height:6px;background:var(--mist);border-radius:3px;min-width:70px}.bar i{display:block;height:100%;border-radius:3px;background:var(--pine)}.bar i.l{background:var(--red)}
details{margin:8px 0}summary{cursor:pointer;color:var(--pine);font-weight:500}
#msg{position:fixed;right:16px;bottom:16px;max-width:320px;background:var(--ink);color:#fff;padding:10px 14px;border-radius:6px;display:none;z-index:9}#msg.err{background:var(--red)}
#login,#setup{max-width:340px;margin:15vh auto;padding:20px;background:#fff;border:1px solid var(--line);border-radius:8px}
#login form{flex-direction:column;align-items:stretch}
@media(max-width:900px){.shop{grid-template-columns:1fr}aside{position:static}}
@media(max-width:700px){#app{grid-template-columns:1fr}nav{flex-direction:row;flex-wrap:wrap;padding:12px}nav h1{width:100%;margin:0 4px 6px}nav .out{margin:0}main{padding:16px}table{display:block;overflow-x:auto}}
@media print{nav,form,button,details{display:none!important}#app{display:block}}
</style></head><body>
<div id="setup" hidden><h2>Not connected</h2><p class="mute" id="why"></p></div>
<div id="login" hidden><h2>Staff sign in</h2><form id="lf"><label>Email<input name="e" type="email" required></label><label>Password<input name="p" type="password" required></label><button class="b">Sign in</button></form><p class="mute">Create staff users in Supabase → Authentication → Users.</p></div>
<div id="app" hidden>
<nav><h1 id="brand">Mini Store</h1>
<button data-v="shop">Shop</button><button data-v="products">Products</button><button data-v="deliveries">Deliveries</button><button data-v="sales">Sales</button><button data-v="restock">Restock</button><button data-v="settings">Settings</button><button class="out" id="so">Sign out</button></nav>
<main id="main"></main></div>
<div id="msg" role="status"></div>
<script>
// Values come from config.js. try/catch keeps the app alive if config.js is missing or incomplete.
const G=f=>{try{return f()}catch(e){return undefined}};
const URL_=G(()=>SUPABASE_URL)||'',KEY_=G(()=>SUPABASE_KEY)||'';
let C={store:G(()=>STORE_NAME)||'Mini Store',cur:G(()=>CURRENCY)||'₱',fee:+G(()=>DELIVERY_FEE)||0};
try{Object.assign(C,JSON.parse(localStorage.getItem('miniStoreCfg')||'{}'))}catch(e){}
let db=null,cart=[];
if(/^https?:\/\//.test(URL_)&&KEY_&&!KEY_.startsWith('YOUR_'))db=supabase.createClient(URL_,KEY_);
const $=s=>document.querySelector(s),main=$('#main'),iso=d=>d.toISOString().slice(0,10);
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const money=n=>C.cur+Number(n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
function toast(t,err){const m=$('#msg');m.textContent=t;m.className=err?'err':'';m.style.display='block';clearTimeout(toast.t);toast.t=setTimeout(()=>m.style.display='none',4000)}
const fail=e=>toast(e.message+(/does not exist|schema cache|permission denied/i.test(e.message)?' (run setup.sql in Supabase and check your policies)':''),1);
async function get(p){const {data,error}=await p;if(error){fail(error);return[]}return data||[]}
async function run(p,ok){const {data,error}=await p;if(error){fail(error);return null}if(ok)toast(ok);return data??true}
const tbl=(cols,rows)=>`<table><tr>${cols.map(c=>`<th class="${c[2]||''}">${c[0]}</th>`).join('')}</tr>${rows.length?rows.map(r=>`<tr>${cols.map(c=>`<td class="${c[2]||''}">${c[1](r)}</td>`).join('')}</tr>`).join(''):`<tr><td colspan="${cols.length}" class="mute">Nothing here yet.</td></tr>`}</table>`;
const csv=(name,head,rows)=>{const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([head+'\n'+rows.map(r=>r.join(',')).join('\n')],{type:'text/csv'}));a.download=name;a.click()};

function showSetup(){$('#login').hidden=$('#app').hidden=true;$('#setup').hidden=false;
 $('#why').textContent=(!URL_&&!KEY_)?'config.js was not found or is empty. Upload config.js next to index.html.':'Open config.js and set SUPABASE_URL and SUPABASE_KEY (anon public key), then reload.'}
async function boot(){document.title=C.store;$('#brand').textContent=C.store;
 if(!db)return showSetup();$('#setup').hidden=true;const {data:{session}}=await db.auth.getSession();
 $('#login').hidden=!!session;$('#app').hidden=!session;if(session)go(location.hash.slice(1)||'shop')}
$('#lf').onsubmit=async e=>{e.preventDefault();if(!db)return showSetup();const f=e.target;
 const {error}=await db.auth.signInWithPassword({email:f.e.value,password:f.p.value});error?toast(error.message,1):boot()};
$('#so').onclick=async()=>{await db.auth.signOut();cart=[];boot()};
document.querySelectorAll('nav [data-v]').forEach(b=>b.onclick=()=>go(b.dataset.v));
async function go(v){if(!views[v])v='shop';location.hash=v;document.querySelectorAll('nav [data-v]').forEach(b=>b.classList.toggle('on',b.dataset.v===v));
 try{await views[v]()}catch(e){main.innerHTML=`<p class="low">Something went wrong: ${esc(e.message)}</p>`}}

const views={
async shop(){
 const [p,rc]=await Promise.all([get(db.from('v_stock').select('*').order('name')),get(db.from('sales').select('*, deliveries(status)').order('created_at',{ascending:false}).limit(8))]);
 const cats=[...new Set(p.map(x=>x.category).filter(Boolean))];let cat='';
 main.innerHTML=`<div class="shop"><section><div class="row"><input id="q" class="grow" placeholder="Search product or SKU"></div><div class="chips" id="chips"></div><div class="grid" id="grid"></div>
 <h3>Recent orders</h3>${tbl([['#',r=>r.sale_no],['Time',r=>new Date(r.created_at).toLocaleString()],['Customer',r=>esc(r.customer_name)||'Walk-in'],['Type',r=>r.deliveries?.status||'Pick-up'],['Total',r=>money(r.total),'r']],rc)}</section>
 <aside><h3>Your order</h3><div id="cart"></div><form id="sf"><label>Customer<input name="c"></label><label>Payment<select name="pay"><option>cash</option><option>card</option><option>e-wallet</option></select></label><label>Order type<select name="d"><option value="">Pick-up at counter</option><option value="1">Delivery</option></select></label>
 <div id="dl" hidden><label>Address<input name="a"></label><label>Phone<input name="ph"></label><label>Delivery fee<input name="fee" type="number" step="0.01" min="0" value="${C.fee}"></label></div><button class="b">Complete order</button></form></aside></div>`;
 const f=$('#sf'),sub=()=>cart.reduce((a,r)=>a+r.qty*r.price,0),fee=()=>f.d.value?+f.fee.value||0:0;
 const draw=()=>{$('#cart').innerHTML=cart.length?tbl([['Item',r=>esc(r.name)],['Qty',r=>`<button class="b alt" data-m="${r.id}">−</button> ${r.qty} <button class="b alt" data-p="${r.id}">+</button>`,'r']],cart)+`<p class="r mute">Subtotal ${money(sub())}${fee()?'<br>Delivery '+money(fee()):''}<br><b style="font-size:18px;color:var(--ink)">Total ${money(sub()+fee())}</b></p>`:'<p class="mute">Cart is empty. Tap Add on a product.</p>';
  document.querySelectorAll('[data-m]').forEach(b=>b.onclick=()=>{const r=cart.find(x=>x.id===b.dataset.m);if(--r.qty<=0)cart=cart.filter(x=>x!==r);draw()});
  document.querySelectorAll('[data-p]').forEach(b=>b.onclick=()=>add(b.dataset.p))};
 const add=id=>{const x=p.find(r=>r.id===id),ex=cart.find(r=>r.id===id);if((ex?ex.qty:0)+1>x.on_hand)return toast('Only '+x.on_hand+' in stock',1);ex?ex.qty++:cart.push({id:x.id,name:x.name,price:x.price,qty:1});draw()};
 const chips=()=>{$('#chips').innerHTML=['',...cats].map(c=>`<button class="${c===cat?'on':''}" data-c="${esc(c)}">${c?esc(c):'All'}</button>`).join('');document.querySelectorAll('[data-c]').forEach(b=>b.onclick=()=>{cat=b.dataset.c;chips();shelf()})};
 const shelf=()=>{const k=$('#q').value.toLowerCase();
  $('#grid').innerHTML=p.filter(x=>(x.name+x.sku).toLowerCase().includes(k)&&(!cat||x.category===cat)).map(x=>`<div class="card"><div class="av">${esc((x.name||'?')[0].toUpperCase())}</div><b>${esc(x.name)}</b><span class="mute">${esc(x.category)}</span><span class="price">${money(x.price)}</span><small class="${x.on_hand?'mute':'low'}">${x.on_hand?x.on_hand+' in stock':'Out of stock'}</small><button class="b" data-a="${x.id}" ${x.on_hand?'':'disabled'}>Add</button></div>`).join('')||'<p class="mute">No matching products.</p>';
  document.querySelectorAll('[data-a]').forEach(b=>b.onclick=()=>add(b.dataset.a))};
 f.d.onchange=()=>{$('#dl').hidden=!f.d.value;draw()};f.fee.oninput=draw;$('#q').oninput=shelf;chips();shelf();draw();
 f.onsubmit=async e=>{e.preventDefault();if(!cart.length)return toast('Add at least one item',1);if(f.d.value&&!f.a.value)return toast('Enter a delivery address',1);
  if(await run(db.rpc('create_sale',{p_items:cart.map(r=>({product_id:r.id,qty:r.qty})),p_customer:f.c.value||null,p_payment:f.pay.value,p_address:f.d.value?f.a.value:null,p_phone:f.ph.value||null,p_fee:fee()}),'Order completed')){cart=[];views.shop()}}},

async products(){
 const p=await get(db.from('v_stock').select('*').order('name')),low=p.filter(x=>x.is_low).length;
 main.innerHTML=`<h2>Products</h2><div class="kpis"><div class="kpi"><b>${p.length}</b><span>Products</span></div><div class="kpi"><b class="${low?'low':''}">${low}</b><span>Low or out of stock</span></div></div>
 <details><summary>Add product</summary><form id="pf"><label>SKU<input name="sku" required></label><label>Name<input name="name" required></label><label>Category<input name="category"></label><label>Price<input name="price" type="number" step="0.01" min="0" required></label><label>Cost<input name="cost" type="number" step="0.01" min="0" value="0"></label><label>Starting stock<input name="st" type="number" min="0" value="0"></label><label>Reorder at<input name="rl" type="number" min="0" value="5"></label><button class="b">Save product</button></form></details>
 <details><summary>Add stock</summary><form id="rf"><label>Product<select name="p">${p.map(x=>`<option value="${x.id}">${esc(x.name)}</option>`).join('')}</select></label><label>Quantity<input name="q" type="number" min="1" required></label><button class="b">Add stock</button></form></details>
 <div class="row"><input id="q" class="grow" placeholder="Search name or SKU"></div><div id="it"></div>`;
 const draw=()=>{const k=$('#q').value.toLowerCase();$('#it').innerHTML=tbl([['SKU',r=>esc(r.sku)],['Product',r=>esc(r.name)],['Category',r=>esc(r.category)],['Price',r=>money(r.price),'r'],['Cost',r=>money(r.cost),'r'],['Stock',r=>`<span class="${r.is_low?'low':''}">${r.on_hand}</span><div class="bar"><i class="${r.is_low?'l':''}" style="width:${Math.min(100,r.on_hand/Math.max(1,r.reorder_level*3)*100)}%"></i></div>`,'r']],p.filter(r=>(r.name+r.sku).toLowerCase().includes(k)))};
 $('#q').oninput=draw;draw();
 $('#pf').onsubmit=async e=>{e.preventDefault();const f=e.target;
  if(await run(db.from('products').insert({sku:f.sku.value.trim(),name:f.name.value.trim(),category:f.category.value.trim()||null,price:+f.price.value,cost:+f.cost.value,stock:+f.st.value,reorder_level:+f.rl.value}),'Product saved'))views.products()};
 $('#rf').onsubmit=async e=>{e.preventDefault();const f=e.target;
  if(await run(db.rpc('receive_stock',{p_product:f.p.value,p_qty:+f.q.value}),'Stock added'))views.products()}},

async deliveries(){
 const d=await get(db.from('deliveries').select('*, sales(sale_no,customer_name,total)').order('created_at',{ascending:false}).limit(100));
 const S=['pending','packed','out_for_delivery','delivered','failed','cancelled'];
 main.innerHTML=`<h2>Deliveries</h2>`+tbl([['Sale',r=>'#'+r.sales.sale_no],['Customer',r=>esc(r.sales.customer_name)||'–'],['Address',r=>esc(r.address)+(r.phone?`<br><span class="mute">${esc(r.phone)}</span>`:'')],['Rider',r=>`<input data-rider="${r.id}" value="${esc(r.rider)}" placeholder="Assign rider" size="12">`],['Status',r=>`<select data-st="${r.id}">${S.map(s=>`<option ${s===r.status?'selected':''} value="${s}">${s.replace(/_/g,' ')}</option>`).join('')}</select>`],['Total',r=>money(+r.sales.total+ +r.fee),'r']],d);
 document.querySelectorAll('[data-st]').forEach(s=>s.onchange=()=>run(db.from('deliveries').update({status:s.value,delivered_at:s.value==='delivered'?new Date().toISOString():null}).eq('id',s.dataset.st),'Status updated'));
 document.querySelectorAll('[data-rider]').forEach(i=>i.onchange=()=>run(db.from('deliveries').update({rider:i.value||null}).eq('id',i.dataset.rider),'Rider saved'))},

async sales(){
 main.innerHTML=`<h2>Sales and profit</h2><form id="rp"><label>From<input name="f" type="date" value="${iso(new Date(Date.now()-29*864e5))}"></label><label>To<input name="t" type="date" value="${iso(new Date())}"></label><button class="b">Show</button><button class="b alt" type="button" id="cv">Download CSV</button></form><div id="out"></div>`;
 let rows=[];
 const show=async()=>{const f=$('#rp').f.value,u=$('#rp').t.value,by={};
  const [pf,d]=await Promise.all([get(db.from('v_profit').select('*').gte('day',f).lte('day',u)),get(db.from('v_daily_sales').select('*').gte('day',f).lte('day',u).order('day',{ascending:false}))]);
  rows=d;pf.forEach(r=>{const o=by[r.name]||(by[r.name]={n:r.name,u:0,rev:0,cogs:0,pr:0});o.u+=r.units;o.rev+=+r.revenue;o.cogs+=+r.cogs;o.pr+=+r.profit});
  const L=Object.values(by).sort((a,b)=>b.pr-a.pr),T=L.reduce((a,r)=>({rev:a.rev+r.rev,cogs:a.cogs+r.cogs,pr:a.pr+r.pr}),{rev:0,cogs:0,pr:0}),m=(x,y)=>y?(x/y*100).toFixed(1)+'%':'–';
  $('#out').innerHTML=`<div class="kpis"><div class="kpi"><b>${money(T.rev)}</b><span>Sales</span></div><div class="kpi"><b>${money(T.cogs)}</b><span>Cost of goods</span></div><div class="kpi"><b class="good">${money(T.pr)}</b><span>Profit (kita)</span></div><div class="kpi"><b>${m(T.pr,T.rev)}</b><span>Margin</span></div></div>
  <h3>Profit by product</h3>${tbl([['Product',r=>esc(r.n)],['Units',r=>r.u,'r'],['Sales',r=>money(r.rev),'r'],['Cost',r=>money(r.cogs),'r'],['Profit',r=>money(r.pr),'r'],['Margin',r=>`<span class="${r.pr/r.rev<.15?'soon':''}">${m(r.pr,r.rev)}</span>`,'r']],L)}
  <h3>Daily sales</h3>${tbl([['Date',r=>r.day],['Orders',r=>r.sales,'r'],['Revenue',r=>money(r.revenue),'r'],['Profit',r=>money(r.profit),'r']],rows)}`};
 $('#rp').onsubmit=e=>{e.preventDefault();show()};$('#cv').onclick=()=>csv('daily-sales.csv','date,orders,revenue,profit',rows.map(r=>[r.day,r.sales,r.revenue,r.profit]));show()},

async restock(){
 const [r,P]=await Promise.all([get(db.from('v_reorder').select('*').order('on_hand')),get(db.from('purchase_orders').select('*, purchase_items(id,qty,unit_cost,products(name))').order('created_at',{ascending:false}).limit(10))]);
 const L=r.map((x,i)=>({...x,i}));
 main.innerHTML=`<h2>Restock</h2><p class="mute">Products at or below their reorder level. Adjust quantities, then create the purchase order.</p><div id="rl"></div>
 <form id="rf2"><label>Supplier<input name="s" required></label><button class="b">Create purchase order</button><button class="b alt" type="button" id="cv">Download CSV</button></form>
 <h3>Purchase orders</h3><div id="pos"></div>`;
 $('#rl').innerHTML=tbl([['Buy',r=>`<input type="checkbox" data-c="${r.i}" checked>`],['Product',r=>esc(r.name)],['On hand',r=>`<span class="low">${r.on_hand}</span>`,'r'],['Sold, 30 days',r=>r.sold_30d,'r'],['Order qty',r=>`<input data-q="${r.i}" type="number" min="1" value="${r.suggested_qty}" style="width:80px">`,'r'],['Unit cost',r=>money(r.cost),'r']],L);
 const picks=()=>L.filter(x=>$(`[data-c="${x.i}"]`).checked).map(x=>({product_id:x.product_id,name:x.name,qty:+$(`[data-q="${x.i}"]`).value,unit_cost:x.cost})).filter(x=>x.qty>0);
 $('#rf2').onsubmit=async e=>{e.preventDefault();const k=picks();if(!k.length)return toast('Select at least one product',1);
  if(await run(db.rpc('create_po',{p_supplier:e.target.s.value,p_items:k.map(({product_id,qty,unit_cost})=>({product_id,qty,unit_cost}))}),'Purchase order created'))views.restock()};
 $('#cv').onclick=()=>csv('restock-list.csv','product,quantity,unit_cost,estimated_cost',picks().map(x=>[`"${x.name}"`,x.qty,x.unit_cost,(x.qty*x.unit_cost).toFixed(2)]));
 $('#pos').innerHTML=tbl([['PO',r=>'#'+r.po_no],['Supplier',r=>esc(r.supplier)],['Items',r=>r.purchase_items.map(i=>esc(i.products.name)+' x'+i.qty).join(', ')],['Cost',r=>money(r.purchase_items.reduce((a,i)=>a+i.qty*i.unit_cost,0)),'r'],['Status',r=>r.status],['',r=>r.status==='ordered'?`<button class="b" data-rc="${r.id}">Receive</button>`:'']],P);
 document.querySelectorAll('[data-rc]').forEach(b=>b.onclick=async()=>{if(!confirm('Mark this order as received and add the items to stock?'))return;
  if(await run(db.rpc('receive_po',{p_po:b.dataset.rc}),'Stock received'))views.restock()})},

async settings(){
 main.innerHTML=`<h2>Settings</h2><form id="st"><label>Store name<input name="s" value="${esc(C.store)}"></label><label>Currency symbol<input name="c" value="${esc(C.cur)}" size="4"></label><label>Default delivery fee<input name="f" type="number" step="0.01" min="0" value="${C.fee}"></label><button class="b">Save settings</button></form>
 <p class="mute">Saved in this browser. Reorder levels are set per product. The Supabase connection is set in config.js.</p>`;
 $('#st').onsubmit=e=>{e.preventDefault();const f=e.target;C.store=f.s.value.trim()||'Mini Store';C.cur=f.c.value||'₱';C.fee=+f.f.value||0;
  try{localStorage.setItem('miniStoreCfg',JSON.stringify(C))}catch(x){toast('Could not save settings',1)}location.reload()}}
};
boot();
</script></body></html>
