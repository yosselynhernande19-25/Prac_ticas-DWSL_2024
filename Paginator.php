<?php

class Paginator
{
    protected $perPage;
    protected $currentPage;
    protected $totalItems;
    protected $items;

    public function __construct($items, $perPage, $currentPage, $totalItems)
    {
        $this->items = $items;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->totalItems = $totalItems;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getTotalPages()
    {
        return ceil($this->totalItems / $this->perPage);
    }

    public function hasMorePages()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getNextPage()
    {
        return $this->hasMorePages() ? $this->currentPage + 1 : null;
    }

    public function getPreviousPage()
    {
        return $this->currentPage > 1 ? $this->currentPage - 1 : null;
    }
}