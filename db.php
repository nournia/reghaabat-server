<?php require('begin.php') ?>
<?php

if (isset($_GET['rebuild'])) {

$commands = array(
'drop table if exists reghaabats',
'drop table if exists logs'
);

$creates = array(
'create table reghaabats (
	id integer not null primary key auto_increment,
	title varchar(255) not null,
	description varchar(1000) null default null,
	synced_at timestamp null default null,
	license varchar(255) null default null)',

'create table logs (
	reghaabat_id integer not null,
	table_name enum("users", "accounts", "permissions", "authors", "publications", "files", "matches", "questions", "answers", "library", "supports", "scores", "transactions", "open_categories", "open_scores", "roots", "branches", "objects", "borrows") not null,
	row_op enum("insert","update", "delete") not null,
	row_id integer not null,
	row_data text null,
	user_id integer null default null,
	created_at timestamp null default null)'
);
$table_conf = ' collate=utf8_general_ci engine=InnoDB';

foreach ($creates as $command)
	$commands[] = $command . $table_conf;

foreach ($commands as $command)
	mysql_query($command);

echo 'Database Rebuilt.';
}

?>
<?php require('end.php') ?>
