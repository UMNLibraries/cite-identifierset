<?php

class Cite_IdentifierSet
{

    protected $mysqli;
    protected $source;

    public function __construct( $params )
    {
        $mysqli = $params['mysqli'];
        if (!($mysqli instanceof mysqli)) {
            throw new Exception("Missing or invalid mysqli database handle in constructor parameters.");
        }
        $this->mysqli = $mysqli;

        $source = $params['source'];
        if (!isset($source)) {
            throw new Exception("Missing 'source' in constructor parameters.");
        }
        $this->source = $source;
    
        $this->select_stmt = $mysqli->prepare("
            select count(*)
            from cite_source
            where source = ?
              and source_id = ?
        ");
        if ($mysqli->error) {
            throw new Exception( $mysqli->error );
        }
    }

    public function has_member( $source_id )
    {
        $select_stmt = $this->select_stmt;
        $select_stmt->bind_param("ss", $this->source, $source_id);
        $select_stmt->execute();

        /* This special method, store_result, is necessary
         * to retrieve blob values. May be unnecessary.
         */
        $select_stmt->store_result();

        unset($count);
        $select_stmt->bind_result($count);
        $select_stmt->fetch();

        return $count > 0 ? true : false;
    }

    // TODO: This is just a klugdey no-op for now. Do something smarter
    // here, along with XML_Record_Deduplicator.
    public function add_member()
    {

    }

} // end class Cite_IdentifierSet

?>
