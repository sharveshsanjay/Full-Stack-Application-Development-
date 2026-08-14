CREATE DATABASE IF NOT EXISTS feedback_system;
USE feedback_system;

DROP TABLE IF EXISTS feedbacks;

CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comments TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data
INSERT INTO feedbacks (full_name, email, rating, comments) VALUES
('Alice Walker', 'alice@example.com', 5, 'Great user experience and fast response time!'),
('Bob Smith', 'bob@example.com', 4, 'Very helpful system, enjoyed using the application.');
