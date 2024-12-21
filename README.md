# Test CRUD Employee and Company

## Features

1. Manage Companies
2. Manage Employees

## Users

1. Administrator (admin@grtech.com:password)
2. Non-Administrator (user@grtech.com:password)

---

## Pre-requisite
1. composer and run `composer install`
2. npm or yarn and run `npm install`

## How to install

1. Copy `.env.example` to `.env`
2. Setup `.env` based on your device (database, url, etc)
3. Run `php artisan migrate --seed`
4. Run `composer run dev`
5. Access to given URL (default: localhost:8000)
