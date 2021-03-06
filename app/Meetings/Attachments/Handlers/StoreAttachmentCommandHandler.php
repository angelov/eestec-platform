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

namespace Angelov\Storgman\Meetings\Attachments\Handlers;

use Angelov\Storgman\Core\FileSystem\FileSystemsRegistry;
use Angelov\Storgman\Meetings\Attachments\Attachment;
use Angelov\Storgman\Meetings\Attachments\AttachmentFile;
use Angelov\Storgman\Meetings\Attachments\Commands\StoreAttachmentCommand;
use Angelov\Storgman\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class StoreAttachmentCommandHandler
{
    protected $members;
    protected $attachments;
    protected $filesystem;

    public function __construct(
        MembersRepositoryInterface $members,
        AttachmentsRepositoryInterface $attachments,
        FileSystemsRegistry $filesystems)
    {
        $this->members = $members;
        $this->attachments = $attachments;
        $this->filesystem = $filesystems->get(AttachmentFile::class);
    }

    public function handle(StoreAttachmentCommand $command)
    {
        $attachment = new Attachment();

        $owner = $this->members->get($command->getOwnerId());
        $attachment->setOwner($owner);

        $file = $command->getFile();
        $filename = $file->getFilename();

        $size = $this->convertSizeToKilobytes(100000); // todo fix

        $attachment->setFilename($filename);
        $attachment->setSize($size);

        $file = $this->filesystem->store($file, true);

        $attachment->setStorageFilename($file->getFilename());

        $this->attachments->store($attachment);

        return $attachment;
    }

    private function convertSizeToKilobytes($inBytes)
    {
        return $inBytes / 1000;
    }
}