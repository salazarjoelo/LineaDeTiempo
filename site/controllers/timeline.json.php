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
                "start_date" => ["year" => date('Y', strtotime($item->date))],
                "text" => [
                    "headline" => $item->title,
                    "text" => $item->description
                ],
                "media" => [
                    "url" => $item->image ?? ''
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($timeline);
        exit;
    }
}
