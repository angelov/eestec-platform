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

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\Exceptions\ResourceNotFoundException;
use Angelov\Eestec\Platform\Http\Requests\StoreMeetingRequest;
use Angelov\Eestec\Platform\Paginators\MeetingsPaginator;
use Angelov\Eestec\Platform\Services\MeetingsService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class MeetingsController extends BaseController
{
    protected $request;
    protected $meetings;
    protected $members;
    protected $paginator;
    protected $meetingsService;
    protected $view;
    protected $authenticator;
    protected $session;
    protected $redirector;

    public function __construct(
        Request $request,
        Factory $view,
        Guard $authenticator,
        Store $session,
        Redirector $redirector,
        MeetingsRepositoryInterface $meetings,
        MembersRepositoryInterface $members,
        MeetingsPaginator $paginator,
        MeetingsService $meetingsService
    ) {
        $this->request = $request;
        $this->meetings = $meetings;
        $this->members = $members;
        $this->paginator = $paginator;
        $this->meetingsService = $meetingsService;
        $this->view = $view;
        $this->authenticator = $authenticator;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    /**
     * Display a listing of the meetings.
     * GET /meetings
     *
     * @return Response
     */
    public function index()
    {
        $page = $this->request->get('page', 1);
        $meetings = $this->paginator->get($page, ['attendants']);

        return $this->view->make('meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting report.
     * GET /meetings/create
     *
     * @return Response
     */
    public function create()
    {
        return $this->view->make('meetings.create');
    }

    /**
     * Store a newly created meeting report in storage.
     * POST /meetings
     *
     * @param StoreMeetingRequest $request
     * @return Response
     */
    public function store(StoreMeetingRequest $request)
    {
        $meeting = new Meeting();
        $meeting->date = $request->get('date');
        $meeting->location = $request->get('location');
        $meeting->info = $request->get('details');

        $ids = $request->get('attendants');
        $attendants = [];

        $parsedIds = $this->meetingsService->parseAttendantsIds($ids);

        if (count($parsedIds) > 0) {
            $attendants = $this->members->getByIds($parsedIds);
        }

        $creator = $this->authenticator->user();

        $this->meetings->store($meeting, $creator, $attendants);

        return $this->redirector->route('meetings.index');

    }

    /**
     * Display the specified resource.
     * GET /meetings/{id}
     *
     * @param  int      $id
     * @return Response
     */
    public function show($id)
    {
        $meeting = $this->meetings->get($id);

        return $this->view->make('meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     * GET /meetings/{id}/edit
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($id)
    {
        $meeting = $this->meetings->get($id);
        $attendantsIds = $this->meetingsService->prepareAttendantsIds($meeting->attendants->all());

        return $this->view->make('meetings.edit', compact('meeting', 'attendantsIds'));
    }

    /**
     * Update the specified resource in storage.
     * PUT /meetings/{id}
     *
     * @param StoreMeetingRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(StoreMeetingRequest $request, $id)
    {
        $attendants = $request->get('attendants');
        $attendants = $this->meetingsService->parseAttendantsIds($attendants);

        $meeting = $this->meetings->get($id);

        $meeting->date = $request->get('date');
        $meeting->location = $request->get('location');
        $meeting->info = $request->get('details');

        $creator = $request->get('created_by');
        $creator = $this->members->get($creator);

        $this->meetings->store($meeting, $creator);
        $this->meetings->updateAttendantsList($meeting, $attendants);

        return $this->redirector->route('meetings.index');

    }

    /**
     * Remove the specified resource from storage.
     * DELETE /meetings/{id}
     *
     * @param  int      $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $data = [];

        try {
            $this->meetings->destroy($id);

            $data['status'] = 'success';
            $data['message'] = 'Meeting deleted successfully.';
        } catch (ResourceNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);
    }

}
