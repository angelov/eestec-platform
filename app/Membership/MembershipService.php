<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Membership;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Membership\Reports\ExpectedAndPaidFeesPerMonthReport;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class MembershipService
{
    protected $members;
    protected $fees;

    public function __construct(MembersRepositoryInterface $members, FeesRepositoryInterface $fees)
    {
        $this->members = $members;
        $this->fees = $fees;
    }

    public function getExpectedAndPaidFeesPerMonthLastYear()
    {
        $to = DateTime::now();
        $from = DateTime::twelveMonthsAgo(true);

        $expected = $this->fees->calculateExpectedFeesPerMonth($from, $to);
        $paid =  $this->fees->calculatePaidFeesPerMonth($from, $to);

        $report = new ExpectedAndPaidFeesPerMonthReport($from, $to);
        $report->setExpectedFees($expected->getMonthsValues());
        $report->setPaidFees($paid->getMonthsValues());

        return $report;
    }

    public function suggestDates(Member $member)
    {
        $exp = $member->getExpirationDate();

        $suggestDates = [];

        if ($exp !== null) {
            $exp = clone $exp;
            $suggestDates['from'] = $exp->modify('+1 day')->format('Y-m-d');
            $suggestDates['to'] = $exp->modify('+1 year')->format('Y-m-d');
        } else {
            $today = new \DateTime('now');
            $suggestDates['from'] = $today->format('Y-m-d');
            $suggestDates['to'] = $today->modify('+1 year')->format('Y-m-d');
        }

        return $suggestDates;
    }
}
