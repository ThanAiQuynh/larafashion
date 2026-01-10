# PROJECT TASKS

## Phase 1: Foundation
- [ ] Initialize Laravel project & Configure .env
- [ ] Setup Database Migrations (Copy schema from DB_SCHEMA.md)
- [ ] Run Migrations & Create Seeders (Admin account, Dummy Categories/Products).

## Phase 2: Core Admin Features
- [ ] Admin Auth & Dashboard Layout.
- [ ] Product Management (CRUD, Image Upload, Summernote/CKEditor for Description).
- [ ] Order Management (List views, Status update).

## Phase 3: Customer Storefront
- [ ] Homepage (Featured Products, Banners).
- [ ] Product Search (Fulltext Search implementation).
- [ ] Product Detail & Cart (Session based).
- [ ] Checkout & Order Placement.

## Phase 4: AI & Marketing Integration (New Strategy)
- [ ] **Feature: Product Feed API**
    - Create endpoint `/api/products/feed` returning JSON list of active products.
    - This is used to train the Tudongchat Bot.
- [ ] **Feature: Webhook Listener**
    - Create endpoint `/api/webhook/tudongchat`.
    - Validate secret key.
    - Store received data into `chatbot_leads` table.
- [ ] **Feature: Leads Management**
    - Create Admin View to list `chatbot_leads`.
    - Add button "Mark as Processed" (Call/Done).
- [ ] **Frontend:** Embed Tudongchat script into `layouts/app.blade.php`.