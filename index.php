<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

    switch (count($iaCore->requestPath)) {
        case 2:
            // video details
            $id = (int)$iaCore->requestPath[1];

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

            $category = $iaVideo->getCategoryById($entry['category_id']);

            iaBreadcrumb::toEnd($category['title'], $iaCore->factory('page')->getUrlByName('video') . $category['slug'] . IA_URL_DELIMITER);
            iaBreadcrumb::toEnd($entry['title']);

            if ($entry['source'] == 'youtube') {
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $entry['url'], $match)) {
                    $entry['url'] = $match[1];
                }
            }

            if ($entry['source'] == 'vimeo') {
                $entry['url'] = substr(parse_url($entry['url'], PHP_URL_PATH), 1);
            }


            $iaView->assign('entry', $entry);

            $iaView->title(iaSanitize::tags($entry['title']));

            $iaView->display('view');

        break;

        case 1:
            //videos in category
            $pagination = [
                'start' => 0,
                'total' => 0,
                'limit' => (int)$iaCore->get('video_number'),
                'url' => IA_SELF . '?page={page}'
            ];

            $category = $iaVideo->getCategoryBySlug($iaCore->requestPath[0]);

            iaBreadcrumb::toEnd($category['title'], IA_SELF);

            $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
            $pagination['start'] = ($page - 1) * $pagination['limit'];

            $rows = $iaVideo->getByCategoryId($category['id'], $pagination['start'], $pagination['limit']);

            $pagination['total'] = $iaVideo->getFoundRows();

            $iaView->assign('category', $category);
            $iaView->assign('entries', $rows);
            $iaView->assign('pagination', $pagination);
            $iaView->add_css('_IA_URL_modules/video/templates/front/css/style');
            $iaView->display('index');

        break;

        case 0:
            //categories
            $categories = $iaVideo->getCategories();

            $iaView->assign('categories', $categories);
            $iaView->display('categories');

        break;

        default:
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
    }
}
