<?php

include __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Schranz\Search\Pucene\Analysis\StandardAnalyzer;
use Schranz\Search\Pucene\Connection\PuceneConnection;
use Schranz\Search\Pucene\PuceneAdapter;
use Schranz\Search\Pucene\Query\QueryFactory;
use Schranz\Search\Pucene\Schema\PuceneSchemaManager;
use Schranz\Search\Pucene\Storage\DocumentRepository;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition;

$fields = [
    'id' => new Field\IdentifierField('id'),
    'title' => new Field\TextField('title'),
    'title.raw' => new Field\KeywordField('title'),
    'article' => new Field\TextField('article'),
    'blocks' => new Field\TypedField('blocks', 'type', [
        'text' => [
            'title' => new Field\TextField('title'),
            'description' => new Field\TextField('description'),
            'media' => new Field\IntegerField('media', multiple: true),
        ],
        'embed' => [
            'title' => new Field\TextField('title'),
            'media' => new Field\IntegerField('media'),
        ],
    ], multiple: true),
    'created' => new Field\DateTimeField('created'),
    'commentsCount' => new Field\IntegerField('commentsCount'),
    'rating' => new Field\FloatField('rating'),
    'comments' => new Field\ObjectField('comments', [
        'email' => new Field\TextField('email'),
        'text' => new Field\TextField('title'),
    ], multiple: true),
    'tags' => new Field\KeywordField('tags', multiple: true),
    'categoryIds' => new Field\IntegerField('categoryIds', multiple: true),
    'published' => new Field\BooleanField('published'),
];

$prefix = 'test_'; // to avoid conflicts the indexes can be prefixed
$newsIndex = new Index($prefix . 'news', $fields);

$schema = new Schema([
    'news' => $newsIndex,
]);

$dbalConnection = DriverManager::getConnection([
    'url' => 'mysql://root@127.0.0.1:3306/pucene?serverVersion=8.0',
]);

$engine = new Engine(
    new PuceneAdapter(
        new PuceneSchemaManager($dbalConnection),
        new PuceneConnection(
            new DocumentRepository($dbalConnection),
            new QueryFactory($dbalConnection),
            new StandardAnalyzer(),
        ),
    ),
    $schema,
);

$engine->createSchema();
$engine->saveDocument('news', [
    'id' => 1,
    'title' => 'Pucene',
    'title.raw' => 'Pucene',
    'article' => '<article><h2>Some Subtitle</h2><p>A html field with some content</p></article>',
    'blocks' => [
        [
            'type' => 'text',
            'title' => 'Titel',
            'description' => '<p>Description</p>',
            'media' => [3, 4],
        ],
        [
            'type' => 'text',
            'title' => 'Titel 2',
            'description' => '<p>Description 2</p>',
        ],
        [
            'type' => 'embed',
            'title' => 'Video',
            'media' => 'https://www.youtube.com/watch?v=iYM2zFP3Zn0',
        ],
    ],
    'created' => '2022-12-24T12:00:00+01:00',
    'commentsCount' => 2,
    'rating' => 3.5,
    'comments' => [
        [
            'email' => 'admin@localhost',
            'text' => 'Awesome blog!',
        ],
        [
            'email' => 'example@localhost',
            'text' => 'Like this blog!',
        ],
    ],
    'tags' => ['Tech', 'UI'],
    'categoryIds' => [1, 2],
    'published' => false,
]);
$engine->saveDocument('news', [
    'id' => 2,
    'title' => 'Sulu',
    'title.raw' => 'Sulu',
    'article' => 'Sulu is awesome',
    'blocks' => [
        [
            'type' => 'text',
            'title' => 'Titel',
            'description' => '<p>Description</p>',
            'media' => [3, 4],
        ],
        [
            'type' => 'text',
            'title' => 'Titel 2',
            'description' => '<p>Description 2</p>',
        ],
        [
            'type' => 'embed',
            'title' => 'Video',
            'media' => 'https://www.youtube.com/watch?v=iYM2zFP3Zn0',
        ],
    ],
    'created' => '2022-12-24T12:00:00+01:00',
    'commentsCount' => 2,
    'rating' => 3.5,
    'comments' => [
        [
            'email' => 'admin@localhost',
            'text' => 'Awesome blog!',
        ],
        [
            'email' => 'example@localhost',
            'text' => 'Like this blog!',
        ],
    ],
    'tags' => ['Tech', 'UI'],
    'categoryIds' => [1, 2],
    'published' => true,
]);

$document = $engine->getDocument('news', 1);
var_dump($document);

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\TermCondition('title', 'Pucene'))
    ->getResult();

var_dump(iterator_to_array($documents));

$engine->deleteDocument('news', '1');

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\IdentifierCondition('1'))
    ->getResult();

var_dump(iterator_to_array($documents));
