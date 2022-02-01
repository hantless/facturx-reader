<?php

/**
 * This is a class reader for the Factur-X XML
 *
 */

class FacturxReader
{
    private $_xml;
    private $_xpath;

    public function __construct(string $facturxXml)
    {
        $this->_xml = $facturxXml;
        $this->_loadDOMXPath();
    }

    public function getId()
    {
        return $this->_query(self::INVOICE_ID)->nodeValue;
    }

    public function getDocumentType()
    {
        return $this->_query(self::DOCUMENT_TYPE)->nodeValue;
    }

    public function getDocumentLines()
    {
        return $this->_query(self::DOCUMENT_LINES, true);
    }

    private function _query(string $query_path, $multiple = false)
    {
        $data = $this->_xpath->query($query_path);

        if (false === $data) {
            throw new Exception('Malformed expression or contextNode invalid.');
        }

        if ($data->length <= 0) {
            throw new Exception('No result.');
        }

        if ($multiple) {
            $array = [];
            $count = $data->length;
            for($i = 0; $i < $count; $i++) {
                $domdoc = new DOMDocument();
                $node = $data->item($i);
                $domdoc->append($node);
                $array[] = new DOMXPath($domdoc);
            }
            return $array;
        }

        return $data->item(0);
    }

    private function _loadDOMXPath()
    {
        $domxpath = new DOMDocument();
        $domxpath->loadXML($this->_xml);
        $this->_xpath = new DOMXPath($domxpath);
    }

    /**
     * XPath to every piece of data
     *
     * available in Factur-X 1.0.05 2020 03 24 FR VF.pdf
     * starting page 41
     */
    const INVOICE_ID = '/rsm:CrossIndustryInvoice/rsm:ExchangedDocument/ram:ID';
    const DOCUMENT_TYPE = '/rsm:CrossIndustryInvoice/rsm:ExchangedDocument/ram:TypeCode';

    const DOCUMENT_LINES = '/rsm:CrossIndustryInvoice/rsm:SupplyChainTradeTransaction/ram:IncludedSupplyChainTradeLineItem';
}