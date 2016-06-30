<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of EESTEC Platform.
 *
 * EESTEC Platform is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EESTEC Platform is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EESTEC Platform
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Meetings\Commands;

use Angelov\Eestec\Platform\Core\Command;

class CreateMeetingCommand extends Command
{
    protected $title;
    protected $location;
    protected $date;
    protected $details;
    protected $notifyMembers;
    protected $creatorId;

    public function __construct($title, $location, $date, $details, $creatorId, $notifyMembers = true)
    {
        $this->title = $title;
        $this->location = $location;
        $this->date = $date;
        $this->details = $details;
        $this->notifyMembers = $notifyMembers;
        $this->creatorId = $creatorId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getNotifyMembers()
    {
        return $this->notifyMembers;
    }

    public function getCreatorId()
    {
        return $this->creatorId;
    }
}