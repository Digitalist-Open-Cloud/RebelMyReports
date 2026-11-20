# Rebel My Reports (for Matomo)

[![Test plugin with Matomo](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/matomo.yaml/badge.svg)](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/matomo.yaml)
[![PHPCS check](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/phpcs.yaml/badge.svg)](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/phpcs.yaml)
[![Semgrep OSS scan](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/semgrep.yaml/badge.svg)](https://github.com/Digitalist-Open-Cloud/RebelMyReports/actions/workflows/semgrep.yaml)

Build your own Matomo reports from any combination of dimensions and metrics. The reports are
archived like Matomo's own, so they load fast and work with segments, date ranges, exports,
dashboards and scheduled reports.

## What a report can be

- **Table:** dimensions nested in each other, three by default, with any number of metrics. It can
  open as a table, a vertical bar graph, a pie chart or a tag cloud.
- **Evolution:** metrics over time.
- **Key figure:** one metric as a number, with the change since the previous period and
  compared with other periods and segments.

A report can also have:

- **Calculated metrics:** a formula over other metrics, such as revenue per visit.
- **A segment filter:** the report only counts the visits that match.
- **Metric filters, sorting and a row limit:** for example only rows with at least 10 visits.
- **Value groups:** several values of a dimension counted as one row, such as Sweden, Norway
  and Denmark as "Nordics".

## Install

Install it from the Matomo Marketplace, or put this repository in `plugins/RebelMyReports` and
activate it:

```sh
./console plugin:activate RebelMyReports
```

## Use

Reports are created under My Reports > Manage reports, by users with write access to the
website. The editor shows a preview while you build. A super user can make a report for all
websites.

Administration > System > My Reports documentation describes every function and has example
reports to start from.

## Archiving single reports

Reports are archived by Matomo's normal archiving. To archive single reports right away:

```sh
./console rebelmyreports:list
./console rebelmyreports:archive
./console rebelmyreports:archive --idmyreport=3
./console rebelmyreports:archive --idsite=1
./console rebelmyreports:archive --idmyreport=3,4 --rearchive --date=2024-01-01,2024-03-31
```

Without `--idmyreport` all reports are archived, or with `--idsite` all reports of those websites. Without `--rearchive` it archives what is
waiting for the report, for example after it was created or changed. With `--rearchive` the data is built again, for `--date` or, without a
date, as far back as Matomo re-archives reports by default. With `--date` only those dates are archived,
together with the week, month and year they belong to; what else is waiting is left for the
next archiving. See `--help` for all options.

To have reports built again without the command line, a super user can use Administration >
System > Invalidate My Reports: choose all or some reports, a day, week, month, year or date
range, and all data or that of one segment. The data is built again the next time reports are
archived.

## Limits

A super user sets them under Administration > System > General settings, in the RebelMyReports
section. A value in `config/config.ini.php` is used instead of the setting, which is then hidden
on that page. All are optional; these are the defaults:

```ini
[RebelMyReports]
; Dimensions a report can have, 9 at most.
max_dimensions = 3
; Rows a report keeps per day, week etc. The rest is summed into one row.
; Default: Matomo's datatable_archiving_maximum_rows_standard.
datatable_archiving_maximum_rows = 500
; The same for the rows below each row of a report with several dimensions.
datatable_archiving_maximum_rows_subtable = 100
; Seconds a query that archives a report may run.
max_execution_time = 600
; Seconds a query of the preview in the report editor may run.
preview_max_execution_time = 30
```

A new row limit applies to what is archived from then on.

On MariaDB the time limits only work when Matomo knows the database is MariaDB:
`schema = Mariadb` in the `[database]` section.

A report whose query is stopped gets no data for that period, and archiving goes on with the
other reports. The list of reports tells which period it was. After making the report lighter
or raising the limit, archive the period again:

```sh
./console rebelmyreports:archive --idmyreport=3 --rearchive --date=2024-03-05,2024-03-05
```

## License

GPL v3 or later, see [LICENSE.md](LICENSE.md).
