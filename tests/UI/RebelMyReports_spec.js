describe('RebelMyReports', function () {
    this.fixture = 'Piwik\\Plugins\\RebelMyReports\\tests\\Fixtures\\SiteWithVisits';

    const params = 'idSite=1&period=day&date=2024-03-05';
    const pageUrl = (subcategory) => `?module=CoreHome&action=index&${params}#?${params}`
        + `&category=RebelMyReports_MyReports&subcategory=${subcategory}`;
    const manageUrl = pageUrl('RebelMyReports_ManageReports');

    before(function () {
        testEnvironment.pluginsToLoad = ['RebelMyReports'];
        testEnvironment.save();
    });

    async function openManagePage() {
        await page.goto(manageUrl);
        await page.waitForSelector('.myReportsList table', { visible: true });
        await page.waitForNetworkIdle();
    }

    function reportNames() {
        return page.evaluate(() => [...document.querySelectorAll('.myReportsList__name')]
            .map((name) => name.textContent.trim()));
    }

    async function clickInRow(reportName, selector) {
        const button = await page.jQuery(`.myReportsList tr:contains(${reportName}) ${selector}`);
        await button.click();
    }

    async function pick(pickerName, itemName) {
        const picker = `.myReportEditor__picker:has(.myReportItemPicker__title:contains(${pickerName}))`;
        await (await page.jQuery(`${picker} .select-wrapper`)).click();
        await (await page.jQuery(`${picker} .expandableSearch`)).type(itemName);
        const item = await page.jQuery(`${picker} .expandableListItem:visible`, { waitFor: true });
        await item.click();
    }

    async function save() {
        await page.click('.myReportEditor__save input');
        await page.waitForSelector('.myReportsList table', { visible: true });
        await page.waitForNetworkIdle();
    }

    it('should tell that there are no reports yet', async function () {
        await openManagePage();

        const text = await page.evaluate(() => document.querySelector('.myReportsList tbody').textContent);
        expect(text).to.contain('There are no reports yet.');
    });

    it('should create a table report in the editor', async function () {
        await page.click('.myReportsList .tableActionBar .btn');
        await page.waitForSelector('.myReportEditor');
        await page.type('.myReportEditor input[name=name]', 'Resolutions');
        await pick('Dimensions', 'Resolution');
        await save();

        expect(await reportNames()).to.deep.equal(['Resolutions']);
    });

    it('should keep a metric filter added when editing', async function () {
        await clickInRow('Resolutions', '.icon-edit');
        await page.waitForSelector('.myReportEditor');
        await page.click('.myReportEditor__addFilter');
        await save();

        await clickInRow('Resolutions', '.icon-edit');
        await page.waitForSelector('.myReportEditor');
        const filters = await page.evaluate(() => document.querySelectorAll('.myReportEditor__removeFilter').length);
        await page.click('.myReportEditor__cancel');

        expect(filters).to.equal(1);
    });

    it('should move a report with the arrow keys', async function () {
        await testEnvironment.callApi('RebelMyReports.addReport', {
            idSite: 1,
            name: 'Visits over time',
            reportType: 'evolution',
            metrics: ['nb_visits'],
        });
        await openManagePage();

        await (await page.jQuery('.myReportsList tr:contains(Visits over time) .myReportsList__handle')).focus();
        await page.keyboard.press('ArrowUp');
        await page.waitForNetworkIdle();

        expect(await reportNames()).to.deep.equal(['Visits over time', 'Resolutions']);
    });

    it('should show only the reports that match the search', async function () {
        await page.type('.myReportsList__search', 'resol');

        expect(await reportNames()).to.deep.equal(['Resolutions']);
    });

    it('should show the data of the report on its page', async function () {
        await page.goto(pageUrl('RebelMyReports_page_1'));
        await page.waitForSelector('.dataTable td.label');
        await page.waitForNetworkIdle();

        const labels = await page.evaluate(() => [...document.querySelectorAll('.dataTable td.label')]
            .map((label) => label.textContent.trim()));
        expect(labels).to.have.members(['1920x1080', '800x600']);
    });

    it('should open the report as the chosen visualisation', async function () {
        await openManagePage();
        await clickInRow('Resolutions', '.icon-edit');
        await page.waitForSelector('.myReportEditor');
        const field = '.myReportEditor__field:has(select[name=visualization])';
        await (await page.jQuery(`${field} input.select-dropdown`)).click();
        await (await page.jQuery(`${field} li:contains(Vertical bar graph)`, { waitFor: true })).click();
        await page.waitForSelector('.myReportPreview__bar');
        const bars = await page.evaluate(() => document.querySelectorAll('.myReportPreview__bar').length);
        await save();

        await page.goto(pageUrl('RebelMyReports_page_1'));
        await page.waitForSelector('.piwik-graph');

        expect(bars).to.equal(2);
    });

    it('should preview and keep a group of values', async function () {
        await openManagePage();
        await clickInRow('Resolutions', '.icon-edit');
        await page.waitForSelector('.myReportEditor');
        await page.click('.myReportEditor__addValueGroup');
        await page.type('.myReportEditor input[name=groupName0]', 'Large screens');
        await page.type('.myReportEditor input[name=groupValue0]', '1920x1080');
        await page.keyboard.press('Enter');
        await page.jQuery('.myReportPreview__barLabel:contains(Large screens)', { waitFor: true });
        await save();

        await clickInRow('Resolutions', '.icon-edit');
        await page.waitForSelector('.myReportEditor');
        const values = await page.evaluate(() => [...document.querySelectorAll('.myReportEditor__groupValue')]
            .map((value) => value.textContent.trim()));
        await page.click('.myReportEditor__cancel');
        expect(values).to.deep.equal(['1920x1080']);
    });

    it('should invalidate a chosen report on the admin page', async function () {
        await page.goto(`?module=RebelMyReports&action=invalidate&${params}`);
        await page.waitForSelector('.invalidateMyReports');
        // The checkboxes are hidden behind their styled labels.
        await page.evaluate(() => {
            document.querySelector('.invalidateMyReports input[name=allReports]').click();
            $('.invalidateMyReports label:contains(Visits over time) input').click();
        });
        await page.click('.invalidateMyReports input.btn');
        await page.waitForSelector('.notification-success');

        const message = await page.evaluate(() => document.querySelector('.notification-success').textContent);
        expect(message).to.contain('Reports marked as outdated: 1.');
    });

    it('should show the documentation with the limits as they are set', async function () {
        await page.goto(`?module=RebelMyReports&action=documentation&${params}`);
        await page.waitForSelector('.rebelMyReportsDocumentation table');

        const limits = await page.evaluate(() => [...document.querySelectorAll('.rebelMyReportsDocumentation tbody tr')]
            .map((row) => `${row.cells[2].textContent.trim()}=${row.cells[1].textContent.trim()}`));
        expect(limits).to.include('max_dimensions=3');
        expect(limits).to.have.lengthOf(5);
    });

    it('should create an example report when the page is opened without a website', async function () {
        await page.goto('?module=RebelMyReports&action=documentation');
        await page.waitForSelector('.myReportExamples__example .btn:not([disabled])');
        await (await page.jQuery('.myReportExamples__example:contains(Returning visitors over time) .btn')).click();
        await page.waitForSelector('.notification-success');
        await openManagePage();

        expect(await reportNames()).to.include('Returning visitors over time');
    });

    it('should delete a report after confirming', async function () {
        await openManagePage();
        await clickInRow('Resolutions', '.icon-delete');
        await (await page.jQuery('.modal.open .modal-footer a:contains(Yes)', { waitFor: true })).click();
        await page.waitForNetworkIdle();

        expect(await reportNames()).to.not.include('Resolutions');
    });
});
