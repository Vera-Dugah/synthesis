CREATE DATABASE IF NOT EXISTS saturday_shelf
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE saturday_shelf;

CREATE TABLE IF NOT EXISTS loans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  borrower VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  title VARCHAR(120) NOT NULL,
  due_date DATE NOT NULL
);

INSERT INTO loans (borrower, email, title, due_date)
SELECT * FROM (
  SELECT 'Ama Owusu' AS borrower, '' AS email, 'Homegoing' AS title, '2026-10-17' AS due_date
  UNION ALL SELECT 'Kofi Mensah', '', 'Bicycle Repair', '2026-10-17'
  UNION ALL SELECT 'Esi Boateng', '', 'English for Form Two', '2026-10-24'
  UNION ALL SELECT 'Yaw Adjei', '', 'Changes', '2026-10-24'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM loans);
