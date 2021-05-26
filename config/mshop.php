<?php

return [
	'cms' => [
		'manager' => array(
			'lists' => array(
				'type' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_cms_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_cms_list_type" ( :names
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES ( :values
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_cms_list_type"
							SET :names
								"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT :columns
								mcmslity."id" AS "cms.lists.type.id", mcmslity."siteid" AS "cms.lists.type.siteid",
								mcmslity."code" AS "cms.lists.type.code", mcmslity."domain" AS "cms.lists.type.domain",
								mcmslity."label" AS "cms.lists.type.label", mcmslity."status" AS "cms.lists.type.status",
								mcmslity."mtime" AS "cms.lists.type.mtime", mcmslity."editor" AS "cms.lists.type.editor",
								mcmslity."ctime" AS "cms.lists.type.ctime", mcmslity."pos" AS "cms.lists.type.position"
							FROM "mshop_cms_list_type" AS mcmslity
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mcmslity."id" AS "cms.lists.type.id", mcmslity."siteid" AS "cms.lists.type.siteid",
								mcmslity."code" AS "cms.lists.type.code", mcmslity."domain" AS "cms.lists.type.domain",
								mcmslity."label" AS "cms.lists.type.label", mcmslity."status" AS "cms.lists.type.status",
								mcmslity."mtime" AS "cms.lists.type.mtime", mcmslity."editor" AS "cms.lists.type.editor",
								mcmslity."ctime" AS "cms.lists.type.ctime", mcmslity."pos" AS "cms.lists.type.position"
							FROM "mshop_cms_list_type" AS mcmslity
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
								SELECT mcmslity."id"
								FROM "mshop_cms_list_type" as mcmslity
								:joins
								WHERE :cond
								ORDER BY mcmslity."id"
								OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
							) AS list
						',
						'mysql' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT mcmslity."id"
								FROM "mshop_cms_list_type" as mcmslity
								:joins
								WHERE :cond
								ORDER BY mcmslity."id"
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_cms_list_type_seq.CURRVAL FROM DUAL',
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
							SELECT :acols, mcmsli."id" AS "id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							GROUP BY :cols, mcmsli."id"
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, COUNT("id") AS "count"
						FROM (
							SELECT :acols, mcmsli."id" AS "id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							GROUP BY :cols, mcmsli."id"
							ORDER BY :order
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_cms_list"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_cms_list" ( :names
							"parentid", "key", "type", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_cms_list"
						SET :names
							"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
							"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mcmsli."id" AS "cms.lists.id", mcmsli."parentid" AS "cms.lists.parentid",
							mcmsli."siteid" AS "cms.lists.siteid", mcmsli."type" AS "cms.lists.type",
							mcmsli."domain" AS "cms.lists.domain", mcmsli."refid" AS "cms.lists.refid",
							mcmsli."start" AS "cms.lists.datestart", mcmsli."end" AS "cms.lists.dateend",
							mcmsli."config" AS "cms.lists.config", mcmsli."pos" AS "cms.lists.position",
							mcmsli."status" AS "cms.lists.status", mcmsli."mtime" AS "cms.lists.mtime",
							mcmsli."editor" AS "cms.lists.editor", mcmsli."ctime" AS "cms.lists.ctime"
						FROM "mshop_cms_list" AS mcmsli
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mcmsli."id" AS "cms.lists.id", mcmsli."parentid" AS "cms.lists.parentid",
							mcmsli."siteid" AS "cms.lists.siteid", mcmsli."type" AS "cms.lists.type",
							mcmsli."domain" AS "cms.lists.domain", mcmsli."refid" AS "cms.lists.refid",
							mcmsli."start" AS "cms.lists.datestart", mcmsli."end" AS "cms.lists.dateend",
							mcmsli."config" AS "cms.lists.config", mcmsli."pos" AS "cms.lists.position",
							mcmsli."status" AS "cms.lists.status", mcmsli."mtime" AS "cms.lists.mtime",
							mcmsli."editor" AS "cms.lists.editor", mcmsli."ctime" AS "cms.lists.ctime"
						FROM "mshop_cms_list" AS mcmsli
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
							SELECT mcmsli."id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							ORDER BY mcmsli."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcmsli."id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							ORDER BY mcmsli."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_cms_list_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_cms"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_cms" ( :names
						"url", "label", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_cms"
					SET :names
						"url" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcms."id" AS "cms.id", mcms."siteid" AS "cms.siteid",
						mcms."url" AS "cms.url", mcms."label" AS "cms.label",
						mcms."status" AS "cms.status", mcms."mtime" AS "cms.mtime",
						mcms."editor" AS "cms.editor", mcms."ctime" AS "cms.ctime"
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY :columns :group
						mcms."id", mcms."siteid", mcms."url", mcms."label",
						mcms."status", mcms."mtime", mcms."editor", mcms."ctime"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcms."id" AS "cms.id", mcms."siteid" AS "cms.siteid",
						mcms."url" AS "cms.url", mcms."label" AS "cms.label",
						mcms."status" AS "cms.status", mcms."mtime" AS "cms.mtime",
						mcms."editor" AS "cms.editor", mcms."ctime" AS "cms.ctime"
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY :group mcms."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcms."id"
						FROM "mshop_cms" AS mcms
						:joins
						WHERE :cond
						GROUP BY mcms."id"
						ORDER BY mcms."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcms."id"
						FROM "mshop_cms" AS mcms
						:joins
						WHERE :cond
						GROUP BY mcms."id"
						ORDER BY mcms."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_cms_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		)
	],
    'post' => [
		'manager' => array(
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
		)
	],
    'category' => array(
        'manager' => array(
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
        ),
    ),
	'locale' => [
		'manager' => [
			'site' => [
				'cleanup' => [
					'shop' => [
						'domains' => [
							'cms' => 'cms'
						]
					]
				]
			]
		]
	]
];
