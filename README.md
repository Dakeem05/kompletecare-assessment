# Uptime Monitor API

This project is a robust, asynchronous Uptime Monitoring API built with Laravel. It allows users to register URLs for monitoring, periodically checks their status, and sends email notifications when a site experiences downtime or recovers.

**Project Specifications:**
- **Framework:** Laravel 13
- **Language:** PHP 8.4+ (Ensure your local environment is running PHP 8.4 or greater)

## API Documentation

Complete API documentation with request/response examples is available via Postman:
[View Postman Documentation](https://documenter.getpostman.com/view/50292908/2sBXqRiGS5)

---

## Detailed Approach & Architecture

My approach to solving this assessment focused on reliability, performance, and clear separation of concerns. 

1. **Database Design**: 
   - `monitors`: Stores the core configuration for each URL (interval, threshold, current status).
   - `monitor_checks`: A historical log of every single ping, capturing HTTP status codes, response times, and boolean `is_up` flags. This allows for rich historical data retrieval without bloating the primary table.
   
2. **Asynchronous Processing (Queues & Schedules)**: 
   - Monitoring a website is an I/O blocking operation. Instead of checking URLs synchronously within the web request cycle, I utilized Laravel's Task Scheduler to dispatch `CheckMonitorJob` jobs to a Queue. 
   - This ensures the application remains highly responsive and can scale to monitor hundreds of URLs concurrently by spinning up more queue workers.

3. **Status Evaluation & Notification Logic**:
   - The system intelligently tracks `consecutive_failures` against the user-defined `threshold`. It only marks a site as `DOWN` (and sends an alert) when the threshold is breached, preventing false positives from transient network blips.
   - It also tracks state transitions to ensure notifications are only sent *once* when the site goes down, and *once* when it comes back up.

4. **Resource Formatting**: 
   - Utilized Laravel API Resources (`MonitorResource` and `MonitorCheckResource`) to explicitly define the JSON contract. This ensures API clients receive predictable, structured data even if the underlying database schema changes.

---

## Setup Instructions

Follow these steps to get the project running on your local machine.

### 1. Clone & Install Dependencies
```bash
git clone <repository-url>
cd kompletecare-assessment
composer install
```

### 2. Environment Configuration
Copy the example environment file:
```bash
cp .env.example .env
```

Generate the application key:
```bash
php artisan key:generate
```

Configure your database credentials in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 3. Database Migration
Run the migrations to create the necessary tables:
```bash
php artisan migrate
```

---

## Email Notifications & Local Mailer

The system sends email alerts when a monitored URL goes offline or comes back online.

### The `UPTIME_NOTIFY_EMAIL` Variable
In your `.env` file (copied from `.env.example`), you will find the `UPTIME_NOTIFY_EMAIL` variable. 
```env
UPTIME_NOTIFY_EMAIL=admin@example.com
```
**Important:** You must set this variable to the email address where you wish to receive the downtime and recovery alerts. 

### Setting up a Local Mailer
To test email functionality locally without spamming real inboxes, it is highly recommended to set up a local mailer like [Mailpit](https://github.com/axllent/mailpit) or [Mailtrap](https://mailtrap.io/).

Update your `.env` with your mailer credentials. For example, using Mailpit (Laravel's default local mailer):
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

---

## Running the Scheduler & Queues (Critical for Local Testing)

Because this application relies on background jobs to ping the URLs, **the monitoring will not work unless you have the scheduler and queue workers running.**

Open two separate terminal windows/tabs and run the following commands from the project root:

**Terminal 1: Start the Queue Worker**
This process listens for jobs (like `CheckMonitorJob` and sending emails) and executes them in the background.
```bash
php artisan queue:work
```

**Terminal 2: Start the Task Scheduler**
This process runs every minute, checks the database for monitors that are due for a check, and dispatches the jobs to the queue.
```bash
php artisan schedule:work
```

*(Note: In a production environment, the scheduler is typically run via a Cron job, and queue workers are managed by a process monitor like Supervisor.)*

### Finally, serve the application

You have two options to serve the application locally:

**Option A: Laravel Herd / Valet (Recommended)**
If you are using Laravel Herd or Laravel Valet (which is how this project was actively developed), your application is automatically served. Simply navigate to the local domain automatically configured by your service (e.g., `http://kompletecare-assessment.test`). 

**Option B: PHP Artisan Serve**
If you are not using Herd or Valet, you can run the built-in PHP server in a third terminal window:
```bash
php artisan serve
```
Your API will now be accessible at `http://localhost:8000`.
