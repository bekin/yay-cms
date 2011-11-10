<?php
class TagCloudWidget extends CWidget
{
	public $cacheDuration = 3600;

	public function init() {
		parent::init();
	}

	public function run() {

		if($this->beginCache('yay_cms_tag_cloud', array(
						'duration' => $this->cacheDuration ))) { 
			$tags = array();
			$result = Yii::app()->db->createCommand()
				->select('tags')
				->from('sitecontent')
				->where('visible > 0')
				->order('tags')
				->queryAll();

			foreach($result as $record) {
				$words = explode(',', strip_tags($record['tags']));	
				if($words)
					foreach($words as $word) {
						$word = CHtml::encode($word);
						if(isset($tags[$word]))
						$tags[trim($word)]++;
					else
						$tags[trim($word)] = 1;
				}
			}

			$this->render('tagcloud', array('tags' => $tags));
			$this->endCache();
		}
	}
} 
?>
