# DATABASE SCHEMA - LaraFashion
Database: MySQL 8.0
Notes: Use Laravel naming conventions (snake_case, plural table names).

## MODULE 1: AUTH & USERS
1. **users**
   - id (PK), name, email (unique), password, phone_number, avatar_url
   - role (enum: 'admin', 'customer')
   - is_active (boolean), remember_token, timestamps
2. **user_addresses**
   - id (PK), user_id (FK), recipient_name, recipient_phone
   - address_line, city, district, ward, is_default
3. **password_reset_tokens** (Laravel Default)
4. **personal_access_tokens** (Laravel Sanctum Default)

## MODULE 2: CATALOG (Optimized)
5. **categories**
   - id (PK), name, slug (index), parent_id (FK), is_active
6. **brands**
   - id (PK), name, slug, logo_url
7. **products**
   - id (PK), name, slug (unique index)
   - sku (string, unique)
   - category_id (FK), brand_id (FK)
   - price (decimal), original_price (decimal, nullable)
   - stock_quantity (integer)
   - description (longtext)
   - thumbnail_url, images (json - gallery)
   - is_active, is_featured
   - *Index Note:* Create FULLTEXT index on (name, description) for search.
8. **product_reviews**
   - id (PK), product_id (FK), user_id (FK), rating (1-5), comment, is_approved

## MODULE 3: SALES
9. **orders**
   - id (PK), order_code (unique string), user_id (FK)
   - status (enum: 'pending', 'confirmed', 'shipping', 'completed', 'cancelled')
   - total_amount (decimal), shipping_fee (decimal)
   - payment_method (enum: 'cod', 'banking'), payment_status
   - shipping_address (json - snapshot), note
10. **order_items**
    - id (PK), order_id (FK), product_id (FK)
    - product_name (snapshot), quantity, unit_price, total_price

## MODULE 4: MARKETING & AI INTEGRATION
11. **banners**
    - id (PK), title, image_url, link_url, is_active
12. **chatbot_configs** (Single row config)
    - id (PK), script_code (text), is_active (boolean), webhook_secret (string)
13. **chatbot_leads** (Data from Webhook)
    - id (PK)
    - tudongchat_session_id (string, index)
    - customer_name, customer_phone, customer_email
    - intent (string) - Summary of user need
    - raw_data (json) - Full webhook payload
    - is_processed (boolean, default: false)
    - created_at