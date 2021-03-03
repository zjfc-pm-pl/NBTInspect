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
namespace Endermanbugzjfc\NBTInspect\sessions;

use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\{CompoundTag, ListTag, NamedTag};

use Endermanbugzjfc\NBTInspect\NBTInspect;

use Endermanbugzjfc\NBTInspect\uis\UIInterface;
use function array_reverse;
use function assert;
use function count;
use function array_shift;
use function get_class;

class InspectSession {

	private $owner;

	/**
	 * @var NamedTag[]
	 */
	private $tag = [];

	/**
	 * @var \Closure|null
	 */
	private $onsave = null;

	/**
	 * @var UIInterface
	 */
	private $ui;

	/**
	 * @var NamedTag
	 */
	private $origin = null;
	
	public function __construct(CommandSender $session_owner, NamedTag $tag = null, callable $onsave = null) {
		$this->owner = $session_owner;
		if (isset($onsave)) $this->setOnSaveCallback($onsave);
		if (isset($tag)) $this->setRootTag($tag);
	}

	public function getSessionOwner() : CommandSender {
		return $this->owner;
	}
	
	public function getUser() : CommandSender {
		return $this->getSessionOwner();
	}

	/**
	 * @var string
	 */
	private $target;

	public function setTarget(string $target) : void {
		$this->target = $target;
	}

	public function getTarget() : string {
		return $this->target;
	}

	public function getRootTag() : ?NamedTag {
		return $this->getAllOpenedTags()[0] ?? null;
	}

	protected function getOriginRootTag() : ?NamedTag {
		return $this->origin;
	}

	public function setRootTag(NamedTag $tag) {
		if (count($this->tag) > 1) throw new \InvalidStateException('Please close all the child tags before changing the root tag');
		$this->origin = clone $tag;
		$this->tag = [clone $tag];

		return $this;
	}

	public function discardChanges() {
		assert($this->getRootTag() instanceof NamedTag);
		$this->tag = [$this->getOriginRootTag()];

		return $this;
	}

	public function saveChanges() {
		$this->getOnSaveCallback()($this->getRootTag());

		return $this;
	}

	public function getCurrentTag() : NamedTag {
		return $this->tag[0];
	}

	public function deleteCurrentTag() {
		$this->closeTag();
		$t = $this->getCurrentTag();
		$tag = $this->getAllOpenedTags()[1];
		switch (true) {
			case $t instanceof CompoundTag:
				if (!$t->hasTag($tag->getName(), get_class($tag))) throw new \InvalidArgumentException('Cannot delete the given tag as it is not a child tag of the parent tag');
				$t->removeTag($tag->getName());
				break;

			case $t instanceof ListTag:
				foreach ($t->getValue() as $k => $v) if ($v === $tag) {
					unset($t[$k]);
					break 2;
				}
				throw new \InvalidArgumentException('Cannot delete the given tag as it is not a child tag of the parent tag');
			
			default:
                assert(false);
        }
		return $this;
	}

	public function getAllOpenedTags(bool $from_current = true) : array {
		if ($from_current) return array_reverse($this->tag);
		return $this->tag;
	}

	public function openTag(NamedTag $tag) {
		$t = $this->getCurrentTag();
		switch (true) {
			case $t instanceof CompoundTag:
				if (!$t->hasTag($tag->getName(), get_class($tag))) throw new \InvalidArgumentException('Cannot open the given tag as it is not a child tag of the parent tag');
				$this->tag[] = $tag->getTag($tag->getName(), get_class($tag));
				break;

			case $t instanceof ListTag:
				foreach ($t->getValue() as $v) if ($v === $tag) {
					$this->tag[] = $v;
					break 2;
				}
				throw new \InvalidArgumentException('Cannot open the given tag as it is not a child tag of the parent tag');
			
			default:
				throw new \InvalidStateException('You cannot open a child tag when the current tag is not ' . CompoundTag::class . ' or ' . ListTag::class);
		}

		return $this;
	}

	public function closeTag(int $amount = 1) {
		for ($i=0; $i < $amount; $i++) {
			if (count($this->tag) <= 1) throw new \BadMethodCallException('Cannot close the root tag, please close the inspect session instead');
			array_shift($this->tag);
		}

		return $this;
	}

	public function inspectCurrentTag() {
		if ($this->getRootTag() === null) $this->getUIInstance()->preInspect();
		else $this->getUIInstance()->inspect();

		return $this;
	}

	public function getUIInstance() : UIInterface {
		if (!isset($this->ui)) $this->switchUI();
		return $this->ui;
	}

	public function switchUI() {
		if (isset($this->ui)) $this->ui->close();
		$ui = NBTInspect::getInstance()->getUserUI(NBTInspect::getInstance()->$this->getSessionOwner());
		assert($ui instanceof UIInterface);
		if ($ui instanceof UIInterface) $this->ui = $ui::create($this);

		return $this;
	}

	public function backToRootTag() : bool {
		if (!$this->getRootTag() instanceof NamedTag) return false;
		$this->tag = [$this->getRootTag()];
		return true;
	}
	
	public function getOnSaveCallback() : ?\Closure {
		return $this->onsave;
	}

	public function setOnSaveCallback(\Closure $onsave) {
		$this->onsave = $onsave;

		return $this;
	}
}
