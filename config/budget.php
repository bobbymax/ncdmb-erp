<?php

return [
    'budget_year' => 2022,

    'organization' => [
        'name' => env('ORG_NAME', 'NCDMB'),
        'url' => env('ORG_URL', 'https://budget.ncdmb.gov.ng'),
        'api' => env('ORG_API', 'https://budget-api.ncdmb.gov.ng')
    ]
];
