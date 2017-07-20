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

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'video';

    protected $_itemName = 'video';

    protected $_helperName = 'video';

    protected $_gridColumns = ['title', 'source', 'date_added', 'date_modified', 'status'];
    protected $_gridFilters = ['status' => self::EQUAL];

    protected $_tooltipsEnabled = true;

    protected $_activityLog = ['item' => 'video'];


    public function init()
    {
        $this->_path = IA_ADMIN_URL . $this->getName() . IA_URL_DELIMITER;
    }

    protected function _modifyGridParams(&$conditions, &$values, array $params)
    {
        if (isset($params['source']))
        {
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
            'featured' => false,
            'status' => iaCore::STATUS_ACTIVE,
            'member_id' => iaUsers::getIdentity()->id,
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

    protected function _getJsonSource()
    {
        if ($rows = $this->_iaField->getField('source', 'video')) {
            $values = explode(',', $rows['values']);

            if (!empty($values)) {
                $data = [];

                foreach ($values as $key => $value) {
                    $data[$key]['value'] = $value;
                    $data[$key]['title'] = iaLanguage::get('field_video_source+' . $value);
                }

                return ['data' => $data];
            }
        }

        return false;
    }
}
