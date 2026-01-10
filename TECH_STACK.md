# TECHNICAL STACK

## Backend Framework
- **Core:** Laravel 11.x
- **Database:** MySQL 8.0
- **Authentication:** Laravel Breeze (Blade Stack)
- **HTTP Client:** GuzzleHttp (Included in Laravel) - Used to communicate with external APIs if needed.

## Frontend
- **Template Engine:** Blade
- **CSS:** Bootstrap 5 (CDN) + Custom CSS
- **JS:** Vanilla JS
- **Icons:** FontAwesome / Bootstrap Icons

## Third-party Integrations
- **AI Chatbot:** App.tudongchat.com
    - Method: Client-side Script Embedding & Server-side Webhook Handling.
- **Payment (Future):** VNPay / Momo (Simulated for now).

## Development Guidelines
- **Models:** Use Eloquent. Use "Mass Assignment" protection ($fillable).
- **Controllers:** Keep logic simple. Use FormRequests for validation.
- **Search:** Use `whereRaw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [$keyword])` for product search.