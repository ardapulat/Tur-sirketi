# Tour Booking System

## Overview
This project is a simple Tour Booking Web Application built using PHP and MySQL. Users can browse tours, create an account, purchase tickets, and manage their reservations. The system also includes a basic admin login for management purposes.

## Features
- User registration and login system
- Tour listing and viewing
- Ticket purchasing functionality
- User ticket management
- Ticket cancellation
- Admin login panel

## Technologies
Frontend: HTML, CSS, JavaScript  
Backend: PHP  
Database: MySQL  

## Project Structure
Project/  
- index.php → main page  
- login.php / register.php → authentication  
- tours.php → tour listing  
- purchase.php → ticket purchasing  
- cancel_ticket.php → ticket cancellation  
- config.php → database connection  
- database.sql → database schema  
- style.css → styling  
- script.js → frontend logic  

## Installation
1. Clone the repository:
git clone https://github.com/yourusername/tour-booking-system.git  

2. Move the project to your local server:
XAMPP → htdocs  
WAMP → www  

3. Start Apache and MySQL  

4. Create a database using phpMyAdmin  

5. Import the database.sql file  

6. Open config.php and update database credentials:
$host = "localhost";  
$user = "root";  
$password = "";  
$database = "your_database_name";  

7. Run the project:
http://localhost/Project/  

## Admin Access
admin_login.php  

## Purpose
This project was created to practice PHP backend development, database operations, and full-stack web application logic.

## Notes
This project is for educational purposes. Security features can be improved (password hashing, validation). UI can be enhanced further.

