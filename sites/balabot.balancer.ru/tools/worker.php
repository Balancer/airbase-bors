<?php

/**
	Скрипт, основной обработчик событий системы
*/

require 'config.php';

# Создаем "воркера" и подключаемся к серверу задач
$gmworker = new GearmanWorker();
$gmworker->addServer();

//pcntl_signal(SIGCHLD, "sig_handler");
//pcntl_signal(SIGTERM, "sig_handler");
//pcntl_signal(SIGINT,  "sig_handler");

# Регистрируем универсальный обработчик событий
$gmworker->addFunction("balabot.work", "dispatcher");
$gmworker->setTimeout(10000);

echo "Worker started\n";

$loop = 100;
while($loop-->0 && (@$gmworker->work() || @$gmworker->returnCode() == GEARMAN_TIMEOUT))
{
	if($gmworker->returnCode() == GEARMAN_TIMEOUT)
	{
		// Normally one would want to do something useful here ...
		echo "\r[".date('r')."][{$loop}] mem usage = ".memory_get_usage()."; peak usage = ".memory_get_peak_usage()."          ";
		continue;
	}

	if($gmworker->returnCode() != GEARMAN_SUCCESS)
	{
		echo "Gearman return_code: " . $gmworker->returnCode() . "\n";
		break;
	}
}

exit("\n");

# Функция-диспетчер
# В аргументах ей передается объект задачи
function dispatcher($job)
{
	$workload = $job->workload();
	$data = unserialize($workload);
	if(empty($data['worker_class_name']))
		return;

	if($child_pid = pcntl_fork())
	{
		if($child_pid > 0)
		{
//			pcntl_signal($child_pid, SIG_IGN); // Сообщаем ОС, что нам пофиг на этот процесс
			// Это основная ветка. Был запущен форк. Возвращаемся за следующим заданием.
//			echo "запущен форк\n";
			usleep(500);
			return;
		}

		echo "\nould not fork!!\nDying...\n";
		return;
	}

//	waitpid(-1, NULL, WNOHANG);

//	echo "работает форк\n";
	// Это уже тело форка.

	// Создаём класс-обработчик
	$bors_worker = object_load($data['worker_class_name'], NULL);
	if($bors_worker)
	{
		if(empty($data['worker_method']))
			$bors_worker->do_work($data);
		else
			call_user_func(array($bors_worker, $data['worker_method']), $data);
	}
	else
		echo "Не могу инициализировать класс ".$data['worker_class_name']."\n";

	exit();
}

function sig_handler($signo)
{
	switch($signo)
	{
		case SIGTERM:
			// handle shutdown tasks
			exit;
			break;
        case SIGHUP:
            // handle restart tasks
            break;
        case SIGUSR1:
            print "Caught SIGUSR1...\n";
            break;
        case SIGCHLD:
            while( pcntl_waitpid(-1,$status,WNOHANG)>0) { }
			break;
		case SIGINT:
        	exit;
        default:
            // not implemented yet...
            break;
     }
}
