<?php
	/**
	 * MongoDB wrapper for PHP
	 *
	 * @author iamanders <anders@iamanders.com>
	 * @todo slave-stuff, replication stuff
	 */
	class db_mongo {
		
		private $host;
		private $port;
		private $persistant_string;
		private $timeout;
		
		private $_mongo;
		private $_db;
		private $_collection;
		private $_collection_name;
		
		
		/**
		 * Constructor
		 */
		public function __construct($host = 'localhost', $port = '27017', $persistant_string = 'php', $timeout = 3000) {
			$this->host = $host;
			$this->port = $port;
			$this->persistant_string = $persistant_string;
			$this->timeout = $timeout;
		}
		
		
		/**
		 * Connect to MongoDB server
		 * @return bool
		 */
		public function connect() {
			try {
				//$mongo = new Mongo(sprintf('mongodb://%s:%s', $this->host, $this->port), array('persist' => $this->persistant_string, 'timeout' => $this->timeout));
				$mongo = new Mongo(sprintf('mongodb://%s:%s', $this->host, $this->port), array('timeout' => $this->timeout));
			} catch(Exception $e) {
				throw new Exception($e->getMessage());
			}
			$this->_mongo = $mongo;
			return true;
		}
		
		
		/**
		 * Select DB
		 * @return bool
		 */
		public function set_db($db_name) {
			if(!$this->_mongo) { throw new Exception('Not connected'); }
			$this->_db = $this->_mongo->selectDB($db_name);
			return true;
		}
		
		
		/**
		 * Select collection
		 * @return bool
		 */
		private function select_collection($collection) {
			if($this->_collection_name == $collection) { return true; }
			$this->_collection = $this->_db->selectCollection($collection);
			$this->_collection_name = $collection;
			return true;
		}
		
		
		/**
		 * Query
		 * @return array
		 */
		public function query($collection, $find = null, $limit = null) {
			$return = array();
			$this->select_collection($collection);
			
			if(is_array($find) && $limit > 0) {
				$cursor = $this->_collection->find($find)->limit($limit);
			} elseif(is_array($find)) {
				$cursor = $this->_collection->find($find);
			} else {
				$cursor = $this->_collection->find();
			}
			$return = iterator_to_array($cursor, false);
			
			return $return;
		}
		
		
		
		/**
		 * Query one row
		 */
		public function query_one($collection, $find = null) {
			$return = $this->query($collection, $find, 1);
			
			if(count($return) > 0) {
				return $return[0];
			}
			
			return null;
		}
		
		
		/**
		 * Insert
		 * @return Mongo ID id safe inserts, else bool
		 */
		public function insert($collection, $data, $safe = true) {
			$this->select_collection($collection);
			if($safe) {
				$this->_collection->insert($data, array('safe' => $safe));
				return $data['_id'];
			} else {
				return $this->_collection->insert($data, array('safe' => $safe));
			}
		}
		
		
		
		/**
		 * Update
		 * @return bool
		 */
		public function update($collection, $data, $where, $safe = true, $multiple = false, $upsert = false) {
			$this->select_collection($collection);
			return $this->_collection->update($where, $data, array('safe' => $safe, 'multiple' => $multiple, 'upsert' => $upsert));
		}
		
		
	}
		
?>