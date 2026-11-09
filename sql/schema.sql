-- Database Initialization Script for HK TutorConnect (With Test Data Seeder)

CREATE DATABASE IF NOT EXISTS `tutor_platform` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tutor_platform`;

-- 1. USER table (account details)
CREATE TABLE IF NOT EXISTS USER (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(20) DEFAULT '',
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('Student', 'Parent', 'Tutor', 'Admin') NOT NULL,
    Phone VARCHAR(50),
    Region VARCHAR(100),
    District VARCHAR(100)
);

-- 2. TUTOR table (tutor details)
CREATE TABLE IF NOT EXISTS TUTOR (
    TutorID INT PRIMARY KEY,
    Bio TEXT,
    Qualifications TEXT,
    HourlyRate DECIMAL(6, 2) NOT NULL,
    AvgRating DECIMAL(3, 2) DEFAULT 0.00,
    FOREIGN KEY (TutorID) REFERENCES USER(UserID) ON DELETE CASCADE
);

-- 3. SUBJECT table (for HKDSE subjects)
CREATE TABLE IF NOT EXISTS SUBJECT (
    SubjectID INT AUTO_INCREMENT PRIMARY KEY,
    SubjectName VARCHAR(100) NOT NULL,
    Category VARCHAR(100) NOT NULL
);

-- 4. TUTOR_SUBJECT table (tutor-subject relationship)
CREATE TABLE IF NOT EXISTS TUTOR_SUBJECT (
    TutorID INT,
    SubjectID INT,
    PRIMARY KEY (TutorID, SubjectID),
    FOREIGN KEY (TutorID) REFERENCES TUTOR(TutorID) ON DELETE CASCADE,
    FOREIGN KEY (SubjectID) REFERENCES SUBJECT(SubjectID) ON DELETE CASCADE
);

-- 5. BOOKING table (tutorial bookings)
CREATE TABLE IF NOT EXISTS BOOKING (
    BookingID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID INT NOT NULL,
    TutorID INT NOT NULL,
    LessonDate DATE NOT NULL,
    LessonTime TIME NOT NULL,
    Duration DECIMAL(3, 1) NOT NULL DEFAULT 1.0,
    Status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (StudentID) REFERENCES USER(UserID) ON DELETE CASCADE,
    FOREIGN KEY (TutorID) REFERENCES USER(UserID) ON DELETE CASCADE
);

-- 6. MESSAGE table
CREATE TABLE IF NOT EXISTS MESSAGE (
    MessageID INT AUTO_INCREMENT PRIMARY KEY,
    SenderID INT NOT NULL,
    ReceiverID INT NOT NULL,
    MessageContent TEXT NOT NULL,
    Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SenderID) REFERENCES USER(UserID) ON DELETE CASCADE,
    FOREIGN KEY (ReceiverID) REFERENCES USER(UserID) ON DELETE CASCADE
);

-- 7. REVIEW table
CREATE TABLE IF NOT EXISTS REVIEW (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    BookingID INT NOT NULL UNIQUE,
    StudentID INT NOT NULL,
    TutorID INT NOT NULL,
    Rating INT NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BookingID) REFERENCES BOOKING(BookingID) ON DELETE CASCADE,
    FOREIGN KEY (StudentID) REFERENCES USER(UserID) ON DELETE CASCADE,
    FOREIGN KEY (TutorID) REFERENCES USER(UserID) ON DELETE CASCADE
);

-- 8. TUTOR_DOCUMENT table (Uploaded certs for admin verification)
CREATE TABLE IF NOT EXISTS TUTOR_DOCUMENT (
    DocID INT AUTO_INCREMENT PRIMARY KEY,
    TutorID INT NOT NULL,
    FilePath VARCHAR(255) NOT NULL,
    OriginalName VARCHAR(255) NOT NULL,
    Status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    UploadedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (TutorID) REFERENCES TUTOR(TutorID) ON DELETE CASCADE
);

-- List of all HKDSE subjects from Google
INSERT INTO SUBJECT (SubjectName, Category) VALUES
('Chinese Language', 'Category 0: Core Subjects'),
('English Language', 'Category 0: Core Subjects'),
('Mathematics (Compulsory Part)', 'Category 0: Core Subjects'),
('Mathematics (Extended Module 1 - M1)', 'Category 0: Core Subjects'),
('Mathematics (Extended Module 2 - M2)', 'Category 0: Core Subjects'),
('Citizenship and Social Development', 'Category 0: Core Subjects'),
('Biology', 'Category A: Elective Subjects'),
('Business, Accounting and Financial Studies (BAFS)', 'Category A: Elective Subjects'),
('Chemistry', 'Category A: Elective Subjects'),
('Chinese History', 'Category A: Elective Subjects'),
('Chinese Literature', 'Category A: Elective Subjects'),
('Design and Applied Technology (DAT)', 'Category A: Elective Subjects'),
('Economics', 'Category A: Elective Subjects'),
('Ethics and Religious Studies', 'Category A: Elective Subjects'),
('Geography', 'Category A: Elective Subjects'),
('Health Management and Social Care (HMSC)', 'Category A: Elective Subjects'),
('History', 'Category A: Elective Subjects'),
('Information and Communication Technology (ICT)', 'Category A: Elective Subjects'),
('Literature in English', 'Category A: Elective Subjects'),
('Music', 'Category A: Elective Subjects'),
('Physical Education', 'Category A: Elective Subjects'),
('Physics', 'Category A: Elective Subjects'),
('Technology and Living', 'Category A: Elective Subjects'),
('Tourism and Hospitality Studies', 'Category A: Elective Subjects'),
('Visual Arts', 'Category A: Elective Subjects'),
('Creative Studies', 'Category B: Applied Learning (ApL) Areas'),
('Media and Communication', 'Category B: Applied Learning (ApL) Areas'),
('Business, Management and Law', 'Category B: Applied Learning (ApL) Areas'),
('Services', 'Category B: Applied Learning (ApL) Areas'),
('Applied Science', 'Category B: Applied Learning (ApL) Areas'),
('Engineering and Production', 'Category B: Applied Learning (ApL) Areas'),
('Applied Learning Chinese', 'Category B: Applied Learning (ApL) Areas'),
('French', 'Category C: Other Languages'),
('German', 'Category C: Other Languages'),
('Japanese', 'Category C: Other Languages'),
('Korean', 'Category C: Other Languages'),
('Spanish', 'Category C: Other Languages');
