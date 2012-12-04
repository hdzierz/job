<?
	class Select{
		var $defaultText = "Please Select";
		var $defaultValue = "0";
		var $name		 = "";
		var $onChange	 = "";
		var $prevValue	 = array();
		var $isDisabled  = false;
		var $isReadOnly  = false;
		var $size=1;
		var $multiple=0;
		var $options = array();
		
		function Select($nameI){
			$this->name = $nameI;
		}
		
		function setOption($value,$text){
			$this->options[$text] = $value;
		}
		
		function startSelect(){
			$this->start();
		}
		
		function writeSelect(){
			foreach($this->options as $key=>$value){
				$this->addOption($value,$key);
			}
		}
		
		function stopSelect(){
			$this->stop();
		}
		function start(){
			
?>
			<select <? if($this->multiple){ ?> multiple <? }?> size=<?=$this->size?> <? if($this->isDisabled){?> disabled <? }?> <? if($this->isReadOnly){?> readonly='true' <? }?> name="<?=$this->name?>" id="<?=$this->name?>"  onChange="<?=$this->onChange?>">
				<option <? if(in_array($this->defaultValue,$this->prevValue)){?> selected <? }?> value="<?=$this->defaultValue?>"><?=$this->defaultText?></option>
<?			
		}
		function addOption($value,$text){
?>
			<option value="<?=$value?>" <? if(in_array($value,$this->prevValue)){?> selected <? }?> ><?=$text?></option>
<?
		}
		function setOptionIsVal($value){
			if(is_array($value))
				$this->prevValue=$value;
			else{
				$this->prevValue[] = $value;
			}
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
		
		function writeYearSelectFT(){
			$from = 2006;
			$to = date('Y');
			$to+=3;
			
			$this->start();
				for($i=$to;$i>=$from;$i--){
					$this->addOption($i,$i);
				}
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