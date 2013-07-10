<?php
require_once dirname(__FILE__) . '/XmlSchemaCompositor.php';

class XmlSchemaSequence extends XmlSchemaCompositor {
	private $subElements = NULL;

	# todo: support any, choice, group, sequence as content
	public function coerce($value) {
		$nodes = array();

		if (!is_array($value)) {
			throw new Exception('Must be array');
		}
		$sub_elements = $this->getSubElements();

		# 1. one type of element
		if (count($sub_elements) === 1) {
			# assume continous numerical array
			if (array_keys($value) !== range(0, count($value) - 1)) {
				throw new Exception('Must be numerical array');
			}

			# convert each value to one element
			$elem = $sub_elements[0];
			foreach ($value AS $item) {
				$nodes = array_merge($nodes, $elem->coerce($item));
			}

		# 2. several element types
		} else {
			# a. is numerical array? (caller must remove handled attributes)
				# assume fixed min-max-counts
				# convert each value to one of the element types, according to element type count
			# b. else
				# assume sub-arrays, with element name as key
				# convert each sub-array to a series of elements
				# check: min-max-count restrictions
		}

		return $nodes;
	}

	private function getSubElements() {
		if ($this->subElements === NULL) {
			$this->subElements = array();
			foreach ($this->schema->query('xsd:element', $this->node) AS $node) {
				$this->subElements[] = new XmlSchemaElement($this->schema, $node);
			}
		}
		return $this->subElements;
	}
}
?>