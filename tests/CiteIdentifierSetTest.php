<?php

namespace UmnLib\EthicShare\Tests;

use UmnLib\EthicShare\CiteIdentifierSet;

class CiteIdentifierSetTest extends \PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $host = getenv('MYSQL_HOST');
    if (!isset($host)) $host = 'localhost';
    $mysqli = new \mysqli(
      $host,
      getenv('MYSQL_USER'),
      getenv('MYSQL_PASS'),
      'mysql'
    );

    if (mysqli_connect_errno()) {
      throw new \RuntimeException("Connect failed: " . mysqli_connect_error());
    }

    $this->source = 'PubMed';
    $this->goodId = '18650511';
    $this->badId = 'bogus';

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
      values (1,"' . $this->goodId . '","' . $this->source . '")',
      );

    foreach ($queries as $query) {
      $mysqli->query($query);
      if ($mysqli->error) {
        // TODO: Why does this exception code produce this error:
        // "Fatal error: Exception thrown without a stack frame in Unknown on line 0"
        // See http://www.phpbuilder.com/board/archive/index.php/t-10304524.html
        // http://bugs.php.net/bug.php?id=32101
        // throw new Exception( $mysqli->error );
        die($mysqli->error);
      }
    }

    $this->mysqli = $mysqli;
  }

  function testNew()
  {
    $idSet = new CiteIdentifierSet(array(
      'mysqli' => $this->mysqli,
      'source' => $this->source,
    ));
    $this->assertInstanceOf('\UmnLib\EthicShare\CiteIdentifierSet', $idSet);
    $this->assertTrue($idSet->hasMember($this->goodId));
    $this->assertFalse($idSet->hasMember($this->badId));
  }

  protected function tearDown()
  {
    $this->mysqli->query('drop database cite_identifier_set_test');
  }
}
