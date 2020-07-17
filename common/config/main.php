<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'doctrine' => [
            'class' => Zaioll\YiiDoctrine\Doctrine::class,
            'dbal' => [
                'url' => "pgsql://postgres:senha%40facil@yii2-db:5432/yii2",
            ],
            'entityPath' => [
                '@common/models/entities',
                '@backend/models/entities',
                '@console/models/entities',
                '@frontend/models/entities',
            ],
            'migrationsNamespace'   => 'console\migrations\orm',
            'migrationsPath'        => '@console/migrations/orm',
        ],
    ],
];
