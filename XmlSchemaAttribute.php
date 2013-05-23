<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaAttribute extends XmlSchemaNode {
	public function isRequired() {
		return $this->elem->getAttribute('use') == 'required';
	}
}
?>