<?php

namespace Piwik\Plugins\RebelMyReports\tests\Fixtures;

use Piwik\Tests\Framework\Fixture;

/**
 * Two visitors on one day, the first returning the next day.
 *
 * Visitor 1 (1920x1080): pages /a, /b, /a, an event with value 5 and an order of 30.
 * Visitor 2 (800x600): page /a and a conversion of goal 1.
 * Next day, visitor 1: page /b.
 */
class TrackedVisits
{
    public const DAY = '2024-03-05';
    public const NEXT_DAY = '2024-03-06';

    public static function track(int $idSite): void
    {
        $visitor1 = Fixture::getTracker($idSite, self::DAY . ' 10:00:00', true, true);
        $visitor1->setTokenAuth(Fixture::getTokenAuth());
        $visitor1->setResolution(1920, 1080);
        $visitor1->setUrl('http://example.org/a');
        Fixture::checkResponse($visitor1->doTrackPageView('A'));
        $visitor1->setForceVisitDateTime(self::DAY . ' 10:01:00');
        $visitor1->setUrl('http://example.org/b');
        Fixture::checkResponse($visitor1->doTrackPageView('B'));
        $visitor1->setForceVisitDateTime(self::DAY . ' 10:02:00');
        $visitor1->setUrl('http://example.org/a');
        Fixture::checkResponse($visitor1->doTrackPageView('A'));
        $visitor1->setForceVisitDateTime(self::DAY . ' 10:03:00');
        Fixture::checkResponse($visitor1->doTrackEvent('Video', 'play', 'intro', 5));
        $visitor1->setForceVisitDateTime(self::DAY . ' 10:04:00');
        $visitor1->addEcommerceItem('SKU1', 'Shoes', 'Clothes', 30, 1);
        Fixture::checkResponse($visitor1->doTrackEcommerceOrder('order-1', 30));

        $visitor2 = Fixture::getTracker($idSite, self::DAY . ' 11:00:00', true, true);
        $visitor2->setTokenAuth(Fixture::getTokenAuth());
        $visitor2->setNewVisitorId();
        $visitor2->setIp('10.0.0.2');
        $visitor2->setResolution(800, 600);
        $visitor2->setUrl('http://example.org/a');
        Fixture::checkResponse($visitor2->doTrackPageView('A'));
        $visitor2->setForceVisitDateTime(self::DAY . ' 11:01:00');
        Fixture::checkResponse($visitor2->doTrackGoal(1));

        $visitor1->setForceVisitDateTime(self::NEXT_DAY . ' 12:00:00');
        $visitor1->setUrl('http://example.org/b');
        Fixture::checkResponse($visitor1->doTrackPageView('B'));
    }
}
