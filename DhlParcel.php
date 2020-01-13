<?php

namespace Keesschepers\DhlParcelApi;

class DhlParcel
{
    private $labelId;
    private $labelType;
    private $trackerCode;
    private $routingCode;
    private $userId;
    private $organizationId;
    private $orderReference;
    private $pdfContent;

    public function __construct(array $parameters)
    {
        $this->labelId = $parameters['labelId'];
        $this->labelType = $parameters['labelType'];
        $this->trackerCode = $parameters['trackerCode'];
        $this->routingCode = $parameters['routingCode'];
        $this->userId = $parameters['userId'];
        $this->organizationId = $parameters['organizationId'];
        $this->orderReference = $parameters['orderReference'];
        $this->pdfContent = $parameters['pdf'];
    }

    public function getTrackerCode()
    {
        return $this->trackerCode;
    }

    public function getLabelId()
    {
        return $this->labelId;
    }

    public function getPdfContent()
    {
        return base64_decode($this->pdfContent);
    }
}
