<?php
require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Methods', '*');
});

$pdo = new PDO('sqlite:' . __DIR__ . '/../db/games.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$app->get('/games', function(Request $request, Response $response) use ($pdo) {
    $stmt = $pdo->query("SELECT * FROM games ORDER BY id DESC");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/games/{id}', function(Request $request, Response $response, $args) use ($pdo) {
    $stmt = $pdo->prepare("SELECT letter, result FROM steps WHERE game_id = ?");
    $stmt->execute([(int)$args['id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($history));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/games', function(Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    if (!$data) {
        $response->getBody()->write(json_encode(["error"=>"Invalid JSON"]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $stmt = $pdo->prepare("INSERT INTO games (player, word, won, wrong, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([
        $data['player'] ?? 'Anonymous',
        $data['word'],
        $data['won'] ? 1 : 0,
        $data['wrong'] ?? 0
    ]);
    $gameId = $pdo->lastInsertId();

    foreach ($data['history'] ?? [] as $h) {
        $stmt2 = $pdo->prepare("INSERT INTO steps (game_id, letter, result) VALUES (?, ?, ?)");
        $stmt2->execute([$gameId, $h['letter'], $h['result']]);
    }

    $response->getBody()->write(json_encode(["id" => $gameId]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/step/{id}', function(Request $request, Response $response, $args) use ($pdo) {
    $data = $request->getParsedBody();

    if (!$data || !isset($data['letter']) || !isset($data['result'])) {
        $response->getBody()->write(json_encode(["error"=>"Invalid JSON"]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $stmt = $pdo->prepare("INSERT INTO steps (game_id, letter, result) VALUES (?, ?, ?)");
    $stmt->execute([(int)$args['id'], $data['letter'], $data['result']]);

    $response->getBody()->write(json_encode(["status"=>"ok"]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/', function (Request $request, Response $response) {
    $indexPath = __DIR__ . '/index.html';
    if (file_exists($indexPath)) {
        $response->getBody()->write(file_get_contents($indexPath));
        return $response->withHeader('Content-Type', 'text/html');
    }
    $response->getBody()->write("Page not found");
    return $response->withStatus(404);
});

$app->any('/{routes:.+}', function (Request $request, Response $response) {
    return $response
        ->withHeader('Location', '/')
        ->withStatus(302);
});

$app->run();