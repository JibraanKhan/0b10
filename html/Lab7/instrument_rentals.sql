DROP DATABASE IF EXISTS instrument_rentals;
CREATE DATABASE instrument_rentals;
USE instrument_rentals;

CREATE TABLE students (
    PRIMARY KEY (student_id),
    student_id      INT AUTO_INCREMENT,
    student_name    VARCHAR(64)
);

CREATE TABLE instruments (
    PRIMARY KEY (instrument_id),
    instrument_id      INT AUTO_INCREMENT,
    instrument_type    VARCHAR(32) 
);

-- This was what I wanted, but is not legal in MariaDB.
CREATE TABLE student_instruments (
    PRIMARY KEY (student_id, instrument_id),
    student_id      INT NOT NULL, -- NOT NULL is probably not necessary
    instrument_id   INT NOT NULL UNIQUE,
    checkout_date   DATE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    checkin_date    DATE DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id),
    FOREIGN KEY (instrument_id) REFERENCES instruments (instrument_id)
);

-- Lets construct a view of only the currently checked out instruments.
CREATE VIEW open_rentals AS
SELECT * 
  FROM student_instruments
       WHERE (checkin_date is NULL);

-- Enroll some students.
INSERT INTO students (student_name)
VALUES ('Neo'),
       ('Morpheous'),
       ('Trinity'),
       ('Cypher'),
       ('Tank');

-- Buy some instruments.
-- Create a prepared statement for this; to allow you to reset the
-- database when debugging.
INSERT INTO instruments (instrument_type)
VALUES ('Guitar'),
       ('Trumpet'),
       ('Flute'),
       ('Theramin'),
       ('Violin'),
       ('Tuba'),
       ('Melodica'),
       ('Trombone'),
       ('Keyboard');

-- Now check out some instruments to studens.
--INSERT INTO student_instruments (student_id, instrument_id)
--VALUES (1, 1),
--       (2, 2),
--       (3, 3),
--       (3, 4);

--INSERT INTO student_instruments (student_id, instrument_id, checkin_date)
--VALUES (1 , 3, '2020-10-01');