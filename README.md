# Online Quick Pharmacy

Laravel final-year project for a single-pharmacy online medicine marketplace with inventory management, customer ordering, wallets, promotions, notifications, ratings, reports, and an optional AI medical-information assistant.

## Current Scope

The current release supports one pharmacy, `CityCare Demo Pharmacy`. The database already uses a separate `pharmacies` entity and pharmacy relationships, so multiple pharmacies can be added later without redesigning the medicine or order tables.

Included:

- Customer, pharmacy staff, rider, and admin roles
- Registration, login, logout, password hashing, throttling, and inactive-account protection
- Medicine search, category filtering, create, update, soft delete, and stock availability
- Customer cart, checkout, cash on delivery, and wallet payment
- Atomic stock deduction during checkout
- Order history and staff/admin order status workflow
- Customer and pharmacy wallets with an auditable transaction ledger
- Admin-managed campaigns, promo codes, dates, limits, and medicine targeting
- Database order notifications and delivered-order ratings
- Admin dashboard, user management, sales, stock, order, medicine, and wallet reports
- Optional GroqCloud, Cerebras, or OpenRouter medical-information chat

Deferred by design:

- Multiple-pharmacy checkout and order splitting
- Live rider GPS and Google Maps tracking
- External payment gateway
- AI diagnosis or prescription generation

## Requirements

- PHP 8.3+
- Composer
- MySQL 8+ or XAMPP MySQL
- Node.js and npm for frontend asset builds

## Installation

From the project directory, run:

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database, then configure the database values in `.env`.

Run the migrations, seed sample catalog data, and build the frontend:

```sh
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
```

Start the local application:

```sh
php artisan serve
```

Open `http://127.0.0.1:8000`.

The seeder creates optional local demo accounts only when all four `DEMO_*_PASSWORD` values are set in `.env`. Use private local values and never commit `.env`.

## AI Configuration

The application runs without an AI key. To enable the optional assistant, add one provider and key to `.env`:

```env
AI_PROVIDER=groq
AI_API_KEY=your-provider-key
AI_MODEL=openai/gpt-oss-20b
```

Supported providers are `groq`, `cerebras`, and `openrouter`. Keys are read server-side only and must never be committed. The assistant provides general educational information, does not diagnose or prescribe, and advises emergency care when appropriate.

## Testing

The installed PHP build has `pdo_mysql` but not the SQLite PDO driver. Tests therefore use the isolated `pharmacy_testing` MySQL database configured in `phpunit.xml`.

```sh
php artisan test
```

The suite covers authentication, authorization, medicine CRUD, cart and checkout transactions, wallet accounting, campaigns, notifications, ratings, AI safety behavior, and admin reports.

## Project Notes

This is a portfolio-ready academic project. It is appropriate to describe it as a project you designed and developed if that accurately reflects your work. Describe it as a final-year or personal project rather than claiming professional employment experience.
