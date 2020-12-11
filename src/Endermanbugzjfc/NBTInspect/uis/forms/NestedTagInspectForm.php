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

use Endermanbugzjfc\NBTInspect\{NBTInspect, uis\UIInterface, uis\InventoryUI};

use function implode;
use function array_map;
use function is_null;
use function array_search;

class NestedTagInspectForm extends BaseForm {

	protected const TYPE = self::SIMPLE;

	private $buttons = [];
	protected $switchui;

	protected function form() : \jojoe77777\FormAPI\Form {
		$f = $this->getForm();
		$s = $this->getUIInstance()->getSession();

		$f->setTitle(TF::BLUE . 'Browse ' . TF::DARK_AQUA . 'Tag');

		$f->setContent(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET . TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $t) : string {
			return $t->getName() . TF::BOLD . '(' . Utils::shortenTagType() . $t . TF::AQUA . ')';
		}, $s->getAllOpenedTags(true))) . "\n" . TF::RESET . TF::YELLOW . 'Tags: ' . TF::AQUA . $t->getCount() . ' of ' . (Utils::printTagType($t) ?? TF::BLACK . 'Mixed') . TF::AQUA . ' type');

		foreach ($t->getValue() as $st) {
			$this->buttons[] = $st;
			$this->getForm()->addButton(TF::BOLD . TF::DARK_AQUA . $st->getName() . "\n" . TF::RESET . Utils::printTagType($st) . ' Tag');
		}

		if (($pui = $s->getPreviousUI()) instanceof UIInterface) {
			if (($r = array_search($pui::class, $uis = NBTInspect::getInstance()->getAllUI())) !== false) unset($uis[$r]);
			$ui = $uis[array_rand($uis)];
		} else $ui = InventoryUI::class;
		$this->switchui = $ui;
		$this->getForm()->addButton(TF::BOLD . TF::DARK_BLUE . 'Switch UI' . TF::RESET . "\n" . TF::BLUE . 'To ' . TF::BOLD . $ui::getName());


		return $f;
	}
	
	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		if (is_null($data)) {
			if ($s->getRootTag() === $s->getCurrentTag()) {
				$f = new ApplyConfirmationForm($this->getUIInstance());
				$s->getPlayer()->sendForm($f->form());
				return;
			}
			$s->closeTag();
			$s->inspectCurrentTag();
		}
		if (is_null($t = $this->buttons[(int)$data] ?? null)) {
			NBTInspect::getInstance()->switchPlayerUI($this->switchui);
			$s->switchUI();
			$s->getUIInstance()->inspect();
			return;
		}
		$s->openTag($t);
		$s->inspectCurrentTag();
	}

}