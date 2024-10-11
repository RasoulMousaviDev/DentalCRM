<?php

namespace App\Http\Controllers;

abstract class Controller
{
    function paginate($paginator)
    {
        return [
            'items' => $paginator->items(),
            'pagiantor' => [
                'totalRecords' => $paginator->total(),
                'first' => $paginator->perPage() * ($paginator->currentPage() - 1),
                'rows' => $paginator->perPage(),
                'page' => $paginator->currentPage(),
                'rowsPerPageOptions' => [1, 5, 10, 20],
                'currentPageReportTemplate' => __('messages.paginatorTemplate'),
                'template' => 'CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown',
            ]
        ];
    }
}
