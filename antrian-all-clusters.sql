SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'users'
        AND COLUMN_NAME = 'all_clusters'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE users ADD COLUMN all_clusters TINYINT(1) NOT NULL DEFAULT 0 AFTER loket_id',
    'SELECT 1'
);

PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;

UPDATE users
SET all_clusters = 1, loket_id = NULL
WHERE email = 'loket1@puskesmas.test';

INSERT INTO migrations (migration, batch)
SELECT '2026_09_25_000002_add_all_clusters_to_users_table', 1
WHERE NOT EXISTS (
    SELECT 1
    FROM migrations
    WHERE migration = '2026_09_25_000002_add_all_clusters_to_users_table'
);
