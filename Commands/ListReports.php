<?php

namespace Piwik\Plugins\RebelMyReports\Commands;

use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Model;

class ListReports extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('rebelmyreports:list');
        $this->setDescription('Lists the reports with their ID, website and archive record name.');
        $this->addRequiredValueOption('idsite', null, 'Only list the reports available for this website.');
    }

    protected function doExecute(): int
    {
        $model = new Model();
        $idSite = $this->getInput()->getOption('idsite');
        $reports = $idSite ? $model->getReports((int) $idSite) : $model->getAllReports();

        $rows = [];
        foreach ($reports as $report) {
            $rows[] = [
                $report['idreport'],
                $report['idsite'] === 0 ? 'all' : $report['idsite'],
                $report['name'],
                implode(', ', $report['dimensions']),
                implode(', ', $report['metrics']),
                Archiver::getRecordName($report),
            ];
        }

        $this->renderTable(['ID', 'Website', 'Name', 'Dimensions', 'Metrics', 'Record'], $rows);

        return self::SUCCESS;
    }
}
