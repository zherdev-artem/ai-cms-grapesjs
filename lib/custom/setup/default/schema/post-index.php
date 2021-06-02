<?php

return array(
    'exclude' => array(
		'idx_mspostindte_content',
	),

	'table' => array(
		'mshop_post_index_text' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_post_index_text' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', ['autoincrement' => true] );
			$table->addColumn( 'postid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'langid', 'string', ['length' => 5, 'notnull' => false] );
			$table->addColumn( 'url', 'string', ['length' => 255] );
			$table->addColumn( 'name', 'string', ['length' => 255] );
			$table->addColumn( 'content', 'text', ['length' => 0xffffff] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->setPrimaryKey( ['id'], 'pk_mspostindte_id' );
			$table->addUniqueIndex( ['postid', 'siteid', 'langid', 'url'], 'unq_mspostindte_pid_sid_lid_url' );
			$table->addIndex( ['postid', 'siteid', 'langid', 'name'], 'idx_mspostindte_pid_sid_lid_name' );

			return $schema;
		},
	),
);
