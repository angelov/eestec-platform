<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Entity;

use Angelov\Eestec\Platform\DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $date
 * @property string $location
 * @property string $info
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property \Illuminate\Database\Eloquent\Collection $attendants
 * @property \Angelov\Eestec\Platform\Entity\Member $creator
 */
class Meeting extends Model
{

    protected $fillable = [];
    protected $table = 'meetings';

    public function attendants()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entity\Member');
    }

    public function creator()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Entity\Member', 'created_by');
    }

    public function getDateAttribute($date)
    {
        $date = new DateTime($date);

        return $date->toDateString();
    }

}
