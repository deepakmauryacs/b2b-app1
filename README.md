# b2b-app1

This project contains a simple B2B management application based on Laravel.

## Role Management

A minimal role and permission system has been added. Roles may contain permissions for modules with actions: add, edit, view and export. Users can be assigned one or more roles. Example seeder creates a **Super Admin** role.

## Setup

Install dependencies and run migrations:

```bash
composer install
php artisan migrate --seed
```
