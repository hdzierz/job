<?
	class Select{
		var $defaultText = "Please Select";
		var $name		 = "";
		var $onChange	 = "";
		var $prevValue	 = "";
		var $isDisabled  = false;
		var $isReadOnly  = false;
		var $size=1;
		var $multiple=0;
		
		function Select($nameI){
			$this->name = $nameI;
		}
		function start(){
?>
			<select <? if($this->multiple){ ?> multiple <? }?> size=<?=$this->size?> <? if($this->isDisabled){?> disabled <? }?> <? if($this->isReadOnly){?> readonly='true' <? }?> name="<?=$this->name?>" onChange="<?=$this->onChange?>">
				<option value=""><?=$this->defaultText?></option>
<?			
		}
		function addOption($value,$text){
?>
			<option value="<?=$value?>" <? if($value==$this->prevValue){?> selected <? }?> ><?=$text?></option>
<?
		}
		function setOptionIsVal($value){
			$this->prevValue=$value;
		}
		function stop(){
?>
			</select>
<?		
		}
		
		function writeMonthSelect(){
			$this->start();
				$this->addOption("1","January");
				$this->addOption("2","February");
				$this->addOption("3","March");
				$this->addOption("4","April");
				$this->addOption("5","May");
				$this->addOption("6","June");
				$this->addOption("7","July");
				$this->addOption("8","August");
				$this->addOption("9","September");
				$this->addOption("10","October");
				$this->addOption("11","November");
				$this->addOption("12","December");
			$this->stop();
		}
		
		function writeYearSelect($before,$after){
		
			$year = date("Y");
			
			$this->start();
				for($i=$year-$before;$i<=$year+$after;$i++){
					$this->addOption($i,$i);
				}
			$this->stop();
		}
	}
?>