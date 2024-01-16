<a class="text-secondary me-1" href="index.php">Back to list</a>

<?php
    require_once "database/conn.php";

    function migrateEmployee($conn)
    {
        $sql = "CREATE TABLE `employees` (
            `id` int NOT NULL AUTO_INCREMENT, 
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (mysqli_query($conn, $sql)) {
            echo "Employee table migrated successfully!\n\n";
        } else {
            echo "Failed to migrate employee table. Please try again later.\n\n";
        }
    }

    function migrateEvent($conn)
    {
        $sql = "CREATE TABLE `events` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `date` date DEFAULT NULL,
            `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (mysqli_query($conn, $sql)) {
            echo "Event table migrated successfully!\n\n";
        } else {
            echo "Failed to migrate event table. Please try again later.\n\n";
        }
    }

    function migrateParticipation($conn)
    {
        $sql = "CREATE TABLE `participations` (
            `id` int NOT NULL AUTO_INCREMENT,
            `employee_id` int NOT NULL,
            `event_id` int NOT NULL,
            `fee` double(10,2) NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `employee_id` (`employee_id`),
            KEY `event_id` (`event_id`),
            CONSTRAINT `participations_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            CONSTRAINT `participations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (mysqli_query($conn, $sql)) {
            echo "Participation table migrated successfully!\n\n";
        } else {
            echo "Failed to migrate participation table. Please try again later.\n\n";
        }
    }

    migrateEmployee($conn);
    migrateEvent($conn);
    migrateParticipation($conn);
?>

  

 