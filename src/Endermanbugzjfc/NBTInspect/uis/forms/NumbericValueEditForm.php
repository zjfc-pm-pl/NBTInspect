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

use pocketmine\utils\TextFormat as TF;
use pocketmine\nbt\tag\{FloatTag, DoubleTag};

use jojoe77777\FormAPI\Form;

use Endermanbugzjfc\NBTInspect\Utils;

use function array_shift;

class NumbericValueEditForm extends ValueEditForm {

	private $invalid = falses;
	
	protected function form() : Form {
		$f = parent::form();
		$s = $this->getUIInstance()->getSession();
		$t = $this->getUIInstance()->getSession()->getCurrentTag();
		$f->addLabel(TF::YELLOW . 'Acceptable value range: ' . TF::BOLD . TF::GOLD . (Utils::getAcceptableNumberValue($t) ?? TF::RED . 'UNKNOWN'));
		if ($this->invalid) $f->addLabel(TF::BOLD . TF::RED . 'Invalid value given!');
		if ($s->getRootTag() !== $s->getCurrentTag()) $f->addSwitch(TF::RED . 'Delete tag');
		return $f;
	}

	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		if (!isset($data)) {
			if ($s->getRootTag() === $s->getCurrentTag()) return;
			$s->closeTag();
			$s->inspectCurrentTag();
			return;
		}
		array_shift($data);
		array_shift($data);
		if ($this->invalid) array_shift($data);
		if ($s->getRootTag() !== $s->getCurrentTag()) {
			if ($data[0]) {
				$s->deleteCurrentTag();
				$s->inspectCurrentTag();
				return;
			}
			array_shift($data);
		}
		$t = $s->getCurrentTag();
		switch (true) {
			case $t instanceof FloatTag:
			case $t instanceof DoubleTag:
				$t->setValue((float)$data[0]);
				break;
			
			default:
				if (!Utils::validateNumberValue($t, (int)$data[0])) {
					$this->invalid = true;
					$s->getPlayer()->sendForm($this->form());
					return;
				}
				$t->setValue((int)$data[0]);
				break;
		}
		if ($s->getRootTag() === $s->getCurrentTag()) {
			$f = new ApplyConfirmationForm($this->getUIInstance());
			$s->getPlayer()->sendForm($f->form());
			return;
		}
		$s->closeTag();
		$s->inspectCurrentTag();
	}
	
}
