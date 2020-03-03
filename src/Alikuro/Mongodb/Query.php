<?php

namespace Alikuro\Mongodb;

class Query {

    protected $config;
    private $connection;
    private $manager;
    private $db = '';
    private $otherdb = '';
    private $collection = '';

    public function __construct() {
        $this->connection = resolve('Alikuro\Mongodb\Manager');
        $this->config = $this->connection->get('config');
        $this->manager = $this->connection->get('manager');
        $this->db = $this->connection->get('db');
        $this->otherdb = $this->connection->get('otherdb');
        $this->collection = $this->connection->get('collection');
    }

    /**
     * Find function, selects documents in a collection or view and returns a cursor to the selected documents.
     * @param
     *     filter (array), specifies filter items
     *        
     *         ex: ['name' => 'ali']
     * 
     *     options (array), specifies optional settings like 
     *         limit the maximum number of documents to return
     *         skip, number of documents to skip. Defaults to 0
     *         sort, the sort specification for the ordering of the results
     *         projection, determine which fields to include in the returned documents
     * 
     *         ex: [ 'limit' => 4, 'projection' => ['_id' => 0, 'name' => 1, 'email' =>1 ] ]
     *
     *     arr (bool), result mode array
     *
     * @example
     *     Xmongo::db()->user->find(['foo' => ['$ne' => null]],['projection'=>['_id'=>0]],true);
     *
     */
    public function find($filter=[],$options=[],$arr=false,$with_err=false){
        $rows = [];
        try {
            if(!empty($this->collection)){
                $destination = (empty($this->otherdb)?($this->db):$this->otherdb).".".$this->collection;
                $query = MongoDriverQuery($filter, $options);
                $cursor = $this->manager->executeQuery($destination, $query)->toArray();
                foreach ($cursor as $value){$rows[] = $value;}
                if ($arr) {
                    $rows = json_decode( MongoDBBSONtoJSON( $rows ), true);
                }
            }
        }catch(Exception $e){if($with_err){$rows = ["err"=>$e->getLine(),"message"=>$e->getMessage()];}}
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $rows;
    }

    /**
     * Find one function, selects documents in a collection or view and returns only one document.
     * @param
     *     filter (array), specifies filter items
     *        
     *         ex: ['name' => 'ali']
     * 
     *     options (array), specifies optional settings like 
     *         projection, determine which fields to include in the returned documents
     * 
     *         ex: [ 'projection' => ['_id' => 0, 'name' => 1, 'email' =>1 ] ]
     *
     *     arr (bool), result mode array
     *
     * @example
     *     Xmongo::db()->user->findOne(['foo' => ['$ne' => null]],['projection'=>['_id'=>0]],true);
     *
     */
    public function findOne($filter=[],$options=[],$arr=false,$with_err=false){
        $rows = []; $options['limit'] = 1;
        try {
            if(!empty($this->collection)){
                $destination = (empty($this->otherdb)?($this->db):$this->otherdb).".".$this->collection;
                $query = MongoDriverQuery($filter, $options);
                $cursor = $this->manager->executeQuery($destination, $query)->toArray();
                foreach ($cursor as $value){$rows[] = $value;}
                if ($arr) {
                    $rows = json_decode( MongoDBBSONtoJSON( $rows ), true);
                }
                if($rows) { $rows = $rows[0]; }
            }
        }catch(Exception $e){if($with_err){$rows = ["err"=>$e->getLine(),"message"=>$e->getMessage()];}}
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $rows;
    }

    /**
     * Count function, count the documents in a collection and returns the total accordance to the filter parameter.
     * @param
     *     filter (array), specifies filter items
     *        
     *         ex: ['name' => 'ali']
     *
     * @example
     *     Xmongo::db()->user->count( ['foo' => ['$ne' => null]] );
     *
     */
    public function count($filter=[],$with_err=false){
        $rows = 0;
        try {
            if(!empty($this->collection)){
                $command = MongoDriverCommand(["count" => $this->collection, "query" => $filter]);
                $cursor = $this->manager->executeCommand((empty($this->otherdb)?($this->db):$this->otherdb), $command)->toArray();
                if ($cursor) { $rows = json_decode(json_encode($cursor),true)[0]['n']; }
            }
        }catch(Exception $e){if($with_err){$rows = -1;}}
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $rows;
    }

    /**
     * Insert function, inserts a document or documents into a collection.
     * @param
     *     fields (array), specifies document items
     *        
     *         ex: [ 'name' => 'ali', 'sex' => 'male' ]
     *
     * @example
     *     Xmongo::db()->user->insert( [ 'name' => 'ali', 'sex' => 'male' ] );
     *
     */
    public function insert($fields=[],$with_err=false){
        $result = null;
        if($fields){
            if(!empty($this->collection)){
                $destination = (empty($this->otherdb)?($this->db):$this->otherdb).".".$this->collection;
                if(empty($fields['_id'])){$fields = ['_id' => MongoBSONObjectID()]+$fields;}
                $bulk = MongoDriverBulkWrite();
                $bulk->insert($fields);
                try {
                    $result = $this->manager->executeBulkWrite($destination, $bulk, MongoDriverWriteConcern());
                    if($result->getInsertedCount()){$result = (string)$fields['_id'];}
                } catch(Exception $e){if($with_err){$result = ["err"=>$e->getLine(),"message"=>$e->getMessage()];}}
            }
        }
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $result;
    }

