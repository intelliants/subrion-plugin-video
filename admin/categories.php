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
    protected $_name = 'categories';

    protected $_table = 'video_category';

    protected $_itemName = 'videocat';

    protected $_gridColumns = ['title', 'slug', 'date_added', 'date_modified', 'status'];
    protected $_gridFilters = ['status' => self::EQUAL];

    protected $_tooltipsEnabled = true;

    protected $_activityLog = ['item' => 'videocat'];


    public function init()
    {
        $this->_path = IA_ADMIN_URL . 'video/' . $this->getName() . IA_URL_DELIMITER;
    }

    protected function _setDefaultValues(array &$entry)
    {
        $entry = [
            'slug' => '',
            'status' => iaCore::STATUS_ACTIVE
        ];
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

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        $entry['slug'] = strtolower(iaSanitize::alias(isset($data['slug']) && $data['slug'] ? $data['slug'] : $entry['title_' . $this->_iaCore->language['iso']]));
        $entry['status'] = $data['status'];
        $requiredFields = ['title_' . $this->_iaCore->language['iso'], 'slug'];

        foreach ($requiredFields as $fieldName) {
            if (empty($entry[$fieldName])) {
                $this->addMessage(iaLanguage::getf('field_is_empty', ['field' => iaLanguage::get($fieldName)]), false);
            }
        }

        $where = 'slug = :slug && id != :id';
        $this->_iaDb->bind($where, ['slug' => $entry['slug'], 'id' => $this->getEntryId()]);

        if ($this->_iaDb->exists($where, null, $this->getTable())) {
            $this->addMessage('category_already_exists');
        }

        return !$this->getMessages();
    }
}
