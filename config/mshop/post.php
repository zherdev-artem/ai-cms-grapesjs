<?php

return [
    'manager' => [
        'lists' => array(
            'type' => array(
                'delete' => array(
                    'ansi' => '
                        DELETE FROM "mshop_post_list_type"
                        WHERE :cond AND siteid = ?
                    '
                ),
                'insert' => array(
                    'ansi' => '
                        INSERT INTO "mshop_post_list_type" ( :names
                            "code", "domain", "label", "pos", "status",
                            "mtime", "editor", "siteid", "ctime"
                        ) VALUES ( :values
                            ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    '
                ),
                'update' => array(
                    'ansi' => '
                        UPDATE "mshop_post_list_type"
                        SET :names
                            "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
                            "status" = ?, "mtime" = ?, "editor" = ?
                        WHERE "siteid" = ? AND "id" = ?
                    '
                ),
                'search' => array(
                    'ansi' => '
                        SELECT :columns
                            mpostlity."id" AS "post.lists.type.id", mpostlity."siteid" AS "post.lists.type.siteid",
                            mpostlity."code" AS "post.lists.type.code", mpostlity."domain" AS "post.lists.type.domain",
                            mpostlity."label" AS "post.lists.type.label", mpostlity."status" AS "post.lists.type.status",
                            mpostlity."mtime" AS "post.lists.type.mtime", mpostlity."editor" AS "post.lists.type.editor",
                            mpostlity."ctime" AS "post.lists.type.ctime", mpostlity."pos" AS "post.lists.type.position"
                        FROM "mshop_post_list_type" AS mpostlity
                        :joins
                        WHERE :cond
                        ORDER BY :order
                        OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                    ',
                    'mysql' => '
                        SELECT :columns
                            mpostlity."id" AS "post.lists.type.id", mpostlity."siteid" AS "post.lists.type.siteid",
                            mpostlity."code" AS "post.lists.type.code", mpostlity."domain" AS "post.lists.type.domain",
                            mpostlity."label" AS "post.lists.type.label", mpostlity."status" AS "post.lists.type.status",
                            mpostlity."mtime" AS "post.lists.type.mtime", mpostlity."editor" AS "post.lists.type.editor",
                            mpostlity."ctime" AS "post.lists.type.ctime", mpostlity."pos" AS "post.lists.type.position"
                        FROM "mshop_post_list_type" AS mpostlity
                        :joins
                        WHERE :cond
                        ORDER BY :order
                        LIMIT :size OFFSET :start
                    '
                ),
                'count' => array(
                    'ansi' => '
                        SELECT COUNT(*) AS "count"
                        FROM (
                            SELECT mpostlity."id"
                            FROM "mshop_post_list_type" as mpostlity
                            :joins
                            WHERE :cond
                            ORDER BY mpostlity."id"
                            OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
                        ) AS list
                    ',
                    'mysql' => '
                        SELECT COUNT(*) AS "count"
                        FROM (
                            SELECT mpostlity."id"
                            FROM "mshop_post_list_type" as mpostlity
                            :joins
                            WHERE :cond
                            ORDER BY mpostlity."id"
                            LIMIT 10000 OFFSET 0
                        ) AS list
                    '
                ),
                'newid' => array(
                    'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
                    'mysql' => 'SELECT LAST_INSERT_ID()',
                    'oracle' => 'SELECT mshop_post_list_type_seq.CURRVAL FROM DUAL',
                    'pgsql' => 'SELECT lastval()',
                    'sqlite' => 'SELECT last_insert_rowid()',
                    'sqlsrv' => 'SELECT @@IDENTITY',
                    'sqlanywhere' => 'SELECT @@IDENTITY',
                ),
            ),
            'aggregate' => array(
                'ansi' => '
                    SELECT :keys, COUNT("id") AS "count"
                    FROM (
                        SELECT :acols, mpostli."id" AS "id"
                        FROM "mshop_post_list" AS mpostli
                        :joins
                        WHERE :cond
                        GROUP BY :cols, mpostli."id"
                        ORDER BY :order
                        OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                    ) AS list
                    GROUP BY :keys
                ',
                'mysql' => '
                    SELECT :keys, COUNT("id") AS "count"
                    FROM (
                        SELECT :acols, mpostli."id" AS "id"
                        FROM "mshop_post_list" AS mpostli
                        :joins
                        WHERE :cond
                        GROUP BY :cols, mpostli."id"
                        ORDER BY :order
                        LIMIT :size OFFSET :start
                    ) AS list
                    GROUP BY :keys
                '
            ),
            'delete' => array(
                'ansi' => '
                    DELETE FROM "mshop_post_list"
                    WHERE :cond AND siteid = ?
                '
            ),
            'insert' => array(
                'ansi' => '
                    INSERT INTO "mshop_post_list" ( :names
                        "parentid", "key", "type", "domain", "refid", "start", "end",
                        "config", "pos", "status", "mtime", "editor", "siteid", "ctime"
                    ) VALUES ( :values
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                '
            ),
            'update' => array(
                'ansi' => '
                    UPDATE "mshop_post_list"
                    SET :names
                        "parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
                        "end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
                    WHERE "siteid" = ? AND "id" = ?
                '
            ),
            'search' => array(
                'ansi' => '
                    SELECT :columns
                        mpostli."id" AS "post.lists.id", mpostli."parentid" AS "post.lists.parentid",
                        mpostli."siteid" AS "post.lists.siteid", mpostli."type" AS "post.lists.type",
                        mpostli."domain" AS "post.lists.domain", mpostli."refid" AS "post.lists.refid",
                        mpostli."start" AS "post.lists.datestart", mpostli."end" AS "post.lists.dateend",
                        mpostli."config" AS "post.lists.config", mpostli."pos" AS "post.lists.position",
                        mpostli."status" AS "post.lists.status", mpostli."mtime" AS "post.lists.mtime",
                        mpostli."editor" AS "post.lists.editor", mpostli."ctime" AS "post.lists.ctime"
                    FROM "mshop_post_list" AS mpostli
                    :joins
                    WHERE :cond
                    ORDER BY :order
                    OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                ',
                'mysql' => '
                    SELECT :columns
                        mpostli."id" AS "post.lists.id", mpostli."parentid" AS "post.lists.parentid",
                        mpostli."siteid" AS "post.lists.siteid", mpostli."type" AS "post.lists.type",
                        mpostli."domain" AS "post.lists.domain", mpostli."refid" AS "post.lists.refid",
                        mpostli."start" AS "post.lists.datestart", mpostli."end" AS "post.lists.dateend",
                        mpostli."config" AS "post.lists.config", mpostli."pos" AS "post.lists.position",
                        mpostli."status" AS "post.lists.status", mpostli."mtime" AS "post.lists.mtime",
                        mpostli."editor" AS "post.lists.editor", mpostli."ctime" AS "post.lists.ctime"
                    FROM "mshop_post_list" AS mpostli
                    :joins
                    WHERE :cond
                    ORDER BY :order
                    LIMIT :size OFFSET :start
                '
            ),
            'count' => array(
                'ansi' => '
                    SELECT COUNT(*) AS "count"
                    FROM (
                        SELECT mpostli."id"
                        FROM "mshop_post_list" AS mpostli
                        :joins
                        WHERE :cond
                        ORDER BY mpostli."id"
                        OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
                    ) AS list
                ',
                'mysql' => '
                    SELECT COUNT(*) AS "count"
                    FROM (
                        SELECT mpostli."id"
                        FROM "mshop_post_list" AS mpostli
                        :joins
                        WHERE :cond
                        ORDER BY mpostli."id"
                        LIMIT 10000 OFFSET 0
                    ) AS list
                '
            ),
            'newid' => array(
                'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
                'mysql' => 'SELECT LAST_INSERT_ID()',
                'oracle' => 'SELECT mshop_post_list_seq.CURRVAL FROM DUAL',
                'pgsql' => 'SELECT lastval()',
                'sqlite' => 'SELECT last_insert_rowid()',
                'sqlsrv' => 'SELECT @@IDENTITY',
                'sqlanywhere' => 'SELECT @@IDENTITY',
            ),
        ),
        'delete' => array(
            'ansi' => '
                DELETE FROM "mshop_post"
                WHERE :cond AND siteid = ?
            '
        ),
        'insert' => array(
            'ansi' => '
                INSERT INTO "mshop_post" ( :names
                    "url", "label", "status", "mtime", "editor", "siteid", "ctime"
                ) VALUES ( :values
                    ?, ?, ?, ?, ?, ?, ?
                )
            '
        ),
        'update' => array(
            'ansi' => '
                UPDATE "mshop_post"
                SET :names
                    "url" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
                WHERE "siteid" = ? AND "id" = ?
            '
        ),
        'search' => array(
            'ansi' => '
                SELECT :columns
                    mpost."id" AS "post.id", mpost."siteid" AS "post.siteid",
                    mpost."url" AS "post.url", mpost."label" AS "post.label",
                    mpost."status" AS "post.status", mpost."mtime" AS "post.mtime",
                    mpost."editor" AS "post.editor", mpost."ctime" AS "post.ctime"
                FROM "mshop_post" AS mpost
                :joins
                WHERE :cond
                GROUP BY :columns :group
                    mpost."id", mpost."siteid", mpost."url", mpost."label",
                    mpost."status", mpost."mtime", mpost."editor", mpost."ctime"
                ORDER BY :order
                OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
            ',
            'mysql' => '
                SELECT :columns
                    mpost."id" AS "post.id", mpost."siteid" AS "post.siteid",
                    mpost."url" AS "post.url", mpost."label" AS "post.label",
                    mpost."status" AS "post.status", mpost."mtime" AS "post.mtime",
                    mpost."editor" AS "post.editor", mpost."ctime" AS "post.ctime"
                FROM "mshop_post" AS mpost
                :joins
                WHERE :cond
                GROUP BY :group mpost."id"
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
                    OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
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
                    LIMIT 10000 OFFSET 0
                ) AS list
            '
        ),
        'newid' => array(
            'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
            'mysql' => 'SELECT LAST_INSERT_ID()',
            'oracle' => 'SELECT mshop_post_seq.CURRVAL FROM DUAL',
            'pgsql' => 'SELECT lastval()',
            'sqlite' => 'SELECT last_insert_rowid()',
            'sqlsrv' => 'SELECT @@IDENTITY',
            'sqlanywhere' => 'SELECT @@IDENTITY',
        ),
    ]
];
