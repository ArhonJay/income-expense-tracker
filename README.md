# Income Expense App

A personal finance tracking application built with **Laravel 11**, **Vue 3**, and **Inertia.js**. This application allows you to track your daily incomes and expenses, visualize your spending with charts, and export your data securely. 

## Features
- **User Authentication**: Secure login and registration.
- **Data Isolation**: Users can only see and manage their own financial records.
- **Financial Dashboard**: See your total income, total expenses, remaining balance, and visual breakdown by category.
- **Manage Transactions**: Add, edit, and delete income and expense records.
- **Export to CSV**: Easily export your incomes and expenses to a CSV file.

## Prerequisites
- PHP 8.2+
- Node.js & npm
- Composer
- SQLite / MySQL / PostgreSQL

## Local Setup

1. **Clone the repository and install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Configure your environment variables:**
   Copy the example environment file and update your database credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *(By default, this project uses SQLite for local development, which requires no extra database setup).*

3. **Run database migrations and seed the database:**
   This will create the necessary tables and populate the app with a test user (`test@example.com` / `password`) and sample data.
   ```bash
   php artisan migrate --seed
   ```

## Running the Application Locally

To start the local development environment, use the provided composer script. This command uses `concurrently` to run the Laravel server, Vite (for Vue compilation), and the queue listener all in one terminal window:

```bash
composer run dev
```
Once running, you can access the application at `http://localhost:8000`.

## Testing & Quality Assurance

This project includes a fully configured CI check script that runs static analysis (PHPStan), linting (Pint & ESLint), and the unit/feature test suite (Pest).

To run all checks before pushing or deploying your code:
```bash
composer run ci:check
```

## Deployment (Render)

This application includes a production-ready `Dockerfile` designed specifically for hosting platforms like Render.

1. **Create your Database**: Create a Postgres database on Render.
2. **Create Web Service**: Create a new Web Service and select your repository.
3. **Environment**: Select **Docker** as the environment (Render will automatically detect the `Dockerfile`).
4. **Variables**: Add your environment variables (`APP_KEY`, `DATABASE_URL`, `APP_ENV=production`, etc.).
5. **Deploy**: The Docker image will automatically install dependencies, compile frontend assets, and run database migrations on boot.
