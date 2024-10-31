# Student Management System

## Overview
The Student Management System is a web application designed to manage student data, including their enrollment, attendance, and grades. It provides functionalities for user registration, login, and management of student information by administrators.

## Features
- User registration and login
- View and manage student population data
- Track student attendance
- Manage student grades
- Add, edit, and delete student records
- Responsive design with a modern user interface

## Technologies Used
- PHP for server-side scripting
- MySQL for database management
- HTML, CSS, and JavaScript for front-end development
- Chart.js for data visualization

## Installation
1. Clone the repository from the Github.
   ```
2. Navigate to the project directory:
   ```bash
   cd src
   ```
3. Create a MySQL database and import the SQL schema provided in the `database` folder (not visible due to privacy reasons)
4. Update the database connection settings in `src/Connection.php`:
   ```php
   $db_host = 'localhost';
   $db_user = 'your_db_user';
   $db_password = 'your_db_password';
   $db_db = 'your_database_name';
   ```
5. Start a local server (e.g., using XAMPP or MAMP) and navigate to the project in your web browser.

## Usage
- Access the application through your web browser.
- Register a new account or log in with existing credentials.
- Navigate through the application to manage student data.

## File Structure
- src/
  - Connection.php # Database connection file
  - welcome.php # Main dashboard for logged-in users
  - login.php # User login page
  - register.php # User registration page
  - population.php # View student population data
  - add_student.php # Add new student details
  - edit_student.php # Edit existing student details
  - student_grade.php # View student grades
  - course_grade.php # View course grades
  - update_course_grade.php # Update course grades
  - delete.php # Delete student records
  - add_course.php # Add new course details


