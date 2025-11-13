# Gym Manager

Gym Manager is a web application designed to manage gym members and their subscription plans. It provides a simple interface to add, edit, view, and delete members, as well as manage subscription types. The project is built using Laravel, Vue 3, Inertia.js, Tailwind CSS, and DaisyUI.

## Features

-   Full CRUD functionality
-   Form validation and profanity filtering
-   Responsive UI
-   Multiple linked webpages
-   Status verification
  

## Tech Stack

| Layer         | Technology                   |
| ------------- | ---------------------------- |
| Backend       | Laravel 10                   |
| Frontend      | Vue 3 + Inertia.js + Node.js |
| Styling       | Tailwind CSS + DaisyUI       |
| Database      | MySQL                        |
| Containerized | Laravel Sail (Docker)        |

## Database Schema

### Subscriptions Table

| Column        | Type     | Description            |
| ------------- | -------- | ---------------------- |
| id            | integer  | Primary key            |
| name          | string   | Subscription plan name |
| duration_days | integer  | Number of active days  |
| price         | decimal  | Cost of subscription   |
| created_at    | datetime | Timestamp created      |
| updated_at    | datetime | Timestamp updated      |

### Members Table

| Column          | Type        | Description                   |
| --------------- | ----------- | ----------------------------- |
| id              | integer     | Primary key                   |
| name            | string      | Member full name              |
| email           | string      | Member email (unique)         |
| subscription_id | foreign key | References `subscriptions.id` |
| created_at      | datetime    | Timestamp created             |
| updated_at      | datetime    | Timestamp updated             |

### Reviews Table

| Column      | Type        | Description                                      |
|------------ |-------------|--------------------------------------------------|
| id          | integer     | Primary key                                      |
| member_id   | foreign key | References `members.id` (who wrote the review)   |
| rating      | integer     | Star rating (1–5)                                |
| comment     | text        | Written feedback from the member                 |
| created_at  | datetime    | Timestamp created                                |
| updated_at  | datetime    | Timestamp updated                                |

#### Relationships

- A **Member** can write **many Reviews**
- A **Review** belongs to **one Member**

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