    /**
     * Update function, Modifies an existing document or documents in a collection.
     * The method can modify specific fields of an existing document or documents or 
     * replace an existing document entirely, depending on the update parameter.
     * @param
     *     filter (array), specifies filter items
     * 
     *         ex: ['foo' => ['$ne' => null]]
     *
     *     fields (array), specifies document items
     *        
     *         ex: [ 'name' => 'ali', 'sex' => 'male' ]
     *
     *     options (array), specifies optional settings like 
     *         collation, allows users to specify language-specific rules for string comparison
     *         multi, update only the first matching document if FALSE, or all matching documents TRUE. FALSE as a default.
     *         upsert, if filter does not match an existing document, insert a single document. FALSE as a default.
     *        
     *         ex: [ 'multi' => true,  'upsert' => false ]
     *
     * @example
     *     Xmongo::db()->user->update( [ 'name' => 'ali' ], [ 'location' => 'Jakarta', 'geo.long' => -6.633234, 'geo.lat' => 107.050781 ], [] );
     *
     */
    public function update($filter,$fields,$options=['multi'=>false,'upsert'=>false],$with_err=false){
        $result = 0;
        if(!empty($this->collection)){
            $destination = (empty($this->otherdb)?($this->db):$this->otherdb).".".$this->collection;
            $bulk = MongoDriverBulkWrite();
            $bulk->update($filter, $fields, $options);
            try {
                $_result = $this->manager->executeBulkWrite($destination, $bulk, MongoDriverWriteConcern());
                $result = $_result->getModifiedCount();
                if(isset($options['upsert'])){
                    if($options['upsert']){
                        if($_result->getUpsertedCount()>0){
                            $result = [];
                            foreach ($_result->getUpsertedIds() as $index => $id){$result[] = (string)$id;}
                        }else{$result = $_result->getUpsertedCount();}
                    }
                }
            } catch(Exception $e){if($with_err){$result = ["err"=>$e->getLine(),"message"=>$e->getMessage()];}}
        }
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $result;
    }

    /**
     * Delete function, removes a single or many document from a collection.
     * @param
     *     filter (array), specifies filter items
     * 
     *         ex: ['foo' => ['$ne' => null]]
     *
     *     options (array), specifies optional settings like 
     *         limit, delete all matching documents (FALSE), or only the first matching document (TRUE). TRUE as a default.
     *        
     *         ex: [ 'limit' => false ]
     *
     * @example
     *     Xmongo::db()->user->delete( [ 'name' => 'ali' ] );
     *
     */
    public function delete($filter,$options=['limit'=>true],$with_err=false){
        $result = 0;
        if(!empty($this->collection)){
            $destination = (empty($this->otherdb)?($this->db):$this->otherdb).".".$this->collection;
            $bulk = MongoDriverBulkWrite();
            $bulk->delete($filter, $options);
            try {
                $_result = $this->manager->executeBulkWrite($destination, $bulk, MongoDriverWriteConcern());
                $result = $_result->getDeletedCount();
            } catch(Exception $e){if($with_err){$result = ["err"=>$e->getLine(),"message"=>$e->getMessage()];}}
        }
        $this->otherdb = ''; $this->collection = ''; unset($destination);
        return $result;
    }

    /**
     * Aggregate
     */
    public function aggregate($pipeline=[], $options=[]) {
        if (empty($pipeline)) {
            return '$pipeline is empty';
        }

        $expectedIndex = 0;     
        foreach ($pipeline as $i => $operation) {
            if ($i !== $expectedIndex) {
                return sprintf('$pipeline is not a list (unexpected index: "%s")', $i);
            }
            $expectedIndex += 1;
        }
        
        $readPreference = !empty($options['readPreference']) ? $options['readPreference'] : null;
        $command = $this->createCommand($this->collection, $pipeline, $options);
        $cursor = $this->manager->executeCommand($this->db, $command, $readPreference);
        
        if (!empty($options['useCursor'])) {
            if (isset($options['typeMap'])) {
                $cursor->setTypeMap($options['typeMap']);
            }
            return $cursor;
        }

        $result = current($cursor->toArray()); 
        return new \ArrayIterator($result->result);
    }

    /**
     * Create the aggregate command.
     *
     * @return Command
     */
    private function createCommand($collectionName, $pipeline, $options)
    {
        $cmd = [
            'aggregate' => $collectionName,
            'pipeline' => $pipeline,
        ];

        $cmd['allowDiskUse'] = !empty($options['allowDiskUse']) ? true : false;

        if ( !empty($options['collation']) ) { $cmd['collation'] = (object) $options['collation']; }
        if ( !empty($options['maxTimeMS']) ) { $cmd['maxTimeMS'] = $options['maxTimeMS']; }
        if ( !empty($options['useCursor']) ) {
            $cmd['cursor'] = isset($options["batchSize"])
                ? (object) ['batchSize' => $options["batchSize"]]
                : new \stdClass;
        }

        return MongoDriverCommand($cmd);
    }

    /**
     * Specified database, this function is to use with specified database instead the query
     * - This function return the db connection object
     * How:
     *     $this->connection->db('database_name')->{'collection_name'}->{'method'}();
     *     Example,
     *     $this->connection->db('test_db')->user->find();
     */
    public function db($db=''){
        $this->otherdb = trim($db);
        return $this;
    }

    /**
     * Magic get
     */
    public function __get($name) {
        $this->collection = $name;
        return $this;
    }

    /**
     * Magic isset
     */
    public function __isset($name) {
        return null;
    }

}
