<?php

return [
    'payments' => [
        'approvals' => [
            'check' => [
                'office' => 'budget-office',
                'action' => 'clear',
                'modify' => false,
                'query' => false
            ],
            'clarify' => [
                'office' => 'treasury',
                'action' => 'clear',
                'modify' => true,
                'query' => false
            ],
            'verify' => [
                'office' => 'budget-office',
                'action' => 'clear',
                'modify' => false,
                'query' => true
            ],
            'posting' => [
                'office' => 'treasury',
                'action' => 'clear',
                'modify' => false,
                'query' => false
            ],
        ]
    ],

    'procurement' => [
        'threshold' => [
            [
                'name' => 'Account Officer',
                'code' => 'AO',
                'min' => 0,
                'max' => 19999999,
            ],
            [
                'name' => 'Parastal Tenders Board',
                'code' => 'PTB',
                'min' => 20000000,
                'max' => 499999999,
            ],
            [
                'name' => 'Ministarial Tenders Board',
                'code' => 'MTB',
                'min' => 500000000,
                'max' => 100000000000,
            ],
        ],

        'process' => [
            [
                'stage' => 'Pre/Post Qualification',
                'slug' => 'pre-post-qualification'
            ],
            [
                'stage' => 'Prep & Submission by MDAs',
                'slug' => 'mda-submissions'
            ],
            [
                'stage' => 'MDA Approval',
                'slug' => 'mda-submissions-approval'
            ],
            [
                'stage' => 'Advertisement for Pre-Qualification',
                'slug' => 'ads-pre-qualification'
            ],
            [
                'stage' => 'Bid Opening',
                'slug' => 'bid-opening'
            ],
            [
                'stage' => 'Bid Invitation',
                'slug' => 'bid-invitation'
            ],
            [
                'stage' => 'Bid Closing',
                'slug' => 'bid-closed'
            ],
            [
                'stage' => 'Bid Evaluation Report',
                'slug' => 'bid-evaluation'
            ],
            [
                'stage' => 'Approval by AO/MTB/FEC',
                'slug' => 'approval-by-ao-mtb-fec'
            ],
            [
                'stage' => 'Contract Offer',
                'slug' => 'offer'
            ],
            [
                'stage' => 'Contract Signature',
                'slug' => 'contract-acceptance'
            ],
            [
                'stage' => 'Commencement',
                'slug' => 'commencement'
            ],
        ],

        'evaluation' => [
            [
                'requirement' => 'Evidence of legal status of company/Registration with CAC (certificate/year of incorporation)',
                'slug' => 'cac-document',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Evidence of strict compliance with the provision of Pension Reform Act. 2004 (Registration & Remittance)',
                'slug' => 'pension-reform',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Evidence of Registration of firm with National Database of Federal contractors by submission of Interim Registration Report (IRR)',
                'slug' => 'irr-evidence',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Evidence of ITF compliance',
                'slug' => 'itf-compliance',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Evidence of compliance with the Employees Compensation Act (remittance to National Social Insurance Trust Fund, NSITF)',
                'slug' => 'nsitf-compliance',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Company’s letter headed paper with it names of Directors',
                'slug' => 'letter-headed-paper',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Submission of sworn affidavit in line with provision of part iv, section 16, sub-section 6(f) of the 2007 public procurement Act',
                'slug' => 'sworn-affidavit-provision',
                'pattern' => 'sighting',
                'value' => ['sighted', 'not-sighted'],
                'children' => []
            ],
            [
                'requirement' => 'Address of company, Head Office, and all established head offices in Nigeria:',
                'slug' => 'organization-addresses',
                'pattern' => 'scoring',
                'value' => 5,
                'children' => [
                    [
                        'name' => 'Head Office',
                        'value' => 3
                    ],
                    [
                        'name' => 'Branch Office',
                        'value' => 2
                    ]
                ]
            ],
            [
                'requirement' => 'Evidence of latest and previous published audited report and account of company for 3 years.',
                'slug' => 'audited-report',
                'pattern' => 'scoring',
                'value' => 10,
                'children' => [
                    [
                        'name' => 'Available for 3 years',
                        'value' => 10
                    ],
                    [
                        'name' => 'Available for 2 years',
                        'value' => 6
                    ],
                    [
                        'name' => 'Available for 1 years',
                        'value' => 3
                    ],
                ]
            ],
            [
                'requirement' => 'Possession of satisfactory quality Assurance Quality Control Manual',
                'slug' => 'assurance-quality',
                'pattern' => 'scoring',
                'value' => 5,
                'children' => []
            ],
            [
                'requirement' => 'Annual turnover of company relating to work: Turnover of 20m',
                'slug' => 'annual-turnover',
                'pattern' => 'scoring',
                'value' => 10,
                'children' => []
            ],
            [
                'requirement' => 'Evidence of at least 5no relevant previous experience on similar projects, executed by the company with their respective dates ',
                'slug' => 'audited-report',
                'pattern' => 'scoring',
                'value' => 10,
                'children' => [
                    [
                        'name' => 'For the last 5 years',
                        'value' => 10
                    ],
                    [
                        'name' => 'For the last 3 years',
                        'value' => 8
                    ],
                    [
                        'name' => 'For the last 1-2 years',
                        'value' => 6
                    ],
                ]
            ],
            [
                'requirement' => 'Evidence of availability of construction plant and equipment to the company',
                'slug' => 'construction-plant-equipment',
                'pattern' => 'scoring',
                'value' => 15,
                'children' => [
                    [
                        'name' => 'Full complement of construction Plant and Equipment as per prepared list',
                        'value' => 15
                    ],
                    [
                        'name' => 'Access according to shortfalls and considering size of equipment and ease of acquisition',
                        'value' => 6
                    ],
                ]
            ],
            [
                'requirement' => 'Income Tax clearance and VAT certificates with Tax Identification Number (TIN) sighted',
                'slug' => 'tax-clearance',
                'pattern' => 'scoring',
                'value' => 10,
                'children' => [
                    [
                        'name' => 'For the last 3 years',
                        'value' => 10
                    ],
                    [
                        'name' => 'For the last 2 years',
                        'value' => 6
                    ],
                    [
                        'name' => 'For the last 1 years',
                        'value' => 3
                    ],
                ]
            ],
            [
                'requirement' => 'Full Account Details including SORT CODE',
                'slug' => 'account-details',
                'pattern' => 'scoring',
                'value' => 6,
                'children' => []
            ],
            [
                'requirement' => 'Tender Memo',
                'slug' => 'tender-memo',
                'pattern' => 'scoring',
                'value' => 8,
                'children' => [
                    [
                        'name' => 'Signed Tender Memo Sighted',
                        'value' => 5
                    ],
                    [
                        'name' => 'Technical bid 2nos hard copies & 1no. Soft copy submitted',
                        'value' => 3
                    ],
                ]
            ],
            [
                'requirement' => 'Reference letter from a reputable Bank (letter of recommendation sighted)',
                'slug' => 'reference-letter',
                'pattern' => 'scoring',
                'value' => 10,
                'children' => []
            ],
            [
                'requirement' => 'Appropriate company’s profile and personnel CV including company’s project manager working experience in relevant field not later than 10yrs',
                'slug' => 'company-profile',
                'pattern' => 'scoring',
                'value' => 6,
                'children' => []
            ],
            [
                'requirement' => 'Packaging and presentation of bid documents',
                'slug' => 'packaging-presentation',
                'pattern' => 'scoring',
                'value' => 5,
                'children' => []
            ],
        ],
    ],
];
