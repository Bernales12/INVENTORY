# Pharmacy Store — PHP + Supabase

## Setup
1. Upload `index.php` and `style.css` to a PHP 8+ hosting account (or place the folder inside `htdocs` for XAMPP).
2. Enable PHP cURL.
3. Open `index.php` and set `SUPABASE_URL` and `SUPABASE_ANON_KEY`, or set environment variables with those names.
4. Confirm your Supabase tables, views, RPC functions and RLS policies from the original project are present.
5. Open the site and sign in using a staff account created in Supabase Authentication.

## Important
- This is a PHP server-rendered conversion. Supabase is still the database and authentication provider.
- Do not use a Supabase service-role key in the website. Use the anon key and appropriate RLS policies.
- Set HTTPS in production. The PHP session stores the Supabase access token.
- The source uses the existing RPCs: `receive_stock`, `create_sale`, `create_po`, plus views/tables such as `v_stock`, `v_daily_sales`, `v_expiring`, `v_profit`, `v_reorder`, `deliveries`, `sales`, and `purchase_orders`.
- The app expects the same database schema as the original supplied HTML file.
