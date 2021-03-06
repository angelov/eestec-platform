<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Angelov\Storgman\Documents\Document;
use Angelov\Storgman\Faculties\Faculty;
use Angelov\Storgman\Faculties\Repositories\FacultiesRepositoryInterface;
use Angelov\Storgman\Membership\Fee;
use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Documents\Tags\Tag;
use Angelov\Storgman\Documents\Repositories\DocumentsRepositoryInterface;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Documents\Tags\Repositories\TagsRepositoryInterface;
use Illuminate\Database\Seeder;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class FakeDataSeeder extends Seeder
{
    protected $members;
    protected $fees;
    protected $meetings;
    protected $documents;
    protected $tags;
    protected $faker;
    protected $faculties;
    protected $generatedMembers;
    protected $generatedTags;
    protected $generatedFaculties;

    public function __construct(
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees,
        MeetingsRepositoryInterface $meetings,
        DocumentsRepositoryInterface $documents,
        TagsRepositoryInterface $tags,
        FacultiesRepositoryInterface $faculties,
        Faker\Factory $fakerFactory
    ) {
        $this->members = $members;
        $this->fees = $fees;
        $this->meetings = $meetings;
        $this->faker = $fakerFactory::create();
        $this->generatedMembers = [];
        $this->gerneratedFaculties = [];
        $this->documents = $documents;
        $this->tags = $tags;
        $this->faculties = $faculties;
    }

    public function run()
    {
        $this->generateFaculties();
        $this->generateMembers(50);
        $this->generateFees();
        $this->generateMeetings(15);
        $this->generateTags(10);
        $this->generateDocuments(5);
    }

    private function generateFaculties()
    {
        $faculties = [
            "Fax 1" => "FAX1",
            "Fax 2" => "FAX2",
            "Fax 3" => "FAX3"
        ];

        foreach ($faculties as $title => $abbr) {
            $faculty = new Faculty();
            $faculty->setTitle($title);
            $faculty->setAbbreviation($abbr);
            $faculty->setUniversity('Example University');

            $this->faculties->store($faculty);

            $this->generatedFaculties[] = $faculty;
        }
    }

    private function generateMembers($count = 200)
    {
        print "Generating members started...\n";

        $fieldOfStudies = ["Computer Science", "Electrical Engineering", "Automation"];
        $birthYearFrom = "-27 years";
        $birthYearTo = "-19 years";

        for ($i = 0; $i <= $count; $i++) {
            $member = new Member();

            $member->setEmail($this->faker->email . rand(0, 9123));
            $member->setPassword(Hash::make('123456'));
            $member->setFirstName($this->faker->firstName);
            $member->setLastName($this->faker->lastName);

            $member->setFaculty($this->faker->randomElement($this->generatedFaculties));

            $member->setFieldOfStudy($this->faker->randomElement($fieldOfStudies));
            $member->setYearOfGraduation($this->faker->numberBetween(2015, 2018));

            $member->setApproved($this->faker->boolean(90));

            $social = strtolower($member->getFirstName() . $member->getLastName());

            if ($this->faker->boolean(60)) {
                $member->setFacebook($social);
            }

            if ($this->faker->boolean(60)) {
                $member->setTwitter($social);
            }

            if ($this->faker->boolean(60)) {
                $member->setGooglePlus($social);
            }

            $member->setPhoneNumber($this->faker->phoneNumber);
            $member->setWebsite("http://". $social .".com");

            $birthday = $this->faker->dateTimeBetween($birthYearFrom, $birthYearTo);

            $member->setBirthday($birthday);
            $member->setBoardMember($this->faker->boolean(20));

            $this->generatedMembers[] = $member;

            $this->members->store($member);
        }

        print "Generated ". $count ." members.\n";
    }

    private function hasGeneratedMembers()
    {
        return (count($this->generatedMembers) > 0);
    }

    private function generateFees()
    {
        if (!$this->hasGeneratedMembers()) {
            return;
        }

        print "Generating fees started...\n";
        $fees = 0;

        foreach ($this->generatedMembers as $member) {
            $from = $this->faker->dateTimeBetween('-10 years', '-1 year');

            for ($i = 0; $i < 3; $i++) {
                $fee = new Fee();

                $fee->setFromDate($from);

                $to = clone $from;
                $to->modify('+1 year');
                $fee->setToDate($to);

                $fee->setMember($member);

                $this->fees->store($fee);
                $fees++;

                $now = new \DateTime('now');

                if ($to > $now) {
                    break;
                }

                $from->modify('+1 day');
                $from->modify('+1 year');
            }
        }

        print "Generated ". $fees ." fees.\n";
    }

    private function generateMeetings($count = 50)
    {
        if (!$this->hasGeneratedMembers()) {
            return;
        }

        print "Generating meetings started...\n";
        $attendings = 0;

        // Calculate the date for the first meeting..
        // There will be one meeting per week.
        $ago = $count * 7;
        $date = new \DateTime('-' . $ago . ' days');

        for ($i = 0; $i < $count; $i++) {
            $meeting = new Meeting();

            $meeting->setDate($date);
            $meeting->setLocation($this->faker->streetAddress);
            $meeting->setInfo("<p>This is fake meeting report with randomly generated information.</p>");

            $creator = $this->generatedMembers[0];
            $attendants = $this->pickGeneratedMembers($this->calculateNeededAttendants());
            $attendings += count($attendants);

            $meeting->setCreator($creator);
            $meeting->addAttendants($attendants);

            $this->meetings->store($meeting);

            $date->modify('+1 week');
        }

        print "Generated ". $count ." meetings with ". $attendings ." total attendings\n";
    }

    private function pickGeneratedMembers($count = 0)
    {
        $pickedIndexes = [];
        $pickedMembers = [];

        for ($i = 0; $i < $count; $i++) {
            $index = $this->faker->numberBetween(0, count($this->generatedMembers) - 1);

            if (!in_array($index, $pickedIndexes)) {
                $pickedIndexes[] = $index;
                $pickedMembers[] = $this->generatedMembers[$index];
            }
        }

        return $pickedMembers;
    }

    private function pickGeneratedTags($count = 0)
    {
        $tags = [];

        for ($i=0; $i < $count; $i++) {
            $tags[] = $this->generatedTags[array_rand($this->generatedTags)];
        }

        return $tags;
    }

    private function calculateNeededAttendants()
    {
        $count = count($this->generatedMembers);

        if ($count == 0) {
            return 0;
        }

        if ($count < 50) {
            return $this->faker->numberBetween(1, $count);
        }

        if ($count < 100) {
            return $this->faker->numberBetween(50, $count);
        }

        return $this->faker->numberBetween(50, 100);
    }

    public function generateDocuments($count = 100)
    {
        if (!$this->hasGeneratedMembers()) {
            return;
        }

        print "Generating documents started...\n";

        $countOpenings = 0;
        $countOpeners = 0;

        for ($i = 0; $i < $count; $i++) {
            $document = new Document();

            $title = $this->faker->sentence(rand(6, 15));
            $document->setTitle($title);
            $document->setDescription($this->faker->realText());
            $document->setUrl("http://storgman.local");

            $submitter = $this->generatedMembers[rand(0, count($this->generatedMembers) - 1)];

            $document->setSubmitter($submitter);

            $openers = $this->pickGeneratedMembers(rand(0, count($this->generatedMembers) - 1));

            foreach ($openers as $opener) {
                for ($j=0; $j < rand(1, 10); $j++) {
                    $document->addOpener($opener);
                    $countOpenings++;
                }

                $countOpeners++;
            }

            $tags = $this->pickGeneratedTags(rand(0, 5));

            foreach ($tags as $tag) {
                $document->addTag($tag);
            }

            $this->documents->store($document);
        }

        printf("Generated %d documents with total %d openings\n", $count, $countOpenings);
    }

    public function generateTags($count = 10)
    {
        print "Generating documents' tags started...\n";

        for ($i=0; $i < $count; $i++) {
            $tag = new Tag();
            $tag->setName($this->faker->domainWord);
            $this->tags->store($tag);

            $this->generatedTags[] = $tag;
        }

        print "Generated ". $count ." tags.\n";
    }
}
