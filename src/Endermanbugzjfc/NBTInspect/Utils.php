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
use pocketmine\nbt\tag\{ByteTag, IntTag, NamedTag, ListTag, ShortTag};

use function str_replace;
use function strpos;
use function substr;

use const PHP_INT_MAX;

class Utils {

	public static function getTagType(NamedTag $tag) : ?string {
		if (($r = strpos($type = $tag->getName(), 'Array')) !== false) $name = substr($type, 0, $r - 1) . ' ' . substr($type, $r - 1);
		$name = str_replace('Tag', '', $name ?? $tag->getName());
		return $name;
	}
	
	public static function getTagChainPath(array $openedtags) : string {
	    $path = '';
		foreach ($openedtags as $i => $tag) $path .= ($i > 0 ? TF::RESET . TF::YELLOW . ' > ' : '') . TF::BOLD . TF::GOLD . (empty($tag->getName()) and isset($openedtags[$i]) and $openedtags[$i] instanceof ListTag ? (self::getListTagChildIndex($openedtags[$i], $tag)) : $tag->getName()) . TF::AQUA . ' (' . self::getTagType($tag) . ')';
		return $path;
	}

	public static function getAcceptableNumberValue(NamedTag $tag) : ?string {
		switch ($tag->getType()) {
				
			case NBT::TAG_Byte:
			case NBT::TAG_ByteArray:
				return 'Int (-128 ~ 127)';

            case NBT::TAG_Short:
				return 'Int (-32768 ~ 32767)';

            case NBT::TAG_Int:
			case NBT::TAG_IntArray:
				return 'Int (-2147483648 ~ 2147483647)';

            case NBT::TAG_Long:
				// return 'Int (-9223372036854775808[-2ˆ63] ~ 9.2233720368548E+18[2ˆ63-1])';
				return 'Int (-9223372036854775808[-2ˆ63] ~ ' . PHP_INT_MAX . '[PHP_INT_MAX])';

            case NBT::TAG_Float:
				return 'Float (Binary32 Single Precision)';

            case NBT::TAG_Double:
				return 'Float (Binary64 Double Precision)';

        }

        return null;
	}

	public static function validateNumberValue(NamedTag $tag, int $value) : bool {
		switch ($tag) {
			case $tag instanceof ByteTag:
				return !($value < -128 or $value > 127);

            case $tag instanceof ShortTag:
				return !($value < -0x8000 or $value > 0x7fff);

            case $tag instanceof IntTag:
				return !($value < -0x80000000 or $value > 0x7fffffff);
        }
		return true;
	}

}