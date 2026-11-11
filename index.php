<?php
declare(strict_types=1);
session_start();

/*
 * Pharmacy Store — PHP + Supabase REST
 * Configure SUPABASE_URL and SUPABASE_ANON_KEY below (or environment variables).
 * This uses Supabase Auth and PostgREST/RPC endpoints; keep RLS enabled.
 */
$SUPABASE_URL = rtrim(getenv('SUPABASE_URL') ?: 'YOUR_PROJECT_URL', '/');
$SUPABASE_KEY = getenv('SUPABASE_ANON_KEY') ?: 'YOUR_ANON_KEY';
$CURRENCY = '$';

function h($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function money($n): string { global $CURRENCY; return $CURRENCY . number_format((float)($n ?? 0), 2); }
function today(): string { return date('Y-m-d'); }
function isoDate($date): string { return date('Y-m-d', strtotime($date)); }
function supabaseRequest(string $method, string $path, ?array $body=null, bool $auth=false, array $extraHeaders=[]): array {
    global $SUPABASE_URL, $SUPABASE_KEY;
    $token = $_SESSION['access_token'] ?? '';
    $headers = [
        'apikey: '.$SUPABASE_KEY,
        'Authorization: Bearer '.($auth && $token ? $token : $SUPABASE_KEY),
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    foreach ($extraHeaders as $header) $headers[] = $header;
    $ch = curl_init($SUPABASE_URL . '/rest/v1/' . ltrim($path, '/'));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $raw = curl_exec($ch); $err = curl_error($ch); $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($raw === false) return ['error'=>['message'=>$err ?: 'Connection failed'], 'status'=>$status];
    $data = json_decode($raw, true);
    if ($status >= 400) return ['error'=>is_array($data)?$data:['message'=>$raw], 'status'=>$status];
    return ['data'=>$data, 'status'=>$status];
}
function authRequest(string $path, array $body): array {
    global $SUPABASE_URL, $SUPABASE_KEY;
    $ch=curl_init($SUPABASE_URL.'/auth/v1/'.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
        CURLOPT_HTTPHEADER=>['apikey: '.$SUPABASE_KEY,'Content-Type: application/json'],
        CURLOPT_POSTFIELDS=>json_encode($body),CURLOPT_TIMEOUT=>30]);
    $raw=curl_exec($ch);$err=curl_error($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
    $data=json_decode((string)$raw,true);
    if($raw===false || $status>=400) return ['error'=>$data['msg']??$data['message']??$err??'Authentication failed'];
    return ['data'=>$data];
}
function rpc(string $name, array $args=[]): array {
    return supabaseRequest('POST','rpc/'.$name,$args,true,['Prefer: return=representation']);
}
function query(string $table, string $select='*', string $params=''): array {
    return supabaseRequest('GET',$table.'?select='.rawurlencode($select).($params ? '&'.$params : ''),null,true);
}
function notify(string $message, bool $error=false): void { $_SESSION['flash']=['message'=>$message,'error'=>$error]; }
function redirectPage(string $page): never { header('Location: ?page='.urlencode($page)); exit; }

if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }
if (isset($_POST['login'])) {
    $result=authRequest('token?grant_type=password',['email'=>trim($_POST['email']??''),'password'=>$_POST['password']??'']);
    if(isset($result['data']['access_token'])) {
        $_SESSION['access_token']=$result['data']['access_token'];
        $_SESSION['refresh_token']=$result['data']['refresh_token']??'';
        $_SESSION['user']=$result['data']['user']??[];
        redirectPage('dashboard');
    }
    notify($result['error']??'Sign in failed',true);
}
$loggedIn=!empty($_SESSION['access_token']);
$page=$_GET['page']??'dashboard';
$allowed=['dashboard','inventory','store','deliveries','profit','restock','reports'];
if(!in_array($page,$allowed,true)) $page='dashboard';
$flash=$_SESSION['flash']??null; unset($_SESSION['flash']);

function tableHtml(array $columns, array $rows): string {
    $html='<div class="table-wrap"><table><thead><tr>';
    foreach($columns as $c) $html.='<th class="'.(!empty($c[2])?'r':'').'">'.h($c[0]).'</th>';
    $html.='</tr></thead><tbody>';
    if(!$rows) $html.='<tr><td colspan="'.count($columns).'" class="mute">Nothing here yet.</td></tr>';
    foreach($rows as $r) { $html.='<tr>'; foreach($columns as $c) $html.='<td class="'.(!empty($c[2])?'r':'').'">'.($c[1])($r).'</td>'; $html.='</tr>'; }
    return $html.'</tbody></table></div>';
}
function kpi($value,$label,$tone=''): string { return '<div class="kpi '.$tone.'"><b>'.h($value).'</b><span>'.h($label).'</span></div>'; }
function formField($name,$label,$type='text',$required=false,$value='',$extra=''): string {
    return '<label>'.h($label).'<input name="'.h($name).'" type="'.h($type).'" '.($required?'required':'').' value="'.h($value).'" '.$extra.'></label>';
}
function pageDashboard(): string {
    $s=query('v_daily_sales','*','order=day.desc&limit=7');
    $l=query('v_stock','*','is_low=eq.true&order=on_hand.asc');
    $e=query('v_expiring','*','order=expiry_date.asc&limit=15');
    $d=query('deliveries','id','status=in.(pending,packed,out_for_delivery)&select=id');
    $sales=$s['data']??[];$low=$l['data']??[];$exp=$e['data']??[];
    $days=array_reverse($sales);$todayRow=null;foreach($sales as $r)if(($r['day']??'')===today())$todayRow=$r;
    $html='<h2>Dashboard</h2><div class="kpis">';
    $html.=kpi(money($todayRow['revenue']??0),'Sales today').kpi(money($todayRow['profit']??0),'Profit today');
    $html.=kpi(count($low),'Low or out of stock',count($low)?'bad':'').kpi(count($exp),'Batches expiring in 90 days',count($exp)?'warn':'').kpi(count($d['data']??[]),'Open deliveries').'</div>';
    $max=max(1,...array_map(fn($x)=>(float)($x['revenue']??0),$days));
    $html.='<h3>Revenue, last 7 days</h3><div class="chart">';
    if(!$days)$html.='<span class="mute">No sales yet.</span>';
    foreach($days as $r)$html.='<div style="height:'.((float)$r['revenue']/$max*100).'%"><small>'.h(substr($r['day'],5)).'</small></div>';
    $html.='</div><h3>Needs reorder</h3>'.tableHtml([['Product',fn($r)=>h($r['name'])],['On hand',fn($r)=>'<span class="low">'.h($r['on_hand']).'</span>',true],['Reorder at',fn($r)=>h($r['reorder_level']),true]],$low);
    $html.='<h3>Expiring soon</h3>'.tableHtml([['Product',fn($r)=>h($r['name'])],['Batch',fn($r)=>h($r['batch_no'])],['Qty',fn($r)=>h($r['qty']),true],['Expires',fn($r)=>'<span class="'.(($r['days_left']??999)<=30?'low':'soon').'">'.h($r['expiry_date']).((($r['days_left']??0)<0)?' (expired)':'').'</span>']],$exp);
    return $html;
}
function pageInventory(): string {
    $p=query('v_stock','*','order=name.asc');$products=$p['data']??[];
    $html='<h2>Inventory</h2><details><summary>Add product</summary><form method="post">
      <input type="hidden" name="action" value="add_product">'.formField('sku','SKU','text',true).formField('name','Name','text',true).formField('category','Category').formField('price','Price','number',true,'','step="0.01"').formField('cost','Cost','number',false,'0','step="0.01"').formField('reorder_level','Reorder at','number',false,'10').'<label>Prescription<select name="requires_rx"><option value="false">No</option><option value="true">Yes</option></select></label><button class="b">Save product</button></form></details>';
    $html.='<details><summary>Receive stock</summary><form method="post"><input type="hidden" name="action" value="receive_stock"><label>Product<select name="product" required>';
    foreach($products as $x)$html.='<option value="'.h($x['id']).'">'.h($x['name']).'</option>';
    $html.='</select></label>'.formField('batch','Batch no.','text',true).formField('expiry','Expiry','date',true).formField('qty','Quantity','number',true,'','min="1"').formField('supplier','Supplier').'<button class="b">Receive stock</button></form></details>';
    $html.='<div class="row"><label>Search<input id="searchTable" placeholder="Name or SKU" oninput="filterRows()"></label></div>';
    $html.='<div id="inventoryTable">'.tableHtml([['SKU',fn($r)=>h($r['sku'])],['Product',fn($r)=>h($r['name']).(!empty($r['requires_rx'])?' <span class="mute">(Rx)</span>':'')],['Category',fn($r)=>h($r['category']??'')],['Price',fn($r)=>money($r['price']),true],['Stock',fn($r)=>'<span class="'.(!empty($r['is_low'])?'low':'').'">'.h($r['on_hand']).'</span>',true],['Next expiry',fn($r)=>h($r['next_expiry']??'–')]],$products).'</div>';
    return $html;
}
function pageStore(): string {
    $p=query('v_stock','*','order=name.asc');$products=$p['data']??[];
    $sales=query('sales','*,deliveries(status)','order=created_at.desc&limit=10');$recent=$sales['data']??[];
    $html='<h2>Store</h2><div class="shop"><section><div class="row"><label>Search<input id="squery" oninput="filterShelf()" placeholder="Search medicine"></label><label>Category<select id="scat" onchange="filterShelf()"><option value="">All</option>';
    $cats=array_unique(array_filter(array_column($products,'category')));foreach($cats as $c)$html.='<option value="'.h($c).'">'.h($c).'</option>';
    $html.='</select></label></div><div class="grid" id="shelf">';
    foreach($products as $x)$html.='<div class="card" data-name="'.h(strtolower(($x['name']??'').' '.($x['sku']??''))).'" data-category="'.h($x['category']??'').'"><b>'.h($x['name']).'</b><span class="mute">'.h($x['category']??'').(!empty($x['requires_rx'])?' (Rx)':'').'</span><span class="price">'.money($x['price']).'</span><small class="'.((int)$x['on_hand']?'':'low').'">'.((int)$x['on_hand']?h($x['on_hand']).' in stock':'Out of stock').'</small><button class="b" '.((int)$x['on_hand']?'':'disabled').' onclick=\'addCart('.json_encode(['id'=>$x['id'],'name'=>$x['name'],'price'=>(float)$x['price'],'stock'=>(int)$x['on_hand']],JSON_HEX_APOS|JSON_HEX_QUOT).')\'>Add</button></div>';
    $html.='</div></section><aside><h3 style="margin-top:0">Order</h3><div id="cartText" class="mute">Cart is empty.</div><form method="post" id="saleForm"><input type="hidden" name="action" value="create_sale"><input type="hidden" name="cart" id="cartInput"><label>Customer<input name="customer"></label><label>Payment<select name="payment"><option>cash</option><option>card</option><option>e-wallet</option></select></label><label>Order type<select name="delivery" id="deliveryType"><option value="">Pick-up at counter</option><option value="1">Delivery</option></select></label>'.formField('address','Address').formField('phone','Phone').formField('fee','Delivery fee','number',false,'0','step="0.01"').'<button class="b">Complete order</button></form></aside></div><h3>Recent orders</h3>';
    $html.=tableHtml([['#',fn($r)=>h($r['sale_no'])],['Time',fn($r)=>h(date('Y-m-d H:i',strtotime($r['created_at'])))],['Customer',fn($r)=>h($r['customer_name']??'Walk-in')],['Payment',fn($r)=>h($r['payment_method'])],['Delivery',fn($r)=>h($r['deliveries']['status']??'–')],['Total',fn($r)=>money($r['total']),true]],$recent);
    return $html;
}
function pageDeliveries(): string {
    $res=query('deliveries','*,sales(sale_no,customer_name,total)','order=created_at.desc&limit=100');$rows=$res['data']??[];
    $html='<h2>Deliveries</h2><div class="table-wrap"><table><thead><tr><th>Sale</th><th>Customer</th><th>Address</th><th>Rider</th><th>Status</th><th class="r">Total</th></tr></thead><tbody>';
    $statuses=['pending','packed','out_for_delivery','delivered','failed','cancelled'];
    foreach($rows as $r){$html.='<tr><td>#'.h($r['sales']['sale_no']??'').'</td><td>'.h($r['sales']['customer_name']??'–').'</td><td>'.h($r['address']).'<div class="mute">'.h($r['phone']??'').'</div></td><td><form method="post" class="inline"><input type="hidden" name="action" value="update_rider"><input type="hidden" name="id" value="'.h($r['id']).'"><input name="rider" value="'.h($r['rider']??'').'" placeholder="Assign rider"><button class="b alt">Save</button></form></td><td><form method="post" class="inline"><input type="hidden" name="action" value="update_delivery"><input type="hidden" name="id" value="'.h($r['id']).'"><select name="status">';
        foreach($statuses as $s)$html.='<option value="'.$s.'" '.($s===$r['status']?'selected':'').'>'.str_replace('_',' ',$s).'</option>';
        $html.='</select><button class="b alt">Update</button></form></td><td class="r">'.money((float)($r['sales']['total']??0)+(float)($r['fee']??0)).'</td></tr>';
    }
    if(!$rows)$html.='<tr><td colspan="6" class="mute">Nothing here yet.</td></tr>';
    return $html.'</tbody></table></div>';
}
function dateRange(): array { return [$_GET['from']??date('Y-m-d',strtotime('-29 days')),$_GET['to']??today()]; }
function pageProfit(): string {
    [$from,$to]=dateRange();$res=query('v_profit','*','day=gte.'.rawurlencode($from).'&day=lte.'.rawurlencode($to));$by=[];
    foreach($res['data']??[] as $r){$n=$r['name'];if(!isset($by[$n]))$by[$n]=['n'=>$n,'u'=>0,'rev'=>0,'cogs'=>0,'pr'=>0];$by[$n]['u']+=(int)$r['units'];$by[$n]['rev']+=(float)$r['revenue'];$by[$n]['cogs']+=(float)$r['cogs'];$by[$n]['pr']+=(float)$r['profit'];}
    $rows=array_values($by);usort($rows,fn($a,$b)=>$b['pr']<=>$a['pr']);$rev=array_sum(array_column($rows,'rev'));$cost=array_sum(array_column($rows,'cogs'));$profit=array_sum(array_column($rows,'pr'));
    $html='<h2>Profit</h2>'.dateForm($from,$to,'Show profit').'<div class="kpis">'.kpi(money($rev),'Sales').kpi(money($cost),'Cost of goods').kpi(money($profit),'Profit (kita)').kpi($rev?number_format($profit/$rev*100,1).'%':'–','Profit margin').'</div><h3>Profit by product</h3>';
    return $html.tableHtml([['Product',fn($r)=>h($r['n'])],['Units sold',fn($r)=>h($r['u']),true],['Sales',fn($r)=>money($r['rev']),true],['Cost',fn($r)=>money($r['cogs']),true],['Profit',fn($r)=>money($r['pr']),true],['Margin',fn($r)=>$r['rev']?number_format($r['pr']/$r['rev']*100,1).'%':'–',true]],$rows);
}
function dateForm($from,$to,$button): string { return '<form method="get"><input type="hidden" name="page" value="'.h($_GET['page']??'profit').'">'.formField('from','From','date',false,$from).formField('to','To','date',false,$to).'<button class="b">'.h($button).'</button></form>'; }
function pageReports(): string {
    [$from,$to]=dateRange();$d=query('v_daily_sales','*','day=gte.'.rawurlencode($from).'&day=lte.'.rawurlencode($to).'&order=day.desc');
    $i=query('sale_items','qty,unit_price,unit_cost,products(name),sales!inner(created_at,status)','sales.created_at=gte.'.rawurlencode($from).'&sales.created_at=lt.'.rawurlencode(date('Y-m-d',strtotime($to.' +1 day'))));
    $rows=$d['data']??[];$top=[];
    foreach($i['data']??[] as $x){if(($x['sales']['status']??'')!=='completed')continue;$n=$x['products']['name'];if(!isset($top[$n]))$top[$n]=['n'=>$n,'q'=>0,'rev'=>0,'pr'=>0];$top[$n]['q']+=(int)$x['qty'];$top[$n]['rev']+=(int)$x['qty']*(float)$x['unit_price'];$top[$n]['pr']+=(int)$x['qty']*((float)$x['unit_price']-(float)$x['unit_cost']);}
    $best=array_values($top);usort($best,fn($a,$b)=>$b['rev']<=>$a['rev']);$sum=fn($k)=>array_sum(array_map(fn($r)=>(float)$r[$k],$rows));
    $html='<h2>Reports</h2>'.dateForm($from,$to,'Run report').'<div class="kpis">'.kpi(money($sum('revenue')),'Revenue').kpi(money($sum('profit')),'Profit').kpi((string)$sum('sales'),'Sales').'</div><h3>Best sellers</h3>';
    $html.=tableHtml([['Product',fn($r)=>h($r['n'])],['Units',fn($r)=>h($r['q']),true],['Revenue',fn($r)=>money($r['rev']),true],['Profit',fn($r)=>money($r['pr']),true]],array_slice($best,0,10));
    return $html.'<h3>Daily sales</h3>'.tableHtml([['Date',fn($r)=>h($r['day'])],['Sales',fn($r)=>h($r['sales']),true],['Revenue',fn($r)=>money($r['revenue']),true],['Profit',fn($r)=>money($r['profit']),true]],$rows);
}
function pageRestock(): string {
    $r=query('v_reorder','*','order=on_hand.asc');$p=query('purchase_orders','*,purchase_items(id,qty,unit_cost,products(name))','order=created_at.desc&limit=10');$items=$r['data']??[];$orders=$p['data']??[];
    $html='<h2>Restock</h2><p class="mute">Products at or below their reorder level. Adjust quantities, then create the purchase order.</p><form method="post" id="poForm"><input type="hidden" name="action" value="create_po"><div class="table-wrap"><table><thead><tr><th>Buy</th><th>Product</th><th class="r">On hand</th><th class="r">Sold, 30 days</th><th>Order qty</th><th class="r">Unit cost</th></tr></thead><tbody>';
    foreach($items as $x)$html.='<tr><td><input type="checkbox" name="selected[]" value="'.h($x['product_id']).'" checked></td><td>'.h($x['name']).'</td><td class="r low">'.h($x['on_hand']).'</td><td class="r">'.h($x['sold_30d']).'</td><td><input type="number" name="qty['.h($x['product_id']).']" min="1" value="'.h($x['suggested_qty']).'" style="width:80px"></td><td class="r">'.money($x['cost']).'</td></tr>';
    if(!$items)$html.='<tr><td colspan="6" class="mute">No products need restocking.</td></tr>';
    $html.='</tbody></table></div>'.formField('supplier','Supplier','text',true).'<button class="b">Create purchase order</button><button class="b alt" type="button" onclick="window.print()">Print list</button></form><h3>Purchase orders</h3>';
    return $html.tableHtml([['PO',fn($r)=>'#'.h($r['po_no'])],['Supplier',fn($r)=>h($r['supplier'])],['Items',fn($r)=>h(implode(', ',array_map(fn($i)=>($i['products']['name']??'').' x'.$i['qty'],$r['purchase_items']??[])))],['Cost',fn($r)=>money(array_sum(array_map(fn($i)=>(float)$i['qty']*(float)$i['unit_cost'],$r['purchase_items']??[]))),true],['Status',fn($r)=>h($r['status'])]],$orders);
}

if($loggedIn && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
    $action=$_POST['action'];$ok=null;
    if($action==='add_product') $ok=supabaseRequest('POST','products',['sku'=>$_POST['sku'],'name'=>$_POST['name'],'category'=>$_POST['category']?:null,'price'=>(float)$_POST['price'],'cost'=>(float)$_POST['cost'],'reorder_level'=>(int)$_POST['reorder_level'],'requires_rx'=>($_POST['requires_rx']==='true')],true,['Prefer: return=minimal']);
    elseif($action==='receive_stock') $ok=rpc('receive_stock',['p_product'=>$_POST['product'],'p_batch'=>$_POST['batch'],'p_expiry'=>$_POST['expiry'],'p_qty'=>(int)$_POST['qty'],'p_supplier'=>$_POST['supplier']?:null]);
    elseif($action==='create_sale') {
        $cart=json_decode($_POST['cart']??'[]',true)?:[];
        if(!empty($_POST['delivery']) && empty($_POST['address'])) { notify('Enter a delivery address',true); redirectPage('store'); }
        if(!$cart) { notify('Add at least one item',true); redirectPage('store'); }
        $ok=rpc('create_sale',['p_items'=>$cart,'p_customer'=>$_POST['customer']?:null,'p_payment'=>$_POST['payment'],'p_address'=>!empty($_POST['delivery'])?$_POST['address']:null,'p_phone'=>$_POST['phone']?:null,'p_fee'=>(float)($_POST['fee']??0)]);
    } elseif($action==='update_delivery') $ok=supabaseRequest('PATCH','deliveries?id=eq.'.rawurlencode($_POST['id']),['status'=>$_POST['status'],'delivered_at'=>$_POST['status']==='delivered'?date('c'):null],true,['Prefer: return=minimal']);
    elseif($action==='update_rider') $ok=supabaseRequest('PATCH','deliveries?id=eq.'.rawurlencode($_POST['id']),['rider'=>$_POST['rider']?:null],true,['Prefer: return=minimal']);
    elseif($action==='create_po') {
        $selected=$_POST['selected']??[];$qty=$_POST['qty']??[];$items=[];
        $stock=query('v_reorder','product_id,cost','product_id=in.('.implode(',',array_map('rawurlencode',$selected)).')');
        $costs=[];foreach($stock['data']??[] as $x)$costs[$x['product_id']]=$x['cost'];
        foreach($selected as $id)if((int)($qty[$id]??0)>0)$items[]=['product_id'=>$id,'qty'=>(int)$qty[$id],'unit_cost'=>(float)($costs[$id]??0)];
        if(!$items){notify('Select at least one product',true);redirectPage('restock');}
        $ok=rpc('create_po',['p_supplier'=>$_POST['supplier'],'p_items'=>$items]);
    }
    if(isset($ok['error']))notify($ok['error']['message']??'Action failed',true);else notify('Saved successfully');
    redirectPage($page);
}

$content='';
if($loggedIn) {
    $content=match($page) {
        'dashboard'=>pageDashboard(),'inventory'=>pageInventory(),'store'=>pageStore(),'deliveries'=>pageDeliveries(),
        'profit'=>pageProfit(),'restock'=>pageRestock(),'reports'=>pageReports(),default=>pageDashboard()
    };
}
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pharmacy Store</title><link rel="stylesheet" href="style.css"></head><body>
<?php if(!$loggedIn): ?>
<div class="login"><h2>Pharmacy Stock</h2><form method="post"><input type="hidden" name="login" value="1"><?=formField('email','Email','email',true)?><?=formField('password','Password','password',true)?><button class="b">Sign in</button></form><p class="mute">Create staff users in Supabase → Authentication → Users.</p></div>
<?php else: ?><div id="app"><nav><h1>Pharmacy Store</h1><?php foreach($allowed as $v): ?><a class="<?=($page===$v?'on':'')?>" href="?page=<?=$v?>"><?=ucfirst($v)?></a><?php endforeach; ?><a class="out" href="?logout=1">Sign out</a></nav><main><?=$content?></main></div><?php endif; ?>
<?php if($flash): ?><div class="msg <?=!empty($flash['error'])?'err':''?>"><?=h($flash['message'])?></div><?php endif; ?>
<script>
let cart=[];
function addCart(p){let old=cart.find(x=>x.product_id===p.id);if((old?old.qty:0)+1>p.stock){alert('Only '+p.stock+' in stock');return;}if(old)old.qty++;else cart.push({product_id:p.id,qty:1,name:p.name,price:p.price});drawCart();}
function drawCart(){let box=document.getElementById('cartText'),input=document.getElementById('cartInput');if(!box)return;let total=cart.reduce((a,x)=>a+x.qty*x.price,0);box.innerHTML=cart.length?cart.map(x=>`<div class="cartline">${x.name} × ${x.qty} <button type="button" class="b alt" onclick="removeCart('${x.product_id}')">−</button></div>`).join('')+`<p class="r"><b>Total $${total.toFixed(2)}</b></p>`:'Cart is empty.';input.value=JSON.stringify(cart.map(({product_id,qty})=>({product_id,qty}))); }
function removeCart(id){let x=cart.find(x=>x.product_id===id);if(x)x.qty--;cart=cart.filter(x=>x.qty>0);drawCart();}
function filterShelf(){let q=(document.getElementById('squery')?.value||'').toLowerCase(),c=document.getElementById('scat')?.value||'';document.querySelectorAll('#shelf .card').forEach(x=>x.hidden=!(x.dataset.name.includes(q)&&(!c||x.dataset.category===c)));}
function filterRows(){let q=(document.getElementById('searchTable')?.value||'').toLowerCase();document.querySelectorAll('#inventoryTable tbody tr').forEach(r=>r.hidden=!r.innerText.toLowerCase().includes(q));}
document.getElementById('saleForm')?.addEventListener('submit',e=>{if(!cart.length){e.preventDefault();alert('Add at least one item');}if(document.getElementById('deliveryType').value && !e.target.address.value){e.preventDefault();alert('Enter a delivery address');}});
</script></body></html>
