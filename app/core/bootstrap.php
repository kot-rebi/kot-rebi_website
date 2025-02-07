<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/Config.php';
$config = Config::getInstance();

require_once $config->get('paths')['models'] . '/Database.php';
require_once $config->get('paths')['models'] . '/ArticleModel.php';
require_once $config->get('paths')['models'] . '/UserModel.php';

require_once $config->get('paths')['controllers_admin'] . '/BaseArticleController.php';