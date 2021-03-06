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

use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Faker\Factory as Faker;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\UrlGenerator;

abstract class BaseContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @return Kernel
     */
    public function getArtisan()
    {
        return app()->make(Kernel::class);
    }

    /**
     * @return Hasher
     */
    public function getPasswordHasher()
    {
        return app()->make(Hasher::class);
    }

    /**
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        return app()->make(UrlGenerator::class);
    }

    /**
     * @return Guard
     */
    public function getAuthenticator()
    {
        return app()->make(Guard::class);
    }

    /**
     * @return MembersRepositoryInterface
     */
    protected function getMembersRepository()
    {
        return app()->make(MembersRepositoryInterface::class);
    }

    public function getPage()
    {
        return $this->getSession()->getPage();
    }

    public function getFaker()
    {
        return Faker::create();
    }
}