<?php
require_once dirname(__FILE__) . '/XmlSchemaSimpleType.php';

class XmlSchemaBuiltinSimpleType extends XmlSchemaSimpleType {
	public function __construct($name, $schema) {
		parent::__construct($name, $schema);
	}

	public function coerce($value) {
		$short_name = substr($this->name, strpos($this->name, ':') + 1); # remove namespace prefix

		switch ($short_name) {
			# todo: also perform validation where cannot convert (e.g. integers: positive, negative, ...)
			case 'boolean': return array(((bool)$value) ? 'true' : 'false');
			case 'string':
			default:
				return array((string)$value);
		}
	}
}
?>