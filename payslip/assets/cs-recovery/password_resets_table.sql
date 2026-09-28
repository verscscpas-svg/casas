-- =====================================================================
--  password_resets TABLE
--  Run this ONCE in phpMyAdmin or MySQL CLI
-- =====================================================================

CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT,
    `emp_id`        INT(11)      NOT NULL,
    `reset_count`   INT(11)      NOT NULL DEFAULT 0,
    `last_reset_at` DATETIME     DEFAULT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_emp_id` (`emp_id`),
    CONSTRAINT `fk_pr_emp`
        FOREIGN KEY (`emp_id`)
        REFERENCES `employees` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  LOGIC:
--  1st reset  → password = "pogiangsupervisor" (bcrypt hashed)
--               → INSERT row in password_resets, reset_count = 1
--  2nd+ reset → random 10-char password (bcrypt hashed)
--               → UPDATE reset_count + 1, update last_reset_at
--
--  Actual hashed password is always saved in employees.password
--  password_resets only tracks count + timestamp
-- =====================================================================
