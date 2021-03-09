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
    ByteTag,
    DoubleTag,
    FloatTag,
    IntTag,
    NamedTag,
    ListTag,
    ShortTag};

use function str_replace;
use function strpos;
use function substr;

use const PHP_FLOAT_MIN;
use const PHP_FLOAT_MAX;

class Utils {

	public static function getTagType(NamedTag $tag) : ?string {
		if (($r = strpos($type = $tag->getName(), 'Array')) !== false) $name = substr($type, 0, $r - 1) . ' ' . substr($type, $r - 1);
		$name = str_replace('Tag', '', $name ?? $tag->getName());
		return $name;
	}

    /**
     * @param NamedTag[] $openedtags
     * @return string
     */
	public static function getTagChainPath(array $openedtags) : string {
	    $path = '';
		foreach ($openedtags as $i => $tag) $path .= ($i > 0 ? TF::RESET . TF::YELLOW . ' > ' : '') . TF::BOLD . TF::GOLD . (empty($tag->getName()) and isset($openedtags[$i]) and $openedtags[$i] instanceof ListTag ? (self::getListTagChildIndex($openedtags[$i], $tag)) : $tag->getName()) . TF::AQUA . ' (' . self::getTagType($tag) . ')';
		return $path;
	}

	public static function getListTagChildIndex(ListTag $parent, NamedTag $tag) : ?int {
	    foreach ($parent as $i => $stag) if ($stag === $tag) return $i;
	    return null;
    }

    /**
     * @param NamedTag|int $tag The NBT tag instance of type ID
     * @return string|null
     */
	public static function getAcceptableNumberValue($tag) : ?string {
	    // Source: https://minecraft.gamepedia.com/NBT_format
		switch ($tag instanceof NamedTag ? $tag->getType() : $tag) {
				
			case NBT::TAG_Byte:
				return 'Int (-128[-(2^7)] ~ 127[2^7-1])';

            case NBT::TAG_Short:
				return 'Int (-32768[-(2^15)] ~ 32767[2^16-1])';

            case NBT::TAG_Int:
				return 'Int (-2147483648[-(2^31)] ~ 2147483647[2^31-1])';

            case NBT::TAG_Long:
				 return 'Int (-9223372036854775808[-(2ˆ63)] ~ 9223372036854775807[2ˆ63-1])';

            case NBT::TAG_Float:
				return 'Float (Maximum value about (3.4*10)^38 [Binary32 Single Precision])';

            case NBT::TAG_Double:
				return 'Float (This machine: ' . PHP_FLOAT_MIN . ' ~ ' . PHP_FLOAT_MAX . ', Wiki: Maximum value about (1.8*10)^308 [Binary64 Double Precision])';

        }

        return null;
	}

    /**
     * @param NamedTag $tag
     * @param int|float $value
     * @return bool
     */
	public static function validateNumberValue(NamedTag $tag, $value) : bool {
		switch ($tag) {
			case $tag instanceof ByteTag:
				return !($value < -128 or $value > 127);

            case $tag instanceof ShortTag:
				return !($value < -0x8000 or $value > 0x7fff);

            case $tag instanceof IntTag:
				return !($value < -0x80000000 or $value > 0x7fffffff);

            case $tag instanceof FloatTag:
            case $tag instanceof DoubleTag:
                return !($value === INF or $value === -INF);
        }
		return true;
	}

}