<?php

/**
	Скрипт, основной обработчик событий системы
*/

require 'config.php';

# Создаем "воркера" и подключаемся к серверу задач
$worker = new GearmanWorker();
$worker->addServer();

# Регистрируем универсальный обработчик событий
$worker->addFunction("balabot.work", "dispatcher");
$worker->setTimeout(10000);

while(1)
{
//	echo "Ждем работы...\n";
//	echo "?";
	echo "\rmem usage = ".memory_get_usage()."; peak usage = ".memory_get_peak_usage()."          ";
	$ret = $worker->work();
	if($worker->returnCode() != GEARMAN_SUCCESS && $worker->returnCode() != GEARMAN_TIMEOUT)
		break;
}

# Функция-диспетчер
# В аргументах ей передается объект задачи
function dispatcher($job)
{
	$workload = $job->workload();
	$data = unserialize($workload);

	if($ret = pcntl_fork())
	{
		if($ret > 0)
		{
			// Это основная ветка. Был запущен форк. Возвращаемся за следующим заданием.
//			echo "запущен форк\n";
			return;
		}

		echo "\nError\n";
		return;
	}

//	echo "работает форк\n";
	// Это уже тело форка.

	// Создаём класс-обработчик
	$bors_worker = object_load($data['worker_class_name'], NULL);
	if($bors_worker)
		$bors_worker->do_work($data);
	else
		echo "Не могу инициализировать класс ".$data['worker_class_name']."\n";

	exit();
}
