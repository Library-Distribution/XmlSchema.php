<?php
require_once dirname(__FILE__) . '/XmlSchemaCompositor.php';

class XmlSchemaChoice extends XmlSchemaCompositor {
	public function coerce($value) {
		return array();
	}
}
?>