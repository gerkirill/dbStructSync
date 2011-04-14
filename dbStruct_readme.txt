The class provides ability to compare 2 database structure dumps and compile a set of sql statements to update
one database to make it structure identical to another.

Live demo could be found here: 
http://gerr.info/dbSync.html

The input for the script could be taken from the phpMyAdmin structure dump, or provided by some custom code
that uses 'SHOW CREATE TABLE' query to get database structure table by table.
The output is either array of sql statements suitable for executions right from php or a string where the
statements are placed each at new line and delimited with ';' - suitable for execution from phpMyAdmin SQL
page.
The resulting sql may contain queries that aim to:
Create missing table (CREATE TABLE query)
Delete table which should not longer exist (DROP TABLE query)
Update, drop or add table field or index definition (ALTER TABLE query)

Some features:
- AUTO_INCREMENT value is ommited during the comparison and in resulting CREATE TABLE sql
- fields with definitions like "(var)char (255) NOT NULL default ''" and "(var)char (255) NOT NULL" are treated
  as equal, the same for (big|tiny)int NOT NULL default 0;
- IF NOT EXISTS is automatically added to the resulting sql CREATE TABLE statement
- fields updating queries always come before key modification ones for each table
Not implemented:
- The class even does not try to insert or re-order fields in the same order as in the original table.
  Does order matter?
IMPORTANT!!! Class will not handle a case when the field was renamed. It will generate 2 queries - one to drop
the column with the old name and one to create column with the new name, so if there is a data in the dropped
column, it will be lost.
Usage example:
$updater = new dbStructUpdater();
$res = $updater->getUpdates($struct1, $struct2);
-----
$res == array (
	[0]=>"ALTER TABLE `b` MODIFY `name` varchar(255) NOT NULL",
	...
)