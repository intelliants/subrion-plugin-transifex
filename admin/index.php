<?php
//##copyright##

$iaDb->setTable('language');
if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (isset($_POST['upload']))
	{
		$error = false;
		$messages = array();

		if (empty($_FILES['file']['name']))
		{
			$error = true;
			$messages[] = iaLanguage::get('attach_file_first');
		}
		else
		{
			$uploadedFileName = IA_UPLOADS . $_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFileName);

			function csvToArray($filename='', $delimiter=',')
			{
				if(!file_exists($filename) || !is_readable($filename))
					return FALSE;

				$header = NULL;
				$data = array();
				if (($handle = fopen($filename, 'r')) !== FALSE)
				{
					while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
					{
						$data[$row[0]] = $row[1];
					}
					fclose($handle);
				}
				return $data;
			}

			// get initial current language phrases array
			if ($phrases = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`code` = '" . $_POST['lang_code'] . "'"))
			{
				// translated language phrases
				$translatedPhrases = csvToArray($uploadedFileName);

				foreach ($phrases as $phrase)
				{
					if (isset($translatedPhrases[$phrase['key']]))
					{
						if ($phrase['original'] != $phrase['value'])
						{
							$phrase['original'] = $phrase['value'];
						}
						elseif ($phrase['original'] == $phrase['value'])
						{
							$phrase['value'] = $phrase['original'] = $translatedPhrases[$phrase['key']];
						}

						$phrase['code'] = $_POST['lang_code'];
						$iaDb->update($phrase);
					}
				}

				$messages[] = iaLanguage::get('successfully_imported');
			}
			else
			{
				$error = true;
				$messages[] = iaLanguage::get('create_lang_first');
			}
		}

		$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
	}

	$iaView->display('index');
}
$iaDb->resetTable();