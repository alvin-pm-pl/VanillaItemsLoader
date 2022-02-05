<?php

declare(strict_types=1);

namespace alvin0319\VanillaItemsLoader;

use alvin0319\CustomItemLoader\CustomItemManager;
use alvin0319\CustomItemLoader\item\CustomItem;
use pocketmine\item\ItemFactory;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\plugin\PluginBase;
use function str_replace;

final class Loader extends PluginBase{

	protected function onEnable() : void{
		$itemDictionary = GlobalItemTypeDictionary::getInstance()->getDictionary();
		/** @var StringToItemParser $parser */
		$parser = StringToItemParser::getInstance();
		/** @var ItemFactory $itemFactory */
		$itemFactory = ItemFactory::getInstance();
		$c = 0;
		foreach($itemDictionary->getEntries() as $entry){
			$stringId = $entry->getStringId();
			if($parser->parse($stringId) === null){
				$itemId = $this->getItemId();
				$item = new CustomItem($name = str_replace("minecraft:", "", $entry->getStringId()), [
					"id" => $itemId,
					"meta" => 0,
					"texture" => $name,
					"namespace" => $entry->getStringId(),
					"name" => $name
				]);
				$itemFactory->register($item);
				$parser->register($name, static fn() => clone $item);
				CustomItemManager::getInstance()->registerItem($item);
				// TODO: register to CreativeInventory
//				CreativeInventory::getInstance()->add(clone $item);
				// TODO
//				(function() use ($itemId, $entry) : void{
//					/** @noinspection PhpUndefinedMethodInspection */
//					$this->registerMapping($entry->getNumericId(), $itemId, 0);
//				})->call(RuntimeBlockMapping::getInstance());
				$c++;
			}
		}
		$this->getLogger()->debug("Registered $c items");
	}

	public function getItemId() : int{
		$customItemId = 1000;
		/** @var ItemFactory $itemFactory */
		$itemFactory = ItemFactory::getInstance();
		while(true){
			if(!$itemFactory->isRegistered($customItemId)){
				break;
			}
			$customItemId++;
		}
		return $customItemId;
	}
}