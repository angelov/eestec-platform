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

namespace Angelov\Storgman\Events\Importing;

use Angelov\Storgman\Core\Exceptions\ResourceNotFoundException;
use Angelov\Storgman\Events\Commands\StoreEventCommand;
use Angelov\Storgman\Events\Commands\UpdateEventCommand;
use Angelov\Storgman\Events\EventImage;
use Angelov\Storgman\Events\Repositories\EventsRepositoryInterface;
use Angelov\Storgman\LocalCommittees\Repositories\LocalCommitteesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/** @todo this cries for refactoring */
class EestecNetEventsImporter
{
    protected $scrapper;
    protected $logger;
    protected $lcs;
    protected $commandBus;
    protected $events;
    protected $baseUrl;

    public function __construct(
        Scrapper $scrapper,
        LocalCommitteesRepositoryInterface $lcs,
        Dispatcher $commandBus,
        EventsRepositoryInterface $events,
        LoggerInterface $logger = null)
    {
        $this->scrapper = $scrapper;
        $this->logger = (isset($logger)) ? $logger : new NullLogger();
        $this->lcs = $lcs;
        $this->commandBus = $commandBus;
        $this->events = $events;
    }

    public function import()
    {
        $this->baseUrl = $url = "http://eestec.net";

        $events = $this->scrapper->parseEvents($url . "/events");

        $this->logger->info("Events importing started.");

        foreach ($events as $event) {
            $this->importEvent($event);
        }

        $this->logger->info("Events importing finished.");
    }

    // @todo refactor
    protected function importEvent(array $data)
    {
        $baseUrl = $this->baseUrl;

        if ($data['organizer'] == "") {
            $this->logger->info(sprintf(
                "Event \"%s\" not imported. Missing organizer.",
                $data['title']
            ));

            return;
        }

        $data['organizer'] = urldecode(html_entity_decode($data['organizer']));
        $organizer = null;

        $parts = explode("LC ", $data['organizer']);

        if (count($parts) < 2) {
            return;
        }

        $data['organizer'] = "LC " . $parts[1];

        try {
            $organizer = $this->lcs->getByTitle($data['organizer']);
        } catch (ResourceNotFoundException $e) {
            $this->logger->info(sprintf(
                "Event \"%s\" not imported. Organizer \"%s\" not found.",
                $data['title'],
                $data['organizer']
            ));

            return;
        }

        if ($data['title'] == "") {
            $this->logger->info("Event not imported. Missing title.");
            return;
        }

        $title = $data['title'];
        $description = html_entity_decode($data['details']);
        $hostId = $organizer->getId();
        $url = $baseUrl . $data['url'];

        // @todo refactor
        try {
            $imgRaw = imagecreatefromstring(file_get_contents($baseUrl . $data['image']));

        } catch (\Exception $e) {
            $this->logger->info("could not fetch image for ". $data['url']);
            return;
        }

        imagejpeg($imgRaw, '/tmp/tmp.jpg',100);
        imagedestroy($imgRaw);
        $filename = rand(0, 1000) .".jpg";
        $image = new EventImage($filename, '/tmp/tmp.jpg');
        $startDate = new Carbon($data['start']);
        $endDate = new Carbon($data['end']);
        $deadline = new Carbon($data['deadline']);

        try {

            $event = $this->events->getByTitle($title);
            $id = $event->getId();

            $command = new UpdateEventCommand($id, $title, $description, $hostId, $url, $image, $startDate, $endDate, $deadline);

            $this->logger->info(sprintf(
                "Event \"%s\" already exists. Will be updated if needed.",
                $title
            ));

        } catch (ResourceNotFoundException $e) {

            $command = new StoreEventCommand($title, $description, $hostId, $url, $image, $startDate, $endDate, $deadline);

            $this->logger->info(sprintf(
                "Event \"%s\" imported",
                $title
            ));

        }

        $this->commandBus->dispatch($command);
    }
}
