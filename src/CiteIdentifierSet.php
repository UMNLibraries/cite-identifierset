<?php

namespace UmnLib\EthicShare;

class CiteIdentifierSet
{
  protected $mysqli;
  protected $source;

  function __construct($params)
  {
    $mysqli = $params['mysqli'];
    if (!($mysqli instanceof \mysqli)) {
      throw new \InvalidArgumentException("Missing or invalid mysqli database handle in constructor parameters.");
    }
    $this->mysqli = $mysqli;

    $source = $params['source'];
    if (!isset($source)) {
      throw new \InvalidArgumentException("Missing 'source' in constructor parameters.");
    }
    $this->source = $source;

    $this->selectStmt = $mysqli->prepare("
      select count(*)
      from cite_source
      where source = ?
      and source_id = ?
      ");
    if ($mysqli->error) {
      throw new \RuntimeException($mysqli->error);
    }
  }

  function hasMember($sourceId)
  {
    $selectStmt = $this->selectStmt;
    $selectStmt->bind_param("ss", $this->source, $sourceId);
    $selectStmt->execute();

    /* This special method, store_result, is necessary
     * to retrieve blob values. May be unnecessary.
     */
    $selectStmt->store_result();

    unset($count);
    $selectStmt->bind_result($count);
    $selectStmt->fetch();

    return $count > 0 ? true : false;
  }

  // TODO: This is just a klugdey no-op for now. Do something smarter
  // here, along with XMLRecord\Deduplicator.
  function addMember()
  {
  }
}
