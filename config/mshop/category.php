<?php

return [
    'manager' => [
        'lists' => array(
            'type' => array(
                'delete' => array(
                    'ansi' => '
                        DELETE FROM "mshop_category_list_type"
                        WHERE :cond AND siteid = ?
                    '
                ),
                'insert' => array(
                    'ansi' => '
                        INSERT INTO "mshop_category_list_type" ( :names
                            "code", "domain", "label", "pos", "status",
                            "mtime", "editor", "siteid", "ctime"
                        ) VALUES ( :values
                            ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    '
                ),
                'update' => array(
                    'ansi' => '
                        UPDATE "mshop_category_list_type"
                        SET :names
                            "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
                            "status" = ?, "mtime" = ?, "editor" = ?
                        WHERE "siteid" = ? AND "id" = ?
                    '
                ),
                'search' => array(
                    'ansi' => '
                        SELECT :columns
                            mcmscatlity."id" AS "category.lists.type.id", mcmscatlity."siteid" AS "category.lists.type.siteid",
                            mcmscatlity."code" AS "category.lists.type.code", mcmscatlity."domain" AS "category.lists.type.domain",
                            mcmscatlity."label" AS "category.lists.type.label", mcmscatlity."mtime" AS "category.lists.type.mtime",
                            mcmscatlity."editor" AS "category.lists.type.editor", mcmscatlity."ctime" AS "category.lists.type.ctime",
                            mcmscatlity."status" AS "category.lists.type.status", mcmscatlity."pos" AS "category.lists.type.position"
                        FROM "mshop_category_list_type" AS mcmscatlity
                        :joins
                        WHERE :cond
                        ORDER BY :order
                        OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                    ',
                    'mysql' => '
                        SELECT :columns
                            mcmscatlity."id" AS "category.lists.type.id", mcmscatlity."siteid" AS "category.lists.type.siteid",
                            mcmscatlity."code" AS "category.lists.type.code", mcmscatlity."domain" AS "category.lists.type.domain",
                            mcmscatlity."label" AS "category.lists.type.label", mcmscatlity."mtime" AS "category.lists.type.mtime",
                            mcmscatlity."editor" AS "category.lists.type.editor", mcmscatlity."ctime" AS "category.lists.type.ctime",
                            mcmscatlity."status" AS "category.lists.type.status", mcmscatlity."pos" AS "category.lists.type.position"
                        FROM "mshop_category_list_type" AS mcmscatlity
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
                            SELECT mcmscatlity."id"
                            FROM "mshop_category_list_type" AS mcmscatlity
                            :joins
                            WHERE :cond
                            ORDER BY mcmscatlity."id"
                            OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
                        ) AS list
                    ',
                    'mysql' => '
                        SELECT COUNT(*) AS "count"
                        FROM (
                            SELECT mcmscatlity."id"
                            FROM "mshop_category_list_type" AS mcmscatlity
                            :joins
                            WHERE :cond
                            ORDER BY mcmscatlity."id"
                            LIMIT 10000 OFFSET 0
                        ) AS list
                    '
                ),
                'newid' => array(
                    'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
                    'mysql' => 'SELECT LAST_INSERT_ID()',
                    'oracle' => 'SELECT mshop_category_list_type_seq.CURRVAL FROM DUAL',
                    'pgsql' => 'SELECT lastval()',
                    'sqlite' => 'SELECT last_insert_rowid()',
                    'sqlsrv' => 'SELECT @@IDENTITY',
                    'sqlanywhere' => 'SELECT @@IDENTITY',
                ),
            ),
            'aggregate' => array(
                'ansi' => '
                    SELECT :keys, :type("val") AS "value"
                    FROM (
                        SELECT :acols, :val AS "val"
                        FROM "mshop_category_list" AS mcmscatli
                        :joins
                        WHERE :cond
                        GROUP BY :cols, mcmscatli."id"
                        ORDER BY :order
                        OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                    ) AS list
                    GROUP BY :keys
                ',
                'mysql' => '
                    SELECT :keys, :type("val") AS "value"
                    FROM (
                        SELECT :acols, :val AS "val"
                        FROM "mshop_category_list" AS mcmscatli
                        :joins
                        WHERE :cond
                        GROUP BY :cols, mcmscatli."id"
                        ORDER BY :order
                        LIMIT :size OFFSET :start
                    ) AS list
                    GROUP BY :keys
                '
            ),
            'delete' => array(
                'ansi' => '
                    DELETE FROM "mshop_category_list"
                    WHERE :cond AND siteid = ?
                '
            ),
            'insert' => array(
                'ansi' => '
                    INSERT INTO "mshop_category_list" ( :names
                        "parentid", "key", "type", "domain", "refid", "start", "end",
                        "config", "pos", "status", "mtime", "editor", "siteid", "ctime"
                    ) VALUES ( :values
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                '
            ),
            'update' => array(
                'ansi' => '
                    UPDATE "mshop_category_list"
                    SET :names
                            "parentid" = ?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
                            "end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
                    WHERE "siteid" = ? AND "id" = ?
                '
            ),
            'search' => array(
                'ansi' => '
                    SELECT :columns
                        mcmscatli."id" AS "category.lists.id", mcmscatli."parentid" AS "category.lists.parentid",
                        mcmscatli."siteid" AS "category.lists.siteid", mcmscatli."type" AS "category.lists.type",
                        mcmscatli."domain" AS "category.lists.domain", mcmscatli."refid" AS "category.lists.refid",
                        mcmscatli."start" AS "category.lists.datestart", mcmscatli."end" AS "category.lists.dateend",
                        mcmscatli."config" AS "category.lists.config", mcmscatli."pos" AS "category.lists.position",
                        mcmscatli."status" AS "category.lists.status", mcmscatli."mtime" AS "category.lists.mtime",
                        mcmscatli."editor" AS "category.lists.editor", mcmscatli."ctime" AS "category.lists.ctime"
                    FROM "mshop_category_list" AS mcmscatli
                    :joins
                    WHERE :cond
                    ORDER BY :order
                    OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
                ',
                'mysql' => '
                    SELECT :columns
                        mcmscatli."id" AS "category.lists.id", mcmscatli."parentid" AS "category.lists.parentid",
                        mcmscatli."siteid" AS "category.lists.siteid", mcmscatli."type" AS "category.lists.type",
                        mcmscatli."domain" AS "category.lists.domain", mcmscatli."refid" AS "category.lists.refid",
                        mcmscatli."start" AS "category.lists.datestart", mcmscatli."end" AS "category.lists.dateend",
                        mcmscatli."config" AS "category.lists.config", mcmscatli."pos" AS "category.lists.position",
                        mcmscatli."status" AS "category.lists.status", mcmscatli."mtime" AS "category.lists.mtime",
                        mcmscatli."editor" AS "category.lists.editor", mcmscatli."ctime" AS "category.lists.ctime"
                    FROM "mshop_category_list" AS mcmscatli
                    USE INDEX (unq_mscmscatli_pid_sid_dm_ty_rid, idx_mscmscatli_pid_dm_sid_pos_rid, idx_mscmscatli_rid_dom_sid_ty, idx_mscmscatli_key_sid)
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
                        SELECT mcmscatli."id"
                        FROM "mshop_category_list" AS mcmscatli
                        :joins
                        WHERE :cond
                        ORDER BY mcmscatli."id"
                        OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
                    ) AS list
                ',
                'mysql' => '
                    SELECT COUNT(*) AS "count"
                    FROM (
                        SELECT mcmscatli."id"
                        FROM "mshop_category_list" AS mcmscatli
                        :joins
                        WHERE :cond
                        ORDER BY mcmscatli."id"
                        LIMIT 10000 OFFSET 0
                    ) AS list
                '
            ),
            'newid' => array(
                'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
                'mysql' => 'SELECT LAST_INSERT_ID()',
                'oracle' => 'SELECT mshop_category_list_seq.CURRVAL FROM DUAL',
                'pgsql' => 'SELECT lastval()',
                'sqlite' => 'SELECT last_insert_rowid()',
                'sqlsrv' => 'SELECT @@IDENTITY',
                'sqlanywhere' => 'SELECT @@IDENTITY',
            ),
        ),
        'cleanup' => array(
            'ansi' => '
                DELETE FROM "mshop_category"
                WHERE :siteid AND "nleft" >= ? AND "nright" <= ?
            '
        ),
        'delete' => array(
            'ansi' => '
                DELETE FROM "mshop_category"
                WHERE "siteid" = :siteid AND "nleft" >= ? AND "nright" <= ?
            '
        ),
        'get' => array(
            'ansi' => '
                SELECT :columns
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft" AS "left", mcmscat."nright" AS "right",
                    mcmscat."mtime", mcmscat."editor", mcmscat."ctime", mcmscat."target"
                FROM "mshop_category" AS mcmscat, "mshop_category" AS parent
                WHERE mcmscat."siteid" = :siteid AND mcmscat."nleft" >= parent."nleft"
                    AND mcmscat."nleft" <= parent."nright"
                    AND parent."siteid" = :siteid AND parent."id" = ?
                    AND mcmscat."level" <= parent."level" + ? AND :cond
                GROUP BY :columns
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft", mcmscat."nright", mcmscat."target",
                    mcmscat."mtime", mcmscat."editor", mcmscat."ctime"
                ORDER BY mcmscat."nleft"
            '
        ),
        'insert' => array(
            'ansi' => '
                INSERT INTO "mshop_category" (
                    "siteid", "label", "code", "status", "parentid", "level",
                    "nleft", "nright", "config", "mtime", "ctime", "editor", "target"
                ) VALUES (
                    :siteid, ?, ?, ?, ?, ?, ?, ?, \'\', \'1970-01-01 00:00:00\', \'1970-01-01 00:00:00\', \'\', \'\'
                )
            '
        ),
        'insert-usage' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET :names "url" = ?, "config" = ?, "mtime" = ?, "editor" = ?, "target" = ?, "ctime" = ?
                WHERE "siteid" = ? AND "id" = ?
            '
        ),
        'update' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET "label" = ?, "code" = ?, "status" = ?
                WHERE "siteid" = :siteid AND "id" = ?
            '
        ),
        'update-parentid' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET "parentid" = ?
                WHERE "siteid" = :siteid AND "id" = ?
            '
        ),
        'update-usage' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET "url" = ?, "config" = ?, "mtime" = ?, "editor" = ?, "target" = ?
                WHERE "siteid" = ? AND "id" = ?
            '
        ),
        'move-left' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET "nleft" = "nleft" + ?, "level" = "level" + ?
                WHERE "siteid" = :siteid AND "nleft" >= ? AND "nleft" <= ?
            '
        ),
        'move-right' => array(
            'ansi' => '
                UPDATE "mshop_category"
                SET "nright" = "nright" + ?
                WHERE "siteid" = :siteid AND "nright" >= ? AND "nright" <= ?
            '
        ),
        'search' => array(
            'ansi' => '
                SELECT :columns
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft" AS "left", mcmscat."nright" AS "right",
                    mcmscat."mtime", mcmscat."editor", mcmscat."ctime", mcmscat."target"
                FROM "mshop_category" AS mcmscat
                WHERE mcmscat."siteid" = :siteid AND mcmscat."nleft" >= ?
                    AND mcmscat."nright" <= ? AND :cond
                ORDER BY :order
            '
        ),
        'search-item' => array(
            'ansi' => '
                SELECT :columns
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft" AS "left", mcmscat."nright" AS "right",
                    mcmscat."mtime", mcmscat."editor", mcmscat."ctime", mcmscat."target"
                FROM "mshop_category" AS mcmscat
                :joins
                WHERE :cond
                GROUP BY :columns :group
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft", mcmscat."nright", mcmscat."mtime", mcmscat."editor",
                    mcmscat."ctime", mcmscat."target"
                ORDER BY :order
                OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
            ',
            'mysql' => '
                SELECT :columns
                    mcmscat."id", mcmscat."code", mcmscat."url", mcmscat."label", mcmscat."config",
                    mcmscat."status", mcmscat."level", mcmscat."parentid", mcmscat."siteid",
                    mcmscat."nleft" AS "left", mcmscat."nright" AS "right",
                    mcmscat."mtime", mcmscat."editor", mcmscat."ctime", mcmscat."target"
                FROM "mshop_category" AS mcmscat
                :joins
                WHERE :cond
                GROUP BY :group mcmscat."id"
                ORDER BY :order
                LIMIT :size OFFSET :start
            '
        ),
        'count' => array(
            'ansi' => '
                SELECT COUNT(*) AS "count"
                FROM (
                    SELECT mcmscat."id"
                    FROM "mshop_category" AS mcmscat
                    :joins
                    WHERE :cond
                    GROUP BY mcmscat."id"
                    ORDER BY mcmscat."id"
                    OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
                ) AS list
            ',
            'mysql' => '
                SELECT COUNT(*) AS "count"
                FROM (
                    SELECT mcmscat."id"
                    FROM "mshop_category" AS mcmscat
                    :joins
                    WHERE :cond
                    GROUP BY mcmscat."id"
                    ORDER BY mcmscat."id"
                    LIMIT 10000 OFFSET 0
                ) AS list
            '
        ),
        'newid' => array(
            'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
            'mysql' => 'SELECT LAST_INSERT_ID()',
            'oracle' => 'SELECT mshop_category_seq.CURRVAL FROM DUAL',
            'pgsql' => 'SELECT lastval()',
            'sqlite' => 'SELECT last_insert_rowid()',
            'sqlsrv' => 'SELECT @@IDENTITY',
            'sqlanywhere' => 'SELECT @@IDENTITY',
        ),
        'lock' => array(
            'db2' => 'LOCK TABLE "mshop_category" IN EXCLUSIVE MODE',
            'mysql' => "DO GET_LOCK('aimeos.category', -1)", // LOCK TABLE implicit commits transactions
            'oracle' => 'LOCK TABLE "mshop_category" IN EXCLUSIVE MODE',
            'pgsql' => 'LOCK TABLE ONLY "mshop_category" IN EXCLUSIVE MODE',
            'sqlanywhere' => 'LOCK TABLE "mshop_category" IN EXCLUSIVE MODE',
            'sqlsrv' => "EXEC sp_getapplock @Resource = 'aimeos.category', @LockMode = 'Exclusive'",
        ),
        'unlock' => array(
            'mysql' => "DO RELEASE_LOCK('aimeos.category')",
            'sqlsrv' => "EXEC sp_releaseapplock @Resource = 'aimeos.category'",
        ),
    ]
];
