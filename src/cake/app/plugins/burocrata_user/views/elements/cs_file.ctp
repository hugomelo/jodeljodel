<?php

switch ($type[0])
{
	case 'buro':
		switch ($type[1])
		{
			case 'form':
				echo $this->Buro->sform(array(), array('model' => 'BurocrataUser.CsFile'));
					echo $this->Buro->submit(array(), array(
						'label' => 'Save',
						'cancel' => array(
							'label' => 'cancel'
						)
					));
				echo $this->Buro->eform();
				echo $this->Bl->floatBreak();
			break;
			
			case 'view':
				echo $this->Bl->pDry(
					'Uploaded file: '.
					$this->Bl->anchor(
						array(),
						array(
							'url' => $this->Bl->fileUrl($data['SfilStoredFile']['id'], '', true)
						),
						$data['SfilStoredFile']['original_filename']
					)
				);
				echo $this->Bl->paraDry(explode("\n", $data['CsFile']['description']));
			break;
		}
	break;
}