<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of Storgman.
 *
 * Storgman is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Storgman is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Storgman
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Core\FeatureContexts;

class WebContext extends BaseContext
{
    /**
     * @Given /^I wait for the modal window to open$/
     */
    public function iWaitForTheModalWindowToOpen()
    {
        $this->getSession()->wait(1000, "$('.modal')");
    }

    /**
     * @Then /^the modal window should disappear$/
     */
    public function theModalWindowShouldDisappear()
    {
        $this->getSession()->wait(500);

        if ($this->getSession()->evaluateScript("return $('.modal').is(':visible')")) {
            throw new \Exception('The modal window did not disappear.');
        }
    }
}