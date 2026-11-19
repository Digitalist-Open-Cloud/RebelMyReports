<?php

namespace Piwik\Plugins\RebelMyReports\Commands;

use Piwik\Archive\ArchiveInvalidator;
use Piwik\Container\StaticContainer;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Model;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Archives single reports with core's own commands, restricted to the report's record.
 */
class Archive extends ConsoleCommand
{
    private const PLUGIN = 'RebelMyReports';

    protected function configure()
    {
        $this->setName('rebelmyreports:archive');
        $this->setDescription('Archives all reports, or the given ones, now, without archiving anything else.');
        $this->setHelp(
            "Without --rearchive, archives what is waiting for the report, for example after it was\n"
            . "created or changed. With --rearchive, the report's data is built again first:\n"
            . "for --date if given, otherwise as far back as reports are re-archived by default.\n\n"
            . "  ./console rebelmyreports:archive\n"
            . "  ./console rebelmyreports:archive --idmyreport=3\n"
            . "  ./console rebelmyreports:archive --idmyreport=3,4 --rearchive --date=2024-01-01,2024-03-31\n"
            . '  ./console rebelmyreports:archive --rearchive --date=2024-03-05 --period=day'
        );
        $this->addRequiredValueOption('idmyreport', null, 'Report IDs separated by commas. Defaults to all reports. See rebelmyreports:list.', 'all');
        $this->addRequiredValueOption('idsite', null, 'Website IDs separated by commas. Defaults to the websites of each report.');
        $this->addNoValueOption('rearchive', null, 'Build the data again, also where it is already archived.');
        $this->addRequiredValueOption('date', null, 'With --rearchive: a date or date range, eg, 2024-03-05 or 2024-01-01,2024-03-31.');
        $this->addRequiredValueOption('period', null, 'With --rearchive: period types separated by commas (day, week, month, year). Defaults to all.', 'all');
        $this->addRequiredValueOption('segment', null, 'With --rearchive: only this segment. Defaults to all visits.');
    }

    protected function doExecute(): int
    {
        $input = $this->getInput();
        $rearchive = (bool) $input->getOption('rearchive');

        if (!$rearchive && ($input->getOption('date') || $input->getOption('segment'))) {
            throw new InvalidArgumentException('--date and --segment need --rearchive.');
        }

        $status = self::SUCCESS;

        foreach ($this->getReports() as $report) {
            if ($report['paused']) {
                $this->getOutput()->writeln(sprintf('<comment>Report %d "%s" is paused and is not archived.</comment>', $report['idreport'], $report['name']));
                continue;
            }

            $record = self::PLUGIN . '.' . Archiver::getRecordName($report);
            $idSites = $this->getIdSites($report);

            $this->getOutput()->writeln(sprintf('<info>Report %d "%s" (%s)</info>', $report['idreport'], $report['name'], $record));

            if ($rearchive) {
                $this->invalidate($report, $idSites);
            }

            $arguments = ['--force-report' => $record, '--disable-scheduled-tasks' => true];
            if ($idSites) {
                $arguments['--force-idsites'] = implode(',', $idSites);
            }

            if ($this->runCommand('core:archive', $arguments) !== self::SUCCESS) {
                $status = self::FAILURE;
            }
        }

        return $status;
    }

    /**
     * @param int[] $idSites empty for all websites
     */
    private function invalidate(array $report, array $idSites): void
    {
        $input = $this->getInput();
        $date = $input->getOption('date');

        if (!$date) {
            // The same as when the report is saved: back to the default re-archive date.
            StaticContainer::get(ArchiveInvalidator::class)->reArchiveReport(
                $idSites ?: 'all',
                self::PLUGIN,
                Archiver::getRecordName($report)
            );
            return;
        }

        $arguments = [
            '--dates' => [$date],
            '--periods' => $input->getOption('period'),
            '--sites' => $idSites ? implode(',', $idSites) : 'all',
            '--plugin' => self::PLUGIN . '.' . Archiver::getRecordName($report),
        ];
        if ($input->getOption('segment')) {
            $arguments['--segment'] = [$input->getOption('segment')];
        }

        $this->runCommand('core:invalidate-report-data', $arguments);
    }

    /**
     * @return array[]
     */
    private function getReports(): array
    {
        $option = (string) $this->getInput()->getOption('idmyreport');
        $model = new Model();

        if ($option === 'all' || $option === '') {
            return $model->getAllReports();
        }

        $ids = array_filter(array_map('trim', explode(',', $option)), fn (string $value) => $value !== '');

        $reports = [];
        foreach ($ids as $id) {
            $report = ctype_digit($id) ? $model->getReport((int) $id) : null;
            if (!$report) {
                throw new InvalidArgumentException("There is no report with ID '$id'. See rebelmyreports:list.");
            }
            $reports[] = $report;
        }

        return $reports;
    }

    /**
     * @return int[] empty for all websites
     */
    private function getIdSites(array $report): array
    {
        $option = (string) $this->getInput()->getOption('idsite');
        $idSites = array_map('intval', array_filter(array_map('trim', explode(',', $option)), fn (string $value) => $value !== ''));

        if ($report['idsite'] === 0) {
            return $idSites;
        }

        if ($idSites && !in_array($report['idsite'], $idSites, true)) {
            throw new InvalidArgumentException(sprintf('Report %d belongs to website %d.', $report['idreport'], $report['idsite']));
        }

        return [$report['idsite']];
    }
}
