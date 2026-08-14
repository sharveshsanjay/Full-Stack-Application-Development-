CREATE DATABASE IF NOT EXISTS audit_logging;
USE audit_logging;

DROP TRIGGER IF EXISTS after_employee_insert;
DROP TRIGGER IF EXISTS after_employee_update;
DROP VIEW IF EXISTS daily_activity_report;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS employees;

-- 1. Employees Table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Audit Logs Table
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    record_id INT NOT NULL,
    details TEXT NOT NULL,
    performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Trigger for AFTER INSERT on employees
DELIMITER //
CREATE TRIGGER after_employee_insert
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, action_type, record_id, details)
    VALUES (
        'employees',
        'INSERT',
        NEW.id,
        CONCAT('New employee added: ', NEW.name, ' | Dept: ', NEW.department, ' | Salary: $', NEW.salary)
    );
END;
//
DELIMITER ;

-- 4. Trigger for AFTER UPDATE on employees
DELIMITER //
CREATE TRIGGER after_employee_update
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, action_type, record_id, details)
    VALUES (
        'employees',
        'UPDATE',
        NEW.id,
        CONCAT('Employee updated (ID: ', NEW.id, ') | Old Salary: $', OLD.salary, ' -> New Salary: $', NEW.salary, ' | Dept: ', NEW.department)
    );
END;
//
DELIMITER ;

-- 5. View for Daily Activity Reports
CREATE VIEW daily_activity_report AS
SELECT 
    DATE(performed_at) AS report_date,
    action_type,
    COUNT(*) AS total_actions
FROM audit_logs
GROUP BY DATE(performed_at), action_type
ORDER BY report_date DESC, action_type ASC;

-- 6. Insert Sample Data
INSERT INTO employees (name, department, salary) VALUES
('John Doe', 'Engineering', 75000.00),
('Jane Smith', 'Marketing', 62000.00),
('Robert Johnson', 'Finance', 80000.00);

-- Update an employee to generate UPDATE audit log entry
UPDATE employees SET salary = 78000.00 WHERE name = 'John Doe';
