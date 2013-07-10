<?php
abstract class XmlSchemaObject {
	protected $schema;
	protected $node;

	protected static $dummy_doc;

	public function __construct($schema, $node) {
		$this->schema = $schema;
		$this->node = $node;

		if (!self::$dummy_doc) {
			self::$dummy_doc = new DOMDocument();
			self::$dummy_doc->appendChild(self::$dummy_doc->createElement('dummy'));
		}
	}

	# Returns an array of DOMNode instances or simple types
	public abstract function coerce($value);
}
?>