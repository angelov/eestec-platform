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

namespace Angelov\Storgman\Meetings\Attachments;

use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Members\Member;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = "meeting_attachments";

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getFilename()
    {
        return $this->getAttribute('filename');
    }

    public function setFilename($filename)
    {
        $this->setAttribute("filename", $filename);
    }

    public function getStorageFilename()
    {
        return $this->getAttribute('storage_filename');
    }

    public function setStorageFilename($filename)
    {
        $this->setAttribute('storage_filename', $filename);
    }

    public function getSize()
    {
        $value = $this->getAttribute('size');
        $size = new FileSize($value, FileSize::UNIT_KILOBYTE);

        return $size;
    }

    public function setSize($size)
    {
        $this->setAttribute('size', $size);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    /**
     * @return Meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    public function setMeeting(Meeting $meeting = null)
    {
        $this->meeting()->associate($meeting);
    }

    public function owner()
    {
        return $this->belongsTo(Member::class, 'owner_id');
    }

    /**
     * @return Member
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(Member $owner)
    {
        $this->owner()->associate($owner);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }
}
