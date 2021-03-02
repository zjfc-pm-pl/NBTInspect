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
namespace Endermanbugzjfc\NBTInspect\uis\forms;

use pocketmine\{
    utils\TextFormat as TF,
    nbt\tag\NamedTag
};

use jojoe77777\FormAPI\Form;

use Endermanbugzjfc\NBTInspect\Utils;

use function array_map;

abstract class ValueEditForm extends BaseForm {

	protected const TYPE = self::CUSTOM;
	
	protected function form() : Form {
		$f = $this->getForm();
		$s = $this->getUIInstance()->getSession();

		$f->setTitle(TF::BLUE . 'Edit ' . TF::DARK_AQUA . 'Tag Value');

		$f->addLabel(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET . TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $t) : string {
			return $t->getName() . TF::BOLD . '(' . Utils::shortenTagType($t) . TF::AQUA . ')';
		}, $s->getAllOpenedTags(true))));

		return $f;
	}

	abstract protected function react($data = null) : void;
	
}
