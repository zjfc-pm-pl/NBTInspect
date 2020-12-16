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
namespace Endermanbugzjfc\NBTInspect\uis\inventories;

use Endermanbugzjfc\NBTInspect\uis\InventoryUI;

use muqsit\invmenu\{InvMenu, transaction\InvMenuTransaction, transaction\InvMenuTransactionResult};

abstract class BaseInventoryUIActionClass {
	
	public function __construct(InventoryUI $ui) {
		$this->ui = $ui;
	}

	public function getUIInstance() : InventoryUI {
		return $this->ui;
	}

	abstract protected function apply() : void;
	abstract protected function react(InvMenuTransaction $transaction) : InvMenuTransactionResult;
	
}
