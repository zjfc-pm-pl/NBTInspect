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
namespace Endermanbugzjfc\NBTInspect\uis;

use pocketmine\{Player, utils\TextFormat as TF};
/*use pocketmine\scheduler\{ClosureTask, TaskHandler};
use pocketmine\nbt\tag\{NamedTag, CompoundTag, ByteTag, ShortTag, IntTag, LongTag, FloatTag, DoubleTag, StringTag, ByteArrayTag, IntArrayTag};*/

use Endermanbugzjfc\NBTInspect\NBTInspect;

class InventoryUI extends BaseUI {

	protected $preinspect = null;
	
	public static function getName() : string {
		return 'Inventory';
	}
	
	public static function accessibleBy(InspectSession $session) : bool {
		return $session->getSessionOwner() instanceof Player;
	}

	public function preInspect() {
		/*$this->preinspect = NBTInspect::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(int $ct) : void {
			$this->getSession()->getPlayer()->sendPopup(TF::YELLOW . 'Loading NBT tag to inspect...');
		}), 40);*/

		return $this;
	}

	public function inspect() {
		/*if ($this->preinspect instanceof TaskHandler) $this->preinspect->cancel();
		switch (true) {
			case $tag instanceof CompoundTag:
			case $tag instanceof ListTag:
				return new forms\NestedTagInspectForm($this);
				break;

			case $tag instanceof StringTag:
			case $tag instanceof ByteArrayTag:
				return new forms\StringValueEditForm($this);
				break;

			case $tag instanceof ByteTag:
			case $tag instanceof ShortTag:
			case $tag instanceof IntTag:
			case $tag instanceof LongTag:
			case $tag instanceof FloatTag:
			case $tag instanceof DoubleTag:
				return new forms\NumbericValueEditForm($this);
				break;

			case $tag instanceof IntArrayTag:
				return new forms\BatchNumbericValueEditForm($this);
				break;

			default:
				throw new \RuntimeException('An invalid tag type has given');
				break;
		}*/
		$f = new jojoe77777\FormAPI\ModalForm(function($data = false) : void {
			NBTInspect::getInstance()->switchPlayerUI(NBTInspect::UI_DEFAULT);
			$this->getSession()->switchUI();
			$this->getSession()->inspectCurrentTag();
		});
		$f->setTitle('');
		$f->setContent(TF::YELLOW . 'More UIs are coming soon!');
		$f->setButton1('');
		$f->setButton2('');
		$s->getPlayer()->sendForm($f);
		return $this;
	}

	public function close() {return $this;}
	
}
