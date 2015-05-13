<?php

namespace DDSBase\Doctrine\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Description of FileSQLLogger
 *
 * @author domanski
 */
class FileSQLLogger implements SQLLogger {

	protected $logFilename = null;
	protected $currentQuery = array();

	public function __construct($options = array()) {
		if (!empty($options['log_filename'])) {
			$this->logFilename = (string) $options['log_filename'];
		}
	}

	public function startQuery($sql, array $params = null, array $types = null) {
		if (empty($this->logFilename))
			throw new \Exception("Invalid log filename: {$this->logFilename}");

		$this->currentQuery = array('sql' => $sql, 'params' => $params, 'types' => $types, 'executionStart' => microtime(true),  'executionMS' => 0);
	}

	public function stopQuery() {
		$file = fopen($this->logFilename, "a");

		if (!$file)
			throw new \Exception("File {$this->logFilename} could not be opened");
		
		$this->currentQuery['executionMS'] = microtime(true) - $this->currentQuery['executionStart'];
		
		fwrite($file, "\n" . date("Y-m-d H:i:s") . ": " . var_export($this->currentQuery, true));
		fclose($file);
	}
}
