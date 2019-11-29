<?php
return [
    'controllers' => [
        'invokables' => [
            'Merchandise\Controller\List' => 'Merchandise\Controller\ListController',
        ],
    ],
    'router' => [
        'routes' => [
            'list' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/merchandise',
                    'defaults' => [
                        'controller' => 'Merchandise\Controller\List',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'merchandise' => __DIR__ . '/../view',
        ],
    ],
//    'doctrine' => [
//        'driver' => [
//            'frontpage_entities' => [
//                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//                'cache' => 'array',
//                'paths' => [__DIR__ . '/../src/Frontpage/Model/']
//            ],
//            'orm_default' => [
//                'drivers' => [
//                    'Frontpage\Model' => 'frontpage_entities'
//                ]
//            ]
//        ]
//    ],
];
