<?php
require('debug.php');
require('src/kugouSource.php');

$downloader = (new ReflectionClass('AumKugouSource'))->newInstance();
$testArray = array(
    array('title' => '一块红布', 'artist' => '崔健'),
    array('title' => '还要多久', 'artist' => '宫阁 / 余佳运'),
    array('title' => '빠빠빠', 'artist' => 'Crayon Pop')
);

foreach ($testArray as $key => $item) {
    echo "\n++++++++++++++++++++++++++++++\n";
    echo "测试 $key 开始...\n";
    if ($key > 0) {
        echo "等待 5 秒...\n";
        sleep(5);
    }
    $testObj = new AudioStationResult();
    $count = $downloader->getLyricsList($item['artist'], $item['title'], $testObj);
    if ($count > 0) {
        $item = $testObj->getFirstItem();
        $downloader->getLyrics($item['id'], $testObj);
    } else {
        echo "没有查找到任何歌词！\n";
    }
    echo "测试 $key 结束。\n";
}
