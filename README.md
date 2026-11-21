# TutorConnect HK

TutorConnect HK is a web platform designed to help HKDSE students and parents find private tutors in Hong Kong. Instead of scrolling through messy listings, users can filter tutors by subject, district, and budget, message them directly, and book lessons online.

This project was developed for my S6 Information and Communication Technology (ICT) School-Based Assessment (SBA).

## Features

- **Tutor Search & Filters:** Search for tutors by keyword, HKDSE subject (Core, Electives, Applied Learning, and Languages), Hong Kong district, or maximum hourly rate.
- **Lesson Booking:** Students and parents can book lessons by picking a date, start time, and duration. The system automatically checks for time conflicts so neither the tutor nor the student gets double-booked.
- **Direct Messaging:** A built-in chat system so students, parents, and tutors can discuss lesson details before or after booking.
- **Tutor Verification:** Tutors can upload proof of their qualifications (PDF, JPG, or PNG). Admins can review these documents and approve them to display a "Verified" badge on the tutor's profile.
- **Ratings & Reviews:** Once a lesson is marked as completed, students can leave a 1-to-5-star rating that updates the tutor's overall score.
- **Accessibility Options:** Includes quick toggles in the navigation bar to adjust text size (`A-`, `A`, `A+`) and switch to a high-contrast mode.

## Built With

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL / MariaDB

## Getting Started

1. **Set up the database:**
   Import `sql/schema.sql` to create the `tutor_platform` database and tables. If you want sample tutors, students, and bookings to test with, import `sql/example_data.sql` as well.

2. **Check database settings:**
   Open `config.php` and make sure `$db_host`, `$db_user`, `$db_pass`, and `$db_name` match your local MySQL/MariaDB setup.

3. **Run the server:**
   Start a local PHP server from the project root folder:
   ```bash
   php -S localhost:8000
   ```
   Then open `http://localhost:8000` in your browser.

## Test Accounts

You can find a full list of pre-made test accounts in `credentials.txt`. All test accounts use `password` as the password:
- **Admin:** `admin@tutorconnect.hk`
- **Tutor:** `tutor1@example.com`
- **Student:** `student1@example.com`
- **Parent:** `parent1@example.com`

## License

This project is licensed under the MIT License - see the `LICENSE` file for details.
