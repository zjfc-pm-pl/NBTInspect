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

use jojoe77777\FormAPI\Form;

use function array_search;
use function array_shift;
use function in_array;

class TagRearrangeForm extends BaseForm {

	protected TYPE = self::CUSTOM;

	protected $illegal = false;
	private $default = [];
	
	protected function form() : Form {
		$f = $this->getForm();
		$s = $this->UIInstance()->getSession();
		$t = $s->getCurrentTag();
		$f->setTitle(TF::BLUE . 'Rearrange ' . TF::DARK_AQUA . 'Tags');
		if ($this->illegal) $f->addLabel(TF::BOLD . TF::RED . 'Illegal arrangement!');

		if (empty($this->getDefaultArrangement())) $this->setDefaultArrangement($t->getValue());

		$value = [];
		foreach ($this->getDefaultArrangement() as $v) $value[] = TF::BOLD . TF::DARK_AQUA . $v->getName() . ' (' . TF::RESET . Utils::shortenTagType($st) . TF::BOLD . TF::DARK_AQUA . ')';
		foreach ($t as $k => $v) $f->addDropdown('', $value, $k);
		return $f;
	}

	protected function react($data = null) : void {
		if (!isset($data)) {
			$s->inspectCurrentTag();
			return;
		}
		$s = $this->UIInstance()->getSession();
		$t = $s->getCurrentTag();
		$rtl = [];
		if ($this->illegal) array_shift($data);
		foreach ($data as $v) $rtl[] = $t[$v];
		foreach ($t as $v) if (!in_array($v, $rtl, true)) {
			$this->illegal = true;
			$this->setDefautArrangement($rtl);
			$s->getPlayer()->sendForm($this->form());
			return;
		}
		foreach ($t as $k => $v) $t->remove($k);
		foreach ($rtl as $k => $v) $t[$k] = $v;
	}

	public function setDefautArrangement(array $da) {
		$this->default = $da;
		return $this;
	}

	public function getDefaultArrangement() : array {
		return $this->default;
	}
	
}
