#!/usr/bin/php -q
<?php

require_once 'simpletest/autorun.php';
SimpleTest :: prefer(new TextReporter());
set_include_path('../php' . PATH_SEPARATOR . get_include_path());
require_once 'Cite/IdentifierSet.php';

ini_set('memory_limit', '2G');

//error_reporting( E_STRICT );
error_reporting( E_ALL );

class CiteIdentifierSetTest extends UnitTestCase
{
    public function __construct()
    {
        // TODO: Figure out how to use a temp db, and hopefully low
        // privileges, for this test. For now, we use environment vars.
        // Ideally, the user shouldn't have to set anything in order 
        // to run this test.
        $host = getenv('MYSQL_HOST');
        if (!isset($host)) $host = 'localhost';
        $mysqli = new mysqli(
            $host,
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASS'),
            'mysql'
        );

        if (mysqli_connect_errno()) {
            throw new Exception("Connect failed: " . mysqli_connect_error());
        }

        $this->source = 'PubMed';
        $this->good_id = '18650511';
        $this->bad_id = 'bogus';

        // Not checking errors on this query, because any error probably
        // means that the db doesn't exist:
        $mysqli->query('drop database cite_identifier_set_test');

        $queries = array(
            'create database cite_identifier_set_test',
            'use cite_identifier_set_test',
            'create table cite_source(
                nid int(10) unsigned NOT NULL,
                source_id varchar(300) NOT NULL,
                source varchar(20) NOT NULL,
                PRIMARY KEY (nid, source_id, source)
             ) ENGINE=MyISAM DEFAULT CHARSET=utf8',
            'insert into cite_source (nid, source_id, source)
             values (1,"' . $this->good_id . '","' . $this->source . '")',
        );

        foreach ($queries as $query) {
            $mysqli->query($query);
            if ($mysqli->error) {
                // TODO: Why does this exception code produce this error:
                // "Fatal error: Exception thrown without a stack frame in Unknown on line 0"
                // See http://www.phpbuilder.com/board/archive/index.php/t-10304524.html
                // http://bugs.php.net/bug.php?id=32101
                // throw new Exception( $mysqli->error );
                die( $mysqli->error );
            }
        }

        $this->mysqli = $mysqli;
    }

    public function test_new()
    {

        $id_set = new Cite_IdentifierSet(array(
            'mysqli' => $this->mysqli,
            'source' => $this->source,
        ));
        $this->assertIsA( $id_set, 'Cite_IdentifierSet' );
        $this->id_set = $id_set;
    }

    public function test_has_member()
    {
        $id_set = $this->id_set;
        $this->assertTrue( $id_set->has_member( $this->good_id ) );
        $this->assertFalse( $id_set->has_member( $this->bad_id ) );
    }

    public function test_teardown()
    {
        $this->mysqli->query('drop database cite_identifier_set_test');
    }

} // end class CiteIdentifierSetTest
