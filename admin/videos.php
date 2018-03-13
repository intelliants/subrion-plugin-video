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

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'video';

    protected $_itemName = 'video';

    protected $_helperName = 'video';

    protected $_gridColumns = ['title', 'category_id', 'date_added', 'date_modified', 'order', 'status'];
    protected $_gridFilters = ['status' => self::EQUAL];

    protected $_tooltipsEnabled = true;

    protected $_activityLog = ['item' => 'video'];


    public function init()
    {
        $this->_path = IA_ADMIN_URL . $this->getName() . IA_URL_DELIMITER;
    }

    protected function _modifyGridParams(&$conditions, &$values, array $params)
    {
        if (isset($params['source'])) {
            $conditions[] = '`source` = :source ';
            $values['source'] = "{$params['source']}";
        }
    }

    protected function _modifyGridResult(array &$entries)
    {
        foreach ($entries as &$entry) {
            empty($entry['source']) || $entry['source'] = iaField::getLanguageValue($this->getItemName(), 'source', $entry['source']);
        }
    }

    protected function _setDefaultValues(array &$entry)
    {
        $entry = [
            'category_id' => 0,
            'featured' => false,
            'status' => iaCore::STATUS_ACTIVE,
            'member_id' => iaUsers::getIdentity()->id,
        ];
    }

    protected function _setPageTitle(&$iaView, array $entryData, $action)
    {
        if (in_array($iaView->get('action'), array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT))) {
            $iaView->title(iaLanguage::get($iaView->get('action') . '_video'));
        }
    }

    protected function _entryUpdate(array $entryData, $entryId)
    {
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryUpdate($entryData, $entryId);
    }

    protected function _entryAdd(array $entryData)
    {
        $entryData['date_added'] = date(iaDb::DATETIME_FORMAT);
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryAdd($entryData);
    }

    protected function _assignValues(&$iaView, array &$entryData)
    {
        parent::_assignValues($iaView, $entryData);

        $categories = $this->_iaDb->keyvalue(['id', 'title_' . $this->_iaCore->language['iso']], null, 'video_category');
        $iaView->assign('categories', $categories);
    }

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        $entry['category_id'] = $data['category_id'];
        if ($entry['category_id'] == 0) {
            $this->addMessage(iaLanguage::getf('field_is_not_selected', ['field' => iaLanguage::get('category_id')]), false);
        }

        return !$this->getMessages();
    }

    protected function _gridQuery($columns, $where, $order, $start, $limit)
    {
        $langCode = $this->_iaCore->language['iso'];

        $sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS v.`id`,  v.`title_$langCode` as `video_title`, v.`date_added`, v.`status` , v.`order` , c.`title_$langCode` as `category`, 1 `update`, 1 `delete`
FROM `:prefixvideo` v
LEFT JOIN `:prefixvideo_category` c ON (c.`id` = v.`category_id`) 
:where 
:order
LIMIT :start, :limit
SQL;

        $sql = iaDb::printf($sql, [
            'columns' => $columns,
            'prefix' => $this->_iaDb->prefix,
            'table_video' => self::getTable(),
            'start' => $start,
            'limit' => $limit,
            'where' => $where ? 'WHERE ' . $where . ' ' : '',
            'order' => $order,
        ]);

        return $this->_iaDb->getAll($sql);
    }
}
