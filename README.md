# EventFlow - Final Project Documentation

EventFlow is a comprehensive event ticketing and management platform built with Laravel 11. 

## Features
- **Authentication & Authorization**: JWT API Auth and Role-based access control (Admin, Organizer, Customer).
- **Event Discovery**: Browsing, searching, and filtering events with optimized caching.
- **Payment Gateway**: Webhook-based mock payment gateway integration.
- **Ticket Generation**: Automatic PDF ticket generation with QR code scanning capabilities.

## Requirements
- PHP 8.4+
- PostgreSQL / SQLite
- Node.js 20+

## Setup Instructions
1. Clone the repository.
2. Run `composer install` and `npm install && npm run build`.
3. Copy `.env.example` to `.env` and set up database credentials.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate --seed` (Uses `DummyDataSeeder` for populating demonstration data).
6. Start the server with `php artisan serve`.

## Deployment
This project is configured to automatically deploy to Railway upon pushing to the `main` branch.
The `docker/` folder contains configurations for SumoPod and HuggingFace Spaces.

---
*Created for Praktikum RPL Kel 41 2026*
