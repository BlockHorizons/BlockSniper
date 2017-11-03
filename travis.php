<?php

$server = proc_open(PHP_BINARY . ' src/pocketmine/PocketMine.php --no-wizard --disable-readline', [
	0 => ['pipe', 'r'],
	1 => ['pipe', 'w'],
	2 => ['pipe', 'w'],
], $pipes);

fwrite($pipes[0], "makeplugin BlockSniper\nstop\n\n");
while(!feof($pipes[1])) {
	echo fgets($pipes[1]);
}

fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);

echo "\n\nReturn value: " . proc_close($server) . "\n";
if(count(glob('plugins/DevTools/BlockSniper*.phar')) === 0) {
	echo "The BlockSniper Travis CI build failed.\n";
	exit(1);
}

echo "The BlockSniper Travis CI build succeeded.\n";
exit(0);