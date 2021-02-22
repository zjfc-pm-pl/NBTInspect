<?php

/*

     					_________	  ______________		
     				   /        /_____|_           /
					  /————/   /        |  _______/_____    
						  /   /_     ___| |_____       /
						 /   /__|    ||    ____/______/
						/   /    \   ||   |   |   
					   /__________\  | \   \  |
					       /        /   \   \ |
						  /________/     \___\|______
						                   |         \ 
							  PRODUCTION   \__________\	

							   翡翠出品 。 正宗廢品  
 
*/

declare(strict_types=1);
namespace Endermanbugzjfc\NBTInspect\uis\fomrs;

use pocketmine\utils\TextFormat as TF;

use jojoe77777\FormAPI\Form;

class SaveConfirmationForm extends BaseForm {

	protected const TYPE = self::SIMPLE;
	
	protected function form() : Form {
		$f = $this->getForm();

		$f->setTitle(TF::BLUE . 'Save ' . TF::DARK_AQUA . 'Confirmation');
		$f->setContent(TF::YELLOW . 'Are you sure to save ' . TF::BOLD . TF::GOLD . 'all the changes?');
		$f->addButton(TF::BOLD . TF::DARK_GREEN . "Save\nAnd Close");
		$f->addButton(TF::DARK_AQUA . "Save And\nContinue inspecting");
		$f->addButton(TF::DARK_AQUA . "Continue\nInspecting");
		$f->addButton(TF::DARK_RED . "Discard\nAnd Close");
		$f->addButton(TF::DARK_RED . "Discard And\nContinue inspecting");

		return $f;
	}

	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		switch ($data ?? 2) {
			case 0:
				$s->saveChanges();
				break;
			
			case 1:
				$s->saveChanges();
				$s->inspectCurrentTag();
				break;

			case 2:
				$s->inspectCurrentTag();
				break;

			case 3:
				$s->discardChanges();
				break;

			case 4:
				$s->discardChanges();
				$s->inspectCurrentTag();
				break;
		}
	}
	
}
