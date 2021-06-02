<?php

return [
    'manager' => [
        'domains' => [
            'post' => 'post',
            'text' => 'text',
            'category' => 'category'
        ],
        'submanagers' => [
            'text' => 'text',
        ],
        'text' => array(
            'delete' => array(
                'ansi' => '
                    DELETE FROM "mshop_post_index_text"
                    WHERE :cond AND "siteid" = ?
                '
            ),
            'insert' => array(
                'ansi' => '
                    INSERT INTO "mshop_post_index_text" (
                        "postid", "langid", "url", "name", "content", "mtime", "siteid"
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?
                    )
                ',
                'pgsql' => '
                    INSERT INTO "mshop_post_index_text" (
                        "postid", "langid", "url", "name", "content", "mtime", "siteid"
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?
                    )
                    ON CONFLICT DO NOTHING
                '
            ),
            'search' => array(
                'ansi' => '
                    SELECT mpost."id" :mincols
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY mpost."id"
                    ORDER BY :order
                    OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                ',
                'mysql' => '
                    SELECT mpost."id" :mincols
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY mpost."id"
                    ORDER BY :order
                    LIMIT :size OFFSET :start
                '
            ),
            'count' => array(
                'ansi' => '
                    SELECT COUNT(*) AS "count"
                    FROM (
                        SELECT mpost."id"
                        FROM "mshop_post" AS mpost
                        :joins
                        WHERE :cond
                        GROUP BY mpost."id"
                        ORDER BY mpost."id"
                        OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
                    ) AS list
                ',
                'mysql' => '
                    SELECT COUNT(*) AS "count"
                    FROM (
                        SELECT mpost."id"
                        FROM "mshop_post" AS mpost
                        :joins
                        WHERE :cond
                        GROUP BY mpost."id"
                        ORDER BY mpost."id"
                        LIMIT 1000 OFFSET 0
                    ) AS list
                '
            ),
            'cleanup' => array(
                'ansi' => '
                    DELETE FROM "mshop_post_index_text"
                    WHERE "mtime" < ? AND "siteid" = ?
                '
            ),
            'optimize' => array(
                'mysql' => array(
                    'OPTIMIZE TABLE "mshop_post_index_text"',
                ),
                'pgsql' => [],
                'sqlsrv' => [],
            ),
        ),
        'aggregate' => array(
            'ansi' => '
                SELECT :keys, :type("val") AS "value"
                FROM (
                    SELECT :acols, :val AS "val" :mincols
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY :cols, :val, mpost."id"
                    ORDER BY :order
                    OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                ) AS list
                GROUP BY :keys
            ',
            'mysql' => '
                SELECT :keys, :type("val") AS "value"
                FROM (
                    SELECT :acols, :val AS "val" :mincols
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY :cols, :val, mpost."id"
                    ORDER BY :order
                    LIMIT :size OFFSET :start
                ) AS list
                GROUP BY :keys
            '
        ),
        'search' => array(
            'ansi' => '
                SELECT mpost."id" :mincols
                FROM "mshop_post" AS mpost
                :joins
                WHERE :cond
                GROUP BY mpost."id"
                ORDER BY :order
                OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
            ',
            'mysql' => '
                SELECT mpost."id" :mincols
                FROM "mshop_post" AS mpost
                :joins
                WHERE :cond
                GROUP BY mpost."id"
                ORDER BY :order
                LIMIT :size OFFSET :start
            '
        ),
        'count' => array(
            'ansi' => '
                SELECT COUNT(*) AS "count"
                FROM (
                    SELECT mpost."id"
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY mpost."id"
                    ORDER BY mpost."id"
                    OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
                ) AS list
            ',
            'mysql' => '
                SELECT COUNT(*) AS "count"
                FROM (
                    SELECT mpost."id"
                    FROM "mshop_post" AS mpost
                    :joins
                    WHERE :cond
                    GROUP BY mpost."id"
                    ORDER BY mpost."id"
                    LIMIT 1000 OFFSET 0
                ) AS list
            '
        ),
        'optimize' => array(
            'mysql' => array(
                'ANALYZE TABLE "mshop_post"',
                'ANALYZE TABLE "mshop_post_list"',
            ),
            'pgsql' => [],
            'sqlsrv' => [],
        ),
    ]
];
