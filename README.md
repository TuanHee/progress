# Progress

Progress is a task management system built on Laravel 8.4.* & Vuejs 3 with inertiajs

# Features
  - Account System
  - Project Management System
  - Project Member Management System
  - Task Management Subsystem
  - Comment System
  - Notification System

# Installation
 - Copy .env.example and save as .env
 - Set all values in .env file
 - Run composer install
 - Run php artisan key:generate
 - Run php artisan migrate
 - Run php artisan storage:link

# Deployment
 - Run php artisan serve
 - Run php artisan queue:work (for email invite and broadcast notifications)
 - Run php artisan schedule:work (for 'due soon task' notifications)
 - Start!

# Additional
If any css error
- Run npm install
- Run npm run dev / Run npm run prod
