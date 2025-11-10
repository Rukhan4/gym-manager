# Gym Manager

Gym Manager is a web application designed to manage gym members and their subscription plans. It provides a simple interface to add, edit, view, and delete members, as well as manage subscription types. The project is built using Laravel, Vue 3, Inertia.js, Tailwind CSS, and DaisyUI.

## Features

-   Manage gym members
-   Manage subscription plans (daily, monthly, yearly, etc.)
-   Add, edit, and delete members
-   Form validation and success feedback messages
-   Smooth modal UI for editing members
-   Fully responsive interface
-   Uses Inertia.js for single-page application (SPA) behavior

## Tech Stack

| Layer         | Technology                   |
| ------------- | ---------------------------- |
| Backend       | Laravel 10                   |
| Frontend      | Vue 3 + Inertia.js + Node.js |
| Styling       | Tailwind CSS + DaisyUI       |
| Database      | MySQL                        |
| Containerized | Laravel Sail (Docker)        |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Database Schema

Subscriptions Table
Column Type Description
id integer Primary key
name string Subscription plan name
duration_days integer Number of active days
price decimal Cost of subscription

## Members Table

Column Type Description
id integer Primary key
name string Member full name
email string Member email (unique)
subscription_id foreign key Links to subscriptions table
