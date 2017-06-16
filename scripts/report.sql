SELECT count(*) AS total_links FROM links;

SELECT count(*) AS status_count,status FROM links GROUP BY status order by status_count;

SELECT count(*) AS tag_missing_count FROM links WHERE tags IS NULL;

SELECT count(*) AS tag_empty_count FROM links WHERE tags = '';
