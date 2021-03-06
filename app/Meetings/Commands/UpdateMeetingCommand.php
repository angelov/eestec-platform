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

namespace Angelov\Storgman\Meetings\Commands;

use Angelov\Storgman\Core\Command;

class UpdateMeetingCommand extends Command
{
    protected $meetingId;
    protected $title;
    protected $location;
    protected $date;
    protected $details;
    protected $attachments = [];

    public function __construct($meetingId, $title, $location, $date, $details, array $attachments = [])
    {
        $this->meetingId = $meetingId;
        $this->title = $title;
        $this->date = $date;
        $this->details = $details;
        $this->location = $location;
        $this->attachments = $attachments;
    }

    public function getMeetingId()
    {
        return $this->meetingId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }
}
