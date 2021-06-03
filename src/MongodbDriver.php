<?php

/**
 * Conversion of MongoDB Driver
 */
if (! function_exists('MongoDriverManager') ) {
    function MongoDriverManager($uri, $options=[], $driver=[]) {
        return new  \MongoDB\Driver\Manager($uri, $options, $driver);
    }
}

if (! function_exists('MongoBSONObjectID') ) {
    function MongoBSONObjectID($id=null) {
        return new \MongoDB\BSON\ObjectID($id);
    }
}

if (! function_exists('MongoBSONUTCDateTime') ) {
    function MongoBSONUTCDateTime($time=null) {
        $time = empty($time) ? ( time() * 1000 ) : $time;
        return new \MongoDB\BSON\UTCDateTime( ( is_numeric($time) && strlen($time) <= 10 ) ? $time * 1000 : $time );
    }
}

if (! function_exists('MongoBSONRegex') ) {
    function MongoBSONRegex($schema, $options=null) {
        return new \MongoDB\BSON\Regex($schema, $options);
    }
}

if (! function_exists('MongoDBBSONtoJSON') ) {
    function MongoDBBSONtoJSON($schema=[]) {
        return \MongoDB\BSON\toJSON( \MongoDB\BSON\fromPHP( empty($schema) ? [] : ( is_array($schema) ? $schema : [] ) ) );
    }
}

if (! function_exists('MongoDriverQuery') ) {
    function MongoDriverQuery($filter, $options=[]) {
        return new  \MongoDB\Driver\Query($filter, $options);
    }
}

if (! function_exists('MongoDriverBulkWrite') ) {
    function MongoDriverBulkWrite() {
        return new \MongoDB\Driver\BulkWrite();
    }
}

if (! function_exists('MongoDriverWriteConcern') ) {
    function MongoDriverWriteConcern() {
        return new \MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 15);
    }
}

if (! function_exists('MongoDriverCommand') ) {
    function MongoDriverCommand($command=null) {
        return new \MongoDB\Driver\Command($command);
    }
}

if (! function_exists('MongoDate') ) {
    function MongoDate($data, $tomongodate=true, $exception=[]) {
        date_default_timezone_set("Asia/Jakarta");

        $date_exception = array_unique( array_merge([
            'number',
            'poin',
            'phone',
            'ip',
            'total',
            'price',          
        ], ( is_array($exception) ? $exception: [] )) );

        if( is_array($data) || is_object($data) ) {
            $pass = true;
            if(is_object($data)){
                if(isset($data->sec)){
                    if(!$tomongodate){ $data = date("Y-m-d H:i:s", $data->sec); $pass = false; }
                }
                try{
                    $_tmp = (String)$data;
                    if(is_numeric($_tmp) && (strlen($_tmp) == 13 || strlen($_tmp) == 12 || strlen($_tmp) == 11)){
                        if(!$tomongodate){
                            $data = date("Y-m-d H:i:s", strtotime((new \MongoDB\BSON\UTCDateTime($_tmp))->toDateTime()->format(DATE_RSS).' UTC'));
                            $pass = false;
                        }
                    }
                } catch (\Exception $e) {;} catch (\Throwable $e) {;}
                if($pass){
                    $_tmp = json_decode( json_encode( $data ), true );
                    if( isset($_tmp['$date']) ) { $data = $_tmp; }
                }
            }

            if( is_array($data) ) {
                if( isset($data['sec']) ) {
                    if(!$tomongodate){ $data = date("Y-m-d H:i:s", $data['sec']); $pass = false; }
                }
                if( isset($data['$date']) ) {
                    if( is_array( $data['$date'] ) && !empty( $data['$date']['$numberLong'] ) && !$tomongodate ) {
                        try {
                            $data = date("Y-m-d H:i:s", strtotime((new \MongoDB\BSON\UTCDateTime($data['$date']['$numberLong']))->toDateTime()->format(DATE_RSS).' UTC'));
                            $pass = false;
                        } catch (\Exception $e) {;} catch (\Throwable $e) {;}
                    }elseif( is_numeric( $data['$date'] ) && !$tomongodate ) {
                        try {
                            $data = date("Y-m-d H:i:s", strtotime((new \MongoDB\BSON\UTCDateTime($data['$date']))->toDateTime()->format(DATE_RSS).' UTC'));
                            $pass = false;
                        } catch (\Exception $e) {;} catch (\Throwable $e) {;}
                    }
                }
            }

            if($pass){
                foreach ($data as $key => $value) {

                    if($key === '_id'){ continue; }

                    if( ( is_array($value) && !empty($value) ) || is_object($value)){
                        if(is_array($data)){ $data[$key] = MongoDate($value,$tomongodate); }
                        if(is_object($data)){ $data->{$key} = MongoDate($value,$tomongodate); }
                    }

                    if(!is_array($value) && !is_object($value)){
                        if($tomongodate){
                            if((bool)strtotime($value) && !preg_match("/^(" . implode('|', $date_exception) . ")$/i", $key)) {
                                if(is_array($data)){ $data[$key] = new \MongoDB\BSON\UTCDateTime(strtotime($value)*1000); }
                                if(is_object($data)){ $data->{$key} = new \MongoDB\BSON\UTCDateTime(strtotime($value)*1000); }
                            }
                            if(is_numeric($value) && strlen($value) == 10){
                                if(is_array($data)){ try{ $data[$key] = new \MongoDB\BSON\UTCDateTime((int)$value*1000); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                                if(is_object($data)){ try{ $data->{$key} = new \MongoDB\BSON\UTCDateTime((int)$value*1000); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                            }
                            if(is_numeric($value) && strlen($value) == 13){
                                if(is_array($data)){ try{ $data[$key] = new \MongoDB\BSON\UTCDateTime((int)$value); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                                if(is_object($data)){ try{ $data->{$key} = new \MongoDB\BSON\UTCDateTime((int)$value); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                            }
                        }
                    }
                }
            }
        }else{
            if(!empty($data)){
                if($tomongodate){
                    if((bool)strtotime($data)){ $data = new \MongoDB\BSON\UTCDateTime(strtotime($data)*1000); }
                    if(is_numeric($data) && strlen($data) == 10){ try{ $data = new \MongoDB\BSON\UTCDateTime((int)$data*1000); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                    if(is_numeric($data) && strlen($data) == 13){ try{ $data = new \MongoDB\BSON\UTCDateTime((int)$data); } catch(\Exception $e){;} catch (\Throwable $e) {;} }
                }
            }
        }
        return $data;
    }
}
