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
namespace Endermanbugzjfc\NBTInspect\uis\defaults\forms;

use pocketmine\utils\TextFormat as TF;

use Endermanbugzjfc\NBTInspect\{NBTInspect as Main, Utils, forms\UIInterface, forms\FormUI};

use function implode;
use function array_map;
use function is_null;
use function array_search;

class NestedTagInspectForm extends BaseForm {

	protected const TYPE = self::SIMPLE;

	private $tpointer = [];
	private $from;

	public function __construct(InspectSession $s, ?UIInterface $from) {
		parent::__construct($s);
		$this->from = $from;
	}

	protected function form() : \jojoe77777\FormAPI\Form {
		$f = $this->getForm();
		$s = $this->getSession();

		$f->setTitle(TF::DARK_AQUA . ($t = $s->getOpenedTags()[0])->getName() . TF::BOLD . '(' . (string)Utils::shortenTagType($t) . TF::RESET . TF::BOLD . TF::DARK_AQUA . ')');

		$f->setContent(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET . TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $t) : string {
			return $t->getName() . TF::BOLD . '(' . Utils::shortenTagType() . $t . TF::AQUA . ')';
		}, $s->getOpenedTags(true))) . "\n" . TF::RESET . TF::YELLOW . 'Tags: ' . TF::AQUA . $t->getCount() . ' of ' . (Utils::printTagType($t) ?? TF::BLACK . 'Mixed') . TF::AQUA . ' type');

		foreach ($t->getValue() as $st) $this->addTagToMenu($st);

		if (isset($this->from)) {
			if (($r = array_search($this->from, $ui = Main::getInstance()->getAllUI())) !== false) unset($ui[$r]);
			$this->addSwitchUIButton($ui[$r + 1] ?? ($ui[0] ?? FormUI::class));
		}

		return $f;
	}

	protected function addTagToMenu(NamedTag $t) {
		$this->tagpointer[] = $t;
		$this->getForm()->addButton(TF::BOLD . TF::DARK_AQUA . $t->getName() . "\n" . TF::RESET . Utils::printTagType($t) . ' Tag');

		return $this;
	}

	protected function getTagsPointer() : array {
		return $this->tpointer;
	}
	
	protected function react($data = null) : void {
		if (is_null($data)) return;
		if (is_null($t = $this->getTagsPointer()[(int)$data] ?? null)) {
			Main::getInstance()->switchPlayerUsingUI($this->getSession()->getPlayer(), Main::UI_INVENTORY);
			$this->getSession()->openInspectUI();
			return;
		}
		$s->openTag($t);
	}

	private function addSwitchUIButton(string $ui) {
		$this->getForm()->addButton(TF::BOLD . TF::DARK_BLUE . 'Switch UI' . TF::RESET . "\n" . TF::BLUE . 'To ' . TF::BOLD . $ui::getName());

		return $this;
	}

}