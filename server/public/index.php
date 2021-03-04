<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Validation\Validator;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'App\Models' => __DIR__ . '/models/',
        'App\Validators' => __DIR__ . '/validators/',
    ]
);
$loader->register();


$container = new FactoryDefault();
$container->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host' => 'db',
                'username' => 'dev',
                'password' => 'plokijuh',
                'dbname' => 'hiring',
            ]
        );
    }
);

$app = new Micro($container);

$app->get(
    '/',
    function () {
        header('Content-type: application/json');
        echo json_encode([
            'available REST endpoints:',
            'GET /api/applicants',
            'GET /api/applicants/{id}',
            'POST /api/applicants',
        ]);
    }
);

$app->get(
    '/api/applicants',
    function () use ($app) {
        $phql = "SELECT id, name, age FROM App\Models\Candidates ORDER BY age";
        $candidates = $app
            ->modelsManager
            ->executeQuery($phql);

        $data = [];

        foreach ($candidates as $cand) {
            $data[] = [
                'type' => 'applicant',
                'id' => $cand->id,
                'attributes' => [
                    'name' => $cand->name,
                    'age' => $cand->age,
                ]
            ];
        }

        header('Content-type: application/vnd.api+json'); // JSON API
        echo json_encode(['data' => $data]);
    }
);

$app->post(
    '/api/applicants',
    function () use ($app) {
        $attributes = $app->request->getJsonRawBody()->data->attributes;

        $insertQuery = 'INSERT INTO App\Models\Candidates (name, age) VALUES (:name:, :age:)';

        $status = $app->modelsManager->executeQuery(
            $insertQuery,
            [
                'name' => $attributes->name,
                'age' => $attributes->age,
            ]
        );

        $response = new Response();
        $response->setContentType('application/json', 'UTF-8');

        if (!$status->success()) {
            $response->setStatusCode(409, 'Conflict');

            $errors = [];
            foreach ($status->getMessages() as $message) {
                $errors[] = [
                    'detail' => $message->getMessage(),
                    'source' => [
                        'pointer' => $message->getField()
                    ],
                ];
            }

            $response->setContent(
                json_encode([
                    'errors' => $errors,
                ])
            );

            return $response;
        }

        $response->setStatusCode(201, 'Created');

        $attributes->id = $status->getModel()->id;

        $response->setContent(
            json_encode([
                'data' => [
                    'type' => 'applicant',
                    'id' => $attributes->id,
                    'attributes' => [
                        'name' => $attributes->name,
                        'age' => $attributes->age,
                    ]
                ]
            ])
        );
        return $response;
    }
);

$app->handle($_SERVER['REQUEST_URI']);
