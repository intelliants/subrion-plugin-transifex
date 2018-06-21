<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
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
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

$iaDb->setTable('language');
if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    if (isset($_POST['upload'])) {
        $error = false;
        $messages = array();

        if (empty($_FILES['file']['name'])) {
            $error = true;
            $messages[] = iaLanguage::get('attach_file_first');
        } else {
            $uploadedFileName = IA_UPLOADS . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFileName);

            function csvToArray($filename = '', $delimiter = ',')
            {
                if (!file_exists($filename) || !is_readable($filename))
                    return FALSE;

                $header = NULL;
                $data = array();
                if (($handle = fopen($filename, 'r')) !== FALSE) {
                    while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                        $data[$row[0]] = $row[1];
                    }
                    fclose($handle);
                }
                return $data;
            }

            // get initial current language phrases array
            if ($phrases = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`code` = '" . $_POST['lang_code'] . "'")) {
                // translated language phrases
                $translatedPhrases = csvToArray($uploadedFileName);

                foreach ($phrases as $phrase) {
                    if (isset($translatedPhrases[$phrase['key']])) {
                        if ($phrase['original'] != $phrase['value']) {
                            $phrase['original'] = $phrase['value'];
                        } elseif ($phrase['original'] == $phrase['value']) {
                            $phrase['value'] = $phrase['original'] = $translatedPhrases[$phrase['key']];
                        }

                        $phrase['code'] = $_POST['lang_code'];
                        $iaDb->update($phrase);
                    }
                }

                $messages[] = iaLanguage::get('successfully_imported');
            } else {
                $error = true;
                $messages[] = iaLanguage::get('create_lang_first');
            }
        }

        $iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
    }

    $iaView->display('index');
}
$iaDb->resetTable();