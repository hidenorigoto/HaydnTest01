<?php
use Quartet\Haydn\Set;
use Quartet\Haydn\IO\Source\ArraySource;
use Quartet\Haydn\IO\ColumnMapper\HashKeyColumnMapper;
use \Quartet\Haydn\Matcher\Matcher;
use Quartet\Haydn\IO\Source\SingleRowSource;

require_once __DIR__.'/vendor/autoload.php';

$data2 = [
    [
        'id' => 8,
        'parent_id' => 7,
        'value' => 'ひ孫1-1-1-1',
    ],
    [
        'id' => 9,
        'parent_id' => 5,
        'value' => '孫2-1-1',
    ],
    [
        'id' => 10,
        'parent_id' => 5,
        'value' => '孫2-1-2',
    ],
    [
        'id' => 11,
        'parent_id' => 2,
        'value' => '子2-2',
    ],
    [
        'id' => 12,
        'parent_id' => 4,
        'value' => '孫1-2-2',
    ],
    [
        'id' => 13,
        'parent_id' => 9,
        'value' => 'ひ孫2-1-1-1',
    ],
    [
        'id' => 1,
        'parent_id' => 0,
        'value' => '親1',
    ],
    [
        'id' => 2,
        'parent_id' => 0,
        'value' => '親2',
    ],
    [
        'id' => 3,
        'parent_id' => 1,
        'value' => '子1-1',
    ],
    [
        'id' => 4,
        'parent_id' => 1,
        'value' => '子1-2',
    ],
    [
        'id' => 5,
        'parent_id' => 2,
        'value' => '子2-1',
    ],
    [
        'id' => 6,
        'parent_id' => 4,
        'value' => '孫1-2-1',
    ],
    [
        'id' => 7,
        'parent_id' => 3,
        'value' => '孫1-1-1',
    ],
    [
        'id' => 14,
        'parent_id' => 5,
        'value' => '孫2-1-3',
    ],
    [
        'id' => 15,
        'parent_id' => 2,
        'value' => '子2-3',
    ],
];

$nodeSet = new Set(new ArraySource('node', $data2, new HashKeyColumnMapper()));

/**
 * @param $parent_id
 * @param Set $allNodes
 * @return Set
 */
function makeForNode($parent_id, Set $allNodes)
{
    $childNodes = $allNodes->filter(new Matcher(['parent_id' => $parent_id]));

    $treeSet = new Set\GroupingSet($childNodes,
        null,
        function ($row) use ($allNodes) {
            $rowSet = new Set(new SingleRowSource('node', $row));
            return $rowSet->union(makeForNode($row['id'], $allNodes));
        },
        null);

    return $treeSet;
}

$treeSet = makeForNode(0, $nodeSet);

foreach ($treeSet as $row) {
    echo $row['id'] . ':' . $row['value'] . PHP_EOL;
}
