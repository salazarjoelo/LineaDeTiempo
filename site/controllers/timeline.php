<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class LineaDeTiempoControllerTimeline extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        $model = $this->getModel('Timeline');
        $items = $model->getItems();

        $timeline = [
            "title" => [
                "text" => [
                    "headline" => "Línea de Tiempo",
                    "text" => "Eventos importantes"
                ]
            ],
            "events" => []
        ];

        foreach ($items as $item) {
            $timeline['events'][] = [
                "start_date" => ["year" => date('Y', strtotime($item->start_date))],
                "end_date" => ["year" => date('Y', strtotime($item->end_date))],
                "text" => [
                    "headline" => $item->title,
                    "text" => $item->description
                ],
                "media" => [
                    "url" => $item->media_url ?? ''
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($timeline);
        exit;
    }
}
