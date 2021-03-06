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

namespace Angelov\Storgman\Events;

use Angelov\Storgman\Events\Comments\Comment;
use Angelov\Storgman\LocalCommittees\LocalCommittee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = "eestec_events";
    protected $dates = ['start_date', 'end_date', 'deadline'];

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
    }

    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    public function setDescription($desc)
    {
        $this->setAttribute('description', $desc);
    }

    public function host()
    {
        return $this->belongsTo(LocalCommittee::class, 'host_id');
    }

    public function setHost(LocalCommittee $localCommittee)
    {
        $this->host()->associate($localCommittee);
    }

    /**
     * @return LocalCommittee
     */
    public function getHost()
    {
        return $this->getAttribute('host');
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->getAttribute('start_date');
    }

    public function setStartDate(Carbon $date)
    {
        $this->setAttribute('start_date', $date);
    }

    /**
     * @return Carbon
     */
    public function getEndDate()
    {
        return $this->getAttribute('end_date');
    }

    public function setEndDate(Carbon $date)
    {
        $this->setAttribute('end_date', $date);
    }

    /**
     * @return Carbon
     */
    public function getDeadline()
    {
        return $this->getAttribute('deadline');
    }

    public function setDeadline(Carbon $date)
    {
        $this->setAttribute('deadline', $date);
    }

    public function isReceivingApplications()
    {
        return Carbon::now() < $this->getDeadline();
    }

    public function getUrl()
    {
        return $this->getAttribute('url');
    }

    public function setUrl($url)
    {
        $this->setAttribute('url', $url);
    }

    public function getImage()
    {
        return $this->getAttribute('image');
    }

    public function setImage($filename)
    {
        $this->setAttribute('image', $filename);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->comments()->orderBy('created_at', 'DESC')->get()->all();
    }

    public function countComments()
    {
        return $this->comments()->count();
    }

    public function hasComments()
    {
        return $this->countComments() > 0;
    }

    public function equals(Event $event)
    {
        return $event->getId() === $this->getId();
    }
}
