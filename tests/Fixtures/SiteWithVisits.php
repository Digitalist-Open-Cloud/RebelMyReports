<?php

namespace Piwik\Plugins\RebelMyReports\tests\Fixtures;

use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Tests\Framework\Fixture;

/**
 * A website with a goal and the visits of TrackedVisits.
 */
class SiteWithVisits extends Fixture
{
    public function setUp(): void
    {
        if (!self::siteCreated(1)) {
            self::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
            GoalsApi::getInstance()->addGoal(1, 'Signup', 'url', '/signup', 'contains');
        }

        TrackedVisits::track(1);
    }

    public function tearDown(): void
    {
    }
}
