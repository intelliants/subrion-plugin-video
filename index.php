<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaVideo = $iaCore->factoryModule('video', 'video');

    $iaDb->setTable('video');

    if (isset($iaCore->requestPath[0])) {
        $id = (int)$iaCore->requestPath[0];

        if (!$id) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        $entry = $iaVideo->getById($id, true);

        if (empty($entry)) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        $iaVideo->incrementViewsCounter($entry['id']);

        $iaUsers = $this->factory('users');

        $member = $iaUsers->getById($entry['member_id']);
        $entry['fullname'] = $member['fullname'];

        iaBreadcrumb::toEnd($entry['title'], IA_SELF);

        $iaView->assign('entry', $entry);

        $iaView->title(iaSanitize::tags($entry['title']));
    } else {
        $pagination = [
            'total' => 0,
            'limit' => (int)$iaCore->get('video_number'),
            'url' => $iaCore->factory('page', iaCore::FRONT)->getUrlByName('video') . '?page={page}'
        ];

        $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
        $start = ($page - 1) * $pagination['limit'];

        $rows = $iaVideo->get('1=1', $start, $pagination['limit']);

        $pagination['total'] = $iaVideo->getFoundRows();

        $iaView->assign('page', $page);
        $iaView->assign('entries', $rows);
        $iaView->assign('pagination', $pagination);
    }

    $iaView->display('index');

    $iaDb->resetTable();
}
