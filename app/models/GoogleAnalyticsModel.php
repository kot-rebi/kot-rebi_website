<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Dotenv\Dotenv;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;

class GoogleAnalyticsModel
{
  /**
   * APIとの接続クライアント
   *
   * @var [type]
   */
  private $client;

  /**
   * プロパティID
   *
   * @var int
   */
  private $propertyId;

  /**
   * GAの計測開始日
   *
   * @var string
   */
  private $startDate;

  public function __construct()
  {
    // .envの読み込み
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    // 環境変数を取得
    $relativePath = $_ENV['GOOGLE_APPLICATION_CREDENTIALS'];
    $absolutePath = realpath(__DIR__ . '/../../' . '/' . $relativePath);
    $this->propertyId = $_ENV['GA_PROPERTY_ID'];
    $this->startDate = $_ENV['GA_START_DATE'];

    // 環境変数として設定
    putenv("GOOGLE_APPLICATION_CREDENTIALS={$absolutePath}");

    $this->client = new BetaAnalyticsDataClient();
  }

  public function getMostViewedPages($startDate, $endDate = 'today', $limit = 10000)
  {
    $request = (new RunReportRequest())
      ->setProperty('properties/' . $this->propertyId)
      ->setDateRanges([
        new DateRange([
          'start_date' => $startDate,
          'end_date' => $endDate,
        ]),
      ])
      ->setDimensions([
        new Dimension(['name' => 'pagePath']),
        new Dimension(['name' => 'pageTitle']),
      ])
      ->setMetrics([
        new Metric(['name' => 'screenPageViews']),
      ])
      ->setOrderBys([
        new OrderBy([
          'desc' => TRUE,
          'metric' => new MetricOrderBy(['metric_name' => 'screenPageViews']),
        ]),
      ])
      ->setLimit($limit);

    $response = $this->client->runReport($request);

    // 返ってきたデータを辞書形式に整形し、それを返す
    $rows = [];
    foreach ($response->getRows() as $row) {
      $rows[] = [
        'path' => $row->getDimensionValues()[0]->getValue(),
        'title' => $row->getDimensionValues()[1]->getValue(),
        'views' => (int)$row->getMetricValues()[0]->getValue(),
      ];
    }

    return $rows;
  }

  public function getMostViewedArticles($startDate = null, $endDate = 'today', $limit = 3) 
  {
    if ($startDate === null)
    {
      $startDate = $this->startDate;
    }

    $rows = $this->getMostViewedPages($startDate, $endDate);

    $articleViews = [];

    foreach($rows as $row) {

      if (preg_match('/(?:\/articles\/|\-)(\d+)(?:\/)?$/', $row['path'], $matches))
      {
        $articleId = (int)$matches[1];

        if (!isset($articleViews[$articleId])) {
          $articleViews[$articleId] = 0;
        }
        $articleViews[$articleId] += $row['views'];
      }
    }
    
    // PV数で降順にソートする
    arsort($articleViews);

    // 上位のIDだけ取り出す
    $topArticlesIds = array_slice(array_keys($articleViews), 0, $limit);

    return $topArticlesIds;
  }

  public function getMostViewedArticlesWithViews($startDate = null, $endDate = 'today', $limit = 100) 
  {
    if ($startDate === null)
    {
      $startDate = $this->startDate;
    }

    $rows = $this->getMostViewedPages($startDate, $endDate);

    $articleViews = [];

    foreach($rows as $row) {

      if (preg_match('/(?:\/articles\/|\-)(\d+)(?:\/)?$/', $row['path'], $matches))
      {
        $articleId = (int)$matches[1];

        if (!isset($articleViews[$articleId])) {
          $articleViews[$articleId] = 0;
        }
        $articleViews[$articleId] += $row['views'];
      }
    }

    // 上位のIDだけ取り出す
    $topArticlesIds = array_slice($articleViews, 0, $limit, true);

    return $topArticlesIds;
  }
}
