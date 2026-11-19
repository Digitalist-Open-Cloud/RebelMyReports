<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureAdminMenu(MenuAdmin $menu)
    {
        if (Piwik::isUserHasSomeWriteAccess()) {
            $menu->addSystemItem('RebelMyReports_Documentation', $this->urlForAction('documentation'), 61);
        }

        if (Piwik::hasUserSuperUserAccess()) {
            $menu->addSystemItem('RebelMyReports_InvalidateReports', $this->urlForAction('invalidate'), 60);
        }
    }
}
