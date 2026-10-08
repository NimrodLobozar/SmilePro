# SmilePro

SmilePro is a web application for managing a dental practice, built as a school project at MBO Utrecht. Staff can manage patients, appointments, treatments, schedules and invoices, and patients and staff can message each other.

## Features

- **Patients**: create, edit and delete patient records
- **Appointments**: plan appointments and change their date
- **Treatments**: manage treatments, costs and active status (admin)
- **Schedules**: employee schedules
- **Invoices**: create and manage invoices with automatic invoice numbers
- **Messages**: conversations between users, with read status
- **Employees and users**: management for admins
- **Dashboards**: a regular dashboard and an admin dashboard with statistics

## Tech stack

- PHP 8.2+ with Laravel 11 and Laravel Breeze (authentication)
- MySQL / MariaDB
- Blade, Tailwind CSS, Alpine.js and Vite

## Running locally

Requirements: PHP 8.2+, Composer, Node.js and a MySQL/MariaDB server.

```bash
git clone https://github.com/NimrodLobozar/SmilePro.git
cd SmilePro

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Create a database called `smilepro` and set the `DB_*` values in `.env`. Then run the migrations with the demo data and start the app:

```bash
php artisan migrate --seed
composer run dev
```

`composer run dev` starts the Laravel server, the queue worker, the log viewer and Vite together. The app runs at http://localhost:8000.

## Running with Docker

There is a Docker setup (PHP 8.3 + Apache and MariaDB 11) for deployment on CasaOS or Portainer. See [DOCKER.md](DOCKER.md) for the steps.

Quick start:

```bash
DB_PASSWORD=change-me DB_ROOT_PASSWORD=change-me-too docker compose up -d --build
```

## Demo accounts

After seeding you can log in with these accounts:

| Role    | Email             | Password    |
| ------- | ----------------- | ----------- |
| Admin   | admin@gmail.com   | Admin1234   |
| Dentist | dentist@gmail.com | Dentist1234 |
| Patient | patient@gmail.com | Patient1234 |
| Test    | test@gmail.com    | Test1234    |

Only use these accounts for local testing and demos.

## Tests

```bash
php artisan test
```

## License

MIT
