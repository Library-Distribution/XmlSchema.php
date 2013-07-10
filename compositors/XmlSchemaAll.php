<?php
require_once dirname(__FILE__) . '/XmlSchemaCompositor.php';

class XmlSchemaAll extends XmlSchemaCompositor {
	public function coerce($value) {
		return array();
	}
}
?>