<?php

class HtmlTokenizer {

	protected $document = null;

	public function __construct($html) {
		libxml_use_internal_errors(true);
		$this->document = new DomDocument();
		//$this->document->formatOutput = false;
		$this->document->loadHTML($html);
		libxml_use_internal_errors(false);
	}

	public function tokens($length = 100) {
		$body = $this->document->getElementsByTagName('body')->item(0);
		$contents = $this->getTokenContents($body);

		if (strlen($contents) > $length) {
			$tokens = array();
			$biggestToken = '';
			foreach ($body->childNodes as $child) {

				$childContent = $this->getTokenContents($child);
				$childContentLenght = strlen($childContent);
				if (!trim($childContent)) {
					continue;
				}

				if (strlen($biggestToken) +  $childContentLenght <= $length) {
					$biggestToken .= $childContent;
				} else if ($childContentLenght > $length && $child->hasChildNodes()) {
					$tokens[] = $biggestToken;
					$biggestToken = '';
					$tokens[] = "<{$child->tagName}>";
					foreach ($child->childNodes as $c) {
						$a = $this->getTokenContents($c);
						if (!$c->hasChildNodes()) {
							$tokens[] = $a;
							continue;
						}
						$tokenizer = new HtmlTokenizer($a);
						$tokens = array_merge($tokens, $tokenizer->tokens($this->getTokenContents($c)));
					}
					$tokens[] = "</{$child->tagName}>";
				} else {
					$tokens[] = $biggestToken;
					$biggestToken = $childContent;
				}

			}
			return $tokens;
		}
	}

	protected function getTokenContents($node) {
		if ($node instanceof DomText) {
			return $node->textContent;
		}
		return $node->C14N();
	}
}