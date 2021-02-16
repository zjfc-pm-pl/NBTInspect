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

use pocketmine\{
	nbt\NBT,
	utils\TextFormat as TF
};
use pocketmine\nbt\tag\{
	NamedTag
};

use function substr;
use function strlen;

use const PHP_INT_MAX;

class Utils {

	public static function printTagType(NamedTag $tag, bool $color = true) : ?string {
		return substr($n = $tag->getName(), 0, strlen($n) - 3);
	}

	public static function getAcceptableNumberValue(NamedTag $tag) : ?string {
		switch ($tag->getType()) {
				
			case NBT::TAG_Byte:
			case NBT::TAG_Byte_Array:
				return 'Int (-128 ~ 127)';
				break;
				
			case NBT::TAG_Short:
				return 'Int (-32768 ~ 32767)';
				break;
				
			case NBT::TAG_Int:
			case NBT::TAG_IntArray:
				return 'Int (-2147483648 ~ 2147483647)';
				break;
				
			case NBT::TAG_Long:
				// return 'Int (-9223372036854775808[-2ˆ63] ~ 9.2233720368548E+18[2ˆ63-1])';
				return 'Int (-9223372036854775808[-2ˆ63] ~ ' . PHP_INT_MAX . '[PHP_INT_MAX])';
				break;
				
			case NBT::TAG_Float:
				return 'Float (Binary32 Single Precision)';
				break;
				
			case NBT::TAG_Double:
				return 'Float (Binary64 Double Precision)';
				break;
				
		}
	}

	public static function validateNumberValue(NamedTag $tag, int $value) : bool {
		switch ($tag) {
			case $tag instanceof ByteTag:
				return !($value < -128 or $value > 127);
				break;

			case $tag instanceof ShortTag:
				return !($value < -0x8000 or $value > 0x7fff);
				break;

			case $tag instanceof IntTag:
				return !($value < -0x80000000 or $value > 0x7fffffff);
				break;
		}
		return true;
	}

}