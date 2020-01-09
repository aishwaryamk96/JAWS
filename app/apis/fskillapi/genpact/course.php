<?php

	die(json_encode(db_query("SELECT c.course_id AS nid, c.course_id AS vid, 'und' AS language, 'class' AS type, c.name AS title, UNIX_TIMESTAMP(m.create_date) AS created, UNIX_TIMESTAMP(m.create_date) + 1297456 AS changed, CONCAT('https://jigsawacademy.net/courses/', m.slug) AS link FROM course AS c INNER JOIN course_meta AS m ON m.course_id = c.course_id WHERE m.category = 'genpact';")));

?>