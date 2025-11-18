-- Insert test users for CompuPlay
-- Run this after importing the main database schema (material_learning.sql)

-- Test Teacher (Cikgu)
-- Email: cikgu@compuplay.edu
-- Password: cikgu123
INSERT INTO `users` (`username`, `email`, `password`, `role`, `full_name`, `is_online`, `last_seen`) 
VALUES ('cikgu_demo', 'cikgu@compuplay.edu', '12345678', 'teacher', 'Demo Cikgu', 0, NOW())
ON DUPLICATE KEY UPDATE email = email;

-- Test Student (Pelajar)
-- Email: pelajar@compuplay.edu
-- Password: pelajar123
INSERT INTO `users` (`username`, `email`, `password`, `role`, `full_name`, `is_online`, `last_seen`) 
VALUES ('pelajar_demo', 'pelajar@compuplay.edu', '12345678', 'student', 'Demo Pelajar', 0, NOW())
ON DUPLICATE KEY UPDATE email = email;

-- Note: To create your own password hash, use PHP:
-- <?php echo password_hash('yourpassword', PASSWORD_DEFAULT); ?>
-- 
-- Or create a temporary PHP file:
-- <?php
-- echo password_hash('cikgu123', PASSWORD_DEFAULT) . "\n";
-- echo password_hash('pelajar123', PASSWORD_DEFAULT) . "\n";
-- ?>

