<?php
// tables_config.php
$MAIN_TABLES = [
    'district' => [
        'label'      => 'Districts',
        'pk'         => 'district_id',
        'fields'     => [
            'district_name'       => ['label' => 'District Name', 'type' => 'text', 'required' => true],
            'population'          => ['label' => 'Population', 'type' => 'number'],
            'children_population' => ['label' => 'Children Population', 'type' => 'number'],
            'poverty_rate'        => ['label' => 'Poverty Rate (%)', 'type' => 'number', 'step' => '0.01'],
            'literacy_rate'       => ['label' => 'Literacy Rate (%)', 'type' => 'number', 'step' => '0.01'],
        ],
        'search_columns' => ['district_name']
    ],

    'local_crisis' => [
        'label'  => 'Local Crises',
        'pk'     => 'crisis_id',
        'fields' => [
            'crisis_name'    => ['label' => 'Crisis Name', 'type' => 'text', 'required' => true],
            'start_date'     => ['label' => 'Start Date', 'type' => 'date'],
            'end_date'       => ['label' => 'End Date', 'type' => 'date'],
            'crisis_type'    => ['label' => 'Crisis Type', 'type' => 'text'],
            'severity_level' => ['label' => 'Severity Level', 'type' => 'text'],
        ],
        'search_columns' => ['crisis_name', 'crisis_type', 'severity_level']
    ],

    'school' => [
        'label'  => 'Schools',
        'pk'     => 'school_id',
        'fields' => [
            'school_name'  => ['label' => 'School Name', 'type' => 'text', 'required' => true],
            'school_type'  => ['label' => 'School Type', 'type' => 'text'],
            'dropout_rate' => ['label' => 'Dropout Rate (%)', 'type' => 'number', 'step' => '0.01'],
            'mid_day_meal' => ['label' => 'Mid-day Meal (Y/N)', 'type' => 'text', 'maxlength' => 1],
            'district_id'  => ['label' => 'District', 'type' => 'fk', 'ref_table' => 'district',
                               'ref_pk' => 'district_id', 'ref_label' => 'district_name', 'required' => false],
        ],
        'search_columns' => ['school_name', 'school_type']
    ],

    'ngo' => [
        'label'  => 'NGOs',
        'pk'     => 'ngo_id',
        'fields' => [
            'ngo_name'       => ['label' => 'NGO Name', 'type' => 'text', 'required' => true],
            'type_service'   => ['label' => 'Type of Service', 'type' => 'text'],
            'ngo_type'       => ['label' => 'NGO Type', 'type' => 'text'],
            'ngo_service'    => ['label' => 'NGO Service', 'type' => 'text'],
            'capacity'       => ['label' => 'Capacity', 'type' => 'number'],
            'funding_source' => ['label' => 'Funding Source', 'type' => 'text'],
        ],
        'search_columns' => ['ngo_name', 'ngo_type', 'ngo_service']
    ],

    'labour' => [
        'label'  => 'Labour Types',
        'pk'     => 'labour_id',
        'fields' => [
            'labor_type'            => ['label' => 'Labor Type', 'type' => 'text', 'required' => true],
            'site_type'             => ['label' => 'Site Type', 'type' => 'text'],
            'typical_hours_per_week'=> ['label' => 'Typical Hours / Week', 'type' => 'number'],
            'typical_wage_amount'   => ['label' => 'Typical Wage Amount', 'type' => 'number', 'step' => '0.01'],
            'wage_period'           => ['label' => 'Wage Period', 'type' => 'text'],
        ],
        'search_columns' => ['labor_type', 'site_type']
    ],

    'child' => [
        'label'  => 'Children',
        'pk'     => 'child_id',
        'fields' => [
            'child_name'      => ['label' => 'Child Name', 'type' => 'text', 'required' => true],
            'age'             => ['label' => 'Age', 'type' => 'number'],
            'gender'          => ['label' => 'Gender', 'type' => 'text'],
            'parental_status' => ['label' => 'Parental Status', 'type' => 'text'],
            'grade_level'     => ['label' => 'Grade Level', 'type' => 'text'],
            'school_id'       => ['label' => 'School', 'type' => 'fk', 'ref_table' => 'school',
                                  'ref_pk' => 'school_id', 'ref_label' => 'school_name'],
        ],
        'search_columns' => ['child_name', 'gender', 'grade_level']
    ],
];
