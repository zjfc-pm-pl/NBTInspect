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
namespace Endermanbugzjfc\NBTInspect;

use poceketmine\{Player, utils\Utils};
use poceketmine\nbt\tag\{CompoundTag, ListTag, NamedTag};

use Endermanbugzjfc\NBTInspect\NBTInspect;

use function array_reverse;
use function assert;
use function count;
use function array_shift;

class InspectSession {

	private $player;
	private $tag = [];
	private $onsave = null;
	private $ui;
	
	public function __construct(Player $player, NamedTag $tag = null, callable $onsave = null) {
		$this->player = $player;
		if (isset($onsave)) $this->setOnSaveCallback($onsave);
		if (isset($tag)) $this->replaceRootTag($tag);
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getRootTag() : ?NamedTag {
		return $this->getAllOpenedTags(true)[0] ?? null;
	}

	public function replaceRootTag(NamedTag $tag) {
		if (count($this->tag) > 1) throw new \InvalidStateException('Please close all the child tags before changing the root tag');
		$this->tag = [clone $tag];

		return $this;
	}

	public function getCurrentTag() : NamedTag {
		return $this->tag[0];
	}

	public function getAllOpenedTags(bool $reverse = false) : array {
		if ($reverse) return array_reverse($this->tag);
		return $this->tag;
	}

	public function openTag(NamedTag $tag) {
		if (!(($this->getCurrentTag() instanceof CompoundTag) or ($this->getCurrentTag() instanceof ListTag))) throw new \InvalidStateException('You cannot open a child tag when the current tag is not ' . CompoundTag::class . ' or ' . ListTag::class);
		$this->tag[] = $tag->getTag($tag->getName(), $tag::class);

		return $this;
	}

	public function inspectCurrentTag() : UIInterface {
		if (!isset($this->getRootTag())) $this->getUIInstance()->preInspect();
		else $this->getUIInstance()->inspect($this->getCurrentTag());
	}

	public function getUIInstance() : UIInterface {
		if (!isset($this->ui)) $this->switchUI();
		return $this->ui;
	}

	public function switchUI() {
		if (isset($this->ui)) $this->ui->close();
		$this->ui = NBTInspect::getPlayerUI($this->getPlayer())::create($this);

		return $this;
	}

	public function closeTag(int $amount = 1) {
		for ($i=0; $i < $amount; $i++) {
			if (count($this->tag) <= 1) throw new \BadMethodCallException('Cannot close the root tag, please close the inspect session instead');
			array_shift($this->tag);
		}

		return $this;
	}

	public function backToRootTag() : bool {
		assert($this->getRootTag() instanceof NamedTag);
		$this->tag = [$this->getRootTag()];
		return $this;
	}
	
	public function getOnSaveCallback() : ?callable {
		return $this->onsave;
	}

	public function setOnSaveCallback(callable $onsave) {
		Utils::validateCallableSignaure(function(NamedTag $edited) {}, $onsave);
		$this->onsave = $onsave;

		return $this;
	}

}
