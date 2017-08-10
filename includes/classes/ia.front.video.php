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
class iaVideo extends abstractModuleFront
{
    protected static $_table = 'video';

    protected $_itemName = 'video';

    private $_foundRows = 0;

    public $coreSearchEnabled = true;
    public $coreSearchOptions = [
        'tableAlias' => 'v',
        'regularSearchFields' => ['title'],
    ];

    const SOURCE_YOUTUBE = 'youtube';
    const SOURCE_VIMEO = 'vimeo';


    public function get($where, $start = null, $limit = null)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS v.*, m.`fullname` '
            . 'FROM `' . self::getTable(true) . '`  v '
            . 'LEFT JOIN `:table_members` m ON (v.`member_id` = m.`id`)'
            . 'WHERE ' . ($where ? $where . ' AND' : '') . "  v.`status` = 'active' "
            . 'ORDER BY v.`order` '
            . ($start || $limit ? "LIMIT $start, $limit" : '');

        $sql = iaDb::printf($sql, [
            'table_members' => iaUsers::getTable(true),
        ]);

        $rows = $this->iaDb->getAll($sql);

        $this->_foundRows = $this->iaDb->foundRows();
        $this->_processValues($rows);

        return $rows;
    }

    public function getById($id, $decorate = true)
    {
        $row = $this->iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id), self::getTable());

        $decorate && $this->_processValues($row, true);

        return $row;
    }

    public function coreSearch($stmt, $start, $limit, $order)
    {
        $data = [];

        foreach ($this->coreSearchOptions['regularSearchFields'] as $column) {
            $data[] = $this->coreSearchOptions['tableAlias'] . '.`' . $column . '_' . $this->iaCore->language['iso'] . '` LIKE "%' . $stmt . '%"';
        }

        $stmt = $stmt ? '(' . implode(' OR ', $data) . ')' : null;
        $rows = $this->get($stmt, $start, $limit);

        return [$this->getFoundRows(), $rows];
    }

    public function getFoundRows()
    {
        return $this->_foundRows;
    }

    protected function _processValues(&$rows, $singleRow = false, $fieldNames = [])
    {
        parent::_processValues($rows, $singleRow, $fieldNames);

        if ($singleRow) {
            $rows = $this->_getVideoInfo($rows);
        } else {
            foreach ($rows as &$row) {
                $row = $this->_getVideoInfo($row);
            }
        }
    }

    protected function _getVideoInfo(array $entry)
    {
        switch ($entry['source']) {
            case self::SOURCE_YOUTUBE:
                if (preg_match('#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#',
                    $entry['url'], $matches)) {
                    $entry['video_id'] = $matches[0];
                    $entry['youtube_preview'] = "https://img.youtube.com/vi/{$entry['video_id']}/sddefault.jpg";
                }

                break;

            case self::SOURCE_VIMEO:
                if (preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/',
                    $entry['url'], $matches)) {
                    $entry['video_id'] = $matches[5];

                    $previewImages = array_shift(unserialize(file_get_contents("http://vimeo.com/api/v2/video/{$entry['video_id']}.php")));
                    $entry['vimeo_preview'] = $previewImages['thumbnail_large'];
                }

                break;

            default:
                $entry['video_id'] = null;
        }

        return $entry;
    }
}
