<?php
namespace iJoshuaHD;

use pocketmine\event\Listener;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\Server;


class Main extends PluginBase implements Listener{

	public $temp = array();
    
    public function onEnable(){
        
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->getLogger()->info(TextFormat::YELLOW . "TimeLimitCMD Initializing [...]");
		
		$this->saveDefaultConfig();
		$this->reloadConfig();
		
		$this->cfg = new Config($this->getDataFolder(). "config.yml", Config::YAML);
	
		$this->getLogger()->info(TextFormat::AQUA ."Everything is Loaded!");

			
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
	
        switch($command->getName()) {
					
				case "tlcmd":
				
					if ((!($sender->hasPermission("tl.command.cmd")) and (!($sender->isOp())))){
						$sender->sendMessage("You dont have permission to use this command.");
					}else{
						if(!isset($args[0])){
							$sender->sendMessage(TextFormat::GREEN . "[TLCMD] Usage: /tlcmd <add / remove> <command>");
						}elseif(($args[0] !== "add") or ($args[0] !== "remove")){
							$sender->sendMessage(TextFormat::GREEN . "[TLCMD] Usage: /tlcmd <add / remove> <command>");
						}else{
							if($args[0] == "add"){
								if(!isset($args[1])){
									$sender->sendMessage(TextFormat::GREEN . "[TLCMD] Usage: /tlcmd add <command>");
								}else{
									//todo
								}
							}elseif($args[0] !== "remove"){
								if(!isset($args[1])){
									$sender->sendMessage(TextFormat::GREEN . "[TLCMD] Usage: /tlcmd remove <command>");
								}else{
									//todo
								}
							}
						}
					}
					
				break;
				
        }
    }

	public function onCMDExecution(PlayerCommandPreprocessEvent $event){
	
		$cmd = strtolower($event->getMessage());
		$cmd_trim = preg_split("/[\s,]+/", $cmd);
		$cmd_name = $cmd_trim[0];
		$cmd_slash = preg_split('//', $cmd_name[0], -1, PREG_SPLIT_NO_EMPTY);
		
        $player = strtolower($event->getPlayer()->getName());
        $getTick = $this->getServer()->getTick();
        $secs = $this->cfg->get("Command Interval");
		
		if($secs < 60){
			$time_dig = $secs;
			if($time_dig == 1){
				$var_name = " sec.";
			}else{
				$var_name = " secs.";
			}
		}elseif($secs == 60 or $secs > 60){
			if($secs == 3600 or $secs > 3600){
				$time_dig = round($secs / 3600);
				if($time_dig == 1){
					$var_name = " hr.";
				}else{
					$var_name = " hrs.";
				}
			}else{
				$time_dig = round($secs / 60);
				if($time_dig == 1){	
					$var_name = " min.";
				}else{
					$var_name = " mins.";
				}
			}
		}
		
		if (!($event->getPlayer()->isOp())){

					if("/help" == $cmd_name){
		
						if(isset($this->temp[$player])){
						
							$playerTick = $this->temp[$player];
							
							$this->temp[$player] = $getTick;
							
							if($getTick - $playerTick < 20 * $secs){
							
								$event->getPlayer()->sendMessage("You can use this CMD again next " . $time_dig . $var_name);
								$event->setCancelled();
								
							}
							
						}else{
							$this->temp[$player] = $getTick;
						}
						
					}
				
				
			
		}
    }   
    
    public function onDisable() {
		$this->cfg->getAll();
		$this->cfg->save();
    }

}
