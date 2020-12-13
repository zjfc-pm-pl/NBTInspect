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

use Endermanbugzjfc\NBTInspect\Utils;

use function is_null;
use function count;
use function ceil;
use function assert;

class BatchNumbericValueEditForm extends ValueEditForm {

	private $page = 0;
	
	protected function form() : \jojoe77777\FormAPI\Form {
		$f = parent::form();
		$s = $this->getUIInstance()->getSession();
		$t = $this->getUIInstance()->getSession()->getCurrentTag();
		$f->addLabel(TF::YELLOW . 'Acceptable value range: ' . TF::BOLD . TF::GOLD . (Utils::getNumbericTagAcceptableValueRange($t) ?? TF::RED . 'UNKNOWN'));
		$en = 0;
		foreach ($t->getValue() as $k => $v) {
			if ((($this->getPage() + 1) * 10) <= $k) continue;
			if ((($this->getPage() + 1) * 10) > $k) continue;
			$f->addInput('', 'Unset', (string)$v, (string)$en++);
		}
		$f->addInput(TF::AQUA . "Go to page\n" . TF::BLUE . ' - ' . TF::DARK_AQUA . implode("\n" . TF::RESET . TF::BLUE . ' - ' . TF::DARK_AQUA, [
			'-1 = ' . TF::GREEN . 'Done',
			'-2 = ' . TF::GOLD . 'Insert new value at this page',
			'-3 = ' . TF::RED . 'Delete tag'
		]), $this->getPage() + 1 . ' / ' . ceil(count($t->getValue()) / 10), 'page');
		return $f;
	}

	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		if (is_null($data)) {
			if ($s->getRootTag() === $s->getCurrentTag()) return;
			$s->closeTag();
			$s->inspectCurrentTag();
			return;
		}
		$t = $s->getCurrentTag();
		switch ($page = (int)($data['page'] ?? 0)) {
			
			case 0:
				$s->getPlayer()->sendForm($this->form());
				break;

			case -1:
				$reflection = new \ReflectionProperty($t, 'value');
				$reflection->setAccessible(true);
				$reflection->setValue($reflection->class, $value);
				if ($s->getRootTag() === $s->getCurrentTag()) {
					$f = new ApplyConfirmationForm($this->getUIInstance());
					$s->getPlayer()->sendForm($f->form());
					return;
				}
				$s->closeTag();
				$s->inspectCurrentTag();
				break;

			case -2:
				$value = [];
				foreach ($t->getValue() as $k => $v) {
					if ($k === ($this->getPage() + 1) * 10) $value[] = null;
					elseif (($this->getPage() + 1) >= ceil(count($t->getValue()) / 10)) $value[] = null;
					$value[] = $v;
				}
				$reflection = new \ReflectionProperty($t, 'value');
				$reflection->setAccessible(true);
				$reflection->setValue($reflection->class, $value);
				$s->getPlayer()->sendForm($this->form());
				break;

			case -3:
				$s->deleteCurrentTag();
				$s->inspectCurrentTag();
				break;

			default:
				$this->setPage($page);
				break;
		}
	}

	public function getPage() : int {
		return $this->page;
	}

	public function setPage(int $page) {
		$this->page = $page;
		return $this;
	}
	
}
