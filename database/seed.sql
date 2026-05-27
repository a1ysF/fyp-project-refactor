-- Demo data for Railway / local testing
-- Passwords are stored in plain text (legacy behaviour — do not use in production)

SET NAMES utf8mb4;

INSERT INTO users (user_id, name, username, email, password, age, user_type, verify, token) VALUES
('T1001', 'Demo Teacher', 'teacher_demo', 'teacher@cryptolearn.test', 'teacher123', NULL, 'teacher', 1, NULL),
('S1001', 'Demo Student', 'student_demo', 'student@cryptolearn.test', 'student123', 20, 'student', 1, NULL);

INSERT INTO `class` (class_id, class_name, class_code, teacher_id) VALUES
('C1001', 'Cryptography 101', 'DEMO01', 'T1001');

INSERT INTO class_users (class_id, user_id) VALUES
('C1001', 'S1001');

INSERT INTO badges (badge_id, badge_file) VALUES
('material1', NULL),
('learning5', NULL),
('assignment5', NULL),
('quiz5', NULL),
('points500', NULL);
